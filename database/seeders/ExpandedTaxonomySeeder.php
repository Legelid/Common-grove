<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Phase: taxonomy expansion for thin categories (Books & Stories, Music,
 * Outdoors & Nature, Learning & Skills, Conversation Preferences).
 *
 * Several requested subcategory names don't exist verbatim in the current
 * taxonomy (e.g. "Genres", "Water Activities", "Winter Activities",
 * "Graphic Novels", "Literary Fiction", "Practical Skills", "Nature") — for
 * those, tags were placed under the closest existing subcategory on a
 * per-tag basis, or omitted entirely where nothing reasonable fit. See
 * UNMAPPED_NO_SUBCATEGORY below and the seeder's final report for exactly
 * what was skipped and why.
 *
 * Also omits several requested tags that are near-duplicates of tags
 * already in the taxonomy under a different exact name (e.g. "Sewing" vs.
 * the existing "Sewing Skills", "Concert Going" vs. "Concert-Going") — an
 * exact-name exists() check wouldn't catch these, so they're excluded here
 * by hand rather than inserted as confusing near-duplicates. See
 * SKIPPED_NEAR_DUPLICATES below.
 */
class ExpandedTaxonomySeeder extends Seeder
{
    /**
     * category name => [subcategory name => [tag names]].
     *
     * @var array<string, array<string, list<string>>>
     */
    private const TAGS = [
        'Books & Stories' => [
            'Fantasy'            => ['Mythology Retellings', 'Romantasy'],
            'Sci-Fi'             => ['Alternate History'],
            'Mystery & Thriller' => ['Historical Mystery'],
        ],
        'Music' => [
            'Rock'            => ['Emo', 'Shoegaze', 'Dream Pop'],
            'Electronic'      => ['Ambient', 'Hyperpop'],
            'Classical'       => ['Classical'],
            'Instruments'     => [
                'Music Production', 'DJing', 'Music Theory', 'Songwriting',
                'Choir', 'Band Practice', 'Learning an Instrument',
            ],
            'Music Discovery' => ['Music Journalism'],
        ],
        'Outdoors & Nature' => [
            'Hiking'                => ['Day Hiking', 'Peak Bagging', 'Urban Hiking', 'Nordic Walking'],
            'Birdwatching'          => ['Wildflower ID', 'Nature Journaling', 'Wildlife Photography'],
            'Weather & Skywatching' => ['Cloud Spotting'],
            'Gardening'             => ['Container Gardening', 'Propagation', 'Seed Saving', 'Rewilding'],
        ],
        'Learning & Skills' => [
            'Languages' => ['Sign Language', 'Conlang Creation', 'Translation'],
            'Science'   => ['Psychology', 'Ecology', 'Climate Science', 'Citizen Science'],
            'History'   => ['Local History', 'Oral History'],
            'DIY Skills' => [
                'Cooking from Scratch', 'Bread Baking', 'Fermentation', 'Electronics',
                'Leatherworking', 'Candle Making', 'Soap Making', 'Home Repair',
            ],
        ],
        'Conversation Preferences' => [
            'Conversation Style' => [
                'Deep Dive Discussions', 'Casual Chatting', 'Venting Welcome',
                'No Advice Please', 'Listening Only', 'Slow Replies OK',
                'Voice Chat Friendly', 'Text Only Please', 'Anonymous Sharing OK',
                'Topic Hopping', 'Structured Discussion',
            ],
        ],
    ];

    /**
     * Requested tags with no existing subcategory reasonable enough to hold
     * them — reported as skipped rather than forced into a poor-fit home.
     *
     * @var array<string, list<string>>
     */
    private const UNMAPPED_NO_SUBCATEGORY = [
        'Books & Stories: Graphic Novels' => [
            'Manga', 'Manhwa', 'Comic Books', 'Webcomics', 'Graphic Memoirs', 'Superhero Comics',
        ],
        'Books & Stories: Literary Fiction' => [
            'Short Stories', 'Essay Collections', 'Autofiction', 'Magical Realism',
        ],
        'Outdoors & Nature: Water Activities' => [
            'Wild Swimming', 'Kayaking', 'Canoeing', 'Surfing', 'Paddleboarding', 'Freediving',
        ],
        'Outdoors & Nature: Winter Activities' => [
            'Skiing', 'Snowboarding', 'Ice Skating', 'Snowshoeing',
        ],
        'Music: Genres (no generic genre bucket exists)' => [
            'Jazz', 'Folk', 'Country', 'R&B', 'Soul', 'Funk', 'Reggae', 'Ska',
            'K-Pop', 'J-Pop', 'Bossa Nova', 'Flamenco', 'Gospel', 'Bluegrass', 'Afrobeats',
        ],
    ];

