@extends('layouts.app')

@section('pageTitle', 'Recuperar senha')

@push('styles')
    @include('partials.auth-shared-styles')
@endpush

@section('content')
<div class="auth-page">
    <header class="auth-head">
        <img class="auth-logo" src="{{ asset('images/Logo_light.png') }}" alt="{{ config('app.name', 'Eduit') }}">
        <h1>Esqueceu a senha?</h1>
        <p class="auth-sub">Informe seu email para receber o link de redefinição</p>
    </header>

    <div class="auth-card">
        @if (session('status'))
            <div class="auth-status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="auth-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
            @csrf

            <div class="auth-field">
                <label for="email">Email</label>
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
                        autofocus
                    >
                </div>
            </div>

            <button type="submit" class="auth-btn-submit">
                Enviar link de recuperação
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
