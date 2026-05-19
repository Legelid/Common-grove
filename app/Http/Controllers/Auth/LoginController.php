<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Handles credential validation and session-based authentication.
 *
 * Using a standard HTTP controller (not a Livewire action) so that
 * Laravel's StartSession middleware can attach the session cookie to
 * the 302 redirect response — Livewire AJAX responses do not reliably
 * propagate Set-Cookie headers across all browser/server configurations.
 */
class LoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login.' . Str::lower($request->input('login')) . '.' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::info('Login: rate limited', ['ip' => $request->ip()]);
            return back()
                ->withErrors(['login' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->withInput($request->only('login', 'remember'));
        }

        Log::info('Login: attempt started', ['input' => $request->input('login'), 'ip' => $request->ip()]);

        $user = $this->resolveUser($request->input('login'));

        if ($user === null) {
            Log::info('Login: user not found', ['input' => $request->input('login')]);
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withErrors(['login' => 'These credentials do not match our records.'])
                ->withInput($request->only('login', 'remember'));
        }

        Log::info('Login: user found', ['gamertag' => $user->gamertag, 'id' => $user->id]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $verified = $passwordService->verify($request->input('password'), $user->getRawOriginal('password'));

        Log::info('Login: password verified', ['result' => $verified ? 'true' : 'false', 'gamertag' => $user->gamertag]);

        if (! $verified) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withErrors(['login' => 'These credentials do not match our records.'])
                ->withInput($request->only('login', 'remember'));
        }

        if ($passwordService->needsRehash($user->getRawOriginal('password'))) {
            $user->password = $passwordService->hash($request->input('password'));
            $user->save();
            Log::info('Login: password rehashed', ['gamertag' => $user->gamertag]);
        }

        RateLimiter::clear($throttleKey);

        $remember = $request->boolean('remember');

        Log::info('Login: calling Auth::login()', ['gamertag' => $user->gamertag, 'remember' => $remember]);

        Auth::login($user, $remember);

        Log::info('Login: after Auth::login()', [
            'auth_id'  => Auth::id(),
            'gamertag' => $user->gamertag,
        ]);

        $request->session()->regenerate();

        Log::info('Login: session regenerated', [
            'auth_id'          => Auth::id(),
            'session_id_prefix'=> substr($request->session()->getId(), 0, 10),
            'csrf_token_prefix'=> substr($request->session()->token(), 0, 10),
        ]);

        if (! $user->hasVerifiedEmail()) {
            Log::info('Login: unverified — redirecting to verification notice', ['gamertag' => $user->gamertag]);
            return redirect()->route('verification.notice');
        }

        $destination = $user->onboarding_completed
            ? route('feed')
            : route('onboarding');

        Log::info('Login: success — redirecting', ['gamertag' => $user->gamertag, 'to' => $destination]);

        return redirect($destination);
    }

    /**
     * Resolve a User by gamertag (case-insensitive) or email address.
     */
    private function resolveUser(string $login): ?User
    {
        if (str_contains($login, '@')) {
            return User::where('email', $login)->first();
        }

        return User::whereRaw('LOWER(gamertag) = ?', [strtolower($login)])->first();
    }
}
