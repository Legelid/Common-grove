<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

/**
 * Resolves the glass UI background theme (forest photo + glass-panel tint)
 * for a given user. Purely cosmetic — never affects layout, safety copy, or
 * functionality, only which photo/tint shows behind the glass panels.
 */
class GlassThemeService
{
    /**
     * All theme definitions from config.
     *
     * @return array<string, array<string, mixed>>
     */
    public function allThemes(): array
    {
        return config('glass_themes', []);
    }

    /**
     * Whether the given theme key is locked for this user.
     */
    public function isLocked(User $user, string $themeKey): bool
    {
        if ($user->is_admin || $user->isSupporter()) {
            return false;
        }

        return (bool) config("glass_themes.{$themeKey}.supporter_only", false);
    }

    /**
     * The resolved theme key for this user — their saved choice if it still
     * exists and they still have access, otherwise the default.
     */
    public function resolveKey(User $user): string
    {
        $key = $user->site_theme ?: 'forest-default';

        if (! config("glass_themes.{$key}")) {
            return 'forest-default';
        }

        if ($this->isLocked($user, $key)) {
            return 'forest-default';
        }

        return $key;
    }

    /**
     * The full theme definition for this user (label, image, etc.).
     *
     * @return array<string, mixed>
     */
    public function resolveTheme(User $user): array
    {
        return config('glass_themes.' . $this->resolveKey($user))
            ?? config('glass_themes.forest-default', []);
    }
}
