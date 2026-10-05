<div>
    <style>
        .my-courses-page .toolbar { grid-template-columns: 1fr minmax(180px, auto); }
        @media (max-width: 760px) {
            .my-courses-item { flex-direction: column; }
            .my-courses-cover { width: 100% !important; height: 160px !important; }
        }
        @media (max-width: 640px) {
            .my-courses-page .tabs { display: flex; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .my-courses-page .tabs::-webkit-scrollbar { display: none; width: 0; height: 0; }
            .my-courses-page .tab { flex: 0 0 auto; }
            .my-courses-page .toolbar { grid-template-columns: 1fr; }
        }
    </style>
    <section class="card card-soft my-courses-page" style="margin-bottom: 14px;">
        <h1 class="page-title" style="margin-bottom: 4px;">Meus cursos</h1>
        <p class="muted" style="margin: 0;">Continue seus estudos e acompanhe seu progresso.</p>
    </section>

    <section class="card my-courses-page">
        <div class="tabs" style="margin-bottom: 12px;">
            <button class="tab {{ $status === 'all' ? 'active' : '' }}" wire:click="$set('status', 'all')">
                Todos ({{ $counts['all'] }})
            </button>
            <button class="tab {{ $status === 'in_progress' ? 'active' : '' }}" wire:click="$set('status', 'in_progress')">
                Em andamento ({{ $counts['in_progress'] }})
            </button>
            <button class="tab {{ $status === 'completed' ? 'active' : '' }}" wire:click="$set('status', 'completed')">
                Concluidos ({{ $counts['completed'] }})
            </button>
            <button class="tab {{ $status === 'not_started' ? 'active' : '' }}" wire:click="$set('status', 'not_started')">
                Nao iniciado ({{ $counts['not_started'] }})
            </button>
        </div>

        <div class="toolbar">
            <label style="display: block;">
                <span class="field-label">Buscar curso</span>
                <input type="text" class="input" wire:model.live.debounce.300ms="search" placeholder="Titulo ou descricao">
            </label>
            <label style="display: block;">
                <span class="field-label">Categoria</span>
                <select class="select" wire:model.live="category">
                    <option value="">Todas</option>
                    @foreach(\App\Models\Course::getCategoryOptions() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    <section style="margin-top: 14px;">
        @forelse($enrollments as $enrollment)
            @php($course = $enrollment->course)
            @php($progressValue = max(0, min(100, (int) $enrollment->progress)))
            <article class="card">
                <div class="my-courses-item" style="display: flex; gap: 12px; align-items: flex-start;">
                    @if($course && $course->cover_image_url)
                        <img class="my-courses-cover" src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" style="width: 140px; height: 86px; object-fit: cover; border-radius: 10px;">
                    @endif
                    <div style="flex: 1;">
                        <div class="kpi">
                            <h3 style="margin: 0; font-size: 18px;">
                                <a href="{{ route('web.courses.show', $enrollment->course_id) }}">{{ $course->title ?? 'Curso' }}</a>
                            </h3>
                            <span class="badge {{ $enrollment->isCompleted() ? 'badge-success' : 'badge-brand' }}">
                                {{ $enrollment->isCompleted() ? 'Concluido' : 'Em andamento' }}
                            </span>
                        </div>
                        <p class="muted" style="margin: 4px 0 0;">
                            {{ $course->short_description ?? $course->description ?? '' }}
                        </p>
                        <progress max="100" value="{{ $progressValue }}" style="margin-top: 8px; width: 100%;"></progress>
                        <div class="kpi" style="margin-top: 10px;">
                            <span class="muted">{{ $progressValue }}% concluido</span>
                            <a class="btn btn-primary" href="{{ route('web.courses.show', $enrollment->course_id) }}">Continuar</a>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <article class="card">
                <h3 style="margin-top: 0;">Nenhum curso encontrado</h3>
                <p class="muted" style="margin-bottom: 0;">Explore novos cursos para iniciar sua trilha.</p>
            </article>
        @endforelse
    </section>

    <div style="margin-top: 16px;">{{ $enrollments->links() }}</div>
</div>

