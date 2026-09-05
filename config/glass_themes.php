<?php

declare(strict_types=1);

/**
 * Glass UI theme definitions — the forest-photo background + glass-panel
 * tint shown on the app's glass routes (feed, explore, messages, rooms,
 * friends, settings, tags — see $glassRoutes in layouts/app.blade.php).
 *
 * Each entry's `image` is a public/images path rendered as the fixed
 * background photo, and its key doubles as the [data-glass-theme="<key>"]
 * selector in resources/css/app.css, where the actual scrim/blur/border
 * tokens for that theme live. Adding a theme means: drop the photo into
 * public/images, add an entry here, add a matching CSS block.
 *
 * supporter_only: false  → available to everyone
 * supporter_only: true   → requires supporter status
 */
return [

    'forest-default' => [
        'label'          => 'Forest Path',
        'description'    => 'A quiet trail through the trees.',
        'supporter_only' => false,
        'image'          => 'images/forest-path.jpg',
    ],

    'rainy-path' => [
        'label'          => 'Rainy Path',
        'description'    => 'A soft rain over green grass.',
        'supporter_only' => false,
        'image'          => 'images/rainy-path.jpg',
    ],

    'golden-marsh' => [
        'label'          => 'Golden Marsh',
        'description'    => 'Misty gold light at first light.',
        'supporter_only' => false,
        'image'          => 'images/golden-marsh.jpg',
    ],

];
