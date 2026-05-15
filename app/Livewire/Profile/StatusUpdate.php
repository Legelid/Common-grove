<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\MoodOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class StatusUpdate extends Component
{
    public string $moodInput      = '';
    public string $statusTextInput = '';

    public ?string $statusMessage = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->moodInput       = $user->hasActiveStatus() ? ($user->status_mood ?? '') : '';
        $this->statusTextInput = $user->hasActiveStatus() ? ($user->status_text ?? '') : '';
    }

    /** @return list<string> */
    public function moodOptions(): array
    {
        return MoodOption::values();
    }

    public function saveStatus(): void
    {
        $this->validate([
            'moodInput'       => ['nullable', Rule::in(MoodOption::values())],
            'statusTextInput' => ['nullable', 'string', 'max:60'],
        ]);

        $this->statusMessage = null;

        Auth::user()->update([
            'status_mood'       => $this->moodInput ?: null,
            'status_text'       => trim($this->statusTextInput) ?: null,
            'status_expires_at' => now()->addHours(24),
        ]);

        $this->statusMessage = 'Status updated. It will expire in 24 hours.';
    }

    public function clearStatus(): void
    {
        Auth::user()->update([
            'status_mood'       => null,
            'status_text'       => null,
            'status_expires_at' => null,
        ]);

        $this->moodInput       = '';
        $this->statusTextInput = '';
        $this->statusMessage   = 'Status cleared.';
    }

    public function render(): View
    {
        return view('livewire.profile.status-update');
    }
}
