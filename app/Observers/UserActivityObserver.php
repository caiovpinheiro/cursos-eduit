<?php

namespace App\Observers;

use App\Models\UserActivity;
use App\Models\UserCourse;
use App\Models\Activity;

class UserActivityObserver
{
    /**
     * Handle the UserActivity "created" event.
     */
    public function created(UserActivity $userActivity): void
    {
        $this->updateCourseProgress($userActivity);
    }

    /**
     * Handle the UserActivity "updated" event.
     */
    public function updated(UserActivity $userActivity): void
    {
        $this->updateCourseProgress($userActivity);
    }

    /**
     * Handle the UserActivity "deleted" event.
     */
    public function deleted(UserActivity $userActivity): void
    {
        $this->updateCourseProgress($userActivity);
    }

    /**
     * Update the course progress based on completed activities
     */
    private function updateCourseProgress(UserActivity $userActivity): void
    {
        // Buscar a atividade para pegar o course_id
        $activity = Activity::find($userActivity->activity_id);
        
        if (!$activity) {
            return;
        }

        $courseId = $activity->course_id;
        $userId = $userActivity->user_id;

        // Buscar ou criar a matrícula do usuário no curso
        $enrollment = UserCourse::firstOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
            ],
            [
                'progress' => 0,
            ]
        );

        // Contar total de atividades do curso
        $totalActivities = Activity::where('course_id', $courseId)->count();

        if ($totalActivities === 0) {
            return;
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
    }
}

