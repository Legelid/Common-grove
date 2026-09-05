<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Follow-up to ExpandedTaxonomySeeder: creates the 5 subcategories that
 * didn't exist yet (see that seeder's UNMAPPED_NO_SUBCATEGORY constant) and
 * inserts the tags that were skipped for lack of a home.
 *
 * Note: the real column on `subcategories` is `sort_order`, not
 * `display_order` — used correctly below.
 */
class MissingSubcategoriesSeeder extends Seeder
{
    /**
     * category name => [subcategory name => [tag names]].
     *
     * @var array<string, array<string, list<string>>>
     */
    private const DATA = [
        'Books & Stories' => [
            'Graphic Novels & Comics' => [
                'Manga', 'Manhwa', 'Comic Books', 'Webcomics', 'Graphic Memoirs', 'Superhero Comics',
            ],
            'Literary Fiction' => [
                'Short Stories', 'Essay Collections', 'Autofiction', 'Magical Realism',
            ],
        ],
        'Music' => [
            'Music Genres' => [
                'Jazz', 'Classical', 'Folk', 'Country', 'R&B', 'Soul', 'Funk', 'Reggae',
                'Ska', 'Metal', 'Punk', 'Emo', 'Shoegaze', 'Dream Pop', 'Synthwave',
                'Vaporwave', 'Hyperpop', 'K-Pop', 'J-Pop', 'Bossa Nova', 'Flamenco',
                'Gospel', 'Bluegrass', 'Afrobeats',
            ],
        ],
        'Outdoors & Nature' => [
            'Water Activities' => [
                'Wild Swimming', 'Kayaking', 'Canoeing', 'Surfing', 'Paddleboarding',
                'Snorkeling', 'Fishing', 'Freediving',
            ],
            'Winter Activities' => [
                'Skiing', 'Snowboarding', 'Ice Skating', 'Snowshoeing', 'Winter Camping',
            ],
        ],
    ];

    public function run(): void
    {
        $subcategoriesCreated = [];
        $addedBySubcategory   = [];
        $existedBySubcategory = [];

        foreach (self::DATA as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->first();

            if ($category === null) {
                $this->command?->error("Category not found, skipping entirely: {$categoryName}");
                continue;
            }

            foreach ($subcategories as $subcategoryName => $tagNames) {
                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryName)
                    ->first();

                if ($subcategory === null) {
                    $nextSortOrder = (int) Subcategory::where('category_id', $category->id)->max('sort_order') + 1;

                    $subcategory = Subcategory::create([
                        'category_id' => $category->id,
                        'name'        => $subcategoryName,
                        'slug'        => $category->slug . '-' . Str::slug($subcategoryName),
                        'sort_order'  => $nextSortOrder,
                        'is_active'   => true,
                        'is_sensitive' => false,
                    ]);

                    $subcategoriesCreated[] = "{$categoryName} > {$subcategoryName} (sort_order {$nextSortOrder})";
                }

                $addedBySubcategory[$subcategoryName]   ??= 0;
                $existedBySubcategory[$subcategoryName] ??= 0;

                foreach ($tagNames as $name) {
                    if (Tag::where('name', $name)->exists()) {
                        $existedBySubcategory[$subcategoryName]++;
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

                    $addedBySubcategory[$subcategoryName]++;
                }
            }
        }

        $this->command?->info('');
        $this->command?->info('Subcategories created:');
        foreach ($subcategoriesCreated as $line) {
            $this->command?->info("  {$line}");
        }

        $this->command?->info('');
        $this->command?->info('Tags added per subcategory:');
        foreach ($addedBySubcategory as $subcategoryName => $count) {
            $this->command?->info("  {$subcategoryName}: {$count} added, {$existedBySubcategory[$subcategoryName]} already existed");
        }

        $this->command?->info('');
        $this->command?->info('Total tags in taxonomy: ' . Tag::count());
    }
}
