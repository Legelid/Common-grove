<?php

declare(strict_types=1);

namespace App\Livewire\Beta;

use App\Models\BetaInvite;
use App\Services\FirstRootsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ClaimFirstRoots extends Component
{
    public string $token = '';

    public bool $tokenValid   = false;
    public bool $tokenExpired = false;
    public bool $claimed      = false;
    public bool $alreadyOwns  = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $invite = BetaInvite::where('token', $token)->first();

        if ($invite === null) {
            return;
        }

        if ($invite->isExpired()) {
            $this->tokenExpired = true;
            return;
        }

        if ($invite->isClaimed()) {
            // If the current user is the one who claimed it, show celebration.
            if (Auth::check() && $invite->claimed_by === Auth::id()) {
                $this->claimed    = true;
                $this->tokenValid = true;
            }
            return;
        }

        $this->tokenValid = true;

        if (Auth::check() && Auth::user()->is_first_roots) {
            $this->alreadyOwns = true;
        }
    }

    public function claim(): void
    {
        if (! $this->tokenValid || $this->claimed) {
            return;
        }

        if (! Auth::check()) {
            session(['beta_invite_token' => $this->token]);
            $this->redirect(route('register'), navigate: false);
            return;
        }

        /** @var FirstRootsService $service */
        $service = app(FirstRootsService::class);

        $success = $service->claimInvite($this->token, Auth::user());

        if ($success) {
            $this->claimed     = true;
            $this->alreadyOwns = false;
        }
    }

    public function redirectToRegister(): void
    {
        session(['beta_invite_token' => $this->token]);
        $this->redirect(route('register'), navigate: false);
    }

    public function redirectToLogin(): void
    {
        session(['beta_invite_token' => $this->token]);
        $this->redirect(route('login'), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.beta.claim-first-roots')
            ->layout('layouts.app', ['title' => 'FirstRoots | Founding Member · CommonGrove']);
    }
}
