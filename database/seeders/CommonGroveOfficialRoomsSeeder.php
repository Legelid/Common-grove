<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HangoutPost;
use App\Models\Tag;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommonGroveOfficialRoomsSeeder extends Seeder
{
    /**
     * Canonical starter rooms.
     * title  — stable key used for upsert matching and accent colour lookup in the feed view.
     * icon   — must match a case in the feed view's @switch block.
     * content — short description shown on the card (≤ 60 chars fits without truncation).
     *
     * @var list<array{title:string, content:string, icon:string, tags:list<string>}>
     */
    private const ROOMS = [
        [
            'title'   => 'Just Existing',
            'content' => 'Sit quietly or stay in the background.',
            'icon'    => 'moon',
            'tags'    => ['introvert-friendly', 'quiet', 'casual', 'low-key'],
        ],
        [
            'title'   => 'Casual Chat',
            'content' => 'Talk about anything, no pressure.',
            'icon'    => 'chat',
            'tags'    => ['casual', 'listener-friendly', 'low-key'],
        ],
        [
            'title'   => 'Brain Dump',
            'content' => 'Say whatever\'s on your mind.',
            'icon'    => 'brain',
            'tags'    => ['adhd-friendly', 'neurodivergent-friendly', 'casual'],
        ],
        [
            'title'   => 'Starting Slow',
            'content' => 'Ease into conversation at your pace.',
            'icon'    => 'leaf',
            'tags'    => ['social-anxiety-friendly', 'anxiety-friendly', 'quiet', 'listener-friendly'],
        ],
    ];

    public function run(): void
    {
        $system = $this->ensureSystemUser();

        $this->command->info('Creating canonical CommonGrove starter rooms…');

        foreach (self::ROOMS as $def) {
            $post = HangoutPost::where('user_id', $system->id)
                ->where('title', $def['title'])
                ->first();

            if ($post === null) {
                $post = HangoutPost::create([
                    'user_id'       => $system->id,
                    'title'         => $def['title'],
                    'content'       => $def['content'],
                    'icon'          => $def['icon'],
                    'is_persistent' => true,
                    'is_official'   => true,
                    'is_active'     => true,
                    'expires_at'    => null,
                    'joined_count'  => 0,
                ]);
                $this->command->info('  + Created: ' . $def['title']);
            } else {
                $post->update([
                    'content'       => $def['content'],
                    'icon'          => $def['icon'],
                    'is_persistent' => true,
                    'is_official'   => true,
                    'is_active'     => true,
                    'expires_at'    => null,
                ]);
                $this->command->info('  ✓ Updated: ' . $def['title']);
            }

            $tagIds = Tag::whereIn('slug', $def['tags'])->pluck('id');
            $post->tags()->syncWithoutDetaching($tagIds);
        }

        // Retire any official rooms that are no longer in the canonical list.
        // They keep their conversations and messages — they just stop appearing
        // in the starter-rooms row on the feed.
        // Note: MySQL's NOT IN silently skips NULL rows, so we also include IS NULL.
        $canonicalTitles = array_column(self::ROOMS, 'title');
        $retired = HangoutPost::where('user_id', $system->id)
            ->where('is_official', true)
            ->where(function ($q) use ($canonicalTitles): void {
                $q->whereNotIn('title', $canonicalTitles)
                  ->orWhereNull('title');
            })
            ->update(['is_official' => false]);

        if ($retired > 0) {
            $this->command->warn("  Retired {$retired} stale official room(s) (set is_official = false).");
        }

        $this->command->info('');
        $this->command->info('Active official rooms: ' . HangoutPost::where('is_official', true)->count());
    }

    private function ensureSystemUser(): User
    {
        $existing = User::where('gamertag', 'CommonGrove')->first();

        if ($existing) {
            return $existing;
        }

        /** @var PasswordService $passwords */
        $passwords = app(PasswordService::class);

        return User::create([
            'gamertag'             => 'CommonGrove',
            'email'                => 'system@commongrove.internal',
            'password'             => $passwords->hash(Str::uuid()->toString()),
            'identity_mode'        => 1,
            'is_admin'             => true,
            'is_supporter'         => true,
            'onboarding_completed' => true,
            'email_verified_at'    => now(),
        ]);
    }
}
