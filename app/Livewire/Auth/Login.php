<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Login form component.
 *
 * Accepts a gamertag OR email address in the login field.
 * Rate limited to 5 attempts per minute per IP.
 * Silently re-hashes the stored password if needsRehash() returns true.
 */
class Login extends Component
{
    /** Gamertag or email address */
    public string $login    = '';
    public string $password = '';
    public bool   $remember = false;

    /**
     * Attempt to authenticate the user.
     */
    public function authenticate(): void
    {
        $this->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login.' . Str::lower($this->login) . '.' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('login', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        Log::info('Login attempt', ['input' => $this->login, 'ip' => request()->ip()]);

        $user = $this->resolveUser($this->login);

        if ($user === null) {
            Log::info('Login failed: user not found', ['input' => $this->login]);
            RateLimiter::hit($throttleKey, 60);
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        Log::info('Login: user found', ['gamertag' => $user->gamertag, 'id' => $user->id]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        if (! $passwordService->verify($this->password, $user->getRawOriginal('password'))) {
            Log::info('Login failed: password mismatch', ['gamertag' => $user->gamertag]);
            RateLimiter::hit($throttleKey, 60);
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        Log::info('Login: password verified', ['gamertag' => $user->gamertag]);

        // Silently re-hash if the work factor has changed
        if ($passwordService->needsRehash($user->getRawOriginal('password'))) {
            $user->password = $passwordService->hash($this->password);
            $user->save();
            Log::info('Login: password rehashed', ['gamertag' => $user->gamertag]);
        }

        RateLimiter::clear($throttleKey);

        Log::info('Login: calling Auth::login()', ['gamertag' => $user->gamertag]);

        Auth::login($user, $this->remember);

        // Regenerate the session once, after login, using the request's session
        // to ensure the database session driver writes the correct user_id.
        request()->session()->regenerate();

        Log::info('Login: session regenerated', [
            'auth_id'    => Auth::id(),
            'session_id' => session()->getId(),
        ]);

        if (Auth::id() === null) {
            Log::error('Login: Auth::id() is null after Auth::login() — session not persisted', [
                'gamertag' => $user->gamertag,
            ]);
            $this->addError('login', 'Something went wrong. Please try again.');
            return;
        }

        // Redirect based on state
        if (! $user->hasVerifiedEmail()) {
            Log::info('Login: redirecting to verification notice', ['gamertag' => $user->gamertag]);
            $this->redirect(route('verification.notice'));
            return;
        }

        $destination = $user->onboarding_completed
            ? route('feed')
            : route('onboarding');

        Log::info('Login: redirecting', ['gamertag' => $user->gamertag, 'to' => $destination]);

        $this->redirect($destination);
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

    /**
     * Render the login view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Sign in — CommonGround']);
    }
}
