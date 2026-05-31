<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Jobs\SendBetaInviteEmail;
use App\Models\BetaInvite;
use App\Services\FirstRootsService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BetaInvites extends Component
{
    public string $singleEmail = '';
    public string $bulkEmails  = '';
    public ?string $feedback   = null;
    public bool $feedbackError  = false;

    // ── Computed ──────────────────────────────────────────────────────────────

    /** @return Collection<int, BetaInvite> */
    #[Computed]
    public function invites(): Collection
    {
        return BetaInvite::with('claimedBy')
            ->orderByDesc('created_at')
            ->get();
    }

    /** @return array<string, int> */
    #[Computed]
    public function stats(): array
    {
        $total    = BetaInvite::count();
        $claimed  = BetaInvite::whereNotNull('claimed_at')->count();

        return [
            'total'    => $total,
            'claimed'  => $claimed,
            'unclaimed' => $total - $claimed,
        ];
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function generateSingle(): void
    {
        $this->validate(['singleEmail' => ['required', 'email', 'max:191']]);

        /** @var FirstRootsService $service */
        $service = app(FirstRootsService::class);
        $invite  = $service->generateInvite($this->singleEmail);

        $this->feedback      = "Invite ready for {$invite->email}";
        $this->feedbackError = false;
        $this->singleEmail   = '';
        unset($this->invites, $this->stats);
    }

    public function generateBulk(): void
    {
        $this->validate(['bulkEmails' => ['required', 'string']]);

        $emails = array_filter(
            array_map('trim', explode("\n", $this->bulkEmails)),
            fn (string $e): bool => $e !== '',
        );

        /** @var FirstRootsService $service */
        $service = app(FirstRootsService::class);
        $created = $service->generateBulkInvites(array_values($emails));

        $this->feedback      = "Generated {$created->count()} invite(s).";
        $this->feedbackError = false;
        $this->bulkEmails    = '';
        unset($this->invites, $this->stats);
    }

    public function sendEmail(string $inviteId): void
    {
        $invite = BetaInvite::find($inviteId);

        if ($invite === null || $invite->isClaimed()) {
            return;
        }

        SendBetaInviteEmail::dispatch($invite);

        $invite->update(['sent_at' => now()]);
        $this->feedback      = "Invite email queued for {$invite->email}";
        $this->feedbackError = false;
        unset($this->invites);
    }

    public function render(): View
    {
        return view('livewire.admin.beta-invites')
            ->layout('layouts.admin', ['title' => 'Beta Invites — FirstRoots']);
    }
}
