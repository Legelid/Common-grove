<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use App\Rules\ValidGamertag;
use App\Services\GamertagSuggestionService;
use App\Services\PasswordService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Registration form component.
 *
 * Handles gamertag availability checking (debounced, real-time),
 * full server-side validation, and account creation.
 */
class Register extends Component
{
    public string $gamertag            = '';
    public string $email               = '';
    public string $password            = '';
    public string $password_confirmation = '';

    /** @var string|null Availability status: 'available', 'taken', or null (unchecked) */
    public ?string $gamertagStatus = null;

    /** @var list<string> Suggestions to show when gamertag is taken */
    public array $suggestions = [];

    #[Locked]
    public bool $registering = false;

    /**
     * Lifecycle: runs on every gamertag property update.
     * Resets availability state and triggers the availability check.
     */
    public function updatedGamertag(): void
    {
        $this->gamertagStatus = null;
        $this->suggestions    = [];
        $this->checkGamertag();
    }

    /**
     * Check gamertag availability.
     * Called automatically by Livewire when the gamertag property updates.
     */
    public function checkGamertag(): void
    {
        $key = 'gamertag-check.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return;
        }
        RateLimiter::hit($key, 60);

        // Validate format first — no point hitting the DB with invalid input
        $formatResult = validator(
            ['gamertag' => $this->gamertag],
            ['gamertag' => ['required', 'string', new ValidGamertag()]],
        );

        if ($formatResult->fails()) {
            $this->gamertagStatus = null;
            $this->suggestions    = [];
            return;
        }

        /** @var GamertagSuggestionService $suggestionService */
        $suggestionService = app(GamertagSuggestionService::class);

        if ($suggestionService->isTaken($this->gamertag)) {
            $this->gamertagStatus = 'taken';
            $this->suggestions    = $suggestionService->suggest($this->gamertag);
        } else {
            $this->gamertagStatus = 'available';
            $this->suggestions    = [];
        }
    }

    /**
     * Fill the gamertag field from a suggestion click.
     *
     * @param  string  $suggested  The suggestion to fill in.
     */
    public function useSuggestion(string $suggested): void
    {
        $this->gamertag       = $suggested;
        $this->gamertagStatus = null;
        $this->suggestions    = [];

        // Re-check availability for the chosen suggestion
        $this->checkGamertag();
    }

    /**
     * Register a new user.
     */
    public function register(): void
    {
        $key = 'register.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('email', 'Too many registration attempts. Please try again later.');
            return;
        }
        RateLimiter::hit($key, 3600);

        $validated = $this->validate([
            'gamertag' => [
                'required',
                'string',
                'max:20',
                new ValidGamertag(),
                'unique:users,gamertag',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)->uncompromised(),
            ],
        ]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $user = User::create([
            'gamertag' => $validated['gamertag'],
            'email'    => $validated['email'],
            'password' => $passwordService->hash($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('verification.notice'), navigate: true);
    }

    /**
     * Render the registration view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register')
            ->layout('layouts.app', ['title' => 'Create your account — CommonGround']);
    }
}
