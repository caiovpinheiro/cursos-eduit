<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Logging\SecurityAuditLogger;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionAuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();
        $emailHash = hash('sha256', strtolower($credentials['email']));

        if ($user && $user->isGoogleUser() && ! $user->password) {
            $this->audit('warning', 'auth.login_blocked_google_only', $request, [
                'email_hash' => $emailHash,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Esta conta usa login com Google. Entre com Google para continuar.'],
            ]);
        }

        if (! Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            $this->audit('warning', 'auth.login_failed', $request, [
                'email_hash' => $emailHash,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Email ou senha invalidos.'],
            ]);
        }

        $request->session()->regenerate();

        $this->audit('info', 'auth.login_success', $request, [
            'user_id' => Auth::id(),
        ]);

        return redirect()->intended(route('web.dashboard'))
            ->with('status', 'Login realizado com sucesso.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $this->audit('info', 'auth.logout_success', $request, [
            'user_id' => $userId,
        ]);

        return redirect()->route('login')
            ->with('status', 'Voce saiu da sua conta com sucesso.');
    }

    /**
     * @param 'info'|'warning' $level
     * @param array<string, mixed> $context
     */
    private function audit(string $level, string $event, Request $request, array $context = []): void
    {
        SecurityAuditLogger::log($level, $event, $request, $context);
    }
}
