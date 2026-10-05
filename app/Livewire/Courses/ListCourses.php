<?php

namespace App\Livewire\Courses;

use App\Models\Course;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListCourses extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'sortBy', except: 'title')]
    public string $sortBy = 'title';

    #[Url(as: 'sortDirection', except: 'asc')]
    public string $sortDirection = 'asc';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'difficulty', except: '')]
    public string $difficulty = '';

    #[Url(as: 'is_free', except: '')]
    public string $isFree = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function updatingSortDirection(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingDifficulty(): void
    {
        $this->resetPage();
    }

    public function updatingIsFree(): void
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    #[Title('Explorar cursos')]
    public function render()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $allowedSorts = ['title', 'created_at', 'workload'];
        $sortBy = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'title';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $query = Course::query();
        if (! $user || $user->isStudent()) {
            $query->where('is_active', true);
        }

        if ($this->search !== '') {
            $query->where(function ($innerQuery) {
                $innerQuery->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category !== '') {
            $query->where('category', $this->category);
        }

        if ($this->difficulty !== '') {
            $query->where('difficulty_level', $this->difficulty);
        }

        if ($this->isFree !== '') {
            if ($this->isFree === 'true') {
                $query->where('price', 0);
            } else {
                $query->where('price', '>', 0);
            }
        }

        $courses = $query->orderBy($sortBy, $sortDirection)->paginate(15);

        return view('livewire.courses.list-courses', [
            'courses' => $courses,
            'categories' => Course::getCategoryOptions(),
            'difficultyLevels' => Course::getDifficultyLevelOptions(),
        ]);
    }
}