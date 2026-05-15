<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HangoutPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CommonGroundDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Mark existing users as onboarded so dev login still works ────────
        User::where('onboarding_completed', false)->update(['onboarding_completed' => true]);

        // ── Create test users ────────────────────────────────────────────────
        $users = $this->createUsers();

        // ── Create hangout posts ─────────────────────────────────────────────
        $this->createHangoutPosts($users);

        $this->command->info('Demo data seeded: ' . count($users) . ' users, 12 hangout posts.');
    }

    /**
     * @return list<User>
     */
    private function createUsers(): array
    {
        $definitions = [
            [
                'gamertag'     => 'Noxara',
                'display_name' => 'Nox',
                'email'        => 'noxara@example.test',
            ],
            [
                'gamertag'     => 'Pixelwren',
                'display_name' => null,
                'email'        => 'pixelwren@example.test',
            ],
            [
                'gamertag'     => 'Veloria',
                'display_name' => 'Vel',
                'email'        => 'veloria@example.test',
            ],
            [
                'gamertag'     => 'Runex',
                'display_name' => null,
                'email'        => 'runex@example.test',
            ],
            [
                'gamertag'     => 'CalmVoid',
                'display_name' => 'Void',
                'email'        => 'calmvoid@example.test',
            ],
            [
                'gamertag'     => 'Foxenred',
                'display_name' => null,
                'email'        => 'foxenred@example.test',
            ],
            [
                'gamertag'     => 'Morelia',
                'display_name' => 'Mo',
                'email'        => 'morelia@example.test',
            ],
            [
                'gamertag'     => 'Aelith',
                'display_name' => null,
                'email'        => 'aelith@example.test',
            ],
            [
                'gamertag'     => 'Tindrel',
                'display_name' => 'Trin',
                'email'        => 'tindrel@example.test',
            ],
            [
                'gamertag'     => 'Soravex',
                'display_name' => null,
                'email'        => 'soravex@example.test',
            ],
        ];

        $users = [];
        $password = Hash::make('testpassword');

        foreach ($definitions as $def) {
            $user = User::firstOrCreate(
                ['gamertag' => $def['gamertag']],
                [
                    'display_name'          => $def['display_name'],
                    'email'                 => $def['email'],
                    'password'              => $password,
                    'email_verified_at'     => now(),
                    'identity_mode'         => 1,
                    'show_names_pref'       => true,
                    'onboarding_completed'  => true,
                    'is_admin'              => false,
                ]
            );

            $users[] = $user;
        }

        return $users;
    }

    /**
     * @param  list<User>  $users
     */
    private function createHangoutPosts(array $users): void
    {
        $rooms = [
            [
                'content'     => 'Late-night calm chat — just here for some low-key company. No pressure to talk. I\'m probably playing something in the background.',
                'user_index'  => 0,
                'interests'   => ['meditation', 'journaling'],
                'experiences' => ['late-night-people'],
                'vibes'       => ['quiet', 'low-key'],
            ],
            [
                'content'     => 'ADHD brain dump — sharing random thoughts, half-finished ideas, things I almost did. Come as you are.',
                'user_index'  => 1,
                'interests'   => ['writing', 'drawing'],
                'experiences' => ['adhd-friendly'],
                'vibes'       => ['casual'],
            ],
            [
                'content'     => 'Autism-friendly book corner. Reading fantasy or sci-fi right now. Quiet chat welcome, no fast pacing.',
                'user_index'  => 2,
                'interests'   => ['fantasy-books', 'sci-fi-books'],
                'experiences' => ['autism-friendly', 'low-stimulation-spaces'],
                'vibes'       => ['quiet', 'deep-talks'],
            ],
            [
                'content'     => 'Cozy gamers, no sweat — playing Stardew or Animal Crossing. Come hang, no skill requirement.',
                'user_index'  => 3,
                'interests'   => ['stardew-valley', 'animal-crossing'],
                'experiences' => [],
                'vibes'       => ['casual', 'chill'],
            ],
            [
                'content'     => 'Creative projects we may or may not finish — drawing, writing, or just talking about the thing I\'m going to start tomorrow.',
                'user_index'  => 4,
                'interests'   => ['drawing', 'writing'],
                'experiences' => ['adhd-friendly'],
                'vibes'       => ['casual'],
            ],
            [
                'content'     => 'Music that gets you through the day — what are you listening to? Sharing lo-fi stuff and guitar rabbit holes.',
                'user_index'  => 5,
                'interests'   => ['lo-fi', 'guitar'],
                'experiences' => [],
                'vibes'       => ['casual', 'deep-talks'],
            ],
            [
                'content'     => 'Outdoors, quiet hobbies, slow mornings. Gardening season is here. Anyone else out in the garden today?',
                'user_index'  => 6,
                'interests'   => ['gardening', 'hiking'],
                'experiences' => ['introvert-friendly'],
                'vibes'       => ['quiet'],
            ],
            [
                'content'     => 'Knitting and calm crafts — working through a blanket that will absolutely be done by winter. No rush. Show me yours.',
                'user_index'  => 7,
                'interests'   => ['knitting', 'crocheting'],
                'experiences' => ['low-stimulation-spaces'],
                'vibes'       => ['quiet', 'low-key'],
            ],
            [
                'content'     => 'Just existing together — no topic, no agenda. A place to sit quietly with other people and feel a little less alone.',
                'user_index'  => 8,
                'interests'   => ['meditation'],
                'experiences' => ['introvert-friendly'],
                'vibes'       => ['quiet', 'chill'],
            ],
            [
                'content'     => 'Sci-fi, fantasy, and comfort worlds — what are you reading or watching? I\'m deep in fantasy books and anime right now.',
                'user_index'  => 9,
                'interests'   => ['fantasy-books', 'sci-fi-books', 'anime'],
                'experiences' => [],
                'vibes'       => ['casual', 'deep-talks'],
            ],
            [
                'content'     => 'Social anxiety soft landing — low pressure, no expectations. If you\'re nervous about joining rooms, this one\'s for you.',
                'user_index'  => 0,
                'interests'   => ['journaling'],
                'experiences' => ['social-anxiety-friendly', 'anxiety-friendly'],
                'vibes'       => ['quiet', 'listener-friendly'],
            ],
            [
                'content'     => 'D&D and tabletop daydreams — not a session, just talking campaigns, characters, and that one encounter that still haunts me.',
                'user_index'  => 2,
                'interests'   => ['dungeons-dragons', 'board-games', 'magic-the-gathering'],
                'experiences' => [],
                'vibes'       => ['casual'],
            ],
        ];

        foreach ($rooms as $room) {
            $user = $users[$room['user_index']];

            $existing = HangoutPost::where('user_id', $user->id)
                ->where('content', $room['content'])
                ->first();

            if ($existing) {
                continue;
            }

            $tagIds = $this->resolveTagSlugs(
                $room['interests'],
                $room['experiences'],
                $room['vibes'],
            );

            DB::transaction(function () use ($user, $room, $tagIds): void {
                $post = HangoutPost::create([
                    'user_id'      => $user->id,
                    'content'      => $room['content'],
                    'expires_at'   => now()->addHours(6),
                    'is_active'    => true,
                    'joined_count' => rand(1, 8),
                ]);

                if (! empty($tagIds)) {
                    $post->tags()->attach($tagIds);
                }
            });
        }
    }

    /**
     * Resolve tag slugs to IDs, skipping any that don't exist.
     *
     * @param  list<string>  $interestSlugs
     * @param  list<string>  $experienceSlugs
     * @param  list<string>  $vibeSlugs
     * @return list<string>
     */
    private function resolveTagSlugs(array $interestSlugs, array $experienceSlugs, array $vibeSlugs): array
    {
        $allSlugs = array_merge($interestSlugs, $experienceSlugs, $vibeSlugs);

        if (empty($allSlugs)) {
            return [];
        }

        return Tag::whereIn('slug', $allSlugs)
            ->where('is_approved', true)
            ->pluck('id')
            ->toArray();
    }
}
