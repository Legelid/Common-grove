<?php

declare(strict_types=1);

/**
 * Central definition of every supporter perk, limit, and pack.
 *
 * All supporter gating throughout the app should read from here rather than
 * hardcoding values inline. This makes it easy to adjust tiers in one place.
 */
return [

    // ── Limits ──────────────────────────────────────────────────────────────
    'limits' => [
        'persistent_rooms' => [
            'free'      => 3,
            'supporter' => 10,
        ],
        'room_collections' => [
            'max_collections'          => 10,
            'max_rooms_per_collection' => 20,
        ],
    ],

    // ── Avatar packs ─────────────────────────────────────────────────────────
    // Category keys from config/avatars.php that are supporter-only.
    // Free users see all other categories in full.
    'avatar_packs' => [
        'retro_digital',
        'seasonal',
    ],

    // ── Gradient themes ──────────────────────────────────────────────────────
    // Keys from config/gradients.php that are supporter-only.
    // Free users get the remaining five gradients.
    'gradient_packs' => [
        'night_window',
        'rainy_crt',
        'lantern_glow',
        'pixel_night',
        'foggy_forest',
        'coffee_shop',
        'aquarium_glow',
        'cassette_evening',
        'snow_quiet',
        'observatory',
        'forest_cabin',
        'deep_ocean',
        'quiet_library',
        'campfire_dusk',
        'stormwatch',
    ],

    // ── Tone packs ───────────────────────────────────────────────────────────
    // Supporter-only tone pack keys from config/tone_packs.php.
    // Free users always have access to the 'default' pack.
    'atmosphere_packs' => [
        'fantasy_tavern',
        'sci_fi_space',
        'retro_web',
        'cozy_rain',
        'quiet_library',
        'campfire',
        'observatory',
        'pixel_night',
        'forest_path',
        'coffee_shop',
    ],

    // ── Feature flags ────────────────────────────────────────────────────────
    // Flip false to disable a supporter feature globally during rollout.
    'features' => [
        'supporter_icon'      => true,
        'extended_room_limit' => true,
        'extra_avatars'       => true,
        'extra_gradients'     => true,
        'room_collections'    => true,
        'atmosphere_packs'    => true,
        'advanced_comfort'    => false, // not yet built
    ],

];
