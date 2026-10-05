<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Resources\CourseResource;
use App\Http\Requests\StoreCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $courses = Course::with(['activities'])
            ->paginate($perPage);

        return response()->json($courses);
    }

    /**
     * Store a newly created course
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
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
    public function show($id): JsonResponse
    {
        $course = Course::with(['activities', 'activities.activityType', 'activities.activityable'])->find($id);
        
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }
        
        return response()->json([
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'short_description' => $course->short_description,
                'long_description' => $course->long_description,
                'cover_image_url' => $course->cover_image_url,
                'workload' => $course->workload,
                'modules_count' => $course->modules_count,
                'difficulty_level' => $course->difficulty_level,
                'difficulty_level_label' => $course->getDifficultyLevelLabel(),
                'category' => $course->category,
                'category_label' => $course->getCategoryLabel(),
                'price' => $course->price,
                'promotional_price' => $course->promotional_price,
                'discount_percentage' => $course->discount_percentage,
                'final_price' => $course->getFinalPrice(),
                'discount_amount' => $course->getDiscountAmount(),
                'is_free' => $course->isFree(),
                'has_discount' => $course->hasDiscount(),
                'is_active' => $course->is_active,
                'activities_count' => $course->activities->count(),
                'activities' => $course->activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'order' => $activity->order,
                        'activity_type' => [
                            'id' => $activity->activityType->id,
                            'name' => $activity->activityType->name,
                        ],
                        'activityable' => $activity->activityable ? [
                            'type' => class_basename($activity->activityable_type),
                            'data' => $activity->activityable,
                        ] : null,
                        'course_id' => $activity->course_id,
                        'created_at' => $activity->created_at,
                        'updated_at' => $activity->updated_at,
                    ];
                }),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id): JsonResponse
    {
        $coverMimes = (string) config('course.cover_upload.mimes', 'jpg,jpeg,png,webp');
        $coverMaxKb = (int) config('course.cover_upload.max_kb', 2048);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'short_description' => 'sometimes|nullable|string|max:500',
            'long_description' => 'sometimes|nullable|string',
            'cover_image' => "sometimes|required|image|mimes:{$coverMimes}|max:{$coverMaxKb}",
            'workload' => 'sometimes|required|integer|min:1',
            'modules_count' => 'sometimes|nullable|integer|min:0',
            'difficulty_level' => [
                'sometimes',
                'nullable',
                Rule::in(array_keys(Course::getDifficultyLevelOptions()))
            ],
            'category' => [
                'sometimes',
                'nullable', 
                Rule::in(array_keys(Course::getCategoryOptions()))
            ],
            'price' => 'sometimes|nullable|numeric|min:0',
            'promotional_price' => 'sometimes|nullable|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|integer|min:0|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $course = Course::find($id);
        
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $updateData = $request->only([
            'title',
            'description', 
            'short_description',
            'long_description',
            'workload',
            'modules_count',
            'difficulty_level',
            'category',
            'price',
            'promotional_price', 
            'discount_percentage',
            'is_active'
        ]);

        $oldCoverUrl = $course->cover_image_url;
        $newCoverUrl = $this->storeCoverImage($request);
        if ($newCoverUrl !== null) {
            $updateData['cover_image_url'] = $newCoverUrl;
            $this->deleteLocalCoverIfManaged($oldCoverUrl);
        }

        $course->update($updateData);
        $course->refresh();

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => new CourseResource($course->load('activities')),
        ]);
    }

    /**
     * Remove the specified course (soft delete)
     */
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully',
        ]);
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
