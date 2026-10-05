<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserCourse;
use Illuminate\Validation\ValidationException;

class ActivityCompletionService
{
    public function complete(User $user, int $activityId): UserActivity
    {
        $activity = Activity::find($activityId);

        if (! $activity) {
            throw ValidationException::withMessages([
                'activity' => ['Activity not found'],
            ]);
        }

        $this->ensureUserIsEnrolled($user, $activity);
        $this->ensureManuallyCompletable($activity);

        return UserActivity::updateOrCreate(
            [
                'user_id' => $user->id,
                'activity_id' => $activityId,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );
    }

    public function incomplete(User $user, int $activityId): void
    {
        $activity = Activity::find($activityId);

        if (! $activity) {
            throw ValidationException::withMessages([
                'activity' => ['Activity not found'],
            ]);
        }

        $this->ensureUserIsEnrolled($user, $activity);

        UserActivity::where('user_id', $user->id)
            ->where('activity_id', $activityId)
            ->delete();
    }

    private function ensureUserIsEnrolled(User $user, Activity $activity): void
    {
        $enrollment = UserCourse::where('user_id', $user->id)
            ->where('course_id', $activity->course_id)
            ->first();

        if (! $enrollment) {
            throw ValidationException::withMessages([
                'enrollment' => ['You are not enrolled in this course'],
            ]);
        }
    }

    private function ensureManuallyCompletable(Activity $activity): void
    {
        if (in_array($activity->activityable_type, [
            'App\\Models\\QuizActivity',
            'App\\Models\\FinalExamActivity',
        ], true)) {
            throw ValidationException::withMessages([
                'activity' => ['Quiz and Final Exam activities must be completed by submitting answers, not manually.'],
            ]);
        }
    }
}
