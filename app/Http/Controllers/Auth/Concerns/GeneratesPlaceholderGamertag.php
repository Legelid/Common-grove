<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;

/**
 * Shared by every OAuth sign-up controller (Google, Discord, ...) — this has
 * no provider-specific logic, just satisfies the users table's NOT NULL +
 * UNIQUE gamertag constraint for a brand-new account until the user picks
 * their real one via the gamertag_setup_required flag / CollectGamertag step.
 */
trait GeneratesPlaceholderGamertag
{
    private function generatePlaceholderGamertag(): string
    {
        do {
            $candidate = 'NewMember' . random_int(10000, 999999);
        } while (User::withTrashed()->whereRaw('LOWER(gamertag) = ?', [strtolower($candidate)])->exists());

        return $candidate;
    }
}
