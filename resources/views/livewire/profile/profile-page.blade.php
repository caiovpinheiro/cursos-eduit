@push('styles')
    <style>
        @media (max-width: 640px) {
            .profile-page .btn { width: 100%; }
        }
    </style>
@endpush

<div class="profile-page">
    <section class="card card-soft" style="margin-bottom: 14px;">
        <h1 class="page-title" style="margin-bottom: 4px;">Meu perfil</h1>
        <p class="muted" style="margin: 0;">Gerencie seus dados pessoais e seguranca da conta.</p>
    </section>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <section class="card">
        <h2 class="section-title">Dados pessoais</h2>
        <form wire:submit="saveProfile" style="display: grid; gap: 12px;">
            <div>
                <label class="field-label" for="profile_name">Nome</label>
                <input id="profile_name" class="input" type="text" wire:model="name">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="profile_email">Email</label>
                <input id="profile_email" class="input" type="email" wire:model="email">
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="profile_phone">Telefone</label>
                <input id="profile_phone" class="input" type="text" wire:model="phone">
                @error('phone') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="profile_education">Escolaridade</label>
                <select id="profile_education" class="select" wire:model="educationLevel">
                    <option value="">Selecione</option>
                    @foreach($educationLevelOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('educationLevel') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <button class="btn btn-primary" type="submit">Salvar perfil</button>
            </div>
        </form>
    </section>

    <section class="card" style="margin-top: 14px;">
        <h2 class="section-title">Alterar senha</h2>
        <form wire:submit="updatePassword" style="display: grid; gap: 12px;">
            <div>
                <label class="field-label" for="current_password">Senha atual</label>
                <input id="current_password" class="input" type="password" wire:model="currentPassword">
                @error('currentPassword') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="new_password">Nova senha</label>
                <input id="new_password" class="input" type="password" wire:model="newPassword">
                @error('newPassword') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="new_password_confirmation">Confirmar nova senha</label>
                <input id="new_password_confirmation" class="input" type="password" wire:model="newPasswordConfirmation">
                @error('newPasswordConfirmation') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <button class="btn" type="submit">Atualizar senha</button>
            </div>
        </form>
    </section>
</div>

