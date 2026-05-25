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

class Login extends Component
{
    public string $login    = '';
    public string $password = '';
    public bool   $remember = false;

    public function submit(): void
    {
        $this->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login.' . Str::lower($this->login) . '.' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::info('Login: rate limited', ['ip' => request()->ip()]);
            $this->addError('login', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        Log::info('Login: attempt started', ['input' => $this->login, 'ip' => request()->ip()]);

        $user = $this->resolveUser($this->login);

        if ($user === null) {
            Log::info('Login: user not found', ['input' => $this->login]);
            RateLimiter::hit($throttleKey, 60);
            $this->password = '';
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        if (! $passwordService->verify($this->password, $user->getRawOriginal('password'))) {
            Log::info('Login: password incorrect', ['gamertag' => $user->gamertag]);
            RateLimiter::hit($throttleKey, 60);
            $this->password = '';
            $this->addError('login', 'These credentials do not match our records.');
            return;
        }

        if ($passwordService->needsRehash($user->getRawOriginal('password'))) {
            $user->password = $passwordService->hash($this->password);
            $user->save();
            Log::info('Login: password rehashed', ['gamertag' => $user->gamertag]);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $this->remember);
        session()->regenerate();

        Log::info('Login: success', [
            'gamertag'          => $user->gamertag,
            'session_id_prefix' => substr(session()->getId(), 0, 10),
            'csrf_token_prefix' => substr(csrf_token(), 0, 10),
        ]);

        if (! $user->hasVerifiedEmail()) {
            // If the user clicked the verification link while logged out, Laravel stored
            // the signed URL in session. Follow it instead of dropping them on the notice page.
            $intended = session()->get('url.intended', '');
            if (str_contains($intended, '/email/verify/')) {
                session()->forget('url.intended');
                $this->redirect($intended, navigate: false);
                return;
            }
            $this->redirect(route('verification.notice'), navigate: false);
            return;
        }

        $destination = $user->onboarding_completed
            ? route('feed')
            : route('onboarding');

        $this->redirect($destination, navigate: false);
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

    public function render(): \Illuminate\View\View
    {
        Log::info('Login render', [
            'session_id_prefix' => substr(session()->getId(), 0, 10),
            'csrf_token_prefix' => substr(csrf_token(), 0, 10),
        ]);

        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Sign in — CommonGrove']);
    }
}
