<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExternalCustomer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'educationLevelOptions' => User::getEducationLevelOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->baseRules() + [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $cpfDigits = preg_replace('/\D/', '', $validated['cpf']);
        $phoneDigits = preg_replace('/\D/', '', $validated['phone']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf' => $cpfDigits,
            'type' => 'student',
            'phone' => $phoneDigits,
            'education_level' => $validated['education_level'],
            'is_customer' => ExternalCustomer::exists($cpfDigits),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('web.dashboard')
            ->with('status', 'Cadastro realizado com sucesso.');
    }

    public function createGoogleComplete(Request $request): View|RedirectResponse
    {
        $data = $request->session()->get('pending_google_signup');

        if (! $data || ! isset($data['email'], $data['name'], $data['google_id'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sua sessao de cadastro com Google expirou. Tente novamente.',
            ]);
        }

        return view('auth.register-google-complete', [
            'educationLevelOptions' => User::getEducationLevelOptions(),
            'prefillName' => $data['name'],
            'prefillEmail' => $data['email'],
        ]);
    }

    public function storeGoogleComplete(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_google_signup');

        if (! $pending || ! isset($pending['email'], $pending['name'], $pending['google_id'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sua sessao de cadastro com Google expirou. Tente novamente.',
            ]);
        }

        $validated = $request->validate([
            'cpf' => ['required', 'string', 'unique:users,cpf'],
            'phone' => ['nullable', 'string', 'max:20'],
            'education_level' => [
                'nullable',
                Rule::in(array_keys(User::getEducationLevelOptions())),
            ],
        ]);

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'google_id' => $pending['google_id'],
            'cpf' => $validated['cpf'],
            'type' => 'student',
            'phone' => $validated['phone'] ?? null,
            'education_level' => $validated['education_level'] ?? null,
            'is_customer' => ExternalCustomer::exists($validated['cpf']),
        ]);

        event(new Registered($user));

        $request->session()->forget('pending_google_signup');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('web.dashboard')
            ->with('status', 'Cadastro realizado com sucesso.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function baseRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (strlen($digits) !== 11) {
                        $fail('O CPF deve ter 11 dígitos.');
                    }
                    if (User::where('cpf', $digits)->exists()) {
                        $fail('Este CPF já está cadastrado.');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (strlen($digits) < 10 || strlen($digits) > 11) {
                        $fail('Informe um telefone válido com DDD.');
                    }
                },
            ],
            'education_level' => [
                'required',
                Rule::in(array_keys(User::getEducationLevelOptions())),
            ],
        ];
    }
}
