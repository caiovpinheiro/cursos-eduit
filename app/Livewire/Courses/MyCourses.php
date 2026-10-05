<?php

namespace App\Livewire\Courses;

use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MyCourses extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: 'all')]
    public string $status = 'all';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    #[Title('Meus cursos')]
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        $query = UserCourse::query()
            ->with('course')
            ->where('user_id', $user->id)
            ->whereHas('course', function ($courseQuery): void {
                if ($this->search !== '') {
                    $courseQuery->where(function ($searchQuery): void {
                        $searchQuery
                            ->where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%')
                            ->orWhere('short_description', 'like', '%' . $this->search . '%');
                    });
                }

                if ($this->category !== '') {
                    $courseQuery->where('category', $this->category);
                }
            });

        if ($this->status === 'in_progress') {
            $query->where(function ($statusQuery): void {
                $statusQuery->where('progress', '>', 0)->where('progress', '<', 100);
            });
        } elseif ($this->status === 'completed') {
            $query->where('progress', '>=', 100)->whereNotNull('completed_at');
        } elseif ($this->status === 'not_started') {
            $query->where('progress', '<=', 0);
        }

        $enrollments = $query
            ->orderByDesc('updated_at')
            ->paginate(12);

        $allEnrollments = UserCourse::with('course')
            ->where('user_id', $user->id)
            ->get();

        return view('livewire.courses.my-courses', [
            'enrollments' => $enrollments,
            'counts' => [
                'all' => $allEnrollments->count(),
                'in_progress' => $allEnrollments->where('progress', '>', 0)->where('progress', '<', 100)->count(),
                'completed' => $allEnrollments->where('progress', '>=', 100)->whereNotNull('completed_at')->count(),
                'not_started' => $allEnrollments->where('progress', '<=', 0)->count(),
            ],
        ]);
    }
}

