@php
    $activityCount = $course->activities->count();
    $totalMin = (int) ($course->total_duration ?? 0);
    if ($totalMin > 0) {
        $h = intdiv($totalMin, 60);
        $m = $totalMin % 60;
        if ($h > 0 && $m > 0) {
            $durationLabel = $h.'h '.$m.'m';
        } elseif ($h > 0) {
            $durationLabel = $h.'h';
        } else {
            $durationLabel = $m.'m';
        }
    } else {
        $durationLabel = ((int) ($course->workload ?? 0)).'h';
    }
    $finalPrice = $course->getFinalPrice();
    $hasDiscount = $course->hasDiscount();
    $learnHtml = $course->long_description;
    $learnBullets = collect(preg_split('/\r\n|\r|\n/', strip_tags($course->description ?? '')))
        ->map(fn ($l) => trim($l))
        ->filter()
        ->values();
@endphp

<div class="course-show">
    <style>
        .course-show { padding-bottom: 8px; }
        .cs-back {
            display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px;
            font-size: 14px; font-weight: 600; color: #181a4d;
        }
        .cs-back:hover { color: #1a6398; }
        .cs-hero {
            display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 32px;
        }
        @media (min-width: 1024px) {
            .cs-hero { grid-template-columns: 2fr 1fr; gap: 40px; align-items: start; }
        }
        .cs-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .cs-badge-diff {
            padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;
        }
        .cs-badge-diff.iniciante { background: #c8e6c9; color: #1b5e20; }
        .cs-badge-diff.intermediario { background: #ffe0b2; color: #e65100; }
        .cs-badge-diff.avancado { background: #ffcdd2; color: #b71c1c; }
        .cs-badge-cat {
            padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;
            background: #ecfccb; color: #3f6212; border: 1px solid #d9f99d;
        }
        .cs-title { margin: 0 0 16px; font-size: clamp(1.75rem, 1.2rem + 2vw, 2.75rem); font-weight: 800; line-height: 1.15; color: #181a4d; letter-spacing: -0.02em; }
        .cs-price-row { margin-bottom: 16px; display: flex; flex-wrap: wrap; align-items: baseline; gap: 10px 14px; }
        .cs-price { font-size: clamp(1.5rem, 1.2rem + 1vw, 1.85rem); font-weight: 800; color: #1a6398; }
        .cs-price-old { font-size: 1.25rem; text-decoration: line-through; color: #64748b; font-weight: 600; }
        .cs-lead { margin: 0 0 24px; font-size: 1.05rem; line-height: 1.65; color: #525379; max-width: 720px; }
        .cs-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px 16px;
            margin-bottom: 8px;
            max-width: 640px;
        }
        @media (min-width: 768px) {
            .cs-stats { grid-template-columns: repeat(4, 1fr); max-width: 720px; }
        }
        .cs-stat { text-align: center; padding: 8px 4px; }
        .cs-stat svg { margin: 0 auto 6px; color: #1a6398; display: block; }
        .cs-stat-label { font-size: 11px; color: #525379; margin: 0 0 4px; }
        .cs-stat-val { font-size: 15px; font-weight: 700; color: #181a4d; margin: 0; }
        .cs-stat-fav { background: none; border: none; cursor: pointer; font-family: inherit; padding: 8px; }
        .cs-stat-fav svg { color: #1a6398; }
        .cs-sidebar {
            border-radius: 16px; overflow: hidden; background: #fff;
            border: 1px solid #e5e7eb; box-shadow: 0 10px 30px rgba(24, 26, 77, .08);
            position: sticky; top: 88px;
        }
        @media (max-width: 1023px) {
            .cs-sidebar { position: static; margin-top: 8px; }
        }
        .cs-sidebar img { width: 100%; height: 220px; object-fit: cover; display: block; background: #e8eefb; }
        .cs-sidebar-body { padding: 20px; }
        .cs-sidebar .progress-line { margin-bottom: 12px; font-size: 13px; color: #525379; }
        .cs-btn-main {
            display: block; width: 100%; text-align: center; padding: 12px 16px; border-radius: 12px;
            font-size: 15px; font-weight: 700; border: none; cursor: pointer; font-family: inherit;
            background: #1a6398; color: #fff;
        }
        .cs-btn-main:hover { background: #2e87c7; color: #fff; }
        .cs-btn-main.secondary { background: #fff; color: #1a6398; border: 2px solid #1a6398; }
        .cs-grid-2 {
            display: grid; grid-template-columns: 1fr; gap: 24px; margin-top: 8px;
        }
        @media (min-width: 1024px) {
            .cs-grid-2 { grid-template-columns: 1fr 1fr; gap: 32px; }
        }
        .cs-panel {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px 28px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, .04);
        }
        .cs-panel h2 { margin: 0 0 18px; font-size: 1.35rem; font-weight: 800; color: #181a4d; }
        .cs-panel ul { margin: 0; padding-left: 1.15rem; color: #181a4d; line-height: 1.65; }
        .cs-panel ul li { margin-bottom: 8px; }
        .cs-panel .prose-html { color: #181a4d; line-height: 1.65; font-size: 15px; }
        .cs-panel .prose-html p { margin: 0 0 10px; }
        .cs-mod {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 14px 16px; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 10px;
            background: #fff; transition: box-shadow .2s;
        }
        .cs-mod:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06); }
        .cs-mod-left { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; }
        .cs-mod-num {
            width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
            background: #1a6398; color: #fff; font-size: 13px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
        }
        .cs-mod-title { font-weight: 700; font-size: 15px; color: #181a4d; margin: 0 0 2px; }
        .cs-mod-sub { font-size: 12px; color: #64748b; margin: 0; line-height: 1.35; }
        .cs-mod-dur { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; flex-shrink: 0; }
        .cs-mod-dur svg { flex-shrink: 0; }
        @media (max-width: 640px) {
            .cs-mod-dur { display: none; }
        }
        .cs-login-hint { font-size: 14px; color: #525379; margin: 0; }
        .cs-login-hint a { color: #1a6398; font-weight: 600; }
    </style>

    <a class="cs-back" href="{{ route('web.courses.index') }}">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Voltar
    </a>

    @if (session('status'))
        <div class="status" style="margin-bottom: 16px;">{{ session('status') }}</div>
    @endif

    <div class="cs-hero">
        <div>
            <div class="cs-badges">
                <span class="cs-badge-diff {{ $course->difficulty_level ?: 'iniciante' }}">{{ $course->getDifficultyLevelLabel() }}</span>
                @if($course->getCategoryLabel())
                    <span class="cs-badge-cat">{{ $course->getCategoryLabel() }}</span>
                @endif
                @if($course->isFree())
                    <span class="badge badge-success">GRATUITO</span>
                @endif
                @if($hasDiscount)
                    <span class="badge" style="background:#fef3c7;border-color:#fcd34d;color:#92400e;">PROMOÇÃO</span>
                @endif
            </div>

            <h1 class="cs-title">{{ $course->title }}</h1>

            @if($course->isFree())
                <div class="cs-price-row">
                    <span class="cs-price" style="color:#16a34a;">Gratuito</span>
                </div>
            @else
                <div class="cs-price-row">
                    @if($hasDiscount)
                        <span class="cs-price-old">R$ {{ number_format((float) $course->price, 2, ',', '.') }}</span>
                    @endif
                    <span class="cs-price">R$ {{ number_format($finalPrice, 2, ',', '.') }}</span>
                    @if($hasDiscount && (float) $course->price > 0)
                        <span class="badge" style="background:#fef3c7;">{{ (int) $course->discount_percentage }}% OFF</span>
                    @endif
                </div>
            @endif

            <p class="cs-lead">{{ $course->short_description ?? $course->description }}</p>

            <div class="cs-stats">
                <div class="cs-stat">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <p class="cs-stat-label">Duração</p>
                    <p class="cs-stat-val">{{ $durationLabel }}</p>
                </div>
                <div class="cs-stat">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <p class="cs-stat-label">Módulos</p>
                    <p class="cs-stat-val">{{ $activityCount }}</p>
                </div>
                <div class="cs-stat">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <p class="cs-stat-label">Prazo</p>
                    <p class="cs-stat-val">30 dias</p>
                </div>
                <div class="cs-stat">
                    @auth
                        <button type="button" class="cs-stat-fav" title="Favoritos em breve" disabled style="opacity:.6;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            <p class="cs-stat-label">Favoritar</p>
                        </button>
                    @else
                        <a class="cs-stat-fav" href="{{ route('login') }}" title="Entre para favoritar">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            <p class="cs-stat-label">Favoritar</p>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <aside class="cs-sidebar">
            @if($course->cover_image_url)
                <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}">
            @else
                <div style="height:220px;background:linear-gradient(135deg,#e0e7ff,#dbeafe);"></div>
            @endif
            <div class="cs-sidebar-body">
                @if($enrollment)
                    <p class="progress-line">Seu progresso: {{ (int) ($progress['progress'] ?? 0) }}%</p>
                    <progress max="100" value="{{ (int) ($progress['progress'] ?? 0) }}" style="width:100%;height:10px;margin-bottom:16px;"></progress>
                    <a class="cs-btn-main" href="{{ route('web.courses.player', $course->id) }}">Continuar curso</a>
                @elseif($isAuthenticated)
                    <form method="POST" action="{{ route('web.courses.enroll', $course->id) }}" style="margin:0;">
                        @csrf
                        <button class="cs-btn-main" type="submit">Matricular-se</button>
                    </form>
                @else
                    <a class="cs-btn-main" href="{{ route('login') }}">Matricular-se</a>
                    <p class="cs-login-hint" style="margin-top:12px;">Já tem conta? <a href="{{ route('login') }}">Entrar</a></p>
                @endif
            </div>
        </aside>
    </div>

    <div class="cs-grid-2">
        <section class="cs-panel">
            <h2>O que você vai aprender</h2>
            @if($learnHtml)
                <div class="prose-html">{!! $learnHtml !!}</div>
            @elseif($learnBullets->isNotEmpty())
                <ul>
                    @foreach($learnBullets as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @else
                <p class="muted" style="margin:0;">Em breve mais detalhes sobre o conteúdo programático.</p>
            @endif
        </section>

        <section class="cs-panel">
            <h2>Conteúdo do curso</h2>
            @forelse($course->activities as $activity)
                <div class="cs-mod">
                    <div class="cs-mod-left">
                        <span class="cs-mod-num">{{ $activity->order }}</span>
                        <div style="min-width:0;">
                            <p class="cs-mod-title">{{ $activity->title }}</p>
                            @php
                                $typeLabel = match (class_basename((string) $activity->activityable_type)) {
                                    'ArticleActivity' => 'Leitura',
                                    'VideoActivity' => 'Vídeo',
                                    'QuizActivity' => 'Quiz',
                                    'FinalExamActivity' => 'Prova final',
                                    'EmbedContentActivity' => 'Conteúdo incorporado',
                                    'SupportMaterialActivity' => 'Material de apoio',
                                    default => 'Atividade',
                                };
                            @endphp
                            <p class="cs-mod-sub">{{ $typeLabel }}</p>
                        </div>
                    </div>
                    <div class="cs-mod-dur">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <span>{{ $activityDurationLabels[$activity->id] ?? '—' }}</span>
                    </div>
                </div>
            @empty
                <p class="muted" style="margin:0;">Este curso ainda não possui módulos cadastrados.</p>
            @endforelse

            @if($enrollment)
                <p class="muted" style="margin: 16px 0 0; font-size: 13px;">
                    Acompanhe e conclua as atividades no <a href="{{ route('web.courses.player', $course->id) }}" style="color:#1a6398;font-weight:600;">player do curso</a>.
                </p>
            @endif
        </section>
    </div>
</div>
