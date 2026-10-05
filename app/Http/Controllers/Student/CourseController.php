<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of available courses for students
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $courses = Course::where('is_active', true)
            ->with(['activities' => function($query) {
                $query->orderBy('order');
            }])
            ->paginate($perPage);

        return response()->json($courses);
    }

    /**
     * Display the specified course
     */
    public function show(Course $course): JsonResponse
    {
        if (!$course->is_active) {
            return response()->json([
                'message' => 'Course not available',
            ], 404);
        }

        $course->load(['activities' => function($query) {
            $query->orderBy('order');
        }, 'activities.activityType', 'activities.activityable']);

        // Check if user is enrolled
        $enrollment = UserCourse::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        return response()->json([
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Enroll user in a course
     */
    public function enroll(Request $request, Course $course): JsonResponse
    {
        if (!$course->is_active) {
            return response()->json([
                'message' => 'Course not available for enrollment',
            ], 400);
        }

        $userId = Auth::id();

        // Check if already enrolled
        $existingEnrollment = UserCourse::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'User is already enrolled in this course',
            ], 400);
        }

        // Create enrollment
        $enrollment = UserCourse::create([
            'user_id' => $userId,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        return response()->json([
            'message' => 'Successfully enrolled in course',
            'enrollment' => $enrollment,
        ], 201);
    }
}
