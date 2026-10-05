@extends('layouts.app')

@section('pageTitle', 'Criar conta')

@push('styles')
    @include('partials.auth-shared-styles')
    <style>
        .auth-page--register {
            max-width: 520px;
        }
        .auth-btn-submit--pastel {
            background: linear-gradient(180deg, #b6d8f0 0%, #96c6e8 100%);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .45);
            box-shadow: 0 10px 28px -10px rgba(26, 99, 152, .4);
            text-shadow: 0 1px 0 rgba(0, 0, 0, .06);
        }
        .auth-btn-submit--pastel:hover {
            background: linear-gradient(180deg, #a3cce9 0%, #7eb8e0 100%);
            filter: brightness(1.02);
        }
        .auth-google-note {
            text-align: center;
            margin: 12px 0 0;
            font-size: 13px;
            color: var(--eduit-muted);
            line-height: 1.45;
        }
    </style>
@endpush

@section('content')
<div class="auth-page auth-page--register">
    <header class="auth-head">
        <img class="auth-logo" src="{{ asset('images/Logo_light.png') }}" alt="{{ config('app.name', 'Eduit') }}">
        <h1>Crie sua conta</h1>
        <p class="auth-sub">Comece sua jornada de crescimento profissional</p>
    </header>

    <div class="auth-card">
        @if (session('status'))
            <div class="auth-status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="auth-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('web.register.store') }}" id="register-form" autocomplete="on">
            @csrf

            <div class="auth-field">
                <label for="name">Nome Completo<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        placeholder="Digite seu nome completo"
                        value="{{ old('name') }}"
                        required
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="cpf">CPF<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="cpf"
                        name="cpf"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="000.000.000-00"
                        value="{{ old('cpf') }}"
                        maxlength="14"
                        required
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="email">Email<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        placeholder="seu.email@exemplo.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="phone">Telefone<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="phone"
                        name="phone"
                        type="tel"
                        inputmode="tel"
                        autocomplete="tel"
                        placeholder="(11) 99999-9999"
                        value="{{ old('phone') }}"
                        maxlength="15"
                        required
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="education_level">Escolaridade<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap auth-input-wrap--select">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </span>
                    <select class="auth-input auth-select" id="education_level" name="education_level" required>
                        <option value="" disabled @selected(old('education_level', '') === '')>Selecione sua escolaridade</option>
                        @foreach ($educationLevelOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('education_level') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="auth-field">
                <label for="password">Senha</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Sua senha"
                        required
                    >
                    <button type="button" class="auth-toggle-pw" data-toggle-pw="password" aria-label="Mostrar ou ocultar senha">
                        <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="auth-field">
                <label for="password_confirmation">Confirmar Senha<span class="auth-req" aria-hidden="true">*</span></label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Confirme sua senha"
                        required
                    >
                    <button type="button" class="auth-toggle-pw" data-toggle-pw="password_confirmation" aria-label="Mostrar ou ocultar confirmação de senha">
                        <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-btn-submit">
                Criar Conta
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="auth-divider" role="separator">ou</div>

        <a href="{{ url('/auth/redirect') }}" class="auth-btn-google">
            <svg class="auth-google-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuar com Google
        </a>

        <p class="auth-google-note">Será necessário completar o cadastro em seguida.</p>

        <p class="auth-footer-card">
            Já tem uma conta? <a href="{{ route('login') }}">Fazer login</a>
        </p>
    </div>

    <p class="auth-legal">
        Ao continuar, você concorda com nossos
        <a href="#">Termos de Uso</a>
        e
        <a href="#">Política de Privacidade</a>.
    </p>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bindPwToggle(btn) {
        var id = btn.getAttribute('data-toggle-pw');
        if (!id) return;
        var input = document.getElementById(id);
        if (!input) return;
        var eye = btn.querySelector('.icon-eye');
        var eyeOff = btn.querySelector('.icon-eye-off');
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            if (eye && eyeOff) {
                eye.style.display = show ? 'none' : 'block';
                eyeOff.style.display = show ? 'block' : 'none';
            }
            btn.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
        });
    }
    document.querySelectorAll('[data-toggle-pw]').forEach(bindPwToggle);

    var cpf = document.getElementById('cpf');
    if (cpf) {
        cpf.addEventListener('input', function () {
            var d = cpf.value.replace(/\D/g, '').slice(0, 11);
            var out = '';
            if (d.length > 0) out += d.slice(0, 3);
            if (d.length > 3) out += '.' + d.slice(3, 6);
            if (d.length > 6) out += '.' + d.slice(6, 9);
            if (d.length > 9) out += '-' + d.slice(9, 11);
            cpf.value = out;
        });
    }

    var phone = document.getElementById('phone');
    if (phone) {
        phone.addEventListener('input', function () {
            var d = phone.value.replace(/\D/g, '').slice(0, 11);
            var out = '';
            if (d.length > 0) out += '(' + d.slice(0, 2);
            if (d.length >= 2) out += ') ';
            if (d.length > 2) out += d.slice(2, 7);
            if (d.length > 7) out += '-' + d.slice(7, 11);
            phone.value = out;
        });
    }
})();
</script>
@endpush
