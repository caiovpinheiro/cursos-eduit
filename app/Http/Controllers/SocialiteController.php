<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    // public function redirectGoogle()
    // {
    //     return Socialite::driver('google')->redirect();
    // }

    // public function callbackGoogle()
    // {
    //     $googleUser = Socialite::driver('google')->user();
    //     dd($googleUser);
    //     $user = User::where('email', $googleUser->getEmail())->firstOrFail();
    //     Auth::login($user);
    //     $user = Auth::user();
    //     $token = $user->createToken('auth-token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login successful',
    //         'user' => new UserResource($user),
    //         'token' => $token,
    //     ]);
    // }

    public function redirectGoogle()
    {
        $frontendUrl = request('redirect_uri');

        // API/SPA flow keeps token redirect behavior.
        if ($frontendUrl) {
            session()->put('oauth_redirect_uri', $frontendUrl);
            return Socialite::driver('google')->redirect();
        }

        // Web flow uses session-based login and intended redirect.
        session()->put('oauth_web_login', true);
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(Request $request)
    {
        $isWebLogin = (bool) session()->pull('oauth_web_login', false);
        $frontendUrl = session()->pull('oauth_redirect_uri') ?? env('DEFAULT_URI_REDIRECT_POST_GOOGLE');

        try{
            if ($request->has('error')) {
                return $isWebLogin
                    ? redirect()->route('login')->withErrors(['email' => 'Falha ao autenticar com Google. Tente novamente.'])
                    : redirect($frontendUrl . '?error=google_auth_failed');
            }
            if (!$request->has('code')) {
                return $isWebLogin
                    ? redirect()->route('login')->withErrors(['email' => 'Codigo de autenticacao ausente. Tente novamente.'])
                    : redirect($frontendUrl . '?error=no_auth_code');
            }
            
            if ($isWebLogin) {
                $googleUser = Socialite::driver('google')->user();
            } else {
                /** @var mixed $statelessDriver */
                $statelessDriver = Socialite::driver('google');
                if (method_exists($statelessDriver, 'stateless')) {
                    $statelessDriver = $statelessDriver->stateless();
                }
                $googleUser = $statelessDriver->user();
            }

            if (!$googleUser->getEmail()) {
                return $isWebLogin
                    ? redirect()->route('login')->withErrors(['email' => 'Nao foi possivel obter seu email do Google.'])
                    : redirect($frontendUrl . '?error=no_email_provided');
            }

            $user = User::where('google_id', $googleUser->getId())->first();
        
            if ($user && $isWebLogin) {
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended(route('web.dashboard'))
                    ->with('status', 'Login realizado com sucesso.');
            }

            if ($user) {
                $token = $user->createToken('auth-token')->plainTextToken;
                return redirect($frontendUrl . '?token=' . $token);
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                return $isWebLogin
                    ? redirect()->route('login')->withErrors(['email' => 'Conta existente sem vinculo Google. Use email e senha para entrar.'])
                    : redirect($frontendUrl . '?error=login_with_password');
            }

            return $isWebLogin
                ? $this->redirectToWebGoogleSignup($request, $googleUser->getName(), $googleUser->getEmail(), $googleUser->getId())
                : redirect(env('SIGN_UP_URI') . '?email=' . $googleUser->getEmail() . '&name=' . $googleUser->getName() . '&google_id=' . $googleUser->getId());
        }
        catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return $isWebLogin
                ? redirect()->route('login')->withErrors(['email' => 'Sua sessao de autenticacao com Google expirou. Tente novamente.'])
                : redirect($frontendUrl . '?error=invalid_state');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $isWebLogin
                ? redirect()->route('login')->withErrors(['email' => 'Erro ao comunicar com Google. Tente novamente em instantes.'])
                : redirect($frontendUrl . '?error=google_api_error');
        } catch (\Exception $e) {
            return $isWebLogin
                ? redirect()->route('login')->withErrors(['email' => 'Erro inesperado na autenticacao com Google.'])
                : redirect($frontendUrl . '?error=unexpected_error');
        }

    }

    private function redirectToWebGoogleSignup(Request $request, ?string $name, string $email, string $googleId)
    {
        $request->session()->put('pending_google_signup', [
            'name' => $name ?? '',
            'email' => $email,
            'google_id' => $googleId,
        ]);

        return redirect()->route('web.register.google.complete');
    }
}