<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class SettingsPage extends Component
{
    public bool $emailNotifications = true;
    public bool $courseReminders = true;
    public bool $marketingEmails = false;

    public function savePreferences(): void
    {
        session()->flash('status', 'Preferencias salvas nesta sessao.');
    }

    #[Layout('layouts.app')]
    #[Title('Configurações')]
    public function render()
    {
        return view('livewire.profile.settings-page', [
            'user' => Auth::user(),
        ]);
    }
}

