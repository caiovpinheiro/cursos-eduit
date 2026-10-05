<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\UserCourse;
use App\Services\CertificateService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class HomeDashboard extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public string $coursesTab = 'todos';

    public string $certificatesTab = 'certificados';

    public function setCoursesTab(string $tab): void
    {
        $this->coursesTab = $tab;
    }

    public function setCertificatesTab(string $tab): void
    {
        $this->certificatesTab = $tab;
    }

    public function render(CertificateService $certificateService)
    {
        /** @var User $user */
        $user = Auth::user();

        $certificates = $certificateService->listForUser($user, 12);

        $enrollments = UserCourse::query()
            ->with('course')
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get();

        $inProgressCount = $enrollments->filter(fn (UserCourse $e) => ! $e->isCompleted())->count();
        $completedCount = $enrollments->filter(fn (UserCourse $e) => $e->isCompleted())->count();

        $studyMinutes = (int) $enrollments->sum(function (UserCourse $enrollment): int {
            $workloadInMinutes = (int) (($enrollment->course->workload ?? 0) * 60);

            return (int) round(($workloadInMinutes * max(0, min(100, $enrollment->progress))) / 100);
        });

        $studyTimeFormatted = $this->formatStudyDuration($studyMinutes);

        $filteredEnrollments = $this->filterEnrollments($enrollments);

        $firstName = explode(' ', trim($user->name))[0] ?? $user->name;

        return view('livewire.dashboard.home-dashboard', [
            'user' => $user,
            'firstName' => $firstName,
            'certificates' => $certificates,
            'enrollments' => $enrollments,
            'filteredEnrollments' => $filteredEnrollments,
            'enrolledCount' => $enrollments->count(),
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'favoritesCount' => 0,
            'studyMinutes' => $studyMinutes,
            'studyTimeFormatted' => $studyTimeFormatted,
            'achievementsCount' => 0,
        ]);
    }

    private function filterEnrollments(Collection $enrollments): Collection
    {
        return match ($this->coursesTab) {
            'em-andamento' => $enrollments->filter(fn (UserCourse $e) => ! $e->isCompleted())->values(),
            'concluidos' => $enrollments->filter(fn (UserCourse $e) => $e->isCompleted())->values(),
            'favoritos' => collect(),
            default => $enrollments,
        };
    }

    private function formatStudyDuration(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0m';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        }
        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$mins}m";
    }
}
