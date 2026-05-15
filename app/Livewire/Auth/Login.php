<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Login form component.
 *
 * Accepts a gamertag OR email address in the login field.
 * Rate limited to 5 attempts per minute per IP (enforced in-component
 * in addition to the route-level throttle middleware).
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

        // Rate limit: 5 attempts per 60 seconds
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('login', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        // Resolve user by gamertag (case-insensitive) or email
        $user = $this->resolveUser($this->login);

        if ($user === null) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        if (! $passwordService->verify($this->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        // Silently re-hash if the work factor has changed
        if ($passwordService->needsRehash($user->password)) {
            $user->password = $passwordService->hash($this->password);
            $user->save();
        }

        RateLimiter::clear($throttleKey);

        // Auth::login() handles session regeneration automatically
        Auth::login($user, $this->remember);

        session()->regenerate();

        $this->redirect(route('feed'), navigate: true);
    }

    /**
     * Resolve a User by gamertag (case-insensitive) or email address.
     *
     * @param  string  $login
     * @return User|null
     */
    private function resolveUser(string $login): ?User
    {
        // Determine if the input looks like an email
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
