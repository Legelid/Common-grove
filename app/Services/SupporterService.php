<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Single place for all supporter-tier eligibility checks.
 * Read config/supporter.php — never hardcode values in call sites.
 */
class SupporterService
{
    /**
     * Avatar category keys (from config/avatars.php) the given user may access.
     *
     * @return list<string>
     */
    public function availableAvatarCategories(User $user): array
    {
        $all         = array_keys(config('avatars.categories', []));
        $supporterOnly = config('supporter.avatar_packs', []);

        if ($user->is_supporter || $user->is_admin) {
            return $all;
        }

        return array_values(array_filter($all, fn (string $k) => ! in_array($k, $supporterOnly, true)));
    }

    /**
     * Gradient keys (from config/gradients.php) the given user may apply.
     *
     * @return array<string, array<string, mixed>>
     */
    public function availableGradients(User $user): array
    {
        $all           = config('gradients', []);
        $supporterOnly = config('supporter.gradient_packs', []);

        if ($user->is_supporter || $user->is_admin) {
            return $all;
        }

        return array_filter($all, fn (string $k) => ! in_array($k, $supporterOnly, true), ARRAY_FILTER_USE_KEY);
    }

    /**
     * True if a named feature is enabled in config AND the user meets the tier.
     */
    public function canAccess(User $user, string $feature): bool
    {
        if (! config("supporter.features.{$feature}", false)) {
            return false; // feature not yet built/enabled
        }

        return $user->is_supporter || $user->is_admin;
    }

    /**
     * Gradient keys that exist but are locked for this user.
     * Used by the UI to render locked previews.
     *
     * @return list<string>
     */
    public function lockedGradientKeys(User $user): array
    {
        if ($user->is_supporter || $user->is_admin) {
            return [];
        }

        return array_values(config('supporter.gradient_packs', []));
    }

    /**
     * Avatar category keys that are locked for this user.
     *
     * @return list<string>
     */
    public function lockedAvatarCategories(User $user): array
    {
        if ($user->is_supporter || $user->is_admin) {
            return [];
        }

        return array_values(config('supporter.avatar_packs', []));
    }
}
