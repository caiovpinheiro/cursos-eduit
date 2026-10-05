<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;

class CourseService
{
    public function listCourses(User $user, int $perPage = 15)
    {
        $query = Course::with(['activities']);

        if ($user->isStudent()) {
            $query->where('is_active', true);
        }

        return $query->paginate($perPage);
    }
}