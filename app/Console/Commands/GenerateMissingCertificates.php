<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCourse;
use App\Models\Certificate;
use App\Events\CourseCompleted;

class GenerateMissingCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate-missing {--course-id= : Generate certificate for a specific course ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate certificates for completed courses that do not have a certificate yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->option('course-id');
        
        $query = UserCourse::where('progress', '>=', 100)
            ->whereNotNull('completed_at')
            ->with(['user', 'course']);

        if ($courseId) {
            $query->where('course_id', $courseId);
            $this->info("Generating missing certificates for course ID: {$courseId}");
        } else {
            $this->info('Generating missing certificates for all completed courses...');
        }

        $enrollments = $query->get();
        
        if ($enrollments->isEmpty()) {
            $this->warn('No completed courses found.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($enrollments->count());
        $progressBar->start();

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($enrollments as $enrollment) {
            try {
                // Check if certificate already exists
                $existingCertificate = Certificate::where('user_id', $enrollment->user_id)
                    ->where('course_id', $enrollment->course_id)
                    ->first();

                if ($existingCertificate) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Ensure relationships are loaded
                if (!$enrollment->relationLoaded('user')) {
                    $enrollment->load('user');
                }
                if (!$enrollment->relationLoaded('course')) {
                    $enrollment->load('course');
                }

                if (!$enrollment->user || !$enrollment->course) {
                    $this->newLine();
                    $this->error("Missing relationships for enrollment ID: {$enrollment->id}");
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                // Dispatch event to generate certificate
                event(new CourseCompleted($enrollment->user, $enrollment->course, $enrollment));
                $generated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error generating certificate for enrollment ID {$enrollment->id}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Done! Generated: {$generated}, Skipped: {$skipped}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}

