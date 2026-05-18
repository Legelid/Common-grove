<?php

declare(strict_types=1);

/**
 * Tone pack definitions — personalized microcopy styles.
 *
 * Each pack changes the emotional language a user sees:
 *   - rotating sidebar phrases
 *   - feed / messages / friends empty states
 *
 * They do NOT affect rules, moderation, safety warnings, legal text, or errors.
 * Other users never see your chosen tone pack — it is entirely personal.
 *
 * supporter_only: false  → available to everyone
 * supporter_only: true   → requires supporter status
 */
return [

    'default' => [
        'label'          => 'CommonGrove',
        'description'    => 'The original CommonGrove voice.',
        'supporter_only' => false,
        'phrases'        => [
            'A place to find your people',
            "You don't have to rush here",
            'Just being here is enough',
            "Take your time — there's no pressure",
            'A quieter corner of the internet',
            'Find people who feel familiar',
            'Come as you are',
            "It's okay to just exist here",
            'No expectations, just connection',
            'A place to feel a little less alone',
            'You can take things slow here',
            'Not everything has to be said right away',
            'Stay as long as you like',
            'A calm place to connect',
            "You're welcome here, however you show up",
            'No pressure to be anything but yourself',
            'Find your pace here',
            "You don't have to perform here",
            'A space that moves at your speed',
            'You can just listen if you want',
        ],
        'empty_feed'     => "It's quiet right now. Check back a little later.",
        'empty_messages' => 'No conversations yet.',
        'empty_friends'  => 'No friends yet. You can go at your own pace.',
    ],

    'fantasy_tavern' => [
        'label'          => 'Fantasy / Tavern',
        'description'    => 'A warm tavern in a quiet corner of the realm.',
        'supporter_only' => true,
        'phrases'        => [
            'Rest here awhile, traveler',
            'The tavern is warm tonight',
            'No need to carry your burdens alone',
            'Journey here while you are on your quest',
            'Leave your worries at the door',
            'Every wanderer finds rest here',
            'The road can wait',
            'Weary travelers are always welcome',
            'A warm fire burns for those who seek company',
            'No quest is required to enter',
        ],
        'empty_feed'     => 'The tavern is quiet tonight. Check back later.',
        'empty_messages' => 'No messages yet — the hearth is warm.',
        'empty_friends'  => 'No companions yet. Every adventurer starts alone.',
    ],

    'sci_fi_space' => [
        'label'          => 'Sci-Fi / Space',
        'description'    => 'A calm station drifting somewhere quiet.',
        'supporter_only' => true,
        'phrases'        => [
            'Dock here for a while',
            'Your signal is welcome here',
            'No need to drift alone',
            'Transmission received',
            'The station is quiet and calm',
            'All frequencies welcome',
            "Take your time — the stars aren't going anywhere",
            "You've found a stable orbit here",
            'Low gravity, low pressure',
            'This channel is always open',
        ],
        'empty_feed'     => 'No signals detected yet. Check back soon.',
        'empty_messages' => 'No transmissions yet.',
        'empty_friends'  => 'No crew yet. All signals are welcome here.',
    ],

    'retro_web' => [
        'label'          => 'Retro Web',
        'description'    => 'A quieter corner of an older internet.',
        'supporter_only' => true,
        'phrases'        => [
            'Connection stable',
            'AFK is okay here',
            "You've entered a quieter corner of the web",
            'No pressure to respond immediately',
            'Lurking is valid here',
            'You can close this tab and come back',
            'Page still loading — take your time',
            'Dial-up speed is fine here',
            '404: rush not found',
            'Status: online, no expectations',
        ],
        'empty_feed'     => 'The feed is empty. Refresh later.',
        'empty_messages' => 'Inbox: empty. No pressure.',
        'empty_friends'  => 'Friends list empty. That\'s okay.',
    ],

    'cozy_rain' => [
        'label'          => 'Cozy Rain',
        'description'    => 'Warm inside, rain on the window.',
        'supporter_only' => true,
        'phrases'        => [
            'The rain can wait outside',
            'Stay awhile',
            "There's warmth here tonight",
            'Let the world be quiet for a bit',
            'Rain on the window, calm inside',
            "Take off your coat, you're home",
            "The kettle's on",
            "No rush — it's raining anyway",
            'Soft light, soft conversations',
            'A good night to stay in',
        ],
        'empty_feed'     => "It's quiet — like rain on a window.",
        'empty_messages' => 'No messages yet. The rain is soft tonight.',
        'empty_friends'  => "No friends yet. You're not alone in here.",
    ],

    'quiet_library' => [
        'label'          => 'Quiet Library',
        'description'    => 'Soft shelves, low voices, no rush.',
        'supporter_only' => true,
        'phrases'        => [
            'Find a comfortable chair',
            'No one will rush you here',
            'Take your time — the shelves can wait',
            'Quiet conversations welcome',
            "Not every visit needs a reason",
            'The good books are in the back',
            'A soft corner of the internet',
            'Pages turn at your pace',
            'You can read, or just sit',
            'Silence is welcome here too',
        ],
        'empty_feed'     => 'The room is quiet — like a reading nook.',
        'empty_messages' => 'No notes passed yet.',
        'empty_friends'  => 'No study partners yet. The library is open.',
    ],

    'campfire' => [
        'label'          => 'Campfire',
        'description'    => 'Small fire, good company, no hurry.',
        'supporter_only' => true,
        'phrases'        => [
            'Pull up a chair',
            "The fire's still going",
            'No story required to sit here',
            'You found the quiet spot',
            'Everyone here keeps the fire small and warm',
            'Sit as long as you like',
            'A good night for a fire',
            'The smoke keeps the noise away',
            "You're welcome at this fire",
            "Good company doesn't need much",
        ],
        'empty_feed'     => 'The fire is burning low tonight.',
        'empty_messages' => 'No messages yet — pull up a seat.',
        'empty_friends'  => 'No campfire companions yet.',
    ],

    'observatory' => [
        'label'          => 'Observatory',
        'description'    => 'Patient, quiet, and open to what is out there.',
        'supporter_only' => true,
        'phrases'        => [
            "Look up when you're ready",
            "There's more than you can see right now",
            'No telescope required',
            'Stay as long as the sky holds',
            'The light here has traveled far',
            'Some things are worth sitting with',
            'Quiet observations welcome',
            "The stars don't rush either",
            "Take your time — the view isn't going anywhere",
            "You've found a good vantage point",
        ],
        'empty_feed'     => 'The sky is clear — nothing visible yet.',
        'empty_messages' => 'No signals received yet.',
        'empty_friends'  => 'No fellow stargazers yet.',
    ],

    'pixel_night' => [
        'label'          => 'Pixel Night',
        'description'    => 'Safe zone. Low ping. No enemies here.',
        'supporter_only' => true,
        'phrases'        => [
            'Player 2 can wait',
            'No enemies here',
            'Save point found',
            'Low ping. High comfort.',
            'Side quest: find some peace',
            'You can go AFK here',
            'No respawn timer in this zone',
            'Safe zone: active',
            'Extra life granted just for showing up',
            'XP for existing: awarded',
        ],
        'empty_feed'     => 'No active lobbies right now.',
        'empty_messages' => 'Inbox empty.',
        'empty_friends'  => 'No party members yet.',
    ],

    'forest_path' => [
        'label'          => 'Forest Path',
        'description'    => 'No destination required.',
        'supporter_only' => true,
        'phrases'        => [
            'The path continues at your pace',
            'No trail to follow — just walk',
            'Rest where the light comes through',
            "The forest doesn't mind if you take your time",
            'Every step counts',
            "You've found a clearing",
            "The trees have been here longer than the rush",
            'Take the quiet route',
            'No destination required',
            'Some paths are worth lingering on',
        ],
        'empty_feed'     => 'The clearing is empty right now.',
        'empty_messages' => 'No messages — the forest is quiet.',
        'empty_friends'  => 'No hiking partners yet.',
    ],

    'coffee_shop' => [
        'label'          => 'Coffee Shop',
        'description'    => 'Laptop open, no clock to watch.',
        'supporter_only' => true,
        'phrases'        => [
            'Table for one is fine here',
            "No one's watching the clock",
            'Stay as long as the cup lasts',
            'Laptop open, no pressure',
            'Background noise: optional',
            'Decaf welcome. So is silence.',
            'Good coffee and low expectations',
            'The window seat is yours',
            "No one needs to know you've been here for hours",
            'Order when you\'re ready',
        ],
        'empty_feed'     => 'The café is quiet right now.',
        'empty_messages' => 'No orders yet — take your time.',
        'empty_friends'  => "No regulars yet. You're always welcome here.",
    ],

];
