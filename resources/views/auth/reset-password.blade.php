@extends('layouts.app')

@section('pageTitle', 'Redefinir senha')

@push('styles')
    @include('partials.auth-shared-styles')
@endpush

@section('content')
<div class="auth-page">
    <header class="auth-head">
        <img class="auth-logo" src="{{ asset('images/Logo_light.png') }}" alt="{{ config('app.name', 'Eduit') }}">
        <h1>Redefinir senha</h1>
        <p class="auth-sub">Digite sua nova senha para concluir</p>
    </header>

    <div class="auth-card">
        @if (session('status'))
            <div class="auth-status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="auth-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="reset-password-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-field">
                <label for="email">Email</label>
                <div class="auth-input-wrap auth-input-wrap--readonly">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input
                        class="auth-input is-readonly"
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        value="{{ old('email', $email) }}"
                        required
                        readonly
                        aria-readonly="true"
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="password">Nova senha</label>
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
                        placeholder="Sua nova senha"
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
                <label for="password_confirmation">Confirmar nova senha</label>
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
                        placeholder="Confirme sua nova senha"
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
                Redefinir senha
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <p class="auth-footer-card">
            <a href="{{ route('login') }}">Voltar para o login</a>
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
})();
</script>
@endpush
