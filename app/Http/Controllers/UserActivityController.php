<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\UserActivity;
use App\Services\ActivityCompletionService;
use App\Services\CourseProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserActivityController extends Controller
{
    /**
     * Mark an activity as completed
     */
    public function complete(Request $request, $activityId, ActivityCompletionService $activityCompletionService): JsonResponse
    {
        try {
            $userActivity = $activityCompletionService->complete($request->user(), (int) $activityId);
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
            $message = collect($errors)->flatten()->first() ?? 'Validation error';
            $status = 422;
            $code = 'validation_error';

            if (isset($errors['activity']) && str_contains(strtolower($message), 'not found')) {
                $status = 404;
                $code = 'activity_not_found';
            }

            if (isset($errors['enrollment'])) {
                $status = 403;
                $code = 'not_enrolled';
            }

            return response()->json([
                'message' => $message,
                'errors' => $errors,
                'code' => $code,
            ], $status);
        }

        return response()->json([
            'message' => 'Activity marked as completed',
            'activity' => $userActivity,
        ]);
    }

    /**
     * Mark an activity as incomplete
     */
    public function incomplete(Request $request, $activityId, ActivityCompletionService $activityCompletionService): JsonResponse
    {
        try {
            $activityCompletionService->incomplete($request->user(), (int) $activityId);
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
            $message = collect($errors)->flatten()->first() ?? 'Validation error';
            $status = 422;
            $code = 'validation_error';

            if (isset($errors['activity']) && str_contains(strtolower($message), 'not found')) {
                $status = 404;
                $code = 'activity_not_found';
            }

            if (isset($errors['enrollment'])) {
                $status = 403;
                $code = 'not_enrolled';
            }

            return response()->json([
                'message' => $message,
                'errors' => $errors,
                'code' => $code,
            ], $status);
        }

        return response()->json([
            'message' => 'Activity marked as incomplete',
        ]);
    }

    /**
     * Get completed activities for a course
     */
    public function getCompletedActivities(Request $request, $courseId, CourseProgressService $progressService): JsonResponse
    {
        $user = $request->user();

        // Verificar se o usuário está matriculado no curso
        $enrollment = $progressService->getEnrollment($user, (int) $courseId);

        if (!$enrollment) {
            return response()->json([
                'message' => 'You are not enrolled in this course',
                'code' => 'not_enrolled',
            ], 403);
        }

        // Buscar atividades concluídas
        $completedActivities = UserActivity::where('user_id', $user->id)
            ->where('completed', true)
            ->whereHas('activity', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with('activity')
            ->get();

        $completedIds = $completedActivities->pluck('activity_id')->toArray();

        return response()->json([
            'completed_activities' => $completedActivities,
            'completed_activity_ids' => $completedIds,
            'total_completed' => $completedActivities->count(),
        ]);
    }

    /**
     * Get course progress with detailed activity completion status
     */
    public function getCourseProgress(Request $request, $courseId, CourseProgressService $progressService): JsonResponse
    {
        $user = $request->user();

        // Verificar se o usuário está matriculado no curso
        $enrollment = $progressService->getEnrollment($user, (int) $courseId);

        if (!$enrollment) {
            return response()->json([
                'message' => 'You are not enrolled in this course',
                'code' => 'not_enrolled',
            ], 403);
        }

        return response()->json(
            $progressService->buildProgressPayload($enrollment, (int) $courseId, $user)
        );
    }
}

