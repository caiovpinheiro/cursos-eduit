<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Logging\SecurityAuditLogger;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function createForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function storeForgot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);
        $emailHash = hash('sha256', strtolower($validated['email']));

        $user = User::where('email', $validated['email'])->first();

        if ($user && $user->isGoogleUser() && empty($user->password)) {
            $this->audit('warning', 'auth.password_recovery_blocked_google_only', $request, [
                'email_hash' => $emailHash,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Esta conta usa login com Google. Entre com Google para continuar.'],
            ]);
        }

        $status = Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            $this->audit('warning', 'auth.password_recovery_send_failed', $request, [
                'email_hash' => $emailHash,
                'status' => $status,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Nao foi possivel enviar o link de recuperacao. Verifique os dados e tente novamente.'],
            ]);
        }

        $this->audit('info', 'auth.password_recovery_send_success', $request, [
            'email_hash' => $emailHash,
        ]);

        return back()->with('status', 'Se o email estiver cadastrado, enviaremos um link de recuperacao em instantes.');
    }

    public function createReset(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function storeReset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $emailHash = hash('sha256', strtolower((string) $request->email));

        $user = User::where('email', $request->email)->first();

        if ($user && $user->isGoogleUser() && empty($user->password)) {
            $this->audit('warning', 'auth.password_reset_blocked_google_only', $request, [
                'email_hash' => $emailHash,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Esta conta usa login com Google. Entre com Google para continuar.'],
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->audit('warning', 'auth.password_reset_failed', $request, [
                'email_hash' => $emailHash,
                'status' => $status,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Token de recuperacao invalido ou expirado. Solicite um novo link para continuar.'],
            ]);
        }

        $this->audit('info', 'auth.password_reset_success', $request, [
            'email_hash' => $emailHash,
        ]);

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso. Faca login para continuar.');
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
