<div class="courses-explore">
    @push('styles')
        <style>
            .courses-explore .toolbar {
                grid-template-columns: 1fr repeat(5, minmax(120px, auto));
            }
            @media (max-width: 1100px) {
                .courses-explore .toolbar { grid-template-columns: 1fr 1fr; }
            }
            @media (max-width: 640px) {
                .courses-explore .toolbar { grid-template-columns: 1fr; }
            }
            .courses-explore .courses-explore-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                align-items: stretch;
            }
            @media (max-width: 1024px) {
                .courses-explore .courses-explore-grid { grid-template-columns: repeat(2, 1fr); }
            }
            @media (max-width: 640px) {
                .courses-explore .courses-explore-grid:not(.courses-explore-grid--empty) {
                    display: flex;
                    overflow-x: auto;
                    scroll-snap-type: x mandatory;
                    gap: 16px;
                    padding-bottom: 12px;
                    margin-inline: -8px;
                    padding-inline: 8px;
                    -webkit-overflow-scrolling: touch;
                }
                .courses-explore .courses-explore-grid:not(.courses-explore-grid--empty) .course-card {
                    flex: 0 0 min(300px, 88vw);
                    scroll-snap-align: start;
                }
                .courses-explore .courses-explore-grid--empty { grid-template-columns: 1fr; }
            }

            .courses-explore .course-card {
                background: #fff;
                border: 1px solid var(--eduit-line);
                border-radius: 16px;
                overflow: hidden;
                box-shadow: var(--eduit-shadow);
                transition: box-shadow .3s;
                display: flex;
                flex-direction: column;
            }
            .courses-explore .course-card:hover { box-shadow: 0 20px 40px rgba(24, 26, 77, .12); }

            .courses-explore .course-media {
                position: relative;
                overflow: hidden;
                cursor: pointer;
                aspect-ratio: 16 / 10;
                min-height: 150px;
                background: linear-gradient(145deg, #e8eefb 0%, #dbeafe 100%);
            }
            .courses-explore .course-media img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform .5s ease;
            }
            .courses-explore .course-card:hover .course-media img { transform: scale(1.06); }
            .courses-explore .course-media::after {
                content: "";
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 48px;
                background: linear-gradient(to top, rgba(0, 0, 0, .35), transparent);
                pointer-events: none;
            }

            .courses-explore .badge-diff {
                position: absolute;
                top: 12px;
                left: 12px;
                z-index: 2;
                padding: 5px 12px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .02em;
            }
            .courses-explore .badge-diff--iniciante { background: #c8e6c9; color: #1b5e20; }
            .courses-explore .badge-diff--intermediario { background: #ffe0b2; color: #e65100; }
            .courses-explore .badge-diff--avancado { background: #ffcdd2; color: #b71c1c; }

            .courses-explore .course-body {
                padding: 22px 22px 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            .courses-explore .course-cat {
                display: inline-flex;
                align-self: flex-start;
                font-size: 12px;
                font-weight: 600;
                padding: 5px 12px;
                border-radius: 999px;
                color: var(--eduit-brand);
                background: rgba(26, 99, 152, .12);
                margin-bottom: 12px;
            }
            .courses-explore .course-title {
                margin: 0 0 10px;
                font-size: clamp(1rem, .95rem + .45vw, 1.2rem);
                font-weight: 800;
                line-height: 1.35;
                color: var(--eduit-text);
                display: -webkit-box;
                -webkit-line-clamp: 2;
                line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .courses-explore .course-title a {
                color: inherit;
                text-decoration: none;
            }
            .courses-explore .course-title a:hover { color: var(--eduit-brand); }
            .courses-explore .course-desc {
                margin: 0;
                font-size: 14px;
                line-height: 1.55;
                color: var(--eduit-muted);
                display: -webkit-box;
                -webkit-line-clamp: 3;
                line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
                min-height: 2.8em;
            }
            .courses-explore .course-meta-row {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px 20px;
                margin-top: 14px;
                font-size: 13px;
                font-weight: 500;
                color: #64748b;
            }
            .courses-explore .course-meta-row span {
                display: inline-flex;
                align-items: center;
                gap: 7px;
            }
            .courses-explore .course-meta-row svg {
                flex-shrink: 0;
                opacity: .9;
            }

            .courses-explore .course-price-line {
                margin: 16px 0 0;
                font-size: 14px;
                display: flex;
                flex-wrap: wrap;
                align-items: baseline;
                gap: 6px 10px;
            }
            .courses-explore .course-price-label {
                color: #94a3b8;
                font-weight: 500;
                font-size: 13px;
            }
            .courses-explore .course-price-line .strike {
                text-decoration: line-through;
                color: #94a3b8;
                font-size: 13px;
                font-weight: 500;
            }
            .courses-explore .course-price-current {
                font-size: 18px;
                font-weight: 800;
                color: var(--eduit-text);
            }
            .courses-explore .course-price-free {
                font-size: 18px;
                font-weight: 800;
                color: #15803d;
            }

            .courses-explore .course-cta {
                margin-top: 15px;
                padding-top: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                width: 100%;
                border-radius: 12px;
                padding: 13px 16px;
                font-size: 15px;
                font-weight: 700;
                background: var(--eduit-brand);
                color: #fff;
                border: 1px solid var(--eduit-brand);
                text-decoration: none;
                transition: background .2s, transform .12s, box-shadow .2s;
            }
            .courses-explore .course-cta:hover {
                background: #2e87c7;
                border-color: #2e87c7;
                color: #fff;
                transform: translateY(-1px);
                box-shadow: 0 8px 18px rgba(26, 99, 152, .28);
            }
            .courses-explore .course-cta svg { width: 20px; height: 20px; flex-shrink: 0; }

            .courses-explore .courses-pagination {
                margin-top: 16px;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            .courses-explore .courses-pagination .pg-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 38px;
                height: 38px;
                padding: 0 12px;
                border-radius: 10px;
                border: 1px solid var(--eduit-line);
                background: #fff;
                color: var(--eduit-text);
                font-size: 13px;
                font-weight: 700;
                text-decoration: none;
                transition: transform .12s, box-shadow .12s, background .12s, color .12s;
            }
            .courses-explore .courses-pagination .pg-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 12px rgba(24, 26, 77, .08);
                background: #f8fafc;
            }
            .courses-explore .courses-pagination .pg-btn.is-current {
                background: var(--eduit-brand);
                border-color: var(--eduit-brand);
                color: #fff;
                box-shadow: 0 6px 14px rgba(26, 99, 152, .24);
            }
            .courses-explore .courses-pagination .pg-btn.is-disabled {
                opacity: .45;
                pointer-events: none;
            }
            .courses-explore .courses-pagination .pg-ellipsis {
                color: #94a3b8;
                font-weight: 700;
                padding: 0 2px;
            }
        </style>
    @endpush

    <section class="card card-soft" style="margin-bottom: 18px;">
        <h1 class="page-title">Explorar cursos</h1>
        <p class="muted" style="margin: 0;">Descubra cursos para impulsionar sua carreira.</p>
    </section>

    <section class="card">
        <div class="toolbar">
            <label style="display: block;">
                <span class="field-label">Buscar</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Titulo ou descricao" class="input">
            </label>

            <label style="display: block;">
                <span class="field-label">Ordenar por</span>
                <select wire:model.live="sortBy" class="select">
                    <option value="title">Titulo</option>
                    <option value="created_at">Data</option>
                    <option value="workload">Carga horaria</option>
                </select>
            </label>

            <label style="display: block;">
                <span class="field-label">Direcao</span>
                <select wire:model.live="sortDirection" class="select">
                    <option value="asc">Crescente</option>
                    <option value="desc">Decrescente</option>
                </select>
            </label>

            <label style="display: block;">
                <span class="field-label">Categoria</span>
                <select wire:model.live="category" class="select">
                    <option value="">Todas</option>
                    @foreach($categories as $categoryKey => $categoryLabel)
                        <option value="{{ $categoryKey }}">{{ $categoryLabel }}</option>
                    @endforeach
                </select>
            </label>

            <label style="display: block;">
                <span class="field-label">Dificuldade</span>
                <select wire:model.live="difficulty" class="select">
                    <option value="">Todas</option>
                    @foreach($difficultyLevels as $difficultyKey => $difficultyLabel)
                        <option value="{{ $difficultyKey }}">{{ $difficultyLabel }}</option>
                    @endforeach
                </select>
            </label>

            <label style="display: block;">
                <span class="field-label">Preco</span>
                <select wire:model.live="isFree" class="select">
                    <option value="">Todos</option>
                    <option value="true">Gratuitos</option>
                    <option value="false">Pagos</option>
                </select>
            </label>
        </div>
    </section>

    <p class="muted" style="margin: 14px 0 10px;">
        {{ $courses->total() }} curso(s) encontrado(s)
    </p>

    <section class="courses-explore-grid{{ $courses->isEmpty() ? ' courses-explore-grid--empty' : '' }}">
        @forelse($courses as $course)
            @php
                $difficultyKey = $course->difficulty_level ?: 'iniciante';
                $diffLabel = $course->getDifficultyLevelLabel();
                $modulesCount = (int) ($course->modules_count ?? 0);
                $durationLabel = $course->getFormattedTotalDurationLabel();
                $finalPrice = $course->getFinalPrice();
                $hasDiscount = $course->hasDiscount();
                $showUrl = route('web.courses.show', $course->id);
            @endphp
            <article class="course-card">
                <a href="{{ $showUrl }}" class="course-media" aria-hidden="true" tabindex="-1">
                    @if($course->cover_image_url)
                        <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" loading="lazy">
                    @endif
                    <span class="badge-diff badge-diff--{{ $difficultyKey }}">{{ $diffLabel }}</span>
                </a>
                <div class="course-body">
                    <span class="course-cat">{{ $course->getCategoryLabel() ?? 'Curso' }}</span>
                    <h2 class="course-title">
                        <a href="{{ $showUrl }}">{{ $course->title }}</a>
                    </h2>
                    <p class="course-desc">{{ \Illuminate\Support\Str::limit($course->short_description ?? $course->description ?? '', 140) }}</p>

                    <div class="course-meta-row">
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ $durationLabel }}
                        </span>
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            {{ $modulesCount }} {{ $modulesCount === 1 ? 'módulo' : 'módulos' }}
                        </span>
                    </div>

                    <div class="course-price-line">
                        @if($course->isFree())
                            <span class="course-price-label">Preço:</span>
                            <span class="course-price-free">Gratuito</span>
                        @else
                            <span class="course-price-label">Preço:</span>
                            @if($hasDiscount)
                                <span class="strike">R$ {{ number_format((float) $course->price, 2, ',', '.') }}</span>
                                <span class="course-price-current">R$ {{ number_format($finalPrice, 2, ',', '.') }}</span>
                            @else
                                <span class="course-price-current">R$ {{ number_format($finalPrice, 2, ',', '.') }}</span>
                            @endif
                        @endif
                    </div>

                    <a class="course-cta" href="{{ $showUrl }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M9 7.5v9l7-4.5L9 7.5z" fill="currentColor" stroke="none"/>
                        </svg>
                        Iniciar Agora
                    </a>
                </div>
            </article>
        @empty
            <article class="card" style="grid-column: 1 / -1;">
                <h3 style="margin-top: 0;">Nenhum curso encontrado</h3>
                <p class="muted" style="margin-bottom: 0;">Tente ajustar os filtros para encontrar cursos.</p>
            </article>
        @endforelse
    </section>

    @if ($courses->hasPages())
        @php
            $current = $courses->currentPage();
            $last = $courses->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
        @endphp
        <nav class="courses-pagination" aria-label="Paginação de cursos">
            <a class="pg-btn {{ $courses->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $courses->onFirstPage() ? '#' : $courses->previousPageUrl() }}" aria-label="Página anterior">‹</a>

            @if($start > 1)
                <a class="pg-btn" href="{{ $courses->url(1) }}">1</a>
                @if($start > 2)
                    <span class="pg-ellipsis">…</span>
                @endif
            @endif

            @for($page = $start; $page <= $end; $page++)
                <a class="pg-btn {{ $page === $current ? 'is-current' : '' }}" href="{{ $courses->url($page) }}">{{ $page }}</a>
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <span class="pg-ellipsis">…</span>
                @endif
                <a class="pg-btn" href="{{ $courses->url($last) }}">{{ $last }}</a>
            @endif

            <a class="pg-btn {{ $courses->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $courses->hasMorePages() ? $courses->nextPageUrl() : '#' }}" aria-label="Próxima página">›</a>
        </nav>
    @endif
</div>