    /**
     * Requested tags deliberately excluded as near-duplicates of an
     * already-existing tag under a different exact name.
     *
     * @var array<string, string> requested name => existing tag it duplicates
     */
    private const SKIPPED_NEAR_DUPLICATES = [
        'Concert Going'      => 'Concert-Going',
        'Vegetable Growing'  => 'Vegetable Gardening',
        'Houseplants'        => 'Indoor Plants',
        'Storm Watching'     => 'Storm Chasing',
        'Archaeological Digs' => 'Archaeology',
        'Sewing'             => 'Sewing Skills',
        'Car Maintenance'    => 'Car Repair',
        'Cyberpunk'          => 'Cyberpunk Books',
        'Time Travel'        => 'Time Travel Books',
        'Cozy Mysteries'     => 'Cozy Mystery',
        'True Crime'         => 'True Crime Books',
    ];

    public function run(): void
    {
        $addedByCategory   = [];
        $existedByCategory = [];

        foreach (self::TAGS as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->first();

            if ($category === null) {
                $this->command?->error("Category not found, skipping entirely: {$categoryName}");
                continue;
            }

            $addedByCategory[$categoryName]   ??= 0;
            $existedByCategory[$categoryName] ??= 0;

            foreach ($subcategories as $subcategoryName => $tagNames) {
                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryName)
                    ->first();

                if ($subcategory === null) {
                    $this->command?->error("Subcategory not found, skipping its tags: {$categoryName} > {$subcategoryName}");
                    continue;
                }

                foreach ($tagNames as $name) {
                    if (Tag::where('name', $name)->exists()) {
                        $existedByCategory[$categoryName]++;
                        continue;
                    }

                    Tag::create([
                        'name'           => $name,
                        'slug'           => Str::slug($name),
                        'type'           => 'interest',
                        'source'         => 'curated',
                        'category'       => $categoryName,
                        'category_id'    => $category->id,
                        'subcategory_id' => $subcategory->id,
                        'is_curated'     => true,
                        'is_approved'    => true,
                        'usage_count'    => 0,
                    ]);

                    $addedByCategory[$categoryName]++;
                }
            }
        }

        $this->command?->info('');
        $this->command?->info('Expanded taxonomy seeding complete.');
        $this->command?->info('');
        $this->command?->info('Added per category:');
        foreach ($addedByCategory as $categoryName => $count) {
            $this->command?->info("  {$categoryName}: {$count} added, {$existedByCategory[$categoryName]} already existed");
        }

        $skippedNoSubcategory = array_sum(array_map('count', self::UNMAPPED_NO_SUBCATEGORY));
        $skippedNearDuplicate = count(self::SKIPPED_NEAR_DUPLICATES);

        $this->command?->info('');
        $this->command?->info("Requested tags skipped — no matching subcategory: {$skippedNoSubcategory}");
        foreach (self::UNMAPPED_NO_SUBCATEGORY as $label => $names) {
            $this->command?->info('  ' . $label . ': ' . implode(', ', $names));
        }

        $this->command?->info('');
        $this->command?->info("Requested tags skipped — near-duplicate of an existing tag: {$skippedNearDuplicate}");
        foreach (self::SKIPPED_NEAR_DUPLICATES as $requested => $existing) {
            $this->command?->info("  \"{$requested}\" ~ existing \"{$existing}\"");
        }

        $this->command?->info('');
        $this->command?->info('Final tag count per category:');
        foreach (array_keys(self::TAGS) as $categoryName) {
            $category = Category::where('name', $categoryName)->first();
            $total    = $category ? Tag::where('category_id', $category->id)->count() : 0;
            $this->command?->info("  {$categoryName}: {$total}");
        }

        $this->command?->info('');
        $this->command?->info('Total tags in taxonomy: ' . Tag::count());
    }
}
