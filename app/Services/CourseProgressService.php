<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserCourse;
use Illuminate\Database\Eloquent\Collection;

class CourseProgressService
{
    public function getEnrollment(User $user, int $courseId): ?UserCourse
    {
        return UserCourse::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * @return array<int, int>
     */
    public function getCompletedActivityIds(User $user, int $courseId): array
    {
        return UserActivity::where('user_id', $user->id)
            ->where('completed', true)
            ->whereHas('activity', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->pluck('activity_id')
            ->toArray();
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getCourseActivitiesWithActivityable(int $courseId): Collection
    {
        return Activity::where('course_id', $courseId)
            ->with('activityable')
            ->orderBy('order')
            ->get();
    }

    /**
     * @return array{
     *   progress: int,
     *   total_activities: int,
     *   completed_activities: int,
     *   activities: array<int, array{id: int, title: string, order: int, completed: bool, duration: int}>,
     *   completed_at: mixed,
     *   is_completed: bool
     * }
     */
    public function buildProgressPayload(UserCourse $enrollment, int $courseId, User $user): array
    {
        $activities = $this->getCourseActivitiesWithActivityable($courseId);
        $completedActivityIds = $this->getCompletedActivityIds($user, $courseId);

        $activitiesWithStatus = $activities->map(function (Activity $activity) use ($completedActivityIds) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'order' => $activity->order,
                'completed' => in_array($activity->id, $completedActivityIds, true),
                'duration' => $this->getActivityDuration($activity),
            ];
        })->values()->all();

        return [
            'progress' => $enrollment->progress,
            'total_activities' => $activities->count(),
            'completed_activities' => count($completedActivityIds),
            'activities' => $activitiesWithStatus,
            'completed_at' => $enrollment->completed_at,
            'is_completed' => $enrollment->isCompleted(),
        ];
    }

    public function calculateCompletedMinutes(int $userId, int $courseId): int
    {
        $completedActivityIds = UserActivity::where('user_id', $userId)
            ->where('completed', true)
            ->whereHas('activity', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->distinct()
            ->pluck('activity_id')
            ->unique()
            ->values()
            ->toArray();

        if (empty($completedActivityIds)) {
            return 0;
        }

        $activities = Activity::whereIn('id', $completedActivityIds)
            ->with('activityable')
            ->get();

        $totalMinutes = 0;

        foreach ($activities as $activity) {
            if (! $activity->activityable) {
                continue;
            }

            $totalMinutes += $this->getActivityDuration($activity);
        }

        return $totalMinutes;
    }

    public function activityDurationMinutes(Activity $activity): int
    {
        return $this->getActivityDuration($activity);
    }

    public function formatActivityDurationLabel(Activity $activity): string
    {
        $minutes = $this->getActivityDuration($activity);

        if ($minutes <= 0) {
            return '—';
        }

        return $minutes.' min';
    }

    private function getActivityDuration(Activity $activity): int
    {
        $activityable = $activity->activityable;

        if (! $activityable) {
            return 0;
        }

        if ($activity->activityable_type === 'App\\Models\\VideoActivity') {
            $seconds = $activityable->duration;
            if ($seconds === null || $seconds === 0) {
                return 0;
            }

            return (int) round((int) $seconds / 60);
        }

        if ($activity->activityable_type === 'App\\Models\\QuizActivity') {
            $minutes = $activityable->duration_minutes;
            return $minutes !== null ? (int) $minutes : 5;
        }

        if ($activity->activityable_type === 'App\\Models\\FinalExamActivity') {
            $minutes = $activityable->duration_minutes;
            return $minutes !== null ? (int) $minutes : 0;
        }

        $estimatedDurations = [
            'App\\Models\\ArticleActivity' => 10,
            'App\\Models\\EmbedContentActivity' => 10,
            'App\\Models\\SupportMaterialActivity' => 10,
            'App\\Models\\MiniGameActivity' => 15,
        ];

        return $estimatedDurations[$activity->activityable_type] ?? 0;
    }
}
