<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Rules\ValidGamertag;
use App\Services\GamertagSuggestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Forces accounts created via "Continue with Google" to pick a real gamertag
 * before continuing — they start with an auto-generated placeholder
 * (gamertag_setup_required = true). Mirrors the gamertag section of
 * Register.php (same validation rule, same live-availability check, same
 * suggestion service) minus everything email/password related.
 */
class CollectGamertag extends Component
{
    public string $gamertag = '';

    /** @var string|null 'available', 'taken', or null (unchecked) */
    public ?string $gamertagStatus = null;

    /** @var list<string> */
    public array $suggestions = [];

    public function mount(): void
    {
        // Already completed — skip to whatever's next (DOB, onboarding, feed).
        if (! Auth::user()->gamertag_setup_required) {
            $this->redirect(route('feed'));
        }
    }

    public function updatedGamertag(): void
    {
        $this->gamertagStatus = null;
        $this->suggestions    = [];
        $this->checkGamertag();
    }

    public function checkGamertag(): void
    {
        $key = 'gamertag-check.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return;
        }
        RateLimiter::hit($key, 60);

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

    public function useSuggestion(string $suggested): void
    {
        $this->gamertag       = $suggested;
        $this->gamertagStatus = null;
        $this->suggestions    = [];
        $this->checkGamertag();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'gamertag' => [
                'required',
                'string',
                'max:20',
                new ValidGamertag(),
                'unique:users,gamertag',
            ],
        ]);

        Auth::user()->update([
            'gamertag'                => $validated['gamertag'],
            'gamertag_setup_required' => false,
        ]);

        $this->redirect(route('feed'));
    }

    public function render(): View
    {
        return view('livewire.account.collect-gamertag')
            ->layout('layouts.onboarding', ['title' => 'Choose your gamertag | CommonGrove']);
    }
}
