<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HangoutPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OpenGrovesSeeder extends Seeder
{
    private const GROVES = [
        [
            'title'   => 'Just Existing',
            'content' => 'Sit quietly or stay in the background.',
            'icon'    => 'moon',
            'tags'    => ['Quiet', 'Low-pressure', 'Introvert-friendly'],
        ],
        [
            'title'   => 'Casual Chat',
            'content' => 'Talk about anything, no pressure.',
            'icon'    => 'chat',
            'tags'    => ['Casual', 'Chill', 'General chat'],
        ],
        [
            'title'   => 'Brain Dump',
            'content' => "Say whatever's on your mind.",
            'icon'    => 'brain',
            'tags'    => ['Casual', 'Free-form', 'ADHD-friendly'],
        ],
        [
            'title'   => 'Starting Slow',
            'content' => 'Ease into conversation at your pace.',
            'icon'    => 'leaf',
            'tags'    => ['Anxiety-friendly', 'Low-pressure', 'Listener-friendly'],
        ],
    ];

    public function run(): void
    {
        $admin = User::where('is_admin', true)->first()
            ?? User::first();

        if (! $admin) {
            $this->command->warn('No users found — skipping OpenGrovesSeeder.');
            return;
        }

        // Deactivate any existing official rooms not in this set
        // (handles both mismatched titles and legacy rooms with NULL title)
        $knownTitles = array_column(self::GROVES, 'title');
        HangoutPost::where('is_official', true)
            ->where(function ($q) use ($knownTitles): void {
                $q->whereNotIn('title', $knownTitles)
                  ->orWhereNull('title');
            })
            ->update(['is_active' => false]);

        foreach (self::GROVES as $grove) {
            $existing = HangoutPost::where('is_official', true)
                ->where('title', $grove['title'])
                ->first();

            $post = HangoutPost::updateOrCreate(
                ['is_official' => true, 'title' => $grove['title']],
                [
                    'user_id'       => $admin->id,
                    'content'       => $grove['content'],
                    'icon'          => $grove['icon'],
                    'is_active'     => true,
                    'is_persistent' => true,
                    'expires_at'    => null,
                    'joined_count'  => $existing?->joined_count ?? 0,
                ]
            );

            $tagIds = collect($grove['tags'])->map(function (string $name): string {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name'        => $name,
                        'slug'        => Str::slug($name),
                        'type'        => 'vibe',
                        'category'    => 'Vibes',
                        'is_curated'  => true,
                        'is_approved' => true,
                        'usage_count' => 0,
                    ]
                );
                return $tag->id;
            });

            $post->tags()->sync($tagIds->all());
        }

        $this->command->info('Open Groves seeded (' . count(self::GROVES) . ' rooms).');
    }
}
