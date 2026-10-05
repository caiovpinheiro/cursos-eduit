<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Question $question): bool
    {
        // Admin can view all questions
        if ($user->isAdmin()) {
            return true;
        }

        // Student can view questions if enrolled in the course
        if ($user->isStudent()) {
            $activity = $question->questionable->activity;
            $isEnrolled = $user->courses()->where('course_id', $activity->course_id)->exists();
            return $isEnrolled && $activity->course->is_active;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->isAdmin();
    }
}

