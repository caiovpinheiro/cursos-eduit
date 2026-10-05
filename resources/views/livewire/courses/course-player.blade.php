<div>
    <style>
        /* Largura total da viewport (main usa .app-shell--player no layout) */
        .player-page { width: 100%; margin: 0; }
        /* Alertas no topo do painel esquerdo (a lista de modulos rola abaixo, sem barra visivel) */
        .player-page-alerts {
            flex-shrink: 0;
            padding: 12px 12px 10px;
            width: 100%;
            box-sizing: border-box;
            background: linear-gradient(180deg, #eef2f7 0%, #e8edf4 100%);
            border-bottom: 1px solid rgba(148, 163, 184, 0.35);
        }
        .player-page-alerts .card {
            margin-bottom: 8px;
            padding: 10px 12px;
        }
        .player-page-alerts .card:last-child {
            margin-bottom: 0;
        }
        .player-page-alerts .section-title {
            font-size: 0.95rem;
            margin-bottom: 6px;
        }
        /* overflow visível + align-items:start para o painel esquerdo poder usar position:sticky */
        .player-shell {
            display: grid;
            grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
            gap: 0;
            position: relative;
            align-items: start;
            min-height: min(70vh, 900px);
            width: 100%;
            border: solid var(--eduit-line);
            border-width: 1px 0;
            border-radius: 0;
            overflow: visible;
            background: #fff;
            box-shadow: none;
        }
        .player-shell.is-collapsed { grid-template-columns: 0 minmax(0, 1fr); }
        .player-shell.is-collapsed .player-main {
            border-radius: 0;
        }
        .player-sidebar {
            background: linear-gradient(180deg, #f1f5f9 0%, #eef2f7 100%);
            border-right: 1px solid var(--eduit-line);
            border-radius: 0;
            display: flex;
            flex-direction: column;
            min-height: 0;
            max-height: calc(100vh - 96px);
            position: sticky;
            top: 88px;
            align-self: start;
            z-index: 6;
            overflow: hidden;
            transition: opacity .2s ease, transform .2s ease;
        }
        .player-shell.is-collapsed .player-sidebar {
            opacity: 0;
            pointer-events: none;
            transform: translateX(-8px);
            overflow: hidden;
            min-width: 0;
        }
        .player-sidebar-inner {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 0;
        }
        /* Só a lista de módulos rola; barra de rolagem invisível */
        .player-sidebar-body {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 14px 12px 18px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .player-sidebar-body::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .player-back {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600; color: var(--eduit-brand);
            margin-bottom: 12px;
        }
        .player-back:hover { text-decoration: underline; }
        .player-course-title {
            font-size: 15px; font-weight: 800; color: var(--eduit-text);
            margin: 0 0 14px; line-height: 1.35;
        }
        .player-progress-label {
            font-size: 12px; font-weight: 700; color: var(--eduit-muted);
            margin: 0 0 6px;
        }
        .player-progress-row {
            display: flex; justify-content: space-between; align-items: baseline;
            font-size: 13px; font-weight: 700; margin-bottom: 8px;
        }
        .player-progress-bar {
            height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden;
        }
        .player-progress-bar > span {
            display: block; height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--eduit-brand), #3b82f6);
            transition: width .25s ease;
        }
        .player-modules-title {
            font-size: 12px; font-weight: 700; color: var(--eduit-muted);
            text-transform: uppercase; letter-spacing: .04em;
            margin: 18px 0 10px;
        }
        .player-act {
            display: block;
            text-align: left;
            width: 100%;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px 10px 10px 8px;
            margin-bottom: 8px;
            background: rgba(255,255,255,.72);
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s, background .15s;
            font: inherit;
            color: inherit;
        }
        a.player-act { text-decoration: none; }
        .player-act:hover { background: #fff; border-color: #cbd5e1; }
        .player-act.is-active {
            background: #e0f2fe;
            border-color: #7dd3fc;
            box-shadow: inset 0 0 0 1px rgba(14, 165, 233, .25);
        }
        .player-act.is-locked {
            opacity: .65;
            cursor: not-allowed;
        }
        .player-act-row {
            display: grid;
            grid-template-columns: 28px 1fr;
            gap: 8px;
            align-items: start;
        }
        .player-act-icon {
            width: 28px; height: 28px; border-radius: 999px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }
        .player-act-icon.is-done { background: #dcfce7; color: #166534; }
        .player-act-icon.is-run { background: #dbeafe; color: #1d4ed8; }
        .player-act-icon.is-pending { background: #f1f5f9; color: #64748b; }
        .player-act-icon.is-lock { background: #fef3c7; color: #b45309; }
        .player-act-title { font-size: 13px; font-weight: 700; line-height: 1.3; margin: 0 0 6px; }
        .player-act-meta {
            display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 600; color: var(--eduit-muted);
        }
        .player-type-tag {
            padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .03em;
        }
        .player-type-tag.is-video { background: #dcfce7; color: #166534; }
        .player-type-tag.is-read { background: #fef9c3; color: #854d0e; }
        .player-type-tag.is-quiz { background: #dcfce7; color: #166534; }
        .player-type-tag.is-exam { background: #fef9c3; color: #854d0e; }
        .player-type-tag.is-embed { background: #e0f2fe; color: #0369a1; }
        .player-type-tag.is-game { background: #ffedd5; color: #9a3412; }
        .player-sidebar-toggle {
            position: absolute;
            left: calc(340px - 14px);
            top: 50%;
            transform: translateY(-50%);
            z-index: 20;
            width: 30px; height: 30px;
            border-radius: 999px;
            border: 1px solid rgba(26, 99, 152, .35);
            background: var(--eduit-brand);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(26, 99, 152, .35);
            transition: left .2s ease, transform .2s ease;
        }
        .player-sidebar-toggle:hover { filter: brightness(1.06); }
        .player-shell.is-collapsed .player-sidebar-toggle { left: 8px; }
        .player-main {
            padding: 0;
            min-width: 0;
            background: #fafbfc;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        /* Equivalente a Tailwind max-w-6xl (72rem): conteúdo centralizado na coluna direita */
        .player-main-inner {
            width: 100%;
            max-width: 72rem;
            margin-left: auto;
            margin-right: auto;
            padding: 20px 24px 32px;
            box-sizing: border-box;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .player-hero-title {
            margin: 0;
            font-size: clamp(1.35rem, 1.1rem + .9vw, 1.85rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--eduit-text);
        }
        .player-video-hero {
            margin-bottom: 18px;
        }
        .player-video-topline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .player-order-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 800;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .player-video-page {
            display: flex;
            flex-direction: column;
            gap: 0;
            flex: 1;
            min-width: 0;
        }
        .player-video-stage {
            width: 100%;
            margin-bottom: 12px;
        }
        .player-video-subbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--eduit-muted);
            margin-bottom: 20px;
        }
        .player-video-subbar-left {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .player-about-module {
            margin-top: 4px;
            padding: 0 0 8px;
        }
        .player-about-module h2 {
            margin: 0 0 10px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--eduit-text);
        }
        .player-content-card {
            background: #fff;
            border: 1px solid var(--eduit-line);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
            flex: 1;
            min-height: 200px;
        }
        .player-prose { font-size: 15px; line-height: 1.65; color: #334155; overflow-wrap: anywhere; word-break: break-word; }
        .player-prose h1, .player-prose h2, .player-prose h3 { color: var(--eduit-text); margin-top: 1.25em; }
        .player-prose table { width: 100%; border-collapse: collapse; margin: 1em 0; font-size: 14px; }
        .player-prose th, .player-prose td {
            border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left;
        }
        .player-prose th { background: #f8fafc; font-weight: 700; }
        .player-video-wrap {
            position: relative; width: 100%; padding-bottom: 56.25%;
            height: 0; overflow: hidden; border-radius: 12px;
            background: #0f172a;
        }
        .player-video-wrap iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;
        }
        .player-embed-sandbox iframe { max-width: 100%; border-radius: 8px; }
        .player-done-banner {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .player-footer-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }
        .player-options label { border: 1px solid var(--eduit-line); border-radius: 10px; padding: 8px 10px; background: #fff; }
        .player-options label:hover { border-color: #c4b5fd; background: #faf5ff; }
        .feedback-item-ok { border-color: #86efac; background: #f0fdf4; }
        .feedback-item-fail { border-color: #fecaca; background: #fff1f2; }
        .player-alert-stack { margin-bottom: 8px; }
        .player-assessment-landing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-height: 320px;
            text-align: center;
            padding: 24px 12px;
        }
        .player-assessment-landing-card {
            max-width: 420px;
            width: 100%;
            background: #fff;
            border: 1px solid var(--eduit-line);
            border-radius: 16px;
            padding: 28px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }
        .player-assessment-landing-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 999px;
            background: var(--eduit-brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .player-assessment-landing-card h2 {
            margin: 0 0 8px;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--eduit-text);
        }
        .player-assessment-landing-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin: 16px 0 20px;
            font-size: 13px;
            font-weight: 600;
            color: var(--eduit-muted);
        }
        .player-assessment-progress-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }
        .player-assessment-q-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            background: var(--eduit-brand);
            color: #fff;
        }
        .player-assessment-qbar {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .player-assessment-qbar > span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--eduit-brand), #3b82f6);
            transition: width .2s ease;
        }
        .player-quiz-question-card {
            background: #fff;
            border: 1px solid var(--eduit-line);
            border-radius: 14px;
            padding: 20px 20px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .player-quiz-question-card > p:first-child {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.45;
            color: var(--eduit-text);
        }
        .player-option-row {
            display: block;
            margin: 8px 0;
            padding: 12px 14px;
            border: 1px solid var(--eduit-line);
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }
        .player-option-row:hover { border-color: #93c5fd; background: #f8fafc; }
        .player-option-row input { margin-right: 10px; }
        .player-quiz-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--eduit-line);
        }
        .player-assessment-result {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-height: 280px;
            text-align: center;
            padding: 16px 12px 8px;
        }
        .player-assessment-result-icon {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 14px;
        }
        .player-assessment-result-icon.is-pass { background: #22c55e; color: #fff; }
        .player-assessment-result-icon.is-fail { background: #ef4444; color: #fff; }
        .player-assessment-result h2 {
            margin: 0 0 12px;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--eduit-text);
        }
        .player-assessment-score-pill {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .player-assessment-score-pill.is-pass { background: #e0f2fe; color: #15803d; }
        .player-assessment-score-pill.is-fail { background: #fee2e2; color: #b91c1c; }
        .player-assessment-result .muted { margin: 0 0 18px; font-size: 14px; }
        @media (max-width: 960px) {
            .player-page-alerts { padding-left: 10px; padding-right: 10px; }
            .player-shell { grid-template-columns: 1fr; min-height: 0; }
            .player-sidebar {
                position: relative;
                top: auto;
                align-self: stretch;
                max-height: 48vh;
                border-radius: 0;
                border-right: none;
                border-bottom: 1px solid var(--eduit-line);
            }
            .player-main { border-radius: 0; }
            .player-main-inner { padding-left: 16px; padding-right: 16px; }
            .player-shell.is-collapsed .player-sidebar { display: none; }
            .player-sidebar-toggle {
                position: fixed;
                bottom: 20px;
                left: 16px;
                top: auto;
                transform: none;
                z-index: 40;
            }
            .player-shell.is-collapsed .player-sidebar-toggle { left: 16px; }
        }
        @media (max-width: 640px) {
            .player-main-inner { padding-left: 12px; padding-right: 12px; padding-top: 16px; }
            .player-content-card { padding: 14px 14px; }
            .player-prose table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .player-sidebar { max-height: 56vh; }
            .player-sidebar-toggle { bottom: 16px; left: 12px; }
        }
    </style>

    <div class="player-page">
        <div class="player-shell {{ $sidebarCollapsed ? 'is-collapsed' : '' }}">
            <aside class="player-sidebar" aria-label="Modulos do curso">
                <div class="player-sidebar-inner">
                    @if (session('status') || $errors->any() || $assessmentFeedback || (($progress['is_completed'] ?? false) && $certificate))
                    <div class="player-page-alerts">
                        @if (session('status'))
                            <div class="status player-alert-stack">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="error player-alert-stack">{{ $errors->first() }}</div>
                        @endif

                        @if ($assessmentFeedback)
                            <section class="card">
                                <h2 class="section-title">Resultado da ultima tentativa</h2>
                                <p style="margin: 4px 0;">Pontuacao: <strong>{{ $assessmentFeedback['score'] }}%</strong></p>
                                <p style="margin: 4px 0;">Acertos: <strong>{{ $assessmentFeedback['correct'] }}/{{ $assessmentFeedback['total'] }}</strong></p>
                                <p style="margin: 4px 0;">
                                    Status:
                                    <span class="badge {{ ($assessmentFeedback['passed'] ?? false) ? 'badge-success' : '' }}">
                                        {{ ($assessmentFeedback['passed'] ?? false) ? 'Aprovado' : 'Nao aprovado' }}
                                    </span>
                                </p>
                                @if(isset($assessmentFeedback['attempts_remaining']))
                                    <p class="muted" style="margin: 4px 0 0;">Tentativas restantes: {{ $assessmentFeedback['attempts_remaining'] }}</p>
                                @endif
                            </section>
                        @endif

                        @if(($progress['is_completed'] ?? false) && $certificate)
                            <section class="card card-soft">
                                <h2 class="section-title" style="margin-bottom: 6px;">Curso concluido</h2>
                                <p class="muted" style="margin: 0 0 10px;">Parabens! Seu certificado ja esta disponivel.</p>
                                <a class="btn btn-primary" href="{{ route('web.certificates.show', $certificate->id) }}">Ver certificado</a>
                            </section>
                        @endif
                    </div>
                    @endif

                    <div class="player-sidebar-body">
                    <a href="{{ route('web.my-courses') }}" class="player-back">
                        <span aria-hidden="true">&#8592;</span> Voltar aos cursos
                    </a>
                    <p class="player-course-title">{{ $course->title }}</p>

                    <p class="player-progress-label">Progresso do curso</p>
                    <div class="player-progress-row">
                        <span>{{ $completedCount }} de {{ $totalActivities }}</span>
                        <span class="muted">{{ $progressPercent }}%</span>
                    </div>
                    <div class="player-progress-bar" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                        <span style="width: {{ $progressPercent }}%;"></span>
                    </div>

                    <p class="player-modules-title">Modulos do curso</p>
                    <div>
                        @foreach($activitiesOrdered as $activity)
                            @php
                                $meta = $activityMeta[$activity->id] ?? ['type_label' => 'Atividade', 'badge_variant' => 'read', 'duration_label' => '—'];
                                $state = $activityStateMap[$activity->id] ?? ['unlocked' => true, 'locked_reason' => null];
                                $isActive = $selectedActivity && $selectedActivity->id === $activity->id;
                                $isDone = in_array($activity->id, $completedActivityIds, true);
                                $unlocked = $state['unlocked'];
                                $variant = $meta['badge_variant'] ?? 'read';
                            @endphp
                            @if($unlocked)
                                <a
                                    href="{{ route('web.courses.player', ['id' => $course->id, 'activity' => $activity->id]) }}"
                                    class="player-act {{ $isActive ? 'is-active' : '' }}"
                                >
                                    <div class="player-act-row">
                                        <span class="player-act-icon @if($isDone) is-done @elseif($isActive) is-run @else is-pending @endif" aria-hidden="true">
                                            @if($isDone)
                                                &#10003;
                                            @elseif(($meta['type_short'] ?? '') === 'VideoActivity')
                                                &#9654;
                                            @elseif(in_array($meta['type_short'] ?? '', ['QuizActivity', 'FinalExamActivity'], true))
                                                &#128203;
                                            @else
                                                &#128214;
                                            @endif
                                        </span>
                                        <div>
                                            <p class="player-act-title">{{ $activity->order }}. {{ $activity->title }}</p>
                                            <div class="player-act-meta">
                                                <span class="player-type-tag is-{{ $variant }}">{{ $meta['type_label'] }}</span>
                                                <span>{{ $meta['duration_label'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="player-act is-locked" title="{{ $state['locked_reason'] }}">
                                    <div class="player-act-row">
                                        <span class="player-act-icon is-lock" aria-hidden="true">&#128274;</span>
                                        <div>
                                            <p class="player-act-title">{{ $activity->order }}. {{ $activity->title }}</p>
                                            <div class="player-act-meta">
                                                <span class="player-type-tag is-exam">{{ $meta['type_label'] }}</span>
                                                <span>Bloqueado</span>
                                            </div>
                                            <p class="muted" style="margin: 6px 0 0; font-size: 11px; font-weight: 500;">{{ $state['locked_reason'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    </div>
                </div>
            </aside>

            <button
                type="button"
                class="player-sidebar-toggle"
                wire:click="toggleSidebar"
                aria-expanded="{{ $sidebarCollapsed ? 'false' : 'true' }}"
                aria-label="{{ $sidebarCollapsed ? 'Expandir menu do curso' : 'Recolher menu do curso' }}"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    @if($sidebarCollapsed)
                        <polyline points="9 18 15 12 9 6"></polyline>
                    @else
                        <polyline points="15 18 9 12 15 6"></polyline>
                    @endif
                </svg>
            </button>

            <main class="player-main">
                <div class="player-main-inner">
                @if($selectedActivity)
                    @php
                        $selMeta = $activityMeta[$selectedActivity->id] ?? ['type_label' => 'Atividade', 'badge_variant' => 'read'];
                        $selState = $activityStateMap[$selectedActivity->id] ?? ['unlocked' => true];
                        $orderPadded = str_pad((string) $selectedActivity->order, 2, '0', STR_PAD_LEFT);
                        $isCurrentCompleted = in_array($selectedActivity->id, $completedActivityIds, true);
                        $activityable = $selectedActivity->activityable;
                    @endphp

                    @php
                        $headerVariant = $selMeta['badge_variant'] ?? 'read';
                    @endphp
                    <div class="player-video-hero">
                        <div class="player-video-topline">
                            <span class="player-order-badge">{{ $orderPadded }}</span>
                            <span class="player-type-tag is-{{ $headerVariant }}">{{ $selMeta['type_label'] }}</span>
                        </div>
                        <h1 class="player-hero-title">{{ $selectedActivity->title }}</h1>
                    </div>

                    @if(!($selState['unlocked'] ?? true))
                        <div class="player-content-card">
                            <p class="muted" style="margin: 0;">Esta atividade esta bloqueada.</p>
                            <p style="margin: 10px 0 0;">{{ $selState['locked_reason'] }}</p>
                        </div>
                    @else
                        @if($selectedActivity->activityable_type === 'App\\Models\\ArticleActivity' || $selectedActivity->activityable_type === 'App\\Models\\SupportMaterialActivity')
                            <div class="player-content-card player-prose">
                                {!! $activityable->content_richtext ?? '<p>Nenhum conteudo disponivel.</p>' !!}
                            </div>
                        @elseif($selectedActivity->activityable_type === 'App\\Models\\EmbedContentActivity')
                            <div class="player-content-card player-embed-sandbox">
                                {!! $activityable->embed_code ?? '<p class="muted">Nenhum embed disponivel.</p>' !!}
                            </div>
                        @elseif($selectedActivity->activityable_type === 'App\\Models\\VideoActivity')
                            <div class="player-video-page">
                                @if($videoEmbedUrl)
                                    <div class="player-video-stage">
                                        <div class="player-video-wrap">
                                            <iframe
                                                src="{{ $videoEmbedUrl }}"
                                                title="Video do curso"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                                                allowfullscreen
                                                loading="lazy"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                            ></iframe>
                                        </div>
                                    </div>
                                @else
                                    <div class="player-content-card">
                                        <p class="muted" style="margin-top: 0;">Nao foi possivel incorporar o video automaticamente. Abra no link abaixo.</p>
                                        <p><a href="{{ $activityable->link ?? '#' }}" target="_blank" rel="noopener" class="btn btn-primary">Abrir video</a></p>
                                    </div>
                                @endif
                                <!-- <div class="player-video-subbar">
                                    <span class="player-video-subbar-left">
                                        <span aria-hidden="true">&#128337;</span>
                                        {{ $selMeta['duration_label'] ?? '—' }}
                                    </span>
                                </div> -->
                                @if($activityable && $activityable->description)
                                    <section class="player-about-module">
                                        <h2>Sobre este módulo</h2>
                                        <div class="player-prose">{!! $activityable->description !!}</div>
                                    </section>
                                @endif
                            </div>
                        @elseif(in_array($selectedActivity->activityable_type, ['App\\Models\\QuizActivity', 'App\\Models\\FinalExamActivity'], true))
                            @php
                                $isQuizType = $selectedActivity->activityable_type === 'App\\Models\\QuizActivity';
                                $questions = $activityable ? $activityable->questions()->orderBy('order')->get() : collect();
                                $attemptsLatest = $attempts->first();
                                $lastIdx = max(0, $questions->count() - 1);
                                $quizPct = $questions->count() > 0 ? (int) round(100 * ($assessmentStep + 1) / $questions->count()) : 0;
                                $showWizard = $assessmentPlayActive && $questions->isNotEmpty();
                                $showFlash = $feedbackMatches;
                                $showFailDb = ! $isCurrentCompleted && $attemptsLatest && ! ($attemptsLatest->passed ?? false) && ! $showWizard && ! $showFlash;
                                $showSuccessDb = $isCurrentCompleted && $attemptsLatest && ($attemptsLatest->passed ?? false) && ! $showWizard && ! $showFlash;
                                $showLanding = ! $showWizard && ! $showFlash && ! $showFailDb && ! $showSuccessDb;
                                $examNoAttempts = false;
                                if (! $isQuizType && $activityable) {
                                    $examMaxAttempts = max(1, (int) ($activityable->max_attempts ?? 3));
                                    $examNoAttempts = $attempts->count() >= $examMaxAttempts && ! $isCurrentCompleted;
                                }
                            @endphp
                            @if($questions->isEmpty())
                                <div class="player-content-card">
                                    <div class="error">{{ $isQuizType ? 'Este quiz esta sem questoes cadastradas ou com dados inconsistentes.' : 'Esta prova esta sem questoes cadastradas ou com dados inconsistentes.' }}</div>
                                </div>
                            @elseif($examNoAttempts)
                                <div class="player-assessment-result">
                                    <div class="player-assessment-result-icon is-fail" aria-hidden="true">&#128274;</div>
                                    <h2>Limite de tentativas</h2>
                                    <p class="muted">Voce usou todas as tentativas permitidas para esta prova.</p>
                                    <a class="btn btn-primary" href="{{ route('web.my-courses') }}">Voltar aos meus cursos</a>
                                </div>
                            @elseif($showWizard)
                                <form wire:submit.prevent="submitAssessment({{ $activityable->id }})">
                                    <div class="player-assessment-progress-head">
                                        <span class="player-assessment-q-badge">Questao {{ $assessmentStep + 1 }} de {{ $questions->count() }}</span>
                                        <span class="muted" style="font-size: 13px; font-weight: 700;">{{ $quizPct }}% completo</span>
                                    </div>
                                    <div class="player-assessment-qbar" role="progressbar" aria-valuenow="{{ $quizPct }}" aria-valuemin="0" aria-valuemax="100">
                                        <span style="width: {{ $quizPct }}%;"></span>
                                    </div>
                                    @foreach($questions as $idx => $question)
                                        <div class="player-quiz-question-card" style="{{ $idx === $assessmentStep ? '' : 'display:none' }}" wire:key="q-{{ $question->id }}">
                                            <p>{{ $question->statement }}</p>
                                            <div class="player-options">
                                                @foreach(['a','b','c','d'] as $option)
                                                    <label class="player-option-row">
                                                        <input type="radio" wire:model.live="assessmentAnswers.{{ $question->id }}" value="{{ $option }}">
                                                        <span><strong>{{ strtoupper($option) }})</strong> {{ $question->getAttribute('option_'.$option) }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <div class="player-quiz-nav">
                                                <button type="button" class="btn" wire:click="assessmentPrev" @if($assessmentStep === 0) disabled @endif>Anterior</button>
                                                @if($assessmentStep < $lastIdx)
                                                    <button type="button" class="btn btn-primary" wire:click="assessmentNext({{ $lastIdx }})">Proxima</button>
                                                @else
                                                    <button type="submit" class="btn btn-primary">{{ $isQuizType ? 'Enviar quiz' : 'Enviar prova' }}</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </form>
                            @elseif($showFlash)
                                @php
                                    $rScore = (float) ($assessmentFeedback['score'] ?? 0);
                                    $rPass = (bool) ($assessmentFeedback['passed'] ?? false);
                                    $rCorrect = (int) ($assessmentFeedback['correct'] ?? 0);
                                    $rTotal = (int) ($assessmentFeedback['total'] ?? 0);
                                    $flashIsFinalExam = ($assessmentFeedback['type'] ?? '') === 'final_exam';
                                @endphp
                                <div class="player-assessment-result">
                                    @if($rPass)
                                        <div class="player-assessment-result-icon is-pass" aria-hidden="true">&#10003;</div>
                                        <h2>Parabens! Voce foi aprovado!</h2>
                                        <span class="player-assessment-score-pill is-pass">Nota: {{ (int) round($rScore) }}</span>
                                        <p class="muted">Voce acertou {{ $rCorrect }} de {{ $rTotal }} questoes</p>
                                    @else
                                        <div class="player-assessment-result-icon is-fail" aria-hidden="true">&#10007;</div>
                                        <h2>Nao foi dessa vez</h2>
                                        <span class="player-assessment-score-pill is-fail">Nota: {{ (int) round($rScore) }}</span>
                                        <p class="muted">Voce acertou {{ $rCorrect }} de {{ $rTotal }} questoes</p>
                                    @endif
                                    @if($flashIsFinalExam)
                                        @if($rPass && $certificate)
                                            <a class="btn btn-primary" href="{{ route('web.certificates.show', $certificate->id) }}">Ver certificado</a>
                                        @elseif($rPass && ! $certificate)
                                            <p class="muted" style="margin-top:8px;">O certificado esta sendo gerado. Atualize a pagina em alguns segundos.</p>
                                        @elseif(! $rPass)
                                            <a class="btn btn-primary" href="{{ route('web.my-courses') }}">Voltar aos meus cursos</a>
                                        @endif
                                    @endif
                                </div>
                            @elseif($showFailDb)
                                @php
                                    $rScore = (float) ($attemptsLatest->score ?? 0);
                                    $rCorrect = (int) ($attemptsLatest->correct_answers ?? 0);
                                    $rTotal = (int) ($attemptsLatest->total_questions ?? 0);
                                @endphp
                                <div class="player-assessment-result">
                                    <div class="player-assessment-result-icon is-fail" aria-hidden="true">&#10007;</div>
                                    <h2>Nao foi dessa vez</h2>
                                    <span class="player-assessment-score-pill is-fail">Nota: {{ (int) round($rScore) }}</span>
                                    <p class="muted">Voce acertou {{ $rCorrect }} de {{ $rTotal }} questoes</p>
                                    <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;">
                                        <button type="button" class="btn btn-primary" wire:click="startAssessmentPlay">Tentar novamente</button>
                                    </div>
                                </div>
                            @elseif($showSuccessDb)
                                @php
                                    $rScore = (float) ($attemptsLatest->score ?? 0);
                                    $rCorrect = (int) ($attemptsLatest->correct_answers ?? 0);
                                    $rTotal = (int) ($attemptsLatest->total_questions ?? 0);
                                @endphp
                                <div class="player-assessment-result">
                                    <div class="player-assessment-result-icon is-pass" aria-hidden="true">&#10003;</div>
                                    <h2>Parabens! Voce foi aprovado!</h2>
                                    <span class="player-assessment-score-pill is-pass">Nota: {{ (int) round($rScore) }}</span>
                                    <p class="muted">Voce acertou {{ $rCorrect }} de {{ $rTotal }} questoes</p>
                                    @if(! $isQuizType)
                                        @if($certificate)
                                            <a class="btn btn-primary" href="{{ route('web.certificates.show', $certificate->id) }}">Ver certificado</a>
                                        @else
                                            <p class="muted" style="margin-top:8px;">O certificado esta sendo gerado. Atualize a pagina em alguns segundos.</p>
                                        @endif
                                    @endif
                                </div>
                            @elseif($showLanding)
                                <div class="player-assessment-landing">
                                    <div class="player-assessment-landing-card">
                                        <div class="player-assessment-landing-icon" aria-hidden="true">&#128202;</div>
                                        <h2>{{ $isQuizType ? 'Quiz de Conhecimento' : 'Prova final' }}</h2>
                                        <p class="muted" style="margin: 0 0 8px; font-size: 14px; line-height: 1.5;">
                                            {{ $isQuizType ? 'Vamos verificar o que voce aprendeu neste modulo.' : ($activityable->description ? strip_tags($activityable->description) : 'Responda com atencao. Voce precisa atingir a nota minima para aprovar.') }}
                                        </p>
                                        <div class="player-assessment-landing-meta">
                                            <span><span aria-hidden="true">&#9776;</span> {{ $questions->count() }} questoes</span>
                                            <span><span aria-hidden="true">&#128337;</span> {{ $selMeta['duration_label'] ?? '—' }}</span>
                                        </div>
                                        <button type="button" class="btn btn-primary" style="width:100%;" wire:click="startAssessmentPlay">{{ $isQuizType ? 'Comecar Quiz' : 'Comecar prova' }}</button>
                                    </div>
                                </div>
                            @endif
                        @elseif($selectedActivity->activityable_type === 'App\\Models\\MiniGameActivity')
                            <div class="player-content-card">
                                <p class="muted" style="margin-top: 0;">Tipo: <strong>{{ $activityable->game_type ?? '—' }}</strong></p>
                                @if($activityable && $activityable->config)
                                    <pre style="white-space: pre-wrap; font-size: 12px; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--eduit-line);">{{ json_encode($activityable->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                                <p class="muted" style="margin-bottom: 0;">Complete a atividade conforme as instrucoes do curso e marque como concluida quando terminar.</p>
                            </div>
                        @else
                            <div class="player-content-card">
                                <p class="muted">Tipo de atividade nao suportado neste player.</p>
                            </div>
                        @endif

                        @if($isCurrentCompleted && ! in_array($selectedActivity->activityable_type, ['App\\Models\\QuizActivity', 'App\\Models\\FinalExamActivity'], true))
                            <div class="player-done-banner">
                                <span aria-hidden="true">&#10003;</span> Atividade concluida!
                            </div>
                        @endif

                        <div class="player-footer-actions">
                            @if(!in_array($selectedActivity->activityable_type, ['App\\Models\\QuizActivity','App\\Models\\FinalExamActivity'], true))
                                @if($isCurrentCompleted)
                                    <form method="POST" action="{{ route('web.activities.incomplete', $selectedActivity->id) }}" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="player">
                                        <button class="btn btn-danger" type="submit">Desmarcar conclusao</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('web.activities.complete', $selectedActivity->id) }}" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="player">
                                        <button class="btn" type="submit">Marcar como concluida</button>
                                    </form>
                                @endif
                            @endif

                            @if($nextActivity)
                                <a
                                    class="btn btn-primary"
                                    href="{{ route('web.courses.player', ['id' => $course->id, 'activity' => $nextActivity->id]) }}"
                                >Proxima atividade &rsaquo;</a>
                            @endif
                        </div>

                        @if($attempts->isNotEmpty())
                            <section class="card card-soft" style="margin-top: 16px;">
                                <h3 style="margin-top: 0;">Historico de tentativas</h3>
                                @foreach($attempts as $attempt)
                                    <p style="margin: 6px 0;">
                                        Tentativa {{ $attempt->attempt_number }} - {{ $attempt->score }}% ({{ $attempt->correct_answers }}/{{ $attempt->total_questions }})
                                    </p>
                                @endforeach
                            </section>
                        @endif
                    @endif
                @else
                    <p class="muted">Nenhuma atividade encontrada neste curso.</p>
                @endif
                </div>
            </main>
        </div>
    </div>
</div>
