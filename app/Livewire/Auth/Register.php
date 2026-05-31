<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use App\Rules\ValidGamertag;
use App\Services\FirstRootsService;
use App\Services\GamertagSuggestionService;
use App\Services\PasswordService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    // Date of birth
    public string $birthMonth = '';
    public string $birthDay   = '';
    public string $birthYear  = '';

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
            'birthMonth' => ['required', 'integer', 'min:1', 'max:12'],
            'birthDay'   => ['required', 'integer', 'min:1', 'max:31'],
            'birthYear'  => [
                'required',
                'integer',
                'min:' . (now()->year - 120),
                'max:' . now()->year,
                function (string $attr, mixed $value, \Closure $fail): void {
                    $month = (int) $this->birthMonth;
                    $day   = (int) $this->birthDay;
                    $year  = (int) $value;

                    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
                        return;
                    }

                    $dob = Carbon::createSafe($year, $month, $day);

                    if ($dob === false) {
                        $fail('Please enter a valid date of birth.');
                        return;
                    }

                    if ($dob->diffInYears(now()) < 18) {
                        $fail('CommonGrove is currently only available to adults.');
                    }
                },
            ],
        ]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $dob = Carbon::createSafe(
            (int) $this->birthYear,
            (int) $this->birthMonth,
            (int) $this->birthDay,
        );

        $user = User::create([
            'gamertag'      => $validated['gamertag'],
            'email'         => $validated['email'],
            'password'      => $passwordService->hash($validated['password']),
            'date_of_birth' => $dob !== false ? $dob->toDateString() : null,
        ]);

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::warning('Registration verification email failed — account created successfully', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        Auth::login($user);
        session()->regenerate();

        $betaToken = session()->pull('beta_invite_token');
        if ($betaToken) {
            app(FirstRootsService::class)->claimInvite((string) $betaToken, $user);
        }

        $this->redirect(route('verification.notice'));
    }

    /**
     * Render the registration view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register')
            ->layout('layouts.app', ['title' => 'Create your account — CommonGrove']);
    }
}
