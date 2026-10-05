<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Course;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        $this->updateCourseTotalDuration($activity->course_id);
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        $this->updateCourseTotalDuration($activity->course_id);
        
        // Se o course_id mudou, atualizar ambos os cursos
        if ($activity->isDirty('course_id')) {
            $this->updateCourseTotalDuration($activity->getOriginal('course_id'));
        }
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        $this->updateCourseTotalDuration($activity->course_id);
    }

    /**
     * Update the total duration and modules count of a course
     */
    private function updateCourseTotalDuration(int $courseId): void
    {
        $course = Course::find($courseId);
        
        if (!$course) {
            return;
        }
        
        $activities = $course->activities()->with('activityable')->get();
        
        $totalDuration = $activities->sum(function ($activity) {
            return $this->getActivityDuration($activity);
        });
        
        $modulesCount = $activities->count();
        
        $course->update([
            'total_duration' => $totalDuration,
            'modules_count' => $modulesCount,
        ]);
    }

    /**
     * Get activity duration in minutes based on type
     */
    private function getActivityDuration($activity): int
    {
        if (!$activity->activityable) {
            return 0;
        }

        $activityable = $activity->activityable;

        // VideoActivity: duration is stored in seconds, convert to minutes
        if ($activity->activityable_type === 'App\\Models\\VideoActivity') {
            $seconds = $activityable->duration;
            // If duration is null or 0, return 0
            if ($seconds === null || $seconds === 0) {
                return 0;
            }
            return (int) round((int) $seconds / 60); // Convert seconds to minutes
        }

        // QuizActivity: duration_minutes is already in minutes
        if ($activity->activityable_type === 'App\\Models\\QuizActivity') {
            $minutes = $activityable->duration_minutes;
            // If duration_minutes is null, use default of 5 minutes
            return $minutes !== null ? (int) $minutes : 5;
        }

        // FinalExamActivity: duration_minutes is already in minutes
        if ($activity->activityable_type === 'App\\Models\\FinalExamActivity') {
            $minutes = $activityable->duration_minutes;
            // If duration_minutes is null or 0, return 0
            return $minutes !== null ? (int) $minutes : 0;
        }

        // Estimated durations for other types
        $estimatedDurations = [
            'App\\Models\\ArticleActivity' => 10,
            'App\\Models\\EmbedContentActivity' => 10,
            'App\\Models\\SupportMaterialActivity' => 10,
            'App\\Models\\MiniGameActivity' => 15,
        ];

        return $estimatedDurations[$activity->activityable_type] ?? 0;
    }
}

