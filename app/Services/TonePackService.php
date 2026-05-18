<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

/**
 * Resolves tone pack phrases for a given user.
 *
 * Tone packs are personal — they change the microcopy a user sees.
 * They never affect moderation text, safety warnings, legal copy, or errors.
 */
class TonePackService
{
    /**
     * Full phrase set for the user's selected tone pack (used by the rotating tagline).
     *
     * @return list<string>
     */
    public function getPhrases(User $user): array
    {
        return $this->resolvePack($user)['phrases'] ?? [];
    }

    /**
     * A single copy string for the given key (e.g. 'empty_feed').
     * Falls back to the default pack, then to $fallback if the key doesn't exist.
     */
    public function getPhrase(User $user, string $key, string $fallback = ''): string
    {
        $pack = $this->resolvePack($user);

        return $pack[$key] ?? config("tone_packs.default.{$key}", $fallback);
    }

    /**
     * All pack definitions from config.
     *
     * @return array<string, array<string, mixed>>
     */
    public function allPacks(): array
    {
        return config('tone_packs', []);
    }

    /**
     * Whether the given pack key is locked for this user.
     */
    public function isLocked(User $user, string $packKey): bool
    {
        if ($user->is_admin || $user->isSupporter()) {
            return false;
        }

        return (bool) config("tone_packs.{$packKey}.supporter_only", false);
    }

    // -------------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function resolvePack(User $user): array
    {
        $key = $user->tone_pack ?: 'default';

        // If the user's saved pack no longer exists or they've lost supporter access,
        // silently fall back to the default pack.
        $pack = config("tone_packs.{$key}");

        if (! $pack) {
            return config('tone_packs.default', []);
        }

        if (($pack['supporter_only'] ?? false) && ! $user->isSupporter() && ! $user->is_admin) {
            return config('tone_packs.default', []);
        }

        return $pack;
    }
}
