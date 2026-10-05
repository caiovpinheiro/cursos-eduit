<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCourse;
use App\Models\UserActivity;
use App\Models\Activity;

class RecalculateCourseProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:recalculate-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate progress for all user course enrollments based on completed activities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating course progress for all enrollments...');
        
        $enrollments = UserCourse::with('course')->get();
        $progressBar = $this->output->createProgressBar($enrollments->count());
        $progressBar->start();
        
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            $userId = $enrollment->user_id;

            // Contar total de atividades do curso
            $totalActivities = Activity::where('course_id', $courseId)->count();

            if ($totalActivities === 0) {
                $progressBar->advance();
                continue;
            }

            // Contar atividades concluídas pelo usuário neste curso
            $completedActivities = UserActivity::where('user_id', $userId)
                ->where('completed', true)
                ->whereHas('activity', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->count();

            // Calcular progresso percentual
            $progress = (int) round(($completedActivities / $totalActivities) * 100);

            // Atualizar progresso
            $enrollment->update(['progress' => $progress]);

            // Se atingiu 100%, marcar como completo
            if ($progress >= 100 && is_null($enrollment->completed_at)) {
                $enrollment->markAsCompleted();
            }
            // Se estava completo mas agora não está mais, remover completed_at
            elseif ($progress < 100 && !is_null($enrollment->completed_at)) {
                $enrollment->update(['completed_at' => null]);
            }

            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info('Done! Recalculated progress for ' . $enrollments->count() . ' enrollments.');
        
        return Command::SUCCESS;
    }
}

