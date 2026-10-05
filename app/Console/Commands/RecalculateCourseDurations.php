<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

class RecalculateCourseDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:recalculate-durations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total_duration and modules_count for all courses based on their activities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating course durations and modules count...');
        
        $courses = Course::with(['activities.activityable'])->get();
        $progressBar = $this->output->createProgressBar($courses->count());
        $progressBar->start();
        
        foreach ($courses as $course) {
            $totalDuration = $course->activities->sum(function ($activity) {
                return $this->getActivityDuration($activity);
            });
            
            $modulesCount = $course->activities->count();
            
            $course->update([
                'total_duration' => $totalDuration,
                'modules_count' => $modulesCount,
            ]);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info('Done! Updated ' . $courses->count() . ' courses.');
        
        return Command::SUCCESS;
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

