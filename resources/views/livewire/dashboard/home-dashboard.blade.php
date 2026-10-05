<div class="dashboard-page">
    <style>
        .dashboard-page { padding-bottom: 8px; }
        .dash-welcome h1 {
            margin: 0 0 8px;
            font-size: clamp(1.5rem, 1.2rem + 1vw, 1.85rem);
            font-weight: 700;
            color: #b384e4;
            line-height: 1.2;
        }
        .dash-welcome p {
            margin: 0;
            font-size: clamp(1rem, .95rem + .3vw, 1.125rem);
            color: #525379;
            line-height: 1.65;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        @media (min-width: 1024px) {
            .dashboard-stats { grid-template-columns: repeat(3, 1fr); gap: 24px; }
            .dash-stat--hours { order: 2; }
            .dash-stat--certs { order: 3; }
        }
        .dash-stat--courses { order: 1; }
        .dash-stat--certs { order: 2; }
        .dash-stat--hours { order: 3; }
        .dash-stat--achieve { order: 4; }
        @media (min-width: 1024px) {
            .dash-stat--certs { order: 3; }
            .dash-stat--hours { order: 2; }
        }
        .dash-stat {
            padding: 18px 20px;
            border-radius: 16px;
            background: #fff;
            outline: 1px solid rgba(229, 231, 235, .5);
            outline-offset: -1px;
        }
        .dash-stat-inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .dash-stat-label { font-size: 12px; color: #525379; margin: 0 0 4px; }
        .dash-stat-value { font-size: clamp(1.25rem, 1.1rem + .5vw, 1.5rem); font-weight: 700; color: #181a4d; margin: 0; }
        .dash-stat-ico {
            display: none;
            width: 48px; height: 48px; border-radius: 10px;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        @media (min-width: 768px) {
            .dash-stat-ico { display: flex; }
        }
        .dash-stat-ico svg { width: 24px; height: 24px; }
        .ico-book { background: rgba(166, 132, 224, .12); color: #a684e0; }
        .ico-clock { background: rgba(245, 158, 11, .12); color: #f59e0b; }
        .ico-award { background: rgba(16, 185, 129, .12); color: #10b981; }
        .ico-star { background: rgba(210, 234, 106, .12); color: #d2ea6a; }

        .dash-grid { display: grid; grid-template-columns: 1fr; gap: 32px; }
        .dash-grid > * { min-width: 0; }
        @media (min-width: 1024px) {
            .dash-grid { grid-template-columns: 2fr 1fr; gap: 32px; align-items: start; }
        }
        .dash-panel {
            padding: 22px 24px;
            border-radius: 16px;
            background: #fff;
            outline: 1px solid rgba(229, 231, 235, .5);
            outline-offset: -1px;
            min-width: 0;
            max-width: 100%;
            overflow-x: hidden;
        }
        .cert-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }
        .dash-panel h2, .dash-panel h3 {
            margin: 0 0 22px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #181a4d;
        }
        .dash-panel h3 { font-size: 1.05rem; margin-bottom: 16px; }
        .pill-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
        .pill-tab {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 14px; border-radius: 999px; font-size: 13px; font-weight: 600;
            border: 2px solid #e5e7eb; background: #fff; color: #181a4d; cursor: pointer;
            transition: transform .15s, box-shadow .15s;
        }
        .pill-tab:hover { box-shadow: 0 4px 6px -1px rgba(0,0,0,.08); }
        .pill-tab.is-on { background: #1a6398; border-color: #1a6398; color: #fff; box-shadow: 0 4px 12px rgba(26,99,152,.25); }
        .pill-tab .cnt {
            padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700;
            background: rgba(26, 99, 152, .12); color: #1a6398;
        }
        .pill-tab.is-on .cnt { background: rgba(255,255,255,.22); color: #fff; }

        .empty-state { text-align: center; padding: 40px 16px; }
        .empty-state svg { width: 64px; height: 64px; color: #94a3b8; margin: 0 auto 16px; }
        .empty-state h4 { margin: 0 0 8px; font-size: 1.05rem; font-weight: 600; color: #181a4d; }
        .empty-state p { margin: 0 0 18px; font-size: 14px; color: #525379; }
        .btn-explore {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600;
            background: #1a6398; color: #fff; border: none; cursor: pointer;
        }
        .btn-explore:hover { background: #2e87c7; }

        .course-row {
            display: flex; gap: 14px; padding: 14px; border-radius: 12px; border: 1px solid #e5e7eb;
            background: #fff; margin-bottom: 10px; align-items: center; transition: box-shadow .2s;
        }
        .course-row:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06); }
        .course-row-thumb {
            width: 96px; height: 64px; border-radius: 8px; overflow: hidden; flex-shrink: 0;
            background: #e8eefb;
        }
        .course-row-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .course-row-body { flex: 1; min-width: 0; }
        .course-row-title { margin: 0 0 6px; font-size: 15px; font-weight: 600; color: #181a4d; }
        .course-row-meta { font-size: 13px; color: #525379; margin-bottom: 8px; }
        .course-row-meta span { margin-right: 8px; }
        .progress-wrap { margin-top: 4px; }
        .progress-wrap .pct { font-size: 12px; color: #525379; margin-bottom: 4px; display: flex; justify-content: space-between; }
        .progress-bar-bg { height: 8px; border-radius: 999px; background: #e5e7eb; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 999px; background: #1a6398; transition: width .3s; }
        .course-row-action { flex-shrink: 0; }
        .tag-done {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 10px;
            background: rgba(34, 197, 94, .1); color: #16a34a; font-size: 13px; font-weight: 600;
        }
        .btn-continue {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 10px;
            background: #1a6398; color: #fff; font-size: 13px; font-weight: 600; border: none; cursor: pointer;
        }
        .btn-continue:hover { opacity: .92; }

        .quick-actions { display: flex; flex-direction: column; gap: 10px; }
        .qa-btn {
            display: flex; align-items: center; gap: 10px; width: 100%; text-align: left;
            padding: 11px 14px; border-radius: 12px; border: 1px solid #e5e7eb; background: #fff;
            font-size: 14px; font-weight: 500; color: #000; cursor: pointer; transition: background .15s;
        }
        .qa-btn:hover { background: #f8fafc; }
        .qa-btn svg { width: 18px; height: 18px; color: #525379; flex-shrink: 0; }

        @media (max-width: 1023px) {
            .quick-actions-col { display: none; }
        }
        @media (max-width: 640px) {
            .dash-panel { padding: 16px 14px; }
            .pill-tabs { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .pill-tabs::-webkit-scrollbar { display: none; width: 0; height: 0; }
            .pill-tab { flex: 0 0 auto; }
            .course-row { flex-direction: column; align-items: stretch; }
            .course-row-thumb { width: 100%; height: 160px; }
            .course-row-action { width: 100%; }
            .btn-continue, .tag-done { width: 100%; justify-content: center; }
            .cert-grid { grid-template-columns: 1fr; }
        }

        .cert-section { margin-top: 8px; }
        .cert-card-mini {
            border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; text-align: center;
            margin-bottom: 12px; background: #fff;
        }
        .cert-card-mini h4 { margin: 8px 0 12px; font-size: 15px; font-weight: 700; color: #181a4d; }
        .cert-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; color: #525379; }
        .cert-row strong { color: #181a4d; font-weight: 600; }
    </style>

    <section class="dash-welcome" style="margin-bottom: 28px;">
        <h1>Olá, {{ $firstName }}! 👋</h1>
        <p>Continue desenvolvendo suas habilidades. Você está indo muito bem!</p>
    </section>

    <section class="dashboard-stats" aria-label="Resumo">
        <article class="dash-stat dash-stat--courses">
            <div class="dash-stat-inner">
                <div>
                    <p class="dash-stat-label">Cursos em Andamento</p>
                    <p class="dash-stat-value">{{ $inProgressCount }}</p>
                </div>
                <div class="dash-stat-ico ico-book" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
            </div>
        </article>
        <article class="dash-stat dash-stat--certs">
            <div class="dash-stat-inner">
                <div>
                    <p class="dash-stat-label">Certificados</p>
                    <p class="dash-stat-value">{{ $completedCount }}</p>
                </div>
                <div class="dash-stat-ico ico-award" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                </div>
            </div>
        </article>
        <article class="dash-stat dash-stat--hours">
            <div class="dash-stat-inner">
                <div>
                    <p class="dash-stat-label">Horas de Estudo</p>
                    <p class="dash-stat-value">{{ $studyTimeFormatted }}</p>
                </div>
                <div class="dash-stat-ico ico-clock" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
            </div>
        </article>
        <!-- <article class="dash-stat dash-stat--achieve">
            <div class="dash-stat-inner">
                <div>
                    <p class="dash-stat-label">Conquistas</p>
                    <p class="dash-stat-value">{{ $achievementsCount }}</p>
                </div>
                <div class="dash-stat-ico ico-star" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
            </div>
        </article> -->
    </section>

    <div class="dash-grid">
        <div>
            <div class="dash-panel">
                <h2>Meus Cursos</h2>
                <div class="pill-tabs" role="tablist">
                    <button type="button" class="pill-tab {{ $coursesTab === 'todos' ? 'is-on' : '' }}" wire:click="setCoursesTab('todos')">
                        <span>Todos</span><span class="cnt">{{ $enrolledCount }}</span>
                    </button>
                    <button type="button" class="pill-tab {{ $coursesTab === 'em-andamento' ? 'is-on' : '' }}" wire:click="setCoursesTab('em-andamento')">
                        <span>Em Andamento</span><span class="cnt">{{ $inProgressCount }}</span>
                    </button>
                    <button type="button" class="pill-tab {{ $coursesTab === 'concluidos' ? 'is-on' : '' }}" wire:click="setCoursesTab('concluidos')">
                        <span>Concluídos</span><span class="cnt">{{ $completedCount }}</span>
                    </button>
                    <button type="button" class="pill-tab {{ $coursesTab === 'favoritos' ? 'is-on' : '' }}" wire:click="setCoursesTab('favoritos')">
                        <span>Favoritos</span><span class="cnt">{{ $favoritesCount }}</span>
                    </button>
                </div>

                @if($filteredEnrollments->isEmpty())
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h4>Nenhum curso encontrado</h4>
                        <p>
                            @if($coursesTab === 'favoritos')
                                Favorite alguns cursos para vê-los aqui
                            @else
                                Comece um novo curso para vê-lo aqui
                            @endif
                        </p>
                        <a class="btn-explore" href="{{ route('web.courses.index') }}">Explorar Cursos</a>
                    </div>
                @else
                    @foreach($filteredEnrollments as $enrollment)
                        @php
                            $c = $enrollment->course;
                            $pv = max(0, min(100, (int) $enrollment->progress));
                            $pvWidth = $pv.'%';
                        @endphp
                        <div class="course-row">
                            <div class="course-row-thumb">
                                @if($c && $c->cover_image_url)
                                    <img src="{{ $c->cover_image_url }}" alt="">
                                @endif
                            </div>
                            <div class="course-row-body">
                                <p class="course-row-title">{{ $c->title ?? 'Curso' }}</p>
                                <div class="course-row-meta">
                                    <span>{{ $c ? $c->getDifficultyLevelLabel() : '' }}</span>
                                    <span>•</span>
                                    <span>{{ (int) ($c->workload ?? 0) }}h</span>
                                </div>
                                <div class="progress-wrap">
                                    <div class="pct"><span>Progresso</span><span>{{ $pv }}%</span></div>
                                    <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $pvWidth }}"></div></div>
                                </div>
                            </div>
                            <div class="course-row-action">
                                @if($enrollment->isCompleted())
                                    <span class="tag-done">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                                        Concluído
                                    </span>
                                @else
                                    <a class="btn-continue" href="{{ route('web.courses.player', $enrollment->course_id) }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                        {{ $pv > 0 ? 'Continuar' : 'Iniciar' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="quick-actions-col">
            <div class="dash-panel">
                <h3>Ações Rápidas</h3>
                <div class="quick-actions">
                    <a class="qa-btn" href="{{ route('web.profile') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Perfil
                    </a>
                    <a class="qa-btn" href="{{ route('web.courses.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Explorar Cursos
                    </a>
                    <a class="qa-btn" href="{{ route('web.certificates.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                        Meus Certificados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="dash-panel cert-section" id="certificados">
        <h2>Meus Certificados</h2>
        <!-- <div class="pill-tabs" style="margin-bottom: 20px;">
            <button type="button" class="pill-tab {{ $certificatesTab === 'certificados' ? 'is-on' : '' }}" wire:click="setCertificatesTab('certificados')">Certificados</button>
            <button type="button" class="pill-tab {{ $certificatesTab === 'conquistas' ? 'is-on' : '' }}" wire:click="setCertificatesTab('conquistas')">Conquistas</button>
        </div> -->

        @if($certificatesTab === 'certificados')
            @if($certificates->isEmpty())
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    <h4>Nenhum certificado ainda</h4>
                    <p>Complete cursos para ganhar certificados</p>
                    <a class="btn-explore" href="{{ route('web.courses.index') }}">Explorar Cursos</a>
                </div>
            @else
                <div class="cert-grid">
                    @foreach($certificates as $certificate)
                        <div class="cert-card-mini">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1a6398" stroke-width="2" style="margin:0 auto;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            <h4>{{ $certificate->course->title ?? 'Curso' }}</h4>
                            <div class="cert-row"><span>Data de conclusão</span><strong>{{ optional($certificate->issue_date)->format('d/m/Y') ?? '—' }}</strong></div>
                            @if($certificate->uuid)
                                <div class="cert-row" style="flex-direction:column;align-items:flex-start;gap:4px;text-align:left;">
                                    <span>UUID</span>
                                    <span style="font-size:11px;font-family:ui-monospace,monospace;word-break:break-all;color:#181a4d;">{{ $certificate->uuid }}</span>
                                </div>
                            @endif
                            <div style="margin-top:12px;display:flex;flex-direction:column;gap:8px;width:100%;">
                                <a class="btn-explore" style="width:100%;" href="{{ route('web.certificates.show', $certificate->id) }}">Ver certificado</a>
                                <a class="btn-explore" style="width:100%;background:#fff;border:1px solid var(--line);" href="{{ route('web.certificates.pdf', $certificate->id) }}">Baixar PDF</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <h4>Nenhuma conquista ainda</h4>
                <p>Continue estudando para desbloquear conquistas</p>
            </div>
        @endif
    </section>
</div>
