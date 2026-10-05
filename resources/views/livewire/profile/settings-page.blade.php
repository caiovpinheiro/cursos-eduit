<div>
    <section class="card card-soft" style="margin-bottom: 14px;">
        <h1 class="page-title" style="margin-bottom: 4px;">Configuracoes</h1>
        <p class="muted" style="margin: 0;">Preferencias visuais e notificacoes da conta.</p>
    </section>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <section class="card">
        <h2 class="section-title">Notificacoes</h2>
        <form wire:submit="savePreferences" style="display: grid; gap: 10px;">
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" wire:model="emailNotifications">
                <span>Receber novidades por email</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" wire:model="courseReminders">
                <span>Lembretes de aulas e progresso</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" wire:model="marketingEmails">
                <span>Receber campanhas e promocoes</span>
            </label>
            <div style="margin-top: 8px;">
                <button class="btn btn-primary" type="submit">Salvar preferencias</button>
            </div>
        </form>
    </section>

    <section class="card" style="margin-top: 14px;">
        <h2 class="section-title">Resumo da conta</h2>
        <p style="margin: 0 0 6px;"><strong>Nome:</strong> {{ $user->name }}</p>
        <p style="margin: 0 0 6px;"><strong>Email:</strong> {{ $user->email }}</p>
        <p style="margin: 0;" class="muted">A autenticacao desta aplicacao web permanece via sessao Laravel.</p>
    </section>
</div>

