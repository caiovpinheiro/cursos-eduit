<?php

namespace App\Observers;

use App\Models\UserCourse;
use App\Models\Certificate;
use App\Events\CourseCompleted;

class UserCourseObserver
{
    /**
     * Handle the UserCourse "updated" event.
     */
    public function updated(UserCourse $userCourse): void
    {
        // Verificar se o curso foi marcado como completo
        // Verificar se completed_at foi alterado de null para um valor (curso acabou de ser completado)
        if ($userCourse->progress >= 100 && !is_null($userCourse->completed_at)) {
            // Verificar se o certificado já existe
            $existingCertificate = Certificate::where('user_id', $userCourse->user_id)
                ->where('course_id', $userCourse->course_id)
                ->first();

            // Se não existe certificado, disparar evento para gerar
            if (!$existingCertificate) {
                // Recarregar relacionamentos para garantir que estão disponíveis
                $userCourse->load(['user', 'course']);
                
                if ($userCourse->user && $userCourse->course) {
                    event(new CourseCompleted($userCourse->user, $userCourse->course, $userCourse));
                }
            }
        }
    }
}

