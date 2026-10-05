<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourse;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrolledCourseResource;
use App\Http\Requests\StoreCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use App\Services\CourseService;
use App\Services\CourseEnrollmentService;
use App\Services\CourseProgressService;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of courses
     */
    public function index(Request $request, CourseService $service): JsonResponse
    {
        $this->authorize('viewAny', Course::class);
        
        $courses = $service->listCourses(
            $request->user(),
            $request->get('per_page', 15)
        );

        return response()->json($courses);
    }

    /**
     * Store a newly created course
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validated();
        $coverUrl = $this->storeCoverImage($request);
        if ($coverUrl !== null) {
            $validated['cover_image_url'] = $coverUrl;
        }
        unset($validated['cover_image']);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => new CourseResource($course->load('activities')),
        ], 201);
    }

    /**
     * Display the specified course
     */
    public function show(Request $request, $id): JsonResponse
    {
        $course = Course::with(['activities', 'activities.activityable'])->find($id);
        
        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
                'code' => 'course_not_found',
            ], 404);
        }
        
        $this->authorize('view', $course);
        
        $user = $request->user();
        $data = ['course' => new CourseResource($course)];
        
        // If student, include enrollment info
        if ($user->isStudent()) {
            $enrollment = UserCourse::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
            
            $data['enrollment'] = $enrollment;
        }

        return response()->json($data);
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id): JsonResponse
    {
        $course = Course::find($id);
        
        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
                'code' => 'course_not_found',
            ], 404);
        }
        
        $this->authorize('update', $course);

        $coverMimes = (string) config('course.cover_upload.mimes', 'jpg,jpeg,png,webp');
        $coverMaxKb = (int) config('course.cover_upload.max_kb', 2048);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'short_description' => 'sometimes|nullable|string|max:500',
            'long_description' => 'sometimes|nullable|string',
            'cover_image' => "sometimes|required|image|mimes:{$coverMimes}|max:{$coverMaxKb}",
            'workload' => 'sometimes|required|integer|min:1',
            'modules_count' => 'sometimes|nullable|integer|min:0',
            'difficulty_level' => ['sometimes', 'nullable', Rule::in(['iniciante', 'intermediario', 'avancado'])],
            'category' => ['sometimes', 'nullable', Rule::in(['programacao', 'banco_dados', 'produtividade', 'lideranca', 'marketing', 'design', 'negocios', 'tecnologia', 'outros'])],
            'price' => 'sometimes|required|numeric|min:0',
            'promotional_price' => 'sometimes|nullable|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|integer|min:0|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldCoverUrl = $course->cover_image_url;
        $newCoverUrl = $this->storeCoverImage($request);
        if ($newCoverUrl !== null) {
            $validated['cover_image_url'] = $newCoverUrl;
            $this->deleteLocalCoverIfManaged($oldCoverUrl);
        }
        unset($validated['cover_image']);

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => new CourseResource($course->fresh('activities')),
        ]);
    }

    /**
     * Remove the specified course
     */
    public function destroy($id): JsonResponse
    {
        $course = Course::find($id);
        
        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
                'code' => 'course_not_found',
            ], 404);
        }
        
        $this->authorize('delete', $course);
        
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully',
        ]);
    }

    /**
     * Get enrolled courses for the authenticated user
     */
    public function enrolled(Request $request, CourseProgressService $progressService): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        // Get courses where user is enrolled (without loading activities)
        $enrollments = UserCourse::where('user_id', $user->id)
            ->with(['course'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Transform to include course data with enrollment info
        $courses = $enrollments->getCollection()->map(function ($enrollment) use ($request, $user, $progressService) {
            $course = $enrollment->course;
            $courseResource = new EnrolledCourseResource($course);
            
            // Calculate total minutes completed
            $completedMinutes = $progressService->calculateCompletedMinutes($user->id, $course->id);
            
            // Add enrollment data
            $courseData = $courseResource->toArray($request);
            $courseData['enrollment'] = [
                'progress' => $enrollment->progress,
                'completed_at' => $enrollment->completed_at,
                'is_completed' => $enrollment->isCompleted(),
                'completed_minutes' => $completedMinutes,
            ];
            
            return $courseData;
        });
        
        return response()->json([
            'data' => $courses,
            'current_page' => $enrollments->currentPage(),
            'last_page' => $enrollments->lastPage(),
            'per_page' => $enrollments->perPage(),
            'total' => $enrollments->total(),
        ]);
    }

    /**
     * Enroll user in a course
     */
    public function enroll(Request $request, $id, CourseEnrollmentService $enrollmentService): JsonResponse
    {
        $course = Course::findOrFail($id);
        $result = $enrollmentService->enroll($request->user(), $course);

        if (! $result['created']) {
            return response()->json([
                'message' => 'User is already enrolled in this course',
                'code' => 'already_enrolled',
            ], 409);
        }

        return response()->json([
            'message' => 'Successfully enrolled in course',
            'enrollment' => $result['enrollment'],
        ], 201);
    }

    private function storeCoverImage(Request $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        $disk = (string) config('course.cover_upload.disk', 'public');
        $dir = trim((string) config('course.cover_upload.dir', 'course-covers'), '/');
        $path = $request->file('cover_image')->store($dir, $disk);

        $diskBaseUrl = rtrim((string) config("filesystems.disks.{$disk}.url", ''), '/');

        if ($diskBaseUrl !== '') {
            return $diskBaseUrl.'/'.ltrim($path, '/');
        }

        return Storage::url($path);
    }

    private function deleteLocalCoverIfManaged(?string $coverUrl): void
    {
        if (! $coverUrl) {
            return;
        }

        $disk = (string) config('course.cover_upload.disk', 'public');
        $dir = trim((string) config('course.cover_upload.dir', 'course-covers'), '/');
        $publicPrefix = '/storage/'.$dir.'/';
        $urlPath = parse_url($coverUrl, PHP_URL_PATH);

        if (! is_string($urlPath) || ! str_starts_with($urlPath, $publicPrefix)) {
            return;
        }

        $relativePath = ltrim(substr($urlPath, strlen('/storage/')), '/');

        if ($relativePath !== '' && Storage::disk($disk)->exists($relativePath)) {
            Storage::disk($disk)->delete($relativePath);
        }
    }
}

