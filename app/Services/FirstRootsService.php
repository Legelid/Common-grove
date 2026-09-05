<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\FirstRootsAwarded;
use App\Models\BetaInvite;
use App\Models\User;
use Illuminate\Support\Collection;

class FirstRootsService
{
    /**
     * Generate a new BetaInvite for the given email, or return the existing one.
     */
    public function generateInvite(string $email): BetaInvite
    {
        $existing = BetaInvite::where('email', $email)->first();

        if ($existing !== null) {
            return $existing;
        }

        return BetaInvite::generate($email);
    }

    /**
     * Claim a beta invite token for a user.
     * Returns true on success, false if the token is invalid, expired, or already claimed.
     */
    public function claimInvite(string $token, User $user): bool
    {
        $invite = BetaInvite::where('token', $token)->first();

        if ($invite === null || $invite->isClaimed() || $invite->isExpired()) {
            return false;
        }

        $this->awardFirstRoots($user);

        $invite->update([
            'claimed_by' => $user->id,
            'claimed_at' => now(),
        ]);

        $user->update(['beta_invite_id' => $invite->id]);

        return true;
    }

    /**
     * Permanently award the FirstRoots badge to a user.
     */
    public function awardFirstRoots(User $user): void
    {
        if ($user->is_first_roots) {
            return;
        }

        $user->update([
            'is_first_roots'          => true,
            'first_roots_awarded_at'  => now(),
        ]);

        event(new FirstRootsAwarded($user));
    }

    /**
     * Whether the beta window is still open.
     * Returns true when config('app.beta_closes_at') is null or in the future.
     */
    public function isEligibleForFirstRoots(): bool
    {
        $closesAt = config('app.beta_closes_at');

        if ($closesAt === null || $closesAt === '') {
            return true;
        }

        return now()->lt(\Carbon\Carbon::parse($closesAt));
    }

    /**
     * Generate invites for a list of email addresses.
     * Skips duplicates by returning the existing invite for already-invited emails.
     *
     * @param  array<int, string>  $emails
     * @return Collection<int, BetaInvite>
     */
    public function generateBulkInvites(array $emails): Collection
    {
        return collect($emails)
            ->map(fn (string $email): string => trim($email))
            ->filter(fn (string $email): bool => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique()
            ->map(fn (string $email): BetaInvite => $this->generateInvite($email))
            ->values();
    }
}
