<?php

declare(strict_types=1);

return [

    // Limits
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

    // Avatar packs (category keys from config/avatars.php)
    'avatar_packs' => [
        'retro_digital',
        'seasonal',
    ],

    // Tone packs — supporter-only keys
    // Free users get: commongrove, forest_path, cozy_rain
    // Supporters get all
    'tone_packs' => [
        'fantasy_tavern',
        'sci_fi_space',
        'retro_web',
        'campfire',
        'observatory',
        'pixel_night',
        'quiet_library',
        'coffee_shop',
    ],

    // Prompt packs — supporter-only keys
    // Free users get: general
    // Supporters get all
    'prompt_packs' => [
        'fantasy_dnd',
        'cozy_gaming',
        'books_stories',
        'sci_fi',
        'music_discovery',
        'creative_projects',
        'deep_talks',
        'quiet_introvert',
        'retro_internet',
        'horror_cozy',
    ],

    // Feature flags
    'features' => [
        'supporter_icon'      => true,
        'extended_room_limit' => true,
        'extra_avatars'       => true,
        'tone_packs'          => true,
        'prompt_packs'        => true,
        'room_collections'    => true,
        'advanced_comfort'    => true,
        'vibe_themes'         => false, // coming soon
    ],

];
