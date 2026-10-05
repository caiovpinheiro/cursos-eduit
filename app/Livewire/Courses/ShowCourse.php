<?php

namespace App\Livewire\Courses;

use App\Models\Course;
use App\Models\UserActivity;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ShowCourse extends Component
{
    use AuthorizesRequests;

    public Course $course;

    public function mount(int $id): void
    {
        $this->course = Course::with(['activities', 'activities.activityable'])->findOrFail($id);

        $user = Auth::user();

        if (! $user && ! $this->course->is_active) {
            abort(404);
        }

        if ($user) {
            $this->authorize('view', $this->course);
        }
    }

    #[Layout('layouts.app')]
    public function render(CourseProgressService $progressService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        $enrollment = null;
        $progress = null;
        $completedActivityIds = [];

        if ($user) {
            $enrollment = $progressService->getEnrollment($user, $this->course->id);

            if ($enrollment) {
                $progress = $progressService->buildProgressPayload($enrollment, $this->course->id, $user);
            }

            $completedActivityIds = UserActivity::where('user_id', $user->id)
                ->where('completed', true)
                ->whereHas('activity', function ($query) {
                    $query->where('course_id', $this->course->id);
                })
                ->pluck('activity_id')
                ->toArray();
        }

        $activityDurationLabels = [];
        foreach ($this->course->activities as $activity) {
            $activityDurationLabels[$activity->id] = $progressService->formatActivityDurationLabel($activity);
        }

        return view('livewire.courses.show-course', [
            'enrollment' => $enrollment,
            'progress' => $progress,
            'completedActivityIds' => $completedActivityIds,
            'isAuthenticated' => (bool) $user,
            'activityDurationLabels' => $activityDurationLabels,
        ])->title($this->course->title);
    }
}
