@extends('layouts.app')

@section('pageTitle', 'Início')

@push('styles')
<style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #181a4d;
            --text-secondary: #525379;
            --muted: #61738f;
            --brand: #1a6398;
            --brand-hover: #2e87c7;
            --accent: #d2ea6a;
            --line: #e5e7eb;
            --shadow: 0 10px 30px rgba(24, 26, 77, .08);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, .06), 0 4px 6px -2px rgba(0, 0, 0, .03);
        }
        .page-home { background: var(--bg); color: var(--text); }
        .page-home a { color: inherit; text-decoration: none; }
        .page-home .wrap { width: min(1280px, calc(100% - 32px)); margin: 0 auto; }
        .page-home .wrap-narrow { width: min(1152px, calc(100% - 32px)); margin: 0 auto; }

        .page-home .hero {
            display: grid; grid-template-columns: 1fr 1fr; gap: clamp(32px, 5vw, 100px);
            padding: 32px 0 28px; align-items: center;
        }
        .page-home .pill {
            display: inline-flex; align-items: center; padding: 10px 16px; border-radius: 16px;
            border: 1px solid rgba(26, 99, 152, .35);
            background: rgba(26, 99, 152, .12); color: var(--brand);
            font-weight: 700; font-size: 13px; margin-bottom: 20px;
        }
        .page-home .hero h1 {
            margin: 0; line-height: 1.08; font-weight: 800;
            font-size: clamp(2rem, 1.4rem + 2.2vw, 3.4rem);
            max-width: 560px; letter-spacing: -0.02em;
        }
        .page-home .hero h1 .accent { color: var(--brand); }
        .page-home .hero-lead {
            margin: 18px 0 0; max-width: 560px; color: var(--text-secondary);
            font-size: clamp(1rem, .95rem + .6vw, 1.25rem); line-height: 1.65;
        }
        .page-home .hero-ctas { display: flex; flex-wrap: wrap; gap: 16px; margin-top: 28px; }
        .page-home .btn-hero {
            border-radius: 16px; padding: 12px 22px; font-size: 15px; font-weight: 600;
            border: 1px solid transparent; transition: transform .2s, background .2s, box-shadow .2s;
        }
        .page-home .btn-hero-primary { background: var(--brand); color: #fff; }
        .page-home .btn-hero-primary:hover { background: var(--brand-hover); transform: scale(1.02); }
        .page-home .btn-hero-accent { background: var(--accent); color: var(--text); border-color: var(--brand); }
        .page-home .btn-hero-accent:hover { background: #c4df4d; transform: scale(1.02); }
        .page-home .hero-visual { display: flex; justify-content: center; align-items: center; }
        .page-home .hero-visual img {
            width: auto; max-width: 100%; height: auto; max-height: min(450px, 55vh);
            display: block;
        }

        .page-home .stats-wrap { padding: 0 0 40px; }
        .page-home .stats {
            background: linear-gradient(149deg, #1a6398 0%, #1a6398 100%);
            color: #fff; border-radius: 24px;
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px;
            padding: 28px 32px; box-shadow: var(--shadow);
        }
        .page-home .stat { text-align: center; }
        .page-home .stat h3 { margin: 0 0 8px; font-size: clamp(1.25rem, 1.1rem + .5vw, 1.5rem); font-weight: 700; }
        .page-home .stat p { margin: 0; font-size: 13px; line-height: 1.45; opacity: .92; }

        .page-home .section-courses { padding: 20px 0 48px; }
        .page-home .section-head { text-align: center; margin-bottom: 44px; }
        .page-home .home-section-title {
            margin: 0; font-size: clamp(1.5rem, 1.2rem + 1.2vw, 2.25rem); font-weight: 800; letter-spacing: -0.02em;
        }
        .page-home .section-subtitle { margin: 14px 0 0; color: var(--text-secondary); font-size: clamp(.95rem, .9rem + .4vw, 1.1rem); }

        .page-home .courses-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; align-items: stretch;
        }
        @media (max-width: 1024px) {
            .page-home .courses-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .page-home .courses-grid:not(.courses-grid--empty) {
                display: flex;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                gap: 16px;
                padding-bottom: 12px;
                margin-inline: -8px;
                padding-inline: 8px;
                -webkit-overflow-scrolling: touch;
            }
            .page-home .courses-grid:not(.courses-grid--empty) .course-card {
                flex: 0 0 min(300px, 88vw);
                scroll-snap-align: start;
            }
            .page-home .courses-grid--empty { grid-template-columns: 1fr; }
        }

        .page-home .course-card {
            background: var(--surface); border: 1px solid var(--line); border-radius: 16px;
            overflow: hidden; box-shadow: var(--shadow); transition: box-shadow .3s; display: flex; flex-direction: column;
        }
        .page-home .course-card:hover { box-shadow: 0 20px 40px rgba(24, 26, 77, .12); }
        .page-home .course-media {
            position: relative; overflow: hidden; cursor: pointer; aspect-ratio: 16 / 10; min-height: 140px;
            background: #e8eefb;
        }
        .page-home .course-media img {
            width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease;
        }
        .page-home .course-card:hover .course-media img { transform: scale(1.08); }
        .page-home .course-media::after {
            content: ""; position: absolute; bottom: 0; left: 0; right: 0; height: 56px;
            background: linear-gradient(to top, rgba(0,0,0,.45), transparent); pointer-events: none;
        }
        .page-home .course-media-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0); transition: background .3s;
            display: flex; align-items: center; justify-content: center; pointer-events: none;
        }
        .page-home .course-card:hover .course-media-overlay { background: rgba(0,0,0,.28); }
        .page-home .play-circle {
            width: 56px; height: 56px; border-radius: 50%; background: var(--brand);
            display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0,0,0,.2);
            opacity: 0; transform: scale(.85); transition: opacity .3s, transform .3s;
        }
        .page-home .course-card:hover .play-circle { opacity: 1; transform: scale(1); }
        .page-home .play-circle svg { margin-left: 3px; }

        .page-home .badge-diff {
            position: absolute; top: 12px; left: 12px; z-index: 2;
            padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
        }
        .page-home .badge-diff--iniciante { background: #c8e6c9; color: #1b5e20; }
        .page-home .badge-diff--intermediario { background: #ffe0b2; color: #e65100; }
        .page-home .badge-diff--avancado { background: #ffcdd2; color: #b71c1c; }

        .page-home .course-body { padding: 16px 18px 20px; flex: 1; display: flex; flex-direction: column; }
        .page-home .course-body-top {
            display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px;
        }
        .page-home .course-cat {
            font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px;
            color: var(--brand); background: rgba(26, 99, 152, .1);
        }
        .page-home .heart-btn {
            padding: 6px; border-radius: 999px; border: none; background: transparent; cursor: default;
            color: var(--text-secondary); line-height: 0;
        }
        .page-home .heart-btn svg { width: 20px; height: 20px; }

        .page-home .course-title {
            margin: 0 0 8px; font-size: clamp(1rem, .95rem + .6vw, 1.25rem); font-weight: 800; line-height: 1.35;
            color: var(--text); display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .page-home .course-desc {
            margin: 0; font-size: 13px; line-height: 1.55; color: var(--text-secondary);
            display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
            min-height: 3.1em;
        }
        .page-home .course-meta-row {
            display: flex; align-items: center; flex-wrap: wrap; gap: 12px 0 0;
            margin-top: 12px; font-size: 12px; color: var(--text-secondary);
        }
        .page-home .course-meta-row span { display: inline-flex; align-items: center; gap: 6px; margin-right: 16px; }
        .page-home .course-meta-row svg { flex-shrink: 0; opacity: .85; }

        .page-home .course-price-line {
            margin: 14px 0 0; font-size: 14px; font-weight: 600; color: var(--text);
            display: flex; flex-wrap: wrap; align-items: baseline; gap: 6px 10px;
        }
        .page-home .course-price-line .strike { text-decoration: line-through; opacity: .55; font-size: 13px; font-weight: 500; }
        .page-home .course-cta {
            margin-top: auto; padding-top: 12px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; border-radius: 12px; padding: 11px 14px; font-size: 14px; font-weight: 700;
            background: var(--brand); color: #fff; border: 1px solid var(--brand);
            transition: background .2s, color .2s;
        }
        .page-home .course-cta:hover { background: var(--accent); color: var(--text); }
        .page-home .course-cta svg { width: 18px; height: 18px; }

        .page-home .see-all-wrap { display: flex; justify-content: center; margin-top: 40px; }
        .page-home .see-all {
            display: inline-flex; align-items: center; gap: 8px; border-radius: 16px;
            padding: 12px 22px; font-size: 15px; font-weight: 600; background: var(--brand); color: #fff;
            border: 1px solid var(--brand); transition: background .2s, transform .2s;
        }
        .page-home .see-all:hover { background: var(--brand-hover); transform: scale(1.02); }
        .page-home .see-all svg { transition: transform .2s; }
        .page-home .see-all:hover svg { transform: translateX(4px); }

        .page-home .section-why { padding: 12px 0 48px; }
        .page-home .benefits-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; max-width: 1152px; margin: 0 auto;
        }
        .page-home .benefit {
            border-radius: 16px; padding: 22px 24px; min-height: 140px;
            display: flex; flex-direction: column; transition: transform .25s;
        }
        .page-home .benefit:hover { transform: scale(1.03); }
        .page-home .benefit-icon {
            width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center; margin-bottom: 12px;
        }
        .page-home .benefit-icon svg { width: 22px; height: 22px; }
        .page-home .benefit h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; }
        .page-home .benefit p { margin: 0; font-size: 13px; line-height: 1.55; opacity: .95; }
        .page-home .benefit-1 { background: #6b5cc8; color: #fff; }
        .page-home .benefit-2 { background: #181a4d; color: #fff; }
        .page-home .benefit-3 { background: #b5caff; color: #181a4d; }
        .page-home .benefit-3 .benefit-icon { background: rgba(255,255,255,.35); }
        .page-home .benefit-3 .benefit-icon svg { color: #181a4d; }
        .page-home .benefit-4 { background: #d2ea6a; color: #181a4d; }
        .page-home .benefit-4 .benefit-icon { background: rgba(255,255,255,.35); }
        .page-home .benefit-5 { background: var(--brand); color: #fff; grid-column: span 2; }
        @media (max-width: 900px) {
            .page-home .benefits-grid { grid-template-columns: 1fr; }
            .page-home .benefit-5 { grid-column: auto; }
        }

        .page-home .cta {
            position: relative;
            overflow: hidden;
            background: #fff;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            width: 100vw;
            max-width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            box-sizing: border-box;
        }
        .page-home .cta::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background: url('/images/Bg_CTA.png') center/cover no-repeat;
            opacity: .88;
        }
        .page-home .cta-inner {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 56px 16px 64px;
        }
        .page-home .cta-inner h2 {
            margin: 0 auto 20px; font-size: clamp(1.6rem, 1.2rem + 1.2vw, 2.75rem);
            font-weight: 800; max-width: 720px; padding: 0 8px;
            line-height: 1.2;
        }
        .page-home .cta-btn {
            border-radius: 16px; padding: 14px 26px; font-size: 15px; font-weight: 600;
            background: var(--brand); color: #fff; border: none; display: inline-block;
            transition: opacity .2s, transform .2s;
        }
        .page-home .cta-btn:hover { opacity: .92; transform: scale(1.02); }

        @media (max-width: 960px) {
            .page-home .hero { grid-template-columns: 1fr; padding-top: 24px; }
            .page-home .hero-visual { order: -1; }
            .page-home .stats { grid-template-columns: 1fr; gap: 14px; padding: 22px 20px; }
        }
        @media (max-width: 640px) {
            .page-home .wrap { width: calc(100% - 24px); }
            .page-home .hero-ctas .btn-hero { width: 100%; text-align: center; justify-content: center; }
            .page-home .cta-inner { padding: 44px 16px 52px; }
            .page-home .cta-btn { display: block; width: min(360px, 100%); margin: 0 auto; }
        }
</style>
@endpush

@section('content')
<div class="page-home">
        <section class="wrap hero">
            <div>
                <span class="pill">Prepare-se para o futuro</span>
                <h1>Conquiste a carreira dos <span class="accent">seus sonhos</span></h1>
                <p class="hero-lead">Aprenda o que o mercado realmente valoriza de forma leve, rápida e sem enrolação.</p>
                <div class="hero-ctas">
                    <a class="btn-hero btn-hero-primary" href="{{ route('web.courses.index') }}">Explorar cursos</a>
                    @auth
                        <a class="btn-hero btn-hero-accent" href="{{ route('web.dashboard') }}">Ir para o painel</a>
                    @else
                        <a class="btn-hero btn-hero-accent" href="{{ route('register') }}">Criar Conta</a>
                    @endauth
                </div>
            </div>
            <div class="hero-visual">
                <img src="/images/IMG_home%204.png" alt="Estudante aprendendo" width="440" height="450">
            </div>
        </section>

        <div class="stats-wrap wrap-narrow">
            <div class="stats">
                <article class="stat">
                    <h3>Certificados</h3>
                    <p>Seu Esforço Reconhecido e Certificado</p>
                </article>
                <article class="stat">
                    <h3>Conteúdo Atualizado</h3>
                    <p>Conteúdo Relevante e Atualizado</p>
                </article>
                <article class="stat">
                    <h3>Cursos 100% Online</h3>
                    <p>Estude no Seu Ritmo, de Onde Quiser</p>
                </article>
            </div>
        </div>

        <section class="wrap section-courses">
            <div class="section-head">
                <h2 class="home-section-title">Cursos Mais Procurados</h2>
                <p class="section-subtitle">Habilidades que o mercado procura, resultados que você merece</p>
            </div>

            <div class="courses-grid{{ $featuredCourses->isEmpty() ? ' courses-grid--empty' : '' }}">
                @forelse($featuredCourses as $course)
                    @php
                        $difficultyKey = $course->difficulty_level ?: 'iniciante';
                        $diffLabel = $course->getDifficultyLevelLabel();
                        $modulesCount = (int) ($course->activities_count ?? $course->modules_count ?? 0);
                        $workloadHours = (int) $course->workload;
                        $finalPrice = $course->getFinalPrice();
                        $hasDiscount = $course->hasDiscount();
                    @endphp
                    <article class="course-card">
                        <a href="{{ route('web.courses.show', $course->id) }}" class="course-media" aria-hidden="true" tabindex="-1">
                            @if($course->cover_image_url)
                                <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" loading="lazy">
                            @endif
                            <span class="badge-diff badge-diff--{{ $difficultyKey }}">{{ $diffLabel }}</span>
                            <div class="course-media-overlay" aria-hidden="true">
                                <span class="play-circle">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 7.5v9l7-4.5L9 7.5z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                        <div class="course-body">
                            <div class="course-body-top">
                                <span class="course-cat">{{ $course->getCategoryLabel() ?? 'Curso' }}</span>
                                <span class="heart-btn" title="Favoritos" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </span>
                            </div>
                            <h3 class="course-title">{{ $course->title }}</h3>
                            <p class="course-desc">{{ \Illuminate\Support\Str::limit($course->short_description ?? $course->description ?? '', 120) }}</p>
                            <div class="course-meta-row">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    {{ $workloadHours }}h
                                </span>
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    {{ $modulesCount }} módulos
                                </span>
                            </div>
                            <div class="course-price-line">
                                @if($course->isFree())
                                    Preço: GRATUITO
                                @elseif($hasDiscount)
                                    <span>Preço:</span>
                                    <span class="strike">R$ {{ number_format((float) $course->price, 2, ',', '.') }}</span>
                                    <span>R$ {{ number_format($finalPrice, 2, ',', '.') }}</span>
                                @else
                                    Preço: R$ {{ number_format($finalPrice, 2, ',', '.') }}
                                @endif
                            </div>
                            <a class="course-cta" href="{{ route('web.courses.show', $course->id) }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" width="18" height="18">
                                    <path d="M9 7.5v9l7-4.5L9 7.5z" fill="currentColor" stroke="none"/>
                                </svg>
                                Iniciar Agora
                            </a>
                        </div>
                    </article>
                @empty
                    <article class="course-card" style="grid-column: 1 / -1;">
                        <div class="course-body">
                            <h3 class="course-title">Nenhum curso disponível no momento</h3>
                            <p class="course-desc">Assim que novos cursos forem publicados, eles aparecerão aqui.</p>
                        </div>
                    </article>
                @endforelse
            </div>

            <div class="see-all-wrap">
                <a class="see-all" href="{{ route('web.courses.index') }}">
                    <span>Ver Todos os Cursos</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </section>

        <section class="wrap section-why">
            <div class="section-head">
                <h2 class="home-section-title">Porque estudar na Eduit?</h2>
            </div>
            <div class="benefits-grid">
                <article class="benefit benefit-1">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <h3>Aprenda no seu ritmo</h3>
                    <p>Acesse a qualquer hora, revise o que precisar e absorva cada conhecimento sem pressa.</p>
                </article>
                <article class="benefit benefit-2">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <h3>Foco na prática</h3>
                    <p>Conteúdo voltado para aplicação real no mercado de trabalho.</p>
                </article>
                <article class="benefit benefit-3">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </div>
                    <h3>Plataforma otimizada</h3>
                    <p>Nossa plataforma moderna e intuitiva garante um estudo fluido em qualquer tela.</p>
                </article>
                <article class="benefit benefit-4">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <h3>Certificados reconhecidos</h3>
                    <p>Ideal para horas complementares e para se destacar no mercado de trabalho. Comprove seu valor e impulsione seu futuro!</p>
                </article>
                <article class="benefit benefit-5">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Qualidade ao Seu Alcance</h3>
                    <p>Descomplicamos o conhecimento. Transformamos temas complexos em aprendizado claro e prático. Nossos cursos garantem que você, de qualquer nível, domine novos assuntos com facilidade e aplique na vida real.</p>
                </article>
            </div>
        </section>

        <section class="cta">
            <div class="wrap cta-inner">
                <h2>Cursos criados para quem quer sair na frente!</h2>
                @auth
                    <a class="cta-btn" href="{{ route('web.dashboard') }}">Ir para o painel</a>
                @else
                    <a class="cta-btn" href="{{ route('register') }}">Criar conta gratuita</a>
                @endauth
            </div>
        </section>
</div>
@endsection
