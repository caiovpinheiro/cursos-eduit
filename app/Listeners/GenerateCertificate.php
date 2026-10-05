<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Models\Activity;
use App\Models\Certificate;
use App\Models\QuizAttempt;
use App\Services\CertificateDocumentService;

class GenerateCertificate
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CourseCompleted $event): void
    {
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', $event->user->id)
            ->where('course_id', $event->course->id)
            ->first();

        if ($existingCertificate) {
            return; // Certificate already exists
        }

        // Validate that final exam (if exists) was passed
        $finalExamActivity = Activity::where('course_id', $event->course->id)
            ->where('activityable_type', 'App\\Models\\FinalExamActivity')
            ->first();

        if ($finalExamActivity) {
            // Check if user has a passed attempt for the final exam
            $passedAttempt = QuizAttempt::where('user_id', $event->user->id)
                ->where('questionable_id', $finalExamActivity->activityable_id)
                ->where('questionable_type', 'App\\Models\\FinalExamActivity')
                ->where('passed', true)
                ->exists();

            if (!$passedAttempt) {
                // Final exam exists but user hasn't passed it yet [safety check]
                \Log::warning('Certificate generation blocked: Final exam not passed', [
                    'user_id' => $event->user->id,
                    'course_id' => $event->course->id,
                ]);
                return;
            }
        }

        $certificate = Certificate::create([
            'user_id' => $event->user->id,
            'course_id' => $event->course->id,
            'issue_date' => now(),
        ]);

        $certificate->load(['user', 'course']);

        try {
            app(CertificateDocumentService::class)->ensurePreviewImage($certificate);
        } catch (\Throwable $e) {
            \Log::warning('Failed to generate certificate preview image', [
                'certificate_id' => $certificate->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}