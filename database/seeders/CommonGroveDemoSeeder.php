<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\HangoutPost;
use App\Models\PinnedRoom;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CommonGroveDemoSeeder extends Seeder
{
    /**
     * Gamertags for every demo user this seeder manages.
     * Used to detect the "primary test user" (first non-demo user).
     *
     * @var list<string>
     */
    private const DEMO_GAMERTAGS = [
        'Noxara', 'Pixelwren', 'CalmVoid', 'Veloria',
        'Runex', 'Tindrel', 'Foxenred', 'MossByte',
    ];

    public function run(): void
    {
        // Ensure the full tag catalogue is present before we reference slugs.
        $this->call(TagSeeder::class);

        $this->command->info('Ensuring demo tags...');
        $this->ensureDemoTags();

        $this->command->info('Ensuring demo users...');
        $users = $this->ensureUsers();

        $this->command->info('Attaching tags to demo users...');
        $this->attachTagsToUsers($users);

        $this->command->info('Ensuring hangout posts...');
        $this->ensureHangoutPosts($users);

        $this->command->info('Ensuring persistent rooms...');
        $this->ensurePersistentRooms($users);

        $this->command->info('Creating pinned rooms for test user...');
        $this->ensurePinnedRooms($users);

        $this->command->info('');
        $this->command->info('✓ CommonGrove demo seed complete.');
        $this->command->info('  Users:          ' . User::count());
        $this->command->info('  Hangouts:       ' . HangoutPost::where('is_active', true)->where('is_persistent', false)->where('expires_at', '>', now())->count());
        $this->command->info('  Rooms:          ' . HangoutPost::where('is_active', true)->where('is_persistent', true)->count());
        $this->command->info('  Pinned rooms:   ' . PinnedRoom::count());
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * Create the few tags referenced by this seeder that may not be in TagSeeder.
     */
    private function ensureDemoTags(): void
    {
        $tags = [
            // Generic interest tags (TagSeeder has specific titles like "Fantasy Books",
            // but the sidebar demo needs broad category-level tags too)
            ['name' => 'Books',            'slug' => 'books',             'type' => 'interest', 'category' => 'Books & Writing'],
            ['name' => 'Music',            'slug' => 'music',             'type' => 'interest', 'category' => 'Music'],
            ['name' => 'Gaming',           'slug' => 'gaming',            'type' => 'interest', 'category' => 'Gaming'],
            ['name' => 'Fishing',          'slug' => 'fishing',           'type' => 'interest', 'category' => 'Nature & Outdoors'],
            // Vibe tags not in the base set
            ['name' => 'Low-pressure',     'slug' => 'low-pressure',      'type' => 'vibe',     'category' => 'Room Vibe'],
            ['name' => 'Just hanging out', 'slug' => 'just-hanging-out',  'type' => 'vibe',     'category' => 'Room Vibe'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                array_merge($tag, ['is_curated' => true, 'is_approved' => true, 'usage_count' => 0])
            );
        }
    }

    // -------------------------------------------------------------------------
    // Users
    // -------------------------------------------------------------------------

    /**
     * @return array<string, User>  keyed by gamertag
     */
    private function ensureUsers(): array
    {
        $password = Hash::make('demopassword123');

        $definitions = [
            ['gamertag' => 'Noxara',    'email' => 'noxara@example.test',    'display_name' => 'Nox'],
            ['gamertag' => 'Pixelwren', 'email' => 'pixelwren@example.test', 'display_name' => null],
            ['gamertag' => 'CalmVoid',  'email' => 'calmvoid@example.test',  'display_name' => 'Void'],
            ['gamertag' => 'Veloria',   'email' => 'veloria@example.test',   'display_name' => 'Vel'],
            ['gamertag' => 'Runex',     'email' => 'runex@example.test',     'display_name' => null],
            ['gamertag' => 'Tindrel',   'email' => 'tindrel@example.test',   'display_name' => 'Trin'],
            ['gamertag' => 'Foxenred',  'email' => 'foxenred@example.test',  'display_name' => null],
            ['gamertag' => 'MossByte',  'email' => 'mossbyte@example.test',  'display_name' => 'Moss'],
        ];

        $users = [];
        foreach ($definitions as $def) {
            $users[$def['gamertag']] = User::firstOrCreate(
                ['gamertag' => $def['gamertag']],
                [
                    'email'                => $def['email'],
                    'display_name'         => $def['display_name'],
                    'password'             => $password,
                    'email_verified_at'    => now(),
                    'identity_mode'        => 1,
                    'show_names_pref'      => true,
                    'onboarding_completed' => true,
                    'is_admin'             => false,
                ]
            );
        }

        // Also mark any existing users as onboarded so dev login works.
        User::where('onboarding_completed', false)->update(['onboarding_completed' => true]);

        return $users;
    }

    // -------------------------------------------------------------------------
    // User tag assignments
    // -------------------------------------------------------------------------

    /**
     * Give each demo user a realistic spread of tags, then also ensure the
     * primary test account (the first non-demo user, i.e. the developer) has
     * at least enough tags to produce Meet People results.
     *
     * @param  array<string, User>  $users
     */
    private function attachTagsToUsers(array $users): void
    {
        // slug → list of slugs to attach per gamertag
        $assignments = [
            'Noxara'    => ['books', 'gaming', 'knitting', 'quiet', 'introvert-friendly'],
            'Pixelwren' => ['music', 'books', 'gaming', 'adhd-friendly', 'casual'],
            'CalmVoid'  => ['books', 'meditation', 'fishing', 'quiet', 'introvert-friendly'],
            'Veloria'   => ['knitting', 'music', 'gaming', 'anxiety-friendly', 'low-pressure'],
            'Runex'     => ['gaming', 'music', 'fishing', 'casual', 'late-night-people'],
            'Tindrel'   => ['books', 'knitting', 'adhd-friendly', 'deep-talks', 'quiet'],
            'Foxenred'  => ['music', 'gaming', 'fishing', 'casual', 'just-hanging-out'],
            'MossByte'  => ['books', 'music', 'gaming', 'autism-friendly', 'introvert-friendly'],
        ];

        $allSlugs = array_unique(array_merge(...array_values($assignments)));
        $tagMap   = Tag::whereIn('slug', $allSlugs)->pluck('id', 'slug');

        foreach ($assignments as $gamertag => $slugs) {
            $user = $users[$gamertag] ?? null;
            if (! $user) {
                continue;
            }
            $this->attachTagSet($user, $slugs, $tagMap);
        }

        // Ensure the primary test user (developer account) has tags so Meet People works.
        $primaryUser = User::whereNotIn('gamertag', self::DEMO_GAMERTAGS)
            ->orderBy('created_at')
            ->first();

        if ($primaryUser && DB::table('user_tags')->where('user_id', $primaryUser->id)->doesntExist()) {
            // Pick tags shared with several demo users so suggestions reliably appear.
            $starterSlugs = ['books', 'gaming', 'music', 'quiet', 'introvert-friendly'];
            $this->attachTagSet($primaryUser, $starterSlugs, $tagMap);
            $this->command->info("  → Added starter tags to primary user: {$primaryUser->gamertag}");
        }
    }

    /**
     * Attach a list of tag slugs to a user, skipping any already attached.
     *
     * @param  array<string, string>  $tagMap  slug → id
     * @param  list<string>           $slugs
     */
    private function attachTagSet(User $user, array $slugs, \Illuminate\Support\Collection $tagMap): void
    {
        foreach ($slugs as $slug) {
            $tagId = $tagMap[$slug] ?? null;
            if (! $tagId) {
                continue;
            }

            $exists = DB::table('user_tags')
                ->where('user_id', $user->id)
                ->where('tag_id', $tagId)
                ->exists();

            if (! $exists) {
                DB::table('user_tags')->insert([
                    'user_id'    => $user->id,
                    'tag_id'     => $tagId,
                    'created_at' => now(),
                ]);
                Tag::where('id', $tagId)->increment('usage_count');
            }
        }
    }

    // -------------------------------------------------------------------------
    // Hangout posts (feed + Find Rooms)
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, User>  $users
     */
    private function ensureHangoutPosts(array $users): void
    {
        // Refresh any previously-seeded posts that have since expired.
        $demoUserIds = collect($users)->pluck('id')->all();
        HangoutPost::whereIn('user_id', $demoUserIds)
            ->where('expires_at', '<', now()->addHours(1))
            ->update(['expires_at' => now()->addHours(4), 'is_active' => true]);

        $rooms = [
            [
                'content'   => 'Late-night calm chat — just here for some low-key company. No pressure to talk.',
                'user'      => 'Noxara',
                'tag_slugs' => ['meditation', 'quiet', 'late-night-people', 'low-pressure'],
            ],
            [
                'content'   => 'ADHD brain dump — sharing random thoughts, half-finished ideas. Come as you are.',
                'user'      => 'Pixelwren',
                'tag_slugs' => ['adhd-friendly', 'casual', 'just-hanging-out'],
            ],
            [
                'content'   => 'Autism-friendly book corner. Reading fantasy or sci-fi right now. Quiet chat welcome.',
                'user'      => 'CalmVoid',
                'tag_slugs' => ['autism-friendly', 'books', 'quiet', 'introvert-friendly'],
            ],
            [
                'content'   => 'Cozy gamers, no sweat — playing Stardew or Animal Crossing. Come hang.',
                'user'      => 'Veloria',
                'tag_slugs' => ['gaming', 'casual', 'low-pressure'],
            ],
            [
                'content'   => 'Knitting and calm crafts — working through a blanket. No rush. Show me yours.',
                'user'      => 'Tindrel',
                'tag_slugs' => ['knitting', 'quiet', 'low-pressure'],
            ],
            [
                'content'   => 'Fishing, outdoors, and quiet hobbies — anyone else out at the water today?',
                'user'      => 'Foxenred',
                'tag_slugs' => ['fishing', 'quiet', 'introvert-friendly'],
            ],
            [
                'content'   => 'Music that gets you through the day — sharing lo-fi stuff and guitar rabbit holes.',
                'user'      => 'Runex',
                'tag_slugs' => ['music', 'casual', 'deep-talks'],
            ],
            [
                'content'   => 'Just existing together — no topic, no agenda. A place to feel a little less alone.',
                'user'      => 'MossByte',
                'tag_slugs' => ['quiet', 'introvert-friendly', 'just-hanging-out'],
            ],
            [
                'content'   => "Sci-fi and fantasy comfort worlds — what are you reading? I'm deep in fantasy books right now.",
                'user'      => 'CalmVoid',
                'tag_slugs' => ['books', 'deep-talks', 'casual'],
            ],
            [
                'content'   => "Social anxiety soft landing — low pressure, no expectations. If you're nervous, this one's for you.",
                'user'      => 'Noxara',
                'tag_slugs' => ['anxiety-friendly', 'quiet', 'low-pressure', 'just-hanging-out'],
            ],
            [
                'content'   => "D&D and tabletop daydreams — not a session, just talking campaigns and characters.",
                'user'      => 'Pixelwren',
                'tag_slugs' => ['gaming', 'casual', 'deep-talks'],
            ],
            [
                'content'   => 'Slow mornings and indoor plants — gardening season is here. What are you growing?',
                'user'      => 'MossByte',
                'tag_slugs' => ['quiet', 'casual', 'low-pressure'],
            ],
        ];

        $allSlugs = array_unique(array_merge(...array_column($rooms, 'tag_slugs')));
        $tagMap   = Tag::whereIn('slug', $allSlugs)->pluck('id', 'slug');

        foreach ($rooms as $room) {
            $user = $users[$room['user']] ?? null;
            if (! $user) {
                continue;
            }

            $existing = HangoutPost::where('user_id', $user->id)
                ->where('content', $room['content'])
                ->first();

            if ($existing) {
                // Refresh expiry if close to or past expiry.
                if ($existing->expires_at->lt(now()->addHours(1))) {
                    $existing->update(['expires_at' => now()->addHours(4), 'is_active' => true]);
                }
                continue;
            }

            $tagIds = collect($room['tag_slugs'])
                ->map(fn ($s) => $tagMap[$s] ?? null)
                ->filter()
                ->values()
                ->all();

            DB::transaction(function () use ($user, $room, $tagIds): void {
                $post = HangoutPost::create([
                    'user_id'      => $user->id,
                    'content'      => $room['content'],
                    'expires_at'   => now()->addHours(4),
                    'is_active'    => true,
                    'joined_count' => rand(1, 8),
                ]);

                if (! empty($tagIds)) {
                    $post->tags()->attach($tagIds);
                }
            });
        }
    }

    // -------------------------------------------------------------------------
    // Persistent rooms (always-open chatrooms)
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, User>  $users
     */
    private function ensurePersistentRooms(array $users): void
    {
        $rooms = [
            [
                'content'   => 'Books & reading — a calm corner for readers of all kinds. Drop in, share what you\'re reading, no pressure.',
                'user'      => 'CalmVoid',
                'tag_slugs' => ['books', 'quiet', 'introvert-friendly'],
            ],
            [
                'content'   => 'Music heads — share what you\'re listening to, discover new stuff, or just vibe quietly together.',
                'user'      => 'Runex',
                'tag_slugs' => ['music', 'casual', 'low-pressure'],
            ],
            [
                'content'   => 'Cozy gamers — no speedruns, no judgment. Stardew, Minecraft, Animal Crossing and other comfort games.',
                'user'      => 'Veloria',
                'tag_slugs' => ['gaming', 'casual', 'low-pressure'],
            ],
            [
                'content'   => 'Night owls — a quiet room for people who are more alive at midnight than 9am.',
                'user'      => 'Noxara',
                'tag_slugs' => ['late-night-people', 'quiet', 'introvert-friendly'],
            ],
            [
                'content'   => 'Creative corner — art, writing, drawing, making stuff. Share your work or just lurk for inspiration.',
                'user'      => 'Tindrel',
                'tag_slugs' => ['casual', 'deep-talks', 'low-pressure'],
            ],
            [
                'content'   => 'Nature & outdoors — hiking, plants, birdwatching, the sea, or just sitting outside. Slow living welcome.',
                'user'      => 'Foxenred',
                'tag_slugs' => ['fishing', 'quiet', 'casual'],
            ],
            [
                'content'   => 'Deep talks only — if you\'re here for real conversation about real things, this is your room.',
                'user'      => 'MossByte',
                'tag_slugs' => ['deep-talks', 'introvert-friendly', 'quiet'],
            ],
            [
                'content'   => 'Anxiety lounge — a soft-landing room. No performance, no expectations. You don\'t have to explain yourself.',
                'user'      => 'Noxara',
                'tag_slugs' => ['anxiety-friendly', 'quiet', 'low-pressure', 'just-hanging-out'],
            ],
            [
                'content'   => 'ADHD & neurodivergent hangout — scattered thoughts welcome. Bring your hyperfixations.',
                'user'      => 'Pixelwren',
                'tag_slugs' => ['adhd-friendly', 'casual', 'just-hanging-out'],
            ],
            [
                'content'   => 'Films, series & comfort watches — what are you watching? Recs, discussions, or quiet company while you watch.',
                'user'      => 'MossByte',
                'tag_slugs' => ['casual', 'deep-talks', 'low-pressure'],
            ],
        ];

        $allSlugs = array_unique(array_merge(...array_column($rooms, 'tag_slugs')));
        $tagMap   = Tag::whereIn('slug', $allSlugs)->pluck('id', 'slug');

        foreach ($rooms as $room) {
            $user = $users[$room['user']] ?? null;
            if (! $user) {
                continue;
            }

            $existing = HangoutPost::where('user_id', $user->id)
                ->where('content', $room['content'])
                ->first();

            if ($existing) {
                // Ensure it stays active and persistent.
                $existing->update(['is_active' => true, 'is_persistent' => true, 'expires_at' => null]);
                continue;
            }

            $tagIds = collect($room['tag_slugs'])
                ->map(fn ($s) => $tagMap[$s] ?? null)
                ->filter()
                ->values()
                ->all();

            DB::transaction(function () use ($user, $room, $tagIds): void {
                $post = HangoutPost::create([
                    'user_id'       => $user->id,
                    'content'       => $room['content'],
                    'is_persistent' => true,
                    'expires_at'    => null,
                    'is_active'     => true,
                    'joined_count'  => rand(2, 20),
                ]);

                if (! empty($tagIds)) {
                    $post->tags()->attach($tagIds);
                }
            });
        }
    }

    // -------------------------------------------------------------------------
    // Pinned rooms (Your Rooms)
    // -------------------------------------------------------------------------

    /**
     * Create Conversation records for two specific rooms, add the primary test
     * user as a participant, and pin both rooms for them.
     *
     * @param  array<string, User>  $users
     */
    private function ensurePinnedRooms(array $users): void
    {
        $primaryUser = User::whereNotIn('gamertag', self::DEMO_GAMERTAGS)
            ->orderBy('created_at')
            ->first()
            ?? ($users['Noxara'] ?? null); // fallback if all users are demo users

        if (! $primaryUser) {
            $this->command->warn('  No primary user found — skipping pinned rooms.');
            return;
        }

        $toPinContents = [
            'Late-night calm chat'      => 'Late-night calm chat — just here for some low-key company. No pressure to talk.',
            'Autism-friendly book corner' => 'Autism-friendly book corner. Reading fantasy or sci-fi right now. Quiet chat welcome.',
        ];

        foreach ($toPinContents as $title => $contentSnippet) {
            $post = HangoutPost::where('content', $contentSnippet)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->first();

            if (! $post) {
                $this->command->warn("  Could not find active post for \"{$title}\" — skipping pin.");
                continue;
            }

            // One conversation per hangout post.
            $conversation = Conversation::firstOrCreate(
                ['hangout_post_id' => $post->id],
                [
                    'type'       => 'room',
                    'name'       => $title,
                    'created_by' => $post->user_id,
                    'is_active'  => true,
                ]
            );

            // Ensure the room creator is a participant.
            $this->ensureParticipant($conversation->id, $post->user_id);

            // Ensure the primary test user is also a participant.
            $this->ensureParticipant($conversation->id, $primaryUser->id);

            // Pin the room for the primary test user.
            PinnedRoom::firstOrCreate([
                'user_id'         => $primaryUser->id,
                'conversation_id' => $conversation->id,
            ]);

            $this->command->info("  → Pinned \"{$title}\" for {$primaryUser->gamertag}");
        }
    }

    /**
     * Insert a participant row if one does not already exist.
     */
    private function ensureParticipant(string $conversationId, string $userId): void
    {
        $exists = DB::table('conversation_participants')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->exists();

        if (! $exists) {
            DB::table('conversation_participants')->insert([
                'conversation_id' => $conversationId,
                'user_id'         => $userId,
                'joined_at'       => now(),
                'last_read_at'    => null,
                'is_muted'        => false,
                'left_at'         => null,
            ]);
        }
    }
}
