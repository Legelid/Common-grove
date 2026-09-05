<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

// Last updated: 2026-08-31
// Total: 11 categories, 102 subcategories, 946 tags
class CommonGroveTaxonomySeeder extends Seeder
{
    /**
     * Taxonomy definition.
     * Each entry: category name => ['desc', 'subcategories' => [subcat name => ['desc', 'tags' => [name => slug|null]]]]
     * A null slug means: auto-generate from name.
     * A string slug means: use exactly this slug (preserves existing user data).
     *
     * @var array<string, array{desc:string, subcategories:array<string, array{desc:string, tags:array<string, string|null>}>}>
     */
    private const TAXONOMY = [

        // ── 1. Gaming ─────────────────────────────────────────────────────────
        'Gaming' => [
            'desc' => 'Video games, tabletop games, and everything in between.',
            'subcategories' => [
                'Cozy Games' => [
                    'desc' => 'Laid-back games with no pressure to perform.',
                    'tags' => [
                        'Stardew Valley'    => 'stardew-valley',
                        'Animal Crossing'   => 'animal-crossing',
                        'Cozy Grove'        => null,
                        'Spiritfarer'       => null,
                        'My Time at Portia' => null,
                        'Slime Rancher'     => null,
                        'Garden Story'      => null,
                        'Littlewood'        => null,
                        'Unpacking'         => null,
                        'A Short Hike'      => null,
                        'Coffee Talk'       => null,
                        'Bear and Breakfast' => null,
                        'Dinkum'            => null,
                        'Sun Haven'         => null,
                        'Coral Island'      => null,
                        'Cozy Games'        => 'cozy-games',
                    ],
                ],
                'Competitive Games' => [
                    'desc' => 'Ranked play, esports, and competitive modes.',
                    'tags' => [
                        'Valorant'          => 'valorant',
                        'League of Legends' => 'league-of-legends',
                        'Counter-Strike'    => 'counter-strike',
                        'Overwatch'         => 'overwatch',
                        'Apex Legends'      => 'apex-legends',
                        'Dota 2'            => 'dota-2',
                        'Rocket League'     => null,
                        'Rainbow Six Siege' => null,
                        'Street Fighter'    => null,
                        'Super Smash Bros'  => null,
                        'Tekken'            => null,
                        'Fortnite'          => 'fortnite',
                        'PUBG'              => null,
                        'Hearthstone'       => 'hearthstone',
                    ],
                ],
                'Multiplayer & Co-op' => [
                    'desc' => 'Playing with others online or locally.',
                    'tags' => [
                        'Among Us'          => 'among-us',
                        'Deep Rock Galactic' => null,
                        'Sea of Thieves'    => null,
                        'It Takes Two'      => null,
                        'Phasmophobia'      => null,
                        'Terraria'          => null,
                        'Valheim'           => null,
                        'Minecraft'         => 'minecraft',
                        'Destiny 2'         => null,
                        'Destiny'           => 'destiny',
                        'Borderlands'       => null,
                        'Left 4 Dead'       => null,
                        'Risk of Rain 2'    => null,
                        'Multiplayer Games' => null,
                    ],
                ],
                'Single-Player Games' => [
                    'desc' => 'Story-driven or solo experiences.',
                    'tags' => [
                        'Elden Ring'        => 'elden-ring',
                        'Dark Souls'        => 'dark-souls',
                        'Hollow Knight'     => 'hollow-knight',
                        'Celeste'           => null,
                        'Hades'             => null,
                        'Disco Elysium'     => null,
                        'Outer Wilds'       => null,
                        'Subnautica'        => null,
                        'The Witcher'       => 'the-witcher',
                        'Bloodborne'        => 'bloodborne',
                        'Cyberpunk 2077'    => 'cyberpunk-2077',
                        'Baldur\'s Gate 3'  => null,
                        'Skyrim'            => null,
                        'God of War'        => null,
                        'The Legend of Zelda' => 'the-legend-of-zelda',
                        'Single-Player Games' => null,
                    ],
                ],
                'Tabletop & TTRPGs' => [
                    'desc' => 'Pen-and-paper RPGs, wargames, and board games.',
                    'tags' => [
                        'Dungeons & Dragons' => 'dungeons-dragons',
                        'Tabletop RPG'      => 'tabletop-rpg',
                        'Pathfinder'        => null,
                        'Call of Cthulhu'   => null,
                        'Warhammer'         => 'warhammer',
                        'Shadowrun'         => null,
                        'Cyberpunk Red'     => null,
                        'Blades in the Dark' => null,
                        'Vampire: The Masquerade' => null,
                        'One-Shot RPGs'     => null,
                        'TTRPG Writing'     => null,
                        'Homebrew'          => null,
                        'Roll20'            => null,
                        'Foundry VTT'       => null,
                        'Magic: The Gathering' => 'magic-the-gathering',
                        'Card Games'        => 'card-games',
                        'Chess'             => 'chess',
                    ],
                ],
                'Retro Games' => [
                    'desc' => 'Classics, emulation, and retro hardware.',
                    'tags' => [
                        'SNES'              => null,
                        'NES'               => null,
                        'Sega Genesis'      => null,
                        'Nintendo 64'       => null,
                        'Game Boy'          => null,
                        'Atari'             => null,
                        'PlayStation 1'     => null,
                        'PlayStation 2'     => null,
                        'Dreamcast'         => null,
                        'CRT Gaming'        => null,
                        'Retro Collecting'  => null,
                        'Emulation'         => null,
                        'DOS Games'         => null,
                        'Arcade Games'      => null,
                        'Retro Games'       => null,
                    ],
                ],
                'Game Development' => [
                    'desc' => 'Making games: engines, jams, and design.',
                    'tags' => [
                        'Unity'             => null,
                        'Unreal Engine'     => null,
                        'Godot'             => null,
                        'Indie Game Dev'    => null,
                        'Game Jams'         => null,
                        'Level Design'      => null,
                        'Game Design'       => null,
                        'Narrative Design'  => null,
                        'RPG Maker'         => null,
                        'Ren\'Py'           => null,
                        'Game Modding'      => null,
                        'Game Development'  => 'game-development',
                    ],
                ],
                'Specific Games' => [
                    'desc' => 'Rooms for individual titles and communities.',
                    'tags' => [
                        'World of Warcraft' => 'world-of-warcraft',
                        'Final Fantasy'     => 'final-fantasy',
                        'RuneScape'         => 'runescape',
                        'OSRS'              => 'osrs',
                        'Pokémon'           => 'pokemon',
                        'Genshin Impact'    => 'genshin-impact',
                        'Honkai: Star Rail' => 'honkai-star-rail',
                        'Path of Exile'     => 'path-of-exile',
                        'Diablo'            => 'diablo',
                        'Guild Wars 2'      => 'guild-wars-2',
                        'Elder Scrolls Online' => 'elder-scrolls-online',
                        'Roblox'            => null,
                        'The Sims'          => null,
                        'Mario'             => 'mario',
                        'Kirby'             => 'kirby',
                        'Fire Emblem'       => 'fire-emblem',
                        'Splatoon'          => 'splatoon',
                        'Halo'              => 'halo',
                        'Dead by Daylight'  => 'dead-by-daylight',
                        'Gaming'            => 'gaming',
                    ],
                ],
                // ── Phase 1 additions ──────────────────────────────────────────
                'Indie & Horror Games' => [
                    'desc' => 'Indie darlings, horror games, and cult classics.',
                    'tags' => [
                        'Undertale'         => null,
                        'Deltarune'         => null,
                        'Omori'             => null,
                        'Little Nightmares' => null,
                        'Alien Isolation'   => null,
                        'Resident Evil'     => null,
                        'Silent Hill'       => null,
                        'Lethal Company'    => null,
                        'Balatro'           => null,
                        'Horror Gaming'     => null,
                        'Indie Gaming'      => null,
                    ],
                ],
                'Action RPGs & Story Games' => [
                    'desc' => 'Action RPGs, story-driven adventures, and AAA epics.',
                    'tags' => [
                        'Hades 2'           => null,
                        'Devil May Cry'     => null,
                        'Persona Series'    => null,
                        'Monster Hunter'    => null,
                        'Dragon Age'        => null,
                        'Mass Effect'       => null,
                        'Final Fantasy XIV' => null,
                        'Vampire Survivors' => null,
                        'Brotato'           => null,
                    ],
                ],
                'Exploration & Survival Games' => [
                    'desc' => 'Open world exploration, survival, and deep simulation.',
                    'tags' => [
                        'Starfield'         => null,
                        'No Man\'s Sky'     => null,
                        'RimWorld'          => null,
                        'Dwarf Fortress'    => null,
                        'Rust (Survival Game)' => 'rust-game',
                    ],
                ],
                'Nintendo Universe' => [
                    'desc' => 'Nintendo franchises and platform exclusives.',
                    'tags' => [
                        'Mario Kart'        => null,
                        'Pikmin'            => null,
                        'Metroid'           => null,
                        'Zelda'             => null,
                        'Pokemon GO'        => null,
                    ],
                ],
                'Gaming Culture' => [
                    'desc' => 'The community, culture, and meta side of gaming.',
                    'tags' => [
                        'Speedrunning'      => null,
                        'ROM Hacking'       => null,
                        'Game Collecting'   => null,
                        'Retro Gaming'      => null,
                        'Gaming Conventions' => null,
                        'Pinball'           => null,
                        'Arcade Gaming'     => null,
                        'Fighting Game Community' => null,
                        'Soulslike Community' => null,
                        'Cozy Gaming'       => null,
                        'Board Game Cafes'  => null,
                    ],
                ],
                'Collectible Card Games' => [
                    'desc' => 'Trading card games and competitive card play.',
                    'tags' => [
                        'Yu-Gi-Oh'          => null,
                        'Pokemon TCG'       => null,
                        'Warhammer 40K'     => null,
                    ],
                ],
            ],
        ],

        // ── 2. Creative ───────────────────────────────────────────────────────
        'Creative' => [
            'desc' => 'Art, writing, crafts, and making things.',
            'subcategories' => [
                'Drawing & Illustration' => [
                    'desc' => 'Digital and traditional visual art.',
                    'tags' => [
                        'Drawing'           => 'drawing',
                        'Digital Art'       => 'digital-art',
                        'Painting'          => 'painting',
                        'Pixel Art'         => 'pixel-art',
                        'Character Design'  => null,
                        'Concept Art'       => null,
                        'Fan Art'           => null,
                        'Portrait Drawing'  => null,
                        'Watercolor'        => null,
                        'Ink Drawing'       => null,
                        'Gesture Drawing'   => null,
                        'Comics'            => null,
                        'Graphic Novels'    => 'graphic-novels',
                        'Art'               => 'art',
                    ],
                ],
                'Writing' => [
                    'desc' => 'Fiction, poetry, scripts, and prose.',
                    'tags' => [
                        'Writing'           => 'writing',
                        'Fiction Writing'   => null,
                        'Short Stories'     => null,
                        'Novel Writing'     => null,
                        'NaNoWriMo'         => null,
                        'Script Writing'    => null,
                        'Poetry'            => 'poetry',
                        'Creative Nonfiction' => null,
                        'Writing Prompts'   => null,
                        'Writing Groups'    => null,
                    ],
                ],
                'Photography' => [
                    'desc' => 'Capturing moments, film, and editing.',
                    'tags' => [
                        'Photography'       => 'photography',
                        'Portrait Photography' => null,
                        'Landscape Photography' => null,
                        'Street Photography' => null,
                        'Night Photography' => null,
                        'Film Photography'  => null,
                        'Astrophotography'  => null,
                        'Wildlife Photography' => null,
                        'Food Photography'  => 'food-photography',
                    ],
                ],
                'Crafts' => [
                    'desc' => 'Hands-on making and textile arts.',
                    'tags' => [
                        'Knitting'          => 'knitting',
                        'Crocheting'        => 'crocheting',
                        'Embroidery'        => 'embroidery',
                        'Sewing'            => 'sewing',
                        'Cross-Stitch'      => null,
                        'Quilting'          => null,
                        'Felting'           => null,
                        'Macramé'           => null,
                        'Candle Making'     => 'candle-making',
                        'Soap Making'       => 'soap-making',
                        'Jewelry Making'    => 'jewelry-making',
                        'Leatherworking'    => 'leatherworking',
                        'Origami'           => 'origami',
                        'Bookbinding'       => null,
                        'Paper Crafts'      => null,
                        'Cosplay'           => 'cosplay',
                    ],
                ],
                'Music Creation' => [
                    'desc' => 'Producing, composing, and recording.',
                    'tags' => [
                        'Music Production'  => 'music-production',
                        'Beat Making'       => null,
                        'Songwriting'       => null,
                        'Music Theory'      => 'music-theory',
                        'Chiptune'          => null,
                        'Ambient Music'     => null,
                        'Electronic Music'  => 'electronic-music',
                    ],
                ],
                'Design' => [
                    'desc' => 'Graphic design, UI/UX, and visual identity.',
                    'tags' => [
                        'Graphic Design'    => 'graphic-design',
                        'UI/UX Design'      => null,
                        'Typography'        => null,
                        'Illustration'      => null,
                        'Motion Graphics'   => 'motion-graphics',
                        'Web Design'        => null,
                        'Logo Design'       => null,
                        'Fashion Design'    => 'fashion-design',
                        'Calligraphy'       => 'calligraphy',
                        'Printmaking'       => 'printmaking',
                    ],
                ],
                'Video Creation' => [
                    'desc' => 'YouTube, filmmaking, and video editing.',
                    'tags' => [
                        'YouTube'           => null,
                        'Short-Form Video'  => null,
                        'Film Making'       => null,
                        'Video Editing'     => null,
                        'Animation'         => 'animation',
                        'Documentary Filmmaking' => null,
                        'Cinematography'    => null,
                    ],
                ],
                'Worldbuilding' => [
                    'desc' => 'Building fictional worlds, maps, and lore.',
                    'tags' => [
                        'Worldbuilding'     => 'worldbuilding',
                        'Map Making'        => null,
                        'Conlangs'          => null,
                        'Lore Writing'      => null,
                        'Fantasy World Design' => null,
                        'Sci-Fi Worldbuilding' => null,
                        'Magic Systems'     => null,
                        'Fictional Cultures' => null,
                    ],
                ],
                // ── Phase 1 additions ──────────────────────────────────────────
                'Sculptural & 3D Crafts' => [
                    'desc' => 'Pottery, model making, miniatures, and sculpting.',
                    'tags' => [
                        'Pottery & Ceramics' => null,
                        'Resin Art'         => null,
                        'Miniature Painting' => null,
                        'Warhammer Painting' => null,
                        'Gunpla'            => null,
                        'Model Building'    => null,
                        'Diorama Making'    => null,
                        'Sculpture'         => null,
                    ],
                ],
                'Print & Paper Arts' => [
                    'desc' => 'Printmaking, paper crafts, and zine culture.',
                    'tags' => [
                        'Linocut Printing'  => null,
                        'Woodburning'       => null,
                        'Pyrography'        => null,
                        'Sticker Making'    => null,
                        'Zine Making'       => null,
                        'Papermaking'       => null,
                        'Paper Cutting'     => null,
                        'Collage Art'       => null,
                        'Mixed Media Art'   => null,
                        'Found Object Art'  => null,
                    ],
                ],
                'Digital Art Tools' => [
                    'desc' => 'Software and platforms for digital creation.',
                    'tags' => [
                        '3D Modeling'       => null,
                        'Blender'           => null,
                        'ZBrush'            => null,
                        'Procreate'         => null,
                        'Clip Studio Paint' => null,
                        'Voxel Art'         => null,
                        'Generative Art'    => null,
                        'Creative Coding'   => null,
                    ],
                ],
                'Comics & Sequential Art' => [
                    'desc' => 'Comic drawing, manga creation, and sequential art.',
                    'tags' => [
                        'Comic Drawing'     => null,
                        'Manga Drawing'     => null,
                        'Graffiti Art'      => null,
                    ],
                ],
                'Specialized Photography' => [
                    'desc' => 'Niche photography disciplines and techniques.',
                    'tags' => [
                        'Darkroom Photography'    => null,
                        'Architecture Photography' => null,
                    ],
                ],
            ],
        ],

        // ── 3. Media & Entertainment ──────────────────────────────────────────
        'Media & Entertainment' => [
            'desc' => 'Movies, TV, anime, YouTube, podcasts, and fandoms.',
            'subcategories' => [
                'Movies' => [
                    'desc' => 'Films of all kinds.',
                    'tags' => [
                        'Horror Films'      => 'horror-films',
                        'Sci-Fi Films'      => 'sci-fi-films',
                        'Indie Films'       => 'indie-films',
                        'Documentary'       => 'documentary',
                        'Foreign Films'     => 'foreign-films',
                        'Action Films'      => 'action-films',
                        'Comedy Films'      => 'comedy-films',
                        'Classic Films'     => null,
                        'Cult Films'        => null,
                        'Film Criticism'    => null,
                    ],
                ],
                'TV Shows' => [
                    'desc' => 'Series, limited runs, and reality TV.',
                    'tags' => [
                        'TV Drama'          => 'tv-drama',
                        'Reality TV'        => 'reality-tv',
                        'Sci-Fi TV'         => null,
                        'Fantasy TV'        => null,
                        'Crime TV'          => null,
                        'Comedy Series'     => null,
                        'Limited Series'    => null,
                        'International TV'  => null,
                        'True Crime'        => 'true-crime',
                        'Animation'         => null,
                    ],
                ],
                'Anime' => [
                    'desc' => 'Japanese animation and manga adaptations.',
                    'tags' => [
                        'Anime'             => 'anime',
                        'Manga'             => 'manga',
                        'Shonen'            => null,
                        'Shojo'             => null,
                        'Seinen'            => null,
                        'Isekai'            => null,
                        'Mecha'             => null,
                        'Slice of Life'     => null,
                        'Sports Anime'      => null,
                        'Magical Girl'      => null,
                        'Studio Ghibli'     => 'studio-ghibli',
                        'One Piece'         => 'one-piece',
                        'Attack on Titan'   => 'attack-on-titan',
                        'Naruto'            => 'naruto',
                        'My Hero Academia'  => 'my-hero-academia',
                        'Demon Slayer'      => 'demon-slayer',
                        'Fullmetal Alchemist' => 'fullmetal-alchemist',
                        'Hunter x Hunter'   => 'hunter-x-hunter',
                        'JoJo\'s Bizarre Adventure' => 'jojos-bizarre-adventure',
                        'Sword Art Online'  => 'sword-art-online',
                        'Bleach'            => 'bleach',
                        'Dragon Ball'       => 'dragon-ball',
                        'Anime Films'       => 'anime-films',
                        'Anime OSTs'        => 'anime-osts',
                    ],
                ],
                'YouTube & Online Video' => [
                    'desc' => 'Creators, channels, and online content.',
                    'tags' => [
                        'Gaming YouTube'    => null,
                        'Educational YouTube' => null,
                        'Comedy YouTube'    => null,
                        'ASMR'              => null,
                        'Vlogs'             => null,
                        'Tech YouTube'      => null,
                        'Art YouTube'       => null,
                        'Cooking YouTube'   => null,
                    ],
                ],
                'Podcasts' => [
                    'desc' => 'Audio shows for every interest.',
                    'tags' => [
                        'True Crime Podcasts' => null,
                        'Comedy Podcasts'   => null,
                        'History Podcasts'  => null,
                        'Science Podcasts'  => null,
                        'Storytelling Podcasts' => null,
                        'Tech Podcasts'     => null,
                        'Mental Health Podcasts' => null,
                        'Interview Podcasts' => null,
                    ],
                ],
                'Horror' => [
                    'desc' => 'Horror across all formats.',
                    'tags' => [
                        'Horror Films'      => null,
                        'Horror Books'      => 'horror-books',
                        'Creepypasta'       => null,
                        'Horror Games'      => null,
                        'Psychological Horror' => null,
                        'Slasher'           => null,
                        'Ghost Stories'     => null,
                        'Paranormal'        => null,
                        'Cosmic Horror'     => null,
                        'Gothic Literature' => null,
                    ],
                ],
                'Comedy' => [
                    'desc' => 'Stand-up, sketch, improv, and sitcoms.',
                    'tags' => [
                        'Stand-Up Comedy'   => null,
                        'Sketch Comedy'     => null,
                        'Improv'            => null,
                        'Sitcoms'           => null,
                        'Dark Humor'        => null,
                        'Absurdist Comedy'  => null,
                        'Comedy Podcasts'   => null,
                        'Comedy Films'      => null,
                    ],
                ],
                'Fandoms' => [
                    'desc' => 'Dedicated fan communities for franchises.',
                    'tags' => [
                        'Marvel'            => null,
                        'DC'                => null,
                        'Star Wars'         => null,
                        'Harry Potter'      => null,
                        'Lord of the Rings' => null,
                        'Avatar'            => null,
                        'Stranger Things'   => null,
                        'Game of Thrones'   => null,
                        'Doctor Who'        => null,
                        'The Last of Us'    => null,
                        'K-Pop'             => 'k-pop',
                    ],
                ],
                // ── Phase 1 additions ──────────────────────────────────────────
                'New Anime Titles' => [
                    'desc' => 'Recent and essential anime series.',
                    'tags' => [
                        'Jujutsu Kaisen'    => null,
                        'Cowboy Bebop'      => null,
                        'Neon Genesis Evangelion' => null,
                        'Frieren'           => null,
                        'Dungeon Meshi'     => null,
                        'Bocchi the Rock'   => null,
                        'Oshi no Ko'        => null,
                        'Horror Anime'      => null,
                    ],
                ],
                'Animation & Cartoons' => [
                    'desc' => 'Western animation, cartoons, and animated series.',
                    'tags' => [
                        'Avatar: The Last Airbender' => null,
                        'Gravity Falls'     => null,
                        'Adventure Time'    => null,
                        'Rick and Morty'    => null,
                        'Futurama'          => null,
                        'Arcane'            => null,
                        'Invincible'        => null,
                        'The Boys'          => null,
                    ],
                ],
                'Prestige TV & Classics' => [
                    'desc' => 'Acclaimed drama series and television classics.',
                    'tags' => [
                        'Breaking Bad'      => null,
                        'The Wire'          => null,
                        'Sherlock'          => null,
                    ],
                ],
                'Webtoons & Digital Comics' => [
                    'desc' => 'Webtoons, manhwa, light novels, and visual media.',
                    'tags' => [
                        'Webtoons'          => null,
                        'Manhwa'            => null,
                        'Light Novels'      => null,
                        'Visual Novels'     => null,
                        'Otome Games'       => null,
                        'Dating Sims'       => null,
                        'Webcomics'         => null,
                    ],
                ],
                'Video & Film Culture' => [
                    'desc' => 'Film criticism, video essays, and online video culture.',
                    'tags' => [
                        'Film Noir'         => null,
                        'Criterion Collection' => null,
                        'Video Essays'      => null,
                        'Mukbang'           => null,
                    ],
                ],
            ],
        ],

        // ── 4. Books & Stories ────────────────────────────────────────────────
        'Books & Stories' => [
            'desc' => 'Reading, writing, and storytelling communities.',
            'subcategories' => [
                'Fantasy' => [
                    'desc' => 'Magical worlds and epic quests.',
                    'tags' => [
                        'Fantasy Books'     => 'fantasy-books',
                        'High Fantasy'      => null,
                        'Urban Fantasy'     => null,
                        'Dark Fantasy'      => null,
                        'Cozy Fantasy'      => null,
                        'YA Fantasy'        => null,
                        'Fae & Fairytale'   => null,
                        'Sword & Sorcery'   => null,
                        'Epic Fantasy'      => null,
                    ],
                ],
                'Sci-Fi' => [
                    'desc' => 'Science fiction, space opera, and speculative fiction.',
                    'tags' => [
                        'Sci-Fi Books'      => 'sci-fi-books',
                        'Hard Sci-Fi'       => null,
                        'Space Opera'       => null,
                        'Cyberpunk Books'   => null,
                        'Solarpunk'         => null,
                        'Dystopia'          => null,
                        'Time Travel Books' => null,
                        'First Contact'     => null,
                        'Military Sci-Fi'   => null,
                    ],
                ],
                'Romance' => [
                    'desc' => 'Love stories of all kinds.',
                    'tags' => [
                        'Romance Books'     => 'romance-books',
                        'Contemporary Romance' => null,
                        'Historical Romance' => null,
                        'Paranormal Romance' => null,
                        'Romantic Comedy'   => null,
                        'Dark Romance'      => null,
                        'Slow Burn'         => null,
                        'Queer Romance'     => null,
                        'Cozy Romance'      => null,
                    ],
                ],
                'Mystery & Thriller' => [
                    'desc' => 'Whodunits, noir, and psychological suspense.',
                    'tags' => [
                        'Mystery Books'     => 'mystery-books',
                        'Cozy Mystery'      => null,
                        'Crime Thriller'    => null,
                        'Psychological Thriller' => null,
                        'True Crime Books'  => null,
                        'Nordic Noir'       => null,
                        'Heist'             => null,
                        'Whodunit'          => null,
                    ],
                ],
                'Horror Books' => [
                    'desc' => 'Scary books and dark fiction.',
                    'tags' => [
                        'Horror Books'      => null,
                        'Cosmic Horror'     => null,
                        'Supernatural Horror' => null,
                        'Folk Horror'       => null,
                        'Gothic Literature' => null,
                        'Horror Short Stories' => null,
                    ],
                ],
                'Nonfiction' => [
                    'desc' => 'History, science, memoir, and essays.',
                    'tags' => [
                        'Nonfiction'        => 'nonfiction',
                        'History Books'     => null,
                        'Science Books'     => null,
                        'Biography'         => null,
                        'Memoir'            => null,
                        'True Crime'        => null,
                        'Nature Writing'    => null,
                        'Essays'            => null,
                        'Popular Science'   => null,
                        'Audiobooks'        => 'audiobooks',
                    ],
                ],
                'Book Clubs' => [
                    'desc' => 'Reading together and discussing books.',
                    'tags' => [
                        'Book Club'         => 'book-club',
                        'Online Book Clubs' => null,
                        'Buddy Reads'       => null,
                        'Reading Challenges' => null,
                        'Annual Reading Goals' => null,
                        'DNF Discussion'    => null,
                        'Goodreads'         => null,
                    ],
                ],
                'Fanfiction' => [
                    'desc' => 'Fan-written stories and creative extensions.',
                    'tags' => [
                        'Fanfiction'        => null,
                        'Fan Fiction Writing' => 'fan-fiction-writing',
                        'AO3'               => null,
                        'Ship Discussion'   => null,
                        'Alternate Universe' => null,
                        'Crossovers'        => null,
                        'Longfic'           => null,
                        'Oneshots'          => null,
                    ],
                ],
                'Graphic Novels & Comics' => [
                    'desc' => 'Manga, comics, and visual storytelling.',
                    'tags' => [
                        'Graphic Memoirs'   => null,
                        'Superhero Comics'  => null,
                    ],
                ],
                'Literary Fiction' => [
                    'desc' => 'Character-driven, literary storytelling.',
                    'tags' => [
                        'Essay Collections' => null,
                        'Autofiction'       => null,
                        'Magical Realism'   => null,
                    ],
                ],
            ],
        ],

        // ── 5. Music ──────────────────────────────────────────────────────────
        'Music' => [
            'desc' => 'Listening, playing, discovering, and creating music.',
            'subcategories' => [
                'Rock' => [
                    'desc' => 'Rock in all its forms.',
                    'tags' => [
                        'Classic Rock'      => null,
                        'Alternative Rock'  => null,
                        'Indie Rock'        => null,
                        'Indie Music'       => 'indie-music',
                        'Punk Rock'         => null,
                        'Post-Rock'         => null,
                        'Grunge'            => null,
                        'Progressive Rock'  => null,
                        'Psychedelic Rock'  => null,
                        'Folk Rock'         => null,
                    ],
                ],
                'Metal' => [
                    'desc' => 'Heavy metal and its many subgenres.',
                    'tags' => [
                        'Metal'             => 'metal',
                        'Heavy Metal'       => null,
                        'Death Metal'       => null,
                        'Black Metal'       => null,
                        'Doom Metal'        => null,
                        'Thrash Metal'      => null,
                        'Metalcore'         => null,
                        'Power Metal'       => null,
                        'Symphonic Metal'   => null,
                        'Nu-Metal'          => null,
                        'Stoner Metal'      => null,
                    ],
                ],
                'Hip-Hop' => [
                    'desc' => 'Rap, trap, boom bap, and related genres.',
                    'tags' => [
                        'Hip-Hop'           => 'hip-hop',
                        'Rap'               => null,
                        'Old School Hip-Hop' => null,
                        'Trap'              => null,
                        'Lo-fi Hip-Hop'     => null,
                        'Boom Bap'          => null,
                        'Underground Hip-Hop' => null,
                        'UK Rap'            => null,
                    ],
                ],
                'Electronic' => [
                    'desc' => 'Dance music, ambient electronic, and synthesized sounds.',
                    'tags' => [
                        'Electronic Music'  => null,
                        'House'             => null,
                        'Techno'            => null,
                        'Drum & Bass'       => null,
                        'Dubstep'           => null,
                        'Ambient Electronic' => null,
                        'Synthwave'         => null,
                        'Vaporwave'         => null,
                        'Chillwave'         => null,
                        'Future Bass'       => null,
                    ],
                ],
                'Classical' => [
                    'desc' => 'Orchestral, opera, chamber, and contemporary classical.',
                    'tags' => [
                        'Classical Music'   => 'classical-music',
                        'Orchestral Music'  => null,
                        'Opera'             => null,
                        'Chamber Music'     => null,
                        'Film Scores'       => null,
                        'Baroque'           => null,
                        'Minimalist Classical' => null,
                        'Jazz'              => 'jazz',
                    ],
                ],
                'Lo-fi' => [
                    'desc' => 'Chill beats, study music, and bedroom pop.',
                    'tags' => [
                        'Lo-fi'             => 'lo-fi',
                        'Lo-fi Hip-Hop'     => null,
                        'Study Music'       => null,
                        'Chill Beats'       => null,
                        'Ambient Lo-fi'     => null,
                        'Bedroom Pop'       => null,
                    ],
                ],
                'Instruments' => [
                    'desc' => 'Playing and learning instruments.',
                    'tags' => [
                        'Guitar'            => 'guitar',
                        'Piano'             => 'piano',
                        'Drums'             => 'drums',
                        'Bass Guitar'       => 'bass-guitar',
                        'Violin'            => 'violin',
                        'Flute'             => 'flute',
                        'Ukulele'           => null,
                        'Singing'           => 'singing',
                        'Music'             => 'music',
                    ],
                ],
                'Music Discovery' => [
                    'desc' => 'Finding new music, niche genres, and record collecting.',
                    'tags' => [
                        'Vinyl Collecting'  => 'vinyl-collecting',
                        'Concert-Going'     => 'concert-going',
                        'New Artist Discovery' => null,
                        'Niche Genres'      => null,
                        'Music Recommendations' => null,
                        'Record Collecting' => null,
                    ],
                ],
                'Music Genres' => [
                    'desc' => 'Musical styles and genres to explore.',
                    'tags' => [
                        'Folk'              => null,
                        'Country'           => null,
                        'R&B'               => null,
                        'Soul'              => null,
                        'Funk'              => null,
                        'Reggae'            => null,
                        'Ska'               => null,
                        'Punk'              => null,
                        'J-Pop'             => null,
                        'Bossa Nova'        => null,
                        'Flamenco'          => null,
                        'Gospel'            => null,
                        'Bluegrass'         => null,
                        'Afrobeats'         => null,
                    ],
                ],
            ],
        ],

        // ── 6. Tech & Internet ────────────────────────────────────────────────
        'Tech & Internet' => [
            'desc' => 'Programming, hardware, AI, and the digital world.',
            'subcategories' => [
                'Programming' => [
                    'desc' => 'Coding languages and software development.',
                    'tags' => [
                        'Programming'       => 'programming',
                        'Python'            => null,
                        'JavaScript'        => null,
                        'TypeScript'        => null,
                        'Rust'              => null,
                        'Go'                => null,
                        'C++'               => null,
                        'PHP'               => null,
                        'Algorithms'        => null,
                        'Data Structures'   => null,
                        'Open Source'       => 'open-source',
                        'Data Science'      => 'data-science',
                    ],
                ],
                'Web Development' => [
                    'desc' => 'Frontend, backend, and full-stack web.',
                    'tags' => [
                        'Web Development'   => 'web-development',
                        'Frontend'          => null,
                        'Backend'           => null,
                        'React'             => null,
                        'Vue'               => null,
                        'Svelte'            => null,
                        'Node.js'           => null,
                        'Django'            => null,
                        'Laravel'           => null,
                        'CSS'               => null,
                        'Web Accessibility' => null,
                    ],
                ],
                'AI & Machine Learning' => [
                    'desc' => 'LLMs, generative AI, and ML engineering.',
                    'tags' => [
                        'AI & Machine Learning' => 'ai-machine-learning',
                        'Machine Learning'  => null,
                        'LLMs'              => null,
                        'Generative AI'     => null,
                        'Computer Vision'   => null,
                        'NLP'               => null,
                        'AI Art'            => null,
                        'Prompt Engineering' => null,
                        'AI Ethics'         => null,
                    ],
                ],
                'Linux' => [
                    'desc' => 'Linux distros, terminals, and open source.',
                    'tags' => [
                        'Linux'             => 'linux',
                        'Ubuntu'            => null,
                        'Arch Linux'        => null,
                        'Debian'            => null,
                        'Fedora'            => null,
                        'NixOS'             => null,
                        'Tiling Window Managers' => null,
                        'Terminal Tools'    => null,
                        'Shell Scripting'   => null,
                    ],
                ],
                'Homelab' => [
                    'desc' => 'Self-hosting, home servers, and networks.',
                    'tags' => [
                        'Homelab'           => 'homelab',
                        'Self-Hosting'      => null,
                        'Raspberry Pi'      => 'raspberry-pi',
                        'Docker'            => 'docker',
                        'Proxmox'           => null,
                        'TrueNAS'           => null,
                        'Home Servers'      => null,
                        'Smart Home'        => null,
                        'Home Automation'   => null,
                        'NAS'               => null,
                    ],
                ],
                'Cybersecurity' => [
                    'desc' => 'Security, hacking, and privacy.',
                    'tags' => [
                        'Cybersecurity'     => 'cybersecurity',
                        'Ethical Hacking'   => null,
                        'CTF Challenges'    => null,
                        'Penetration Testing' => null,
                        'Bug Bounty'        => null,
                        'OSINT'             => null,
                        'Privacy'           => null,
                        'Network Security'  => null,
                    ],
                ],
                'Hardware' => [
                    'desc' => 'PC building, keyboards, and electronics.',
                    'tags' => [
                        'PC Building'       => 'pc-building',
                        'Mechanical Keyboards' => 'mechanical-keyboards',
                        '3D Printing'       => '3d-printing',
                        'Electronics'       => 'electronics',
                        'Arduino'           => 'arduino',
                        'Ham Radio'         => 'ham-radio',
                        'Custom Keyboards'  => null,
                        'GPU & CPU'         => null,
                        'Soldering'         => null,
                    ],
                ],
                'Maker Projects' => [
                    'desc' => 'DIY electronics, robotics, and hands-on builds.',
                    'tags' => [
                        'Retro Computing'   => 'retro-computing',
                        'DIY Electronics'   => null,
                        'Robotics'          => null,
                        'CNC Machining'     => null,
                        'Laser Cutting'     => null,
                        'RC Cars'           => 'rc-cars',
                        'Drones'            => 'drones',
                    ],
                ],
            ],
        ],

        // ── 7. Outdoors & Nature ──────────────────────────────────────────────
        'Outdoors & Nature' => [
            'desc' => 'The outdoors, wildlife, gardening, and the natural world.',
            'subcategories' => [
                'Fishing' => [
                    'desc' => 'Freshwater, saltwater, and fly fishing.',
                    'tags' => [
                        'Fishing'           => null,
                        'Fly Fishing'       => null,
                        'Sea Fishing'       => null,
                        'Ice Fishing'       => null,
                        'Bass Fishing'      => null,
                        'Catch and Release' => null,
                    ],
                ],
                'Hiking' => [
                    'desc' => 'Trail hiking, backpacking, and long-distance walks.',
                    'tags' => [
                        'Hiking'            => 'hiking',
                        'Backpacking'       => null,
                        'Long-Distance Trails' => null,
                        'Mountain Hiking'   => null,
                        'Trail Running'     => null,
                        'Leave No Trace'    => null,
                    ],
                ],
                'Camping' => [
                    'desc' => 'Camping, bushcraft, and outdoor overnights.',
                    'tags' => [
                        'Camping'           => 'camping',
                        'Hammock Camping'   => null,
                        'Ultralight Camping' => null,
                        'Winter Camping'    => null,
                        'Bushcraft'         => null,
                        'Campfire Cooking'  => null,
                        'Urban Exploration' => 'urban-exploration',
                    ],
                ],
                'Gardening' => [
                    'desc' => 'Growing plants, indoors and out.',
                    'tags' => [
                        'Gardening'         => 'gardening',
                        'Vegetable Gardening' => null,
                        'Flower Gardening'  => null,
                        'Indoor Plants'     => 'indoor-plants',
                        'Succulents'        => null,
                        'Bonsai'            => null,
                        'Permaculture'      => null,
                        'Composting'        => null,
                        'Urban Gardening'   => null,
                        'Terrarium Building' => 'terrarium-building',
                    ],
                ],
                'Birdwatching' => [
                    'desc' => 'Birding, birding travel, and bird photography.',
                    'tags' => [
                        'Birdwatching'      => 'birdwatching',
                        'Backyard Birds'    => null,
                        'Bird Photography'  => null,
                        'Rare Birds'        => null,
                        'Migratory Birds'   => null,
                        'Owl Watching'      => null,
                        'Rock Collecting'   => 'rock-collecting',
                    ],
                ],
                'Aquariums' => [
                    'desc' => 'Fish keeping, planted tanks, and reef aquariums.',
                    'tags' => [
                        'Aquariums'         => 'aquariums',
                        'Freshwater Aquariums' => null,
                        'Saltwater Aquariums' => null,
                        'Planted Tanks'     => null,
                        'Reef Tanks'        => null,
                        'Nano Tanks'        => null,
                        'Aquascaping'       => null,
                        'Shrimp Keeping'    => null,
                    ],
                ],
                'Pets' => [
                    'desc' => 'Dogs, cats, and all companion animals.',
                    'tags' => [
                        'Dogs'              => null,
                        'Cats'              => null,
                        'Rabbits'           => null,
                        'Guinea Pigs'       => null,
                        'Reptiles'          => null,
                        'Birds as Pets'     => null,
                        'Ferrets'           => null,
                        'Exotic Pets'       => null,
                        'Pet Photography'   => null,
                        'Pet Training'      => null,
                        'Beekeeping'        => 'beekeeping',
                    ],
                ],
                'Weather & Skywatching' => [
                    'desc' => 'Astronomy, stargazing, and weather phenomena.',
                    'tags' => [
                        'Astronomy'         => 'astronomy',
                        'Stargazing'        => 'stargazing',
                        'Astrophotography'  => null,
                        'Storm Chasing'     => 'storm-chasing',
                        'Aurora Borealis'   => null,
                        'Meteor Showers'    => null,
                        'Snorkeling'        => 'snorkeling',
                        'Foraging'          => 'foraging',
                        'Geocaching'        => 'geocaching',
                    ],
                ],
                'Water Activities' => [
                    'desc' => 'Swimming, paddling, and life on the water.',
                    'tags' => [
                        'Wild Swimming'     => null,
                        'Kayaking'          => null,
                        'Canoeing'          => null,
                        'Surfing'           => null,
                        'Paddleboarding'    => null,
                        'Freediving'        => null,
                    ],
                ],
                'Winter Activities' => [
                    'desc' => 'Snow sports and cold-weather fun.',
                    'tags' => [
                        'Skiing'            => null,
                        'Snowboarding'      => null,
                        'Ice Skating'       => null,
                        'Snowshoeing'       => null,
                    ],
                ],
            ],
        ],

        // ── 8. Lifestyle & Hobbies ────────────────────────────────────────────
        'Lifestyle & Hobbies' => [
            'desc' => 'Food, fitness, collecting, and everyday passions.',
            'subcategories' => [
                'Cooking & Food' => [
                    'desc' => 'Home cooking, baking, and food culture.',
                    'tags' => [
                        'Cooking'           => 'cooking',
                        'Baking'            => 'baking',
                        'Sourdough Bread'   => 'sourdough-bread',
                        'Meal Prep'         => null,
                        'Veganism'          => 'veganism',
                        'Vegetarianism'     => null,
                        'International Cuisine' => null,
                        'Coffee'            => 'coffee',
                        'Tea'               => 'tea',
                        'Craft Beer'        => 'craft-beer',
                        'Whiskey'           => 'whiskey',
                        'Ramen'             => 'ramen',
                    ],
                ],
                'Fitness' => [
                    'desc' => 'Exercise, sport, and physical wellbeing.',
                    'tags' => [
                        'Fitness'           => 'fitness',
                        'Weightlifting'     => null,
                        'Running'           => 'running',
                        'Cycling'           => 'cycling',
                        'Yoga'              => 'yoga',
                        'Meditation'        => 'meditation',
                        'Pilates'           => null,
                        'Martial Arts'      => null,
                        'Swimming'          => null,
                        'Climbing'          => 'climbing',
                        'Skateboarding'     => 'skateboarding',
                    ],
                ],
                'Journaling & Stationery' => [
                    'desc' => 'Writing, planning, and analog tools.',
                    'tags' => [
                        'Journaling'        => 'journaling',
                        'Bullet Journaling' => 'bullet-journaling',
                        'Art Journaling'    => null,
                        'Gratitude Journaling' => null,
                        'Stationery'        => null,
                        'Washi Tape'        => null,
                        'Fountain Pens'     => null,
                        'Planners'          => null,
                    ],
                ],
                'Collecting' => [
                    'desc' => 'Accumulating, curating, and trading things.',
                    'tags' => [
                        'LEGO'              => 'lego',
                        'Vintage Collecting' => 'vintage-collecting',
                        'Coin Collecting'   => 'coin-collecting',
                        'Stamp Collecting'  => 'stamp-collecting',
                        'Puzzles'           => 'puzzles',
                        'Model Trains'      => 'model-trains',
                        'Trading Cards'     => null,
                        'Action Figures'    => null,
                        'Comic Books'       => 'comic-books',
                    ],
                ],
                'Cars & Vehicles' => [
                    'desc' => 'Cars, motorcycles, and motorsport.',
                    'tags' => [
                        'Classic Cars'      => null,
                        'JDM'               => null,
                        'Tuning'            => null,
                        'Off-Road'          => null,
                        'Electric Vehicles' => null,
                        'Car Photography'   => null,
                        'Motorsport'        => null,
                        'Road Trips'        => null,
                    ],
                ],
                'Home & DIY' => [
                    'desc' => 'Home improvement, DIY projects, and interior design.',
                    'tags' => [
                        'Interior Design'   => null,
                        'Home Renovation'   => null,
                        'DIY Home'          => null,
                        'Minimalism'        => 'minimalism',
                        'Thrifting'         => 'thrifting',
                        'Sustainability'    => 'sustainability',
                        'Zero Waste'        => 'zero-waste',
                        'Upcycling'         => null,
                        'Home Organization' => null,
                        'Tiny Homes'        => null,
                        'Woodworking'       => 'woodworking',
                        'Magic Tricks'      => 'magic-tricks',
                        'Escape Rooms'      => 'escape-rooms',
                    ],
                ],
                'Board Games' => [
                    'desc' => 'Tabletop board and card games.',
                    'tags' => [
                        'Board Games'       => 'board-games',
                        'Eurogames'         => null,
                        'Cooperative Games' => null,
                        'Deck Builders'     => null,
                        'Worker Placement'  => null,
                        'Party Games'       => null,
                        'Wargames'          => null,
                        'Solo Board Games'  => null,
                    ],
                ],
                'Slow Living' => [
                    'desc' => 'Intentional, quiet, and gentle ways of living.',
                    'tags' => [
                        'Slow Living'       => null,
                        'Hygge'             => null,
                        'Cottagecore'       => null,
                        'Goblincore'        => null,
                        'Dark Academia'     => null,
                        'Simple Living'     => null,
                        'Intentional Living' => null,
                        'Digital Nomad'     => 'digital-nomad',
                    ],
                ],
            ],
        ],

        // ── 9. Learning & Skills ──────────────────────────────────────────────
        'Learning & Skills' => [
            'desc' => 'Languages, science, history, and personal growth.',
            'subcategories' => [
                'Languages' => [
                    'desc' => 'Language learning and linguistics.',
                    'tags' => [
                        'Language Learning' => null,
                        'Japanese'          => null,
                        'Spanish'           => null,
                        'French'            => null,
                        'German'            => null,
                        'Mandarin'          => null,
                        'Korean'            => null,
                        'Arabic'            => null,
                        'Italian'           => null,
                        'Latin'             => null,
                        'ASL'               => null,
                        'Linguistics'       => null,
                        'Etymology'         => null,
                        'Polyglot Community' => null,
                    ],
                ],
                'History' => [
                    'desc' => 'Ancient, medieval, modern, and local history.',
                    'tags' => [
                        'History'           => null,
                        'Ancient History'   => null,
                        'Medieval History'  => null,
                        'Modern History'    => null,
                        'Military History'  => null,
                        'Art History'       => null,
                        'World History'     => null,
                        'Archaeology'       => null,
                        'Mythology'         => null,
                    ],
                ],
                'Science' => [
                    'desc' => 'Physics, chemistry, biology, and more.',
                    'tags' => [
                        'Science'           => null,
                        'Physics'           => null,
                        'Chemistry'         => null,
                        'Biology'           => null,
                        'Neuroscience'      => null,
                        'Genetics'          => null,
                        'Environmental Science' => null,
                        'Science Communication' => null,
                    ],
                ],
                'Math' => [
                    'desc' => 'Mathematics from recreational to advanced.',
                    'tags' => [
                        'Mathematics'       => null,
                        'Statistics'        => null,
                        'Algebra'           => null,
                        'Calculus'          => null,
                        'Number Theory'     => null,
                        'Recreational Math' => null,
                        'Math Competitions' => null,
                    ],
                ],
                'Productivity & Note-Taking' => [
                    'desc' => 'Systems for thinking, working, and learning better.',
                    'tags' => [
                        'Productivity'      => null,
                        'Note-Taking'       => null,
                        'Zettelkasten'      => null,
                        'Pomodoro'          => null,
                        'Second Brain'      => null,
                        'Professional Development' => null,
                        'Online Courses'    => null,
                    ],
                ],
                'DIY Skills' => [
                    'desc' => 'Practical hands-on skills for everyday life.',
                    'tags' => [
                        'Woodworking'       => null,
                        'Metalworking'      => null,
                        'Sewing Skills'     => null,
                        'Car Repair'        => null,
                        'Computer Repair'   => null,
                        'First Aid'         => null,
                        'Survival Skills'   => null,
                    ],
                ],
                'Study Rooms' => [
                    'desc' => 'Accountability, focus sessions, and learning together.',
                    'tags' => [
                        'Study Together'    => null,
                        'Focus Sessions'    => null,
                        'Accountability Rooms' => null,
                        'Exam Prep'         => null,
                        'Dissertation Support' => null,
                        'Academic Writing'  => null,
                        'STEM Studying'     => null,
                    ],
                ],
                'Philosophy' => [
                    'desc' => 'Ethics, metaphysics, and big questions.',
                    'tags' => [
                        'Philosophy'        => null,
                        'Ethics'            => null,
                        'Metaphysics'       => null,
                        'Stoicism'          => null,
                        'Existentialism'    => null,
                        'Eastern Philosophy' => null,
                        'Philosophy of Mind' => null,
                        'Political Philosophy' => null,
                    ],
                ],
            ],
        ],

        // ── 10. Identity, Support & Shared Experiences ────────────────────────
        'Identity, Support & Shared Experiences' => [
            'desc' => 'Safe spaces for community, identity, and shared life experiences.',
            'subcategories' => [
                'Autism-friendly' => [
                    'desc' => 'Spaces built with autistic people in mind.',
                    'tags' => [
                        'Autism-friendly'   => 'autism-friendly',
                        'Autistic Community' => null,
                        'Special Interests' => null,
                        'Sensory Needs'     => null,
                        'Autistic Joy'      => null,
                        'AuDHD'             => null,
                        'Late Diagnosis'    => null,
                        'Autistic Adults'   => null,
                    ],
                ],
                'ADHD-friendly' => [
                    'desc' => 'Spaces that work with ADHD brains.',
                    'tags' => [
                        'ADHD-friendly'     => 'adhd-friendly',
                        'ADHD Community'    => null,
                        'Hyperfocus'        => null,
                        'Executive Function' => null,
                        'Time Blindness'    => null,
                        'ADHD Adults'       => null,
                        'Late Diagnosis ADHD' => null,
                    ],
                ],
                'Anxiety-friendly' => [
                    'desc' => 'Low-pressure spaces for people with anxiety.',
                    'tags' => [
                        'Anxiety-friendly'  => 'anxiety-friendly',
                        'Social anxiety-friendly' => 'social-anxiety-friendly',
                        'Social Anxiety'    => null,
                        'Generalized Anxiety' => null,
                        'OCD-friendly'      => null,
                        'Panic Disorder'    => null,
                        'Calm Spaces'       => null,
                    ],
                ],
                'Introvert-friendly' => [
                    'desc' => 'Spaces that respect introverts and quiet people.',
                    'tags' => [
                        'Introvert-friendly' => 'introvert-friendly',
                        'Introvert Community' => null,
                        'Alone Time'        => null,
                        'Social Battery'    => null,
                        'Quiet Spaces'      => null,
                        'Deep Conversations' => null,
                        'Listener-friendly' => 'listener-friendly',
                        'Sensitive Introvert' => null,
                    ],
                ],
                'Neurodivergent-friendly' => [
                    'desc' => 'Broad neurodiversity community and support.',
                    'tags' => [
                        'Neurodivergent-friendly' => 'neurodivergent-friendly',
                        'Neurodiversity'    => null,
                        'ND Community'      => null,
                        'Dyslexia'          => null,
                        'Dyspraxia'         => null,
                        'Tourette\'s'       => null,
                        'ND Pride'          => null,
                        'LGBTQ+ friendly'   => 'lgbtq-friendly',
                    ],
                ],
                'Chronic Illness-friendly' => [
                    'desc' => 'Spaces for people living with chronic illness or pain.',
                    'tags' => [
                        'Chronic illness-friendly' => 'chronic-illness-friendly',
                        'Chronic Pain'      => null,
                        'Fibromyalgia'      => null,
                        'ME/CFS'            => null,
                        'Lupus'             => null,
                        'Migraine'          => null,
                        'Invisible Illness' => null,
                        'Spoonie Community' => null,
                        'Rest & Recovery'   => null,
                    ],
                ],
                'Grief-friendly' => [
                    'desc' => 'Supportive spaces for those experiencing loss.',
                    'tags' => [
                        'Grief-friendly'    => 'grief-friendly',
                        'Grief Support'     => null,
                        'Pet Loss'          => null,
                        'Loss of a Loved One' => null,
                        'Anticipatory Grief' => null,
                        'Grief & Identity'  => null,
                        'New here'          => 'new-here',
                        'Late-night people' => 'late-night-people',
                    ],
                ],
                'Low-stimulation spaces' => [
                    'desc' => 'Calm, gentle spaces with no pressure.',
                    'tags' => [
                        'Low-stimulation spaces' => 'low-stimulation-spaces',
                        'Quiet'             => 'quiet',
                        'Casual'            => 'casual',
                        'Low-key'           => 'low-key',
                        'Chill'             => 'chill',
                        'Deep talks'        => 'deep-talks',
                        'Advice welcome'    => 'advice-welcome',
                        'Open to strangers' => 'open-to-strangers',
                        'Listener-friendly' => null,
                    ],
                ],
            ],
        ],

        // ── 11. Conversation Preferences ────────────────────────────────────────
        // Note: the 9 tags above under "Low-stimulation spaces" (Quiet, Casual,
        // Low-key, Chill, Deep talks, Advice welcome, Open to strangers,
        // Listener-friendly, Low-stimulation spaces) are ALSO part of this
        // category conceptually — they were relocated here from Identity,
        // Support & Shared Experiences > Low-stimulation spaces via a data fix
        // (Phase 6). Left undeclared here (rather than duplicated) to avoid
        // fighting that block on every reseed; run() relocates them by slug
        // after the main loop below, deterministically, regardless of array
        // order. Only the tags genuinely new to Conversation Preferences are
        // listed here.
        'Conversation Preferences' => [
            'desc' => 'How you like to talk — pace, depth, and style of conversation.',
            'subcategories' => [
                'Conversation Style' => [
                    'desc' => 'Preferences for how conversations feel and flow.',
                    'tags' => [
                        'Deep Dive Discussions' => null,
                        'Casual Chatting'       => null,
                        'Venting Welcome'       => null,
                        'No Advice Please'      => null,
                        'Listening Only'        => null,
                        'Slow Replies OK'       => null,
                        'Voice Chat Friendly'   => null,
                        'Text Only Please'      => null,
                        'Anonymous Sharing OK'  => null,
                        'Topic Hopping'         => null,
                        'Structured Discussion' => null,
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $this->command->info('Seeding CommonGrove taxonomy...');
        $this->command->newLine();

        $catOrder = 0;
        foreach (self::TAXONOMY as $categoryName => $categoryData) {
            $catOrder++;

            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name'        => $categoryName,
                    'slug'        => Str::slug($categoryName),
                    'description' => $categoryData['desc'],
                    'sort_order'  => $catOrder,
                    'is_active'   => true,
                ]
            );

            $this->command->info("  [{$catOrder}] {$categoryName}");

            $subcatOrder = 0;
            foreach ($categoryData['subcategories'] as $subcategoryName => $subcategoryData) {
                $subcatOrder++;

                // "Conversation Style" predates this seeder ever defining it
                // (created directly against the database, plain slug, no
                // category prefix) — special-cased so updateOrCreate matches
                // that real row instead of generating a compound slug and
                // creating a duplicate.
                $subcategorySlug = ($categoryName === 'Conversation Preferences' && $subcategoryName === 'Conversation Style')
                    ? 'conversation-style'
                    : Str::slug("{$categoryName}-{$subcategoryName}");

                $subcategory = Subcategory::updateOrCreate(
                    ['slug' => $subcategorySlug],
                    [
                        'category_id' => $category->id,
                        'name'        => $subcategoryName,
                        'slug'        => $subcategorySlug,
                        'description' => $subcategoryData['desc'],
                        'sort_order'  => $subcatOrder,
                        'is_active'   => true,
                    ]
                );

                // 'Low-stimulation spaces' holds vibe tags (Quiet, Casual, etc.) even
                // though its parent category is Identity/Support — give it the correct type.
                $type = match(true) {
                    $subcategoryName === 'Low-stimulation spaces'                 => 'vibe',
                    $categoryName    === 'Identity, Support & Shared Experiences' => 'shared_experience',
                    default                                                       => 'interest',
                };

                foreach ($subcategoryData['tags'] as $tagName => $slug) {
                    $slug = $slug ?? Str::slug($tagName);

                    Tag::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name'           => $tagName,
                            'slug'           => $slug,
                            'type'           => $type,
                            'category'       => $categoryName,
                            'category_id'    => $category->id,
                            'subcategory_id' => $subcategory->id,
                            'is_curated'     => true,
                            'is_approved'    => true,
                        ]
                    );
                }
            }
        }

        $this->flagSensitiveSubcategories();
        $this->relocateConversationStyleTags();

        $this->command->newLine();
        $this->command->info('Taxonomy complete:');
        $this->command->info('  Categories:    ' . Category::count());
        $this->command->info('  Subcategories: ' . Subcategory::count());
        $this->command->info('  Tags:          ' . Tag::count() . ' total (' . Tag::whereNotNull('category_id')->count() . ' mapped)');
    }

