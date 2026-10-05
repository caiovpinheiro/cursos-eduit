<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProfilePage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $educationLevel = '';

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->phone = (string) ($user->phone ?? '');
        $this->educationLevel = (string) ($user->education_level ?? '');
    }

    public function saveProfile(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'educationLevel' => ['nullable', Rule::in(array_keys(User::getEducationLevelOptions()))],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'education_level' => $validated['educationLevel'] ?: null,
        ]);

        session()->flash('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8', 'same:newPasswordConfirmation'],
            'newPasswordConfirmation' => ['required', 'string'],
        ]);

        if (! Hash::check($validated['currentPassword'], (string) $user->password)) {
            $this->addError('currentPassword', 'Senha atual invalida.');
            return;
        }

        $user->update([
            'password' => Hash::make($validated['newPassword']),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        session()->flash('status', 'Senha atualizada com sucesso.');
    }

    #[Layout('layouts.app')]
    #[Title('Perfil')]
    public function render()
    {
        return view('livewire.profile.profile-page', [
            'educationLevelOptions' => User::getEducationLevelOptions(),
        ]);
    }
}

