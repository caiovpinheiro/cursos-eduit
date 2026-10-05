<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/favicon.png" type="image/png" sizes="any">
    <title>
        @hasSection('pageTitle')
            @yield('pageTitle') — {{ config('app.name', 'Curso Platform') }}
        @elseif(isset($title) && trim((string) $title) !== '')
            {{ trim((string) $title) }} — {{ config('app.name', 'Curso Platform') }}
        @else
            {{ config('app.name', 'Curso Platform') }}
        @endif
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --eduit-bg: #f8fafc;
            --eduit-text: #181a4d;
            --eduit-muted: #525379;
            --eduit-brand: #1a6398;
            --eduit-line: #e5e7eb;
            --eduit-shadow: 0 10px 15px -3px rgba(0, 0, 0, .06), 0 4px 6px -2px rgba(0, 0, 0, .03);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Inter", system-ui, -apple-system, sans-serif;
            background: var(--eduit-bg);
            color: var(--eduit-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }
        .app-shell { flex: 1; width: min(1280px, calc(100% - 32px)); margin: 0 auto; padding: 24px 16px 48px; }
        .app-shell:has(.page-home) { padding-bottom: 0; }
        /* Player de curso: largura total; slot sem padding lateral (conteúdo controlado na view) */
        .app-shell.app-shell--player {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0 0 48px;
        }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(248, 250, 252, .94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--eduit-line);
            box-shadow: var(--eduit-shadow);
        }
        .topbar-inner {
            width: min(1280px, calc(100% - 32px)); margin: 0 auto;
            min-height: 64px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
            padding: 8px 0;
        }
        .brand img { height: 36px; width: auto; display: block; }
        .nav { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
        .nav-btn {
            padding: 8px 14px; border-radius: 12px; font-size: 13px; font-weight: 600;
            color: var(--eduit-text); border: none; background: transparent; cursor: pointer;
            transition: background .15s ease;
        }
        .nav-btn:hover { background: rgba(26, 99, 152, .08); }
        .nav-btn.is-active { background: var(--eduit-brand); color: #fff; box-shadow: 0 4px 6px -1px rgba(26, 99, 152, .25); }
        .topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .user-pill {
            display: flex; align-items: center; gap: 8px; padding: 4px 10px 4px 4px; border-radius: 999px;
            background: rgba(255,255,255,.9); border: 1px solid var(--eduit-line);
            max-width: 220px;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease;
        }
        .user-pill:hover {
            background: rgba(26, 99, 152, .08);
            border-color: rgba(26, 99, 152, .25);
        }
        .user-pill.is-active {
            border-color: var(--eduit-brand);
            box-shadow: 0 0 0 1px rgba(26, 99, 152, .15);
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #1a6398, #6b5cc8);
            color: #fff; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            color: var(--eduit-brand);
            background: rgba(255, 255, 255, .9);
            border: 1px solid var(--eduit-line);
            border-radius: 10px;
            cursor: pointer;
            padding: 8px 12px;
            transition: background .15s ease, border-color .15s ease;
        }
        .btn-logout:hover {
            background: rgba(26, 99, 152, .08);
            border-color: rgba(26, 99, 152, .25);
        }
        .btn-logout svg { flex-shrink: 0; }

        /* Rodape padrao (mesmo da home) */
        .site-footer { background: #dfe6f8; padding: 22px 0 16px; margin-top: auto; }
        .footer-inner { width: min(1280px, calc(100% - 32px)); margin: 0 auto; }
        .site-footer .footer-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .site-footer .footer-grid { grid-template-columns: 1fr; }
        }
        .site-footer .footer-label { font-size: 14px; font-weight: 600; color: #1e6883; min-width: 100px; margin-right: 8px; }
        .site-footer .footer-row { display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: baseline; }
        .site-footer .footer-row a { font-size: 14px; color: #181a4d; }
        .site-footer .footer-row a:hover { opacity: .75; }
        .site-footer .footer-logo img { height: 32px; width: auto; }
        .site-footer .footer-copy {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(24, 26, 77, .2);
            font-size: 13px;
            color: #181a4d;
        }
        .footer-mobile-logo { display: none; margin-bottom: 4px; }
        @media (max-width: 900px) {
            .footer-mobile-logo { display: block; }
            .footer-logo-desktop { display: none; }
        }
        @media (max-width: 900px) {
            .topbar-inner { flex-wrap: wrap; }
            .nav { width: 100%; order: 3; }
            .topbar-right { margin-left: auto; }
        }
        @media (max-width: 640px) {
            .topbar-inner { gap: 10px; }
            .nav {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .nav::-webkit-scrollbar { display: none; width: 0; height: 0; }
            .nav-btn { flex: 0 0 auto; padding: 8px 12px; }
            .user-name { max-width: 120px; }
        }

        /* Visitantes: mesmo padrão da landing (login / registro) */
        .nav-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background .15s ease, border-color .15s ease, color .15s ease;
            white-space: nowrap;
        }
        .nav-cta--ghost {
            background: rgba(255, 255, 255, .9);
            border-color: var(--eduit-brand);
            color: var(--eduit-brand);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }
        .nav-cta--ghost:hover { background: rgba(26, 99, 152, .1); }
        .nav-cta--ghost.is-active {
            background: rgba(26, 99, 152, .12);
            border-color: var(--eduit-brand);
        }
        .nav-cta--primary {
            background: var(--eduit-brand);
            border-color: var(--eduit-brand);
            color: #fff;
            box-shadow: 0 4px 6px -1px rgba(26, 99, 152, .22);
        }
        .nav-cta--primary:hover {
            background: #2e87c7;
            border-color: #2e87c7;
            color: #fff;
        }
        .nav-cta--primary.is-active { background: #155a8a; border-color: #155a8a; }

        .page-auth .site-footer { display: none; }

        /* --- Componentes compartilhados (Livewire: cursos, player, perfil, certificados) --- */
        .page-title {
            margin: 0 0 8px;
            font-size: clamp(1.55rem, 1.15rem + 1.1vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--eduit-text);
        }
        .section-title {
            margin: 0 0 12px;
            font-size: clamp(1.05rem, .95rem + .45vw, 1.3rem);
            font-weight: 700;
            color: var(--eduit-text);
        }
        .muted { color: var(--eduit-muted); }
        .metric { font-size: clamp(1.45rem, 1.15rem + .9vw, 1.85rem); font-weight: 800; margin: 4px 0; color: var(--eduit-text); }
        .card {
            background: #fff;
            border: 1px solid var(--eduit-line);
            border-radius: 16px;
            padding: 16px;
            box-shadow: var(--eduit-shadow);
            margin-bottom: 12px;
        }
        .card-soft {
            background: #f8faff;
            border-color: #dbe7ff;
        }
        .toolbar {
            display: grid;
            grid-template-columns: 1fr repeat(4, minmax(140px, auto));
            gap: 10px;
            align-items: end;
        }
        .field-label {
            display: block;
            margin-bottom: 4px;
            color: var(--eduit-muted);
            font-size: 13px;
            font-weight: 600;
        }
        .input, .select, textarea {
            border: 1px solid var(--eduit-line);
            border-radius: 10px;
            padding: 10px 12px;
            width: 100%;
            box-sizing: border-box;
            background: #fff;
            color: var(--eduit-text);
            font-size: 14px;
            font-family: inherit;
        }
        .input:focus, .select:focus, textarea:focus {
            outline: 2px solid rgba(26, 99, 152, .22);
            outline-offset: 1px;
            border-color: var(--eduit-brand);
        }
        .cert-preview-img {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 297 / 210;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid var(--eduit-line);
            background: #fff;
            box-shadow: var(--eduit-shadow);
        }
        .cert-preview--full .cert-preview-img { margin-bottom: 4px; }
        .cert-preview--thumb { display: block; margin-bottom: 12px; }
        .cert-preview--thumb .cert-preview-img {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(24, 26, 77, .06);
        }
        .cert-preview-placeholder {
            width: 100%;
            aspect-ratio: 297 / 210;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px dashed var(--eduit-line);
            background: #f1f5f9;
            color: var(--eduit-muted);
            font-size: 13px;
            font-weight: 600;
        }
        .cert-show-layout {
            display: grid;
            gap: 20px;
        }
        @media (min-width: 900px) {
            .cert-show-layout {
                grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
                align-items: start;
            }
        }
        .cert-list-card-thumb {
            display: block;
            margin: -4px -4px 0;
        }
        .cert-list-card-thumb:hover .cert-preview-img {
            border-color: rgba(26, 99, 152, .35);
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid var(--eduit-line);
            color: var(--eduit-muted);
            background: #fff;
        }
        .badge-brand {
            background: #ede9fe;
            border-color: #ddd6fe;
            color: #5b21b6;
        }
        .badge-success {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }
        .btn {
            display: inline-block;
            border: 1px solid var(--eduit-line);
            border-radius: 10px;
            background: #fff;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: var(--eduit-text);
            transition: transform .12s ease, box-shadow .12s ease;
            text-align: center;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 14px rgba(24, 26, 77, .08);
        }
        .btn-primary {
            background: var(--eduit-brand);
            border-color: var(--eduit-brand);
            color: #fff;
        }
        .btn-primary:hover {
            background: #2e87c7;
            border-color: #2e87c7;
            color: #fff;
        }
        .btn-danger {
            background: #fff5f5;
            border-color: #fecaca;
            color: #dc2626;
        }
        .kpi {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .tabs { display: flex; flex-wrap: wrap; gap: 8px; }
        .tab {
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--eduit-line);
            background: #fff;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
            color: var(--eduit-text);
        }
        .tab.active {
            background: var(--eduit-brand);
            border-color: var(--eduit-brand);
            color: #fff;
        }
        .status {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-weight: 600;
        }
        .error {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: #b91c1c;
            font-weight: 600;
        }
        @media (max-width: 1100px) {
            .toolbar { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .toolbar { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
    @livewireStyles
</head>
<body @class([
    'page-auth' => request()->routeIs(['login', 'register', 'password.request', 'password.reset']),
])>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ url('/') }}">
                <img src="/images/Logo_light.png" alt="Eduit">
            </a>
            <nav class="nav" aria-label="Principal">
                <a class="nav-btn {{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}">Início</a>
                <a class="nav-btn {{ request()->routeIs('web.courses.index') || request()->routeIs('web.courses.show') || request()->routeIs('web.courses.player') ? 'is-active' : '' }}" href="{{ route('web.courses.index') }}">Explorar Cursos</a>
                @auth
                    <a class="nav-btn {{ request()->routeIs('web.dashboard') ? 'is-active' : '' }}" href="{{ route('web.dashboard') }}">Dashboard</a>
                    <a class="nav-btn {{ request()->routeIs('web.certificates.*') ? 'is-active' : '' }}" href="{{ route('web.certificates.index') }}">Meus Certificados</a>
                @endauth
            </nav>
            @auth
                <div class="topbar-right">
                    @php
                        $navFullName = trim((string) (auth()->user()->name ?? ''));
                        $navFirstName = $navFullName !== '' ? explode(' ', $navFullName, 2)[0] : '?';
                        $navInitial = strtoupper(mb_substr($navFirstName, 0, 1));
                    @endphp
                    <a
                        class="user-pill {{ request()->routeIs('web.profile') ? 'is-active' : '' }}"
                        href="{{ route('web.profile') }}"
                        aria-label="Ir para o perfil"
                    >
                        <span class="user-avatar" aria-hidden="true">{{ $navInitial }}</span>
                        <span class="user-name">{{ $navFirstName }}</span>
                    </a>
                    <form method="POST" action="{{ route('web.logout') }}" style="margin:0;">
                        @csrf
                        <button class="btn-logout" type="submit" aria-label="Sair da conta">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Sair
                        </button>
                    </form>
                </div>
            @else
                <div class="topbar-right">
                    <a class="nav-cta nav-cta--ghost {{ request()->routeIs('login') ? 'is-active' : '' }}" href="{{ route('login') }}">Entrar</a>
                    <a class="nav-cta nav-cta--primary {{ request()->routeIs('register') ? 'is-active' : '' }}" href="{{ route('register') }}">Criar Conta</a>
                </div>
            @endauth
        </div>
    </header>

    <main @class(['app-shell', 'app-shell--player' => request()->routeIs('web.courses.player')])>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-mobile-logo">
                <img src="/images/Logo_light.png" alt="Eduit" style="height:40px;width:auto;">
            </div>
            <div class="footer-grid">
                <div>
                    <div class="footer-row" style="margin-bottom: 18px;">
                        <span class="footer-label">Links Rápidos</span>
                        <a href="{{ url('/') }}">Início</a>
                        <a href="{{ route('web.courses.index') }}">Explorar cursos</a>
                    </div>
                    <div class="footer-row">
                        <span class="footer-label">Suporte</span>
                        <a href="mailto:contato@eduit.com.br">contato@eduit.com.br</a>
                    </div>
                </div>
                <div class="footer-logo footer-logo-desktop" style="justify-self: end;">
                    <img src="/images/Logo_light.png" alt="Eduit">
                </div>
            </div>
            <div class="footer-copy">© 2025 Eduit. Todos os direitos reservados.</div>
        </div>
    </footer>

    @stack('scripts')
    @livewireScripts
</body>
</html>
