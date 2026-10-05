<?php

namespace App\Observers;

use App\Models\VideoActivity;
use App\Models\Activity;
use App\Models\Course;

class VideoActivityObserver
{
    /**
     * Handle the VideoActivity "updated" event.
     */
    public function updated(VideoActivity $videoActivity): void
    {
        // Se a duração mudou, atualizar o total_duration do curso
        if ($videoActivity->isDirty('duration')) {
            $activity = Activity::where('activityable_type', 'App\\Models\\VideoActivity')
                ->where('activityable_id', $videoActivity->id)
                ->first();
            
            if ($activity) {
                $this->updateCourseTotalDuration($activity->course_id);
            }
        }
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

