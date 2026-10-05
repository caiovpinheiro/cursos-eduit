<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Auth\Access\AuthorizationException;

class CourseEnrollmentService
{
    /**
     * @return array{created: bool, enrollment: UserCourse}
     *
     * @throws AuthorizationException
     */
    public function enroll(User $user, Course $course): array
    {
        if (! $user->can('enroll', $course)) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $existingEnrollment = UserCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return [
                'created' => false,
                'enrollment' => $existingEnrollment,
            ];
        }

        $enrollment = UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        return [
            'created' => true,
            'enrollment' => $enrollment,
        ];
    }
}
