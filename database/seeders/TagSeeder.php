<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Gaming' => [
                'OSRS',
                'Dungeons & Dragons',
                'Minecraft',
                'Elden Ring',
                'Pokémon',
                'Valorant',
                'League of Legends',
                'Final Fantasy',
                'Animal Crossing',
                'Stardew Valley',
                'Hollow Knight',
                'Dead by Daylight',
                'Fortnite',
                'Apex Legends',
                'World of Warcraft',
                'Halo',
                'Destiny',
                'Path of Exile',
                'RuneScape',
                'Tabletop RPG',
                'Board Games',
                'Card Games',
                'Chess',
                'Warhammer',
                'Cyberpunk 2077',
                'The Witcher',
                'Dark Souls',
                'Bloodborne',
                'The Legend of Zelda',
                'Mario',
                'Kirby',
                'Fire Emblem',
                'Splatoon',
                'Among Us',
                'Genshin Impact',
                'Honkai: Star Rail',
                'Overwatch',
                'Counter-Strike',
                'Dota 2',
                'Diablo',
                'Magic: The Gathering',
                'Hearthstone',
                'Guild Wars 2',
                'Elder Scrolls Online',
            ],

            'Creative' => [
                'Drawing',
                'Digital Art',
                'Painting',
                'Photography',
                'Writing',
                'Fanfiction',
                'Worldbuilding',
                'Cosplay',
                'Knitting',
                'Crocheting',
                'Embroidery',
                'Sewing',
                'Woodworking',
                'Sculpture',
                'Ceramics',
                'Origami',
                'Pixel Art',
                'Motion Graphics',
                'Graphic Design',
                'Fashion Design',
                'Printmaking',
                'Calligraphy',
                'Leatherworking',
                'Jewelry Making',
                'Candle Making',
                'Soap Making',
            ],

            'Music' => [
                'Guitar',
                'Piano',
                'Drums',
                'Singing',
                'Music Production',
                'Lo-fi',
                'Jazz',
                'Metal',
                'Classical Music',
                'K-Pop',
                'Anime OSTs',
                'Vinyl Collecting',
                'Bass Guitar',
                'Violin',
                'Flute',
                'Electronic Music',
                'Hip-Hop',
                'Indie Music',
                'Music Theory',
                'Concert-Going',
            ],

            'Tech' => [
                'Programming',
                'Linux',
                'Homelab',
                'Cybersecurity',
                '3D Printing',
                'Electronics',
                'Mechanical Keyboards',
                'PC Building',
                'Game Development',
                'Web Development',
                'Open Source',
                'Data Science',
                'AI & Machine Learning',
                'Retro Computing',
                'Raspberry Pi',
                'Arduino',
                'Docker',
                'Ham Radio',
            ],

            'Anime & Manga' => [
                'Anime',
                'Manga',
                'One Piece',
                'Attack on Titan',
                'My Hero Academia',
                'Studio Ghibli',
                'Demon Slayer',
                'Naruto',
                'Bleach',
                'Dragon Ball',
                "JoJo's Bizarre Adventure",
                'Hunter x Hunter',
                'Fullmetal Alchemist',
                'Sword Art Online',
                'Re:Zero',
                'Overlord',
            ],

            'Books & Writing' => [
                'Fantasy Books',
                'Sci-Fi Books',
                'Horror Books',
                'Book Club',
                'Poetry',
                'Comic Books',
                'Graphic Novels',
                'Mystery Books',
                'Romance Books',
                'Nonfiction',
                'Audiobooks',
                'Fan Fiction Writing',
            ],

            'Film & TV' => [
                'Horror Films',
                'Sci-Fi Films',
                'Indie Films',
                'Documentary',
                'True Crime',
                'Reality TV',
                'Animation',
                'Action Films',
                'Comedy Films',
                'Foreign Films',
                'Anime Films',
                'TV Drama',
            ],

            'Nature & Outdoors' => [
                'Hiking',
                'Birdwatching',
                'Gardening',
                'Astronomy',
                'Camping',
                'Foraging',
                'Urban Exploration',
                'Rock Collecting',
                'Beekeeping',
                'Storm Chasing',
                'Snorkeling',
                'Stargazing',
            ],

            'Food & Drink' => [
                'Cooking',
                'Baking',
                'Coffee',
                'Tea',
                'Cocktails',
                'Food Photography',
                'Veganism',
                'Cheese',
                'Whiskey',
                'Craft Beer',
                'Ramen',
                'Sourdough Bread',
            ],

            'Lifestyle' => [
                'Meditation',
                'Yoga',
                'Fitness',
                'Running',
                'Cycling',
                'Journaling',
                'Minimalism',
                'Thrifting',
                'Bullet Journaling',
                'Sustainability',
                'Zero Waste',
                'Indoor Plants',
                'Digital Nomad',
            ],

            'Other Hobbies' => [
                'LEGO',
                'Puzzles',
                'Model Trains',
                'Vintage Collecting',
                'Coin Collecting',
                'Stamp Collecting',
                'Skateboarding',
                'Climbing',
                'Magic Tricks',
                'RC Cars',
                'Drones',
                'Escape Rooms',
                'Geocaching',
                'Aquariums',
                'Terrarium Building',
            ],
        ];

        foreach ($categories as $category => $names) {
            foreach ($names as $name) {
                Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name'        => $name,
                        'slug'        => Str::slug($name),
                        'type'        => 'interest',
                        'category'    => $category,
                        'is_curated'  => true,
                        'is_approved' => true,
                        'usage_count' => 0,
                    ]
                );
            }
        }

        // Ensure existing rows without a type get one
        Tag::whereNull('type')->orWhere('type', '')->update(['type' => 'interest']);

        // ── Shared experience tags ──────────────────────────────────────────
        $sharedExperiences = [
            'Autism-friendly',
            'ADHD-friendly',
            'Anxiety-friendly',
            'Introvert-friendly',
            'Neurodivergent-friendly',
            'Social anxiety-friendly',
            'Chronic illness-friendly',
            'Grief-friendly',
            'LGBTQ+ friendly',
            'Low-stimulation spaces',
            'Late-night people',
            'New here',
        ];

        foreach ($sharedExperiences as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'type'        => 'shared_experience',
                    'category'    => 'Shared Experience',
                    'is_curated'  => true,
                    'is_approved' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // ── Room vibe tags ──────────────────────────────────────────────────
        $vibes = [
            'Quiet',
            'Casual',
            'Deep talks',
            'Advice welcome',
            'Chill',
            'Listener-friendly',
            'Open to strangers',
            'Low-key',
        ];

        foreach ($vibes as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'type'        => 'vibe',
                    'category'    => 'Room Vibe',
                    'is_curated'  => true,
                    'is_approved' => true,
                    'usage_count' => 0,
                ]
            );
        }

        $this->command->info('Tags seeded: ' . Tag::count() . ' total.');
    }
}
