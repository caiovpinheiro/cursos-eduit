@extends('layouts.app')

@section('pageTitle', 'Completar cadastro')

@push('styles')
    @include('partials.auth-shared-styles')
    <style>
        .auth-page--register { max-width: 520px; }
    </style>
@endpush

@section('content')
<div class="auth-page auth-page--register">
    <header class="auth-head">
        <img class="auth-logo" src="{{ asset('images/Logo_light.png') }}" alt="{{ config('app.name', 'Eduit') }}">
        <h1>Completar cadastro</h1>
        <p class="auth-sub">Seu login Google foi reconhecido. Complete os dados para finalizar.</p>
    </header>

    <div class="auth-card">
        @if (session('status'))
            <div class="auth-status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="auth-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="auth-field">
            <label for="google_name">Nome</label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <input class="auth-input" id="google_name" type="text" value="{{ $prefillName }}" disabled>
            </div>
        </div>

        <div class="auth-field">
            <label for="google_email">Email</label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input class="auth-input" id="google_email" type="email" value="{{ $prefillEmail }}" disabled>
            </div>
        </div>

        <form method="POST" action="{{ route('web.register.google.complete.store') }}" id="google-register-complete-form">
            @csrf

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
                <label for="phone">Telefone</label>
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
                    >
                </div>
            </div>

            <div class="auth-field">
                <label for="education_level">Escolaridade</label>
                <div class="auth-input-wrap auth-input-wrap--select">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </span>
                    <select class="auth-input auth-select" id="education_level" name="education_level">
                        <option value="" @selected(old('education_level', '') === '')>Selecione sua escolaridade</option>
                        @foreach ($educationLevelOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('education_level') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="auth-btn-submit">
                Finalizar cadastro
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

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
