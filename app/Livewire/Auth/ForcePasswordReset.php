<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ForcePasswordReset extends Component
{
    public string $password              = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        if (! Auth::user()->password_reset_required) {
            $this->redirect(route('feed'), navigate: false);
        }
    }

    public function save(): void
    {
        $this->validate([
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $user                        = Auth::user();
        $user->password              = $passwordService->hash($this->password);
        $user->password_reset_required = false;
        $user->save();

        session()->flash('status', 'Your password has been updated. Welcome to CommonGrove!');

        $this->redirect(route('feed'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.force-password-reset')
            ->layout('layouts.app', ['title' => 'Set a new password — CommonGrove']);
    }
}