    /**
     * Flags every subcategory under "Identity, Support & Shared Experiences"
     * as sensitive (InterestPrivacyService reads this to keep those
     * interests out of match explanations). Previously this backfill lived
     * only in a migration that looked up the category by name — since
     * migrations run before seeders, that lookup found nothing on a fresh
     * database and silently no-op'd, leaving nothing flagged (Phase 6 bug
     * fix). Doing it here instead makes seeding self-sufficient regardless
     * of migration/seeder ordering.
     */
    private function flagSensitiveSubcategories(): void
    {
        $categoryId = Category::where('name', 'Identity, Support & Shared Experiences')->value('id');

        if ($categoryId === null) {
            return;
        }

        $count = Subcategory::where('category_id', $categoryId)->update(['is_sensitive' => true]);
        $this->command->info("  Flagged {$count} sensitive subcategories under Identity, Support & Shared Experiences.");
    }

    /**
     * Relocates the 9 tags originally defined under "Identity, Support &
     * Shared Experiences" > "Low-stimulation spaces" to "Conversation
     * Preferences" > "Conversation Style", matching a Phase 4/6 data fix
     * applied directly against the database. Matches by slug, run after the
     * main loop, so it wins regardless of array order — the alternative
     * (duplicating these tags under the new category above) would fight the
     * still-present original block on every reseed.
     */
    private function relocateConversationStyleTags(): void
    {
        $category    = Category::where('name', 'Conversation Preferences')->first();
        $subcategory = $category
            ? Subcategory::where('category_id', $category->id)->where('name', 'Conversation Style')->first()
            : null;

        if ($category === null || $subcategory === null) {
            return;
        }

        $slugs = [
            'low-stimulation-spaces', 'quiet', 'casual', 'low-key', 'chill',
            'deep-talks', 'advice-welcome', 'open-to-strangers', 'listener-friendly',
        ];

        $count = Tag::whereIn('slug', $slugs)->update([
            'category'       => 'Conversation Preferences',
            'category_id'    => $category->id,
            'subcategory_id' => $subcategory->id,
        ]);

        $this->command->info("  Relocated {$count} tags to Conversation Preferences > Conversation Style.");
    }
}
