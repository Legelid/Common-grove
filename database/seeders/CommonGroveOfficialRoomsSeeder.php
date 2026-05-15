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
     * Official starter rooms created by CommonGrove.
     * Each entry: [content, tag_slugs[]]
     *
     * @var list<array{content:string, tags:list<string>}>
     */
    private const ROOMS = [
        [
            'content' => 'A quiet room for people who want company without pressure. No agenda. Just existing together.',
            'tags'    => ['introvert-friendly', 'quiet', 'casual', 'low-key'],
        ],
        [
            'content' => 'For night owls, overthinkers, and anyone awake when the world gets quiet. Late-night calm chat.',
            'tags'    => ['quiet', 'casual', 'chill', 'listener-friendly'],
        ],
        [
            'content' => 'Talk about books, comfort reads, stories, and fictional worlds. A book corner for readers of all kinds.',
            'tags'    => ['fantasy-books', 'sci-fi-books', 'book-club', 'quiet'],
        ],
        [
            'content' => 'For casual gamers who want friendly conversation without competition. Cozy gamers, no sweat.',
            'tags'    => ['cozy-games', 'gaming', 'casual', 'low-key'],
        ],
        [
            'content' => 'A low-pressure space for scattered thoughts, hyperfixations, and random ideas. ADHD brain dump.',
            'tags'    => ['adhd-friendly', 'neurodivergent-friendly', 'casual'],
        ],
        [
            'content' => 'A calmer space where special interests, slower replies, and low-pressure chatting are welcome. Autism-friendly quiet room.',
            'tags'    => ['autism-friendly', 'neurodivergent-friendly', 'quiet', 'low-stimulation-spaces'],
        ],
        [
            'content' => 'Share ideas, unfinished projects, writing, art, music, or things you might someday finish. Creative projects corner.',
            'tags'    => ['writing', 'art', 'music', 'casual'],
        ],
        [
            'content' => 'A gentle room for people who want to ease into conversation slowly. Social anxiety soft landing.',
            'tags'    => ['social-anxiety-friendly', 'anxiety-friendly', 'quiet', 'listener-friendly'],
        ],
    ];

    public function run(): void
    {
        $system = $this->ensureSystemUser();

        $this->command->info('Creating official CommonGrove starter rooms...');

        foreach (self::ROOMS as $def) {
            $post = HangoutPost::where('user_id', $system->id)
                ->where('is_official', true)
                ->where('content', 'LIKE', '%' . Str::words($def['content'], 4, '') . '%')
                ->first();

            if ($post === null) {
                $post = HangoutPost::create([
                    'user_id'       => $system->id,
                    'content'       => $def['content'],
                    'is_persistent' => true,
                    'is_official'   => true,
                    'is_active'     => true,
                    'expires_at'    => null,
                    'joined_count'  => 0,
                ]);
            } else {
                $post->update([
                    'is_persistent' => true,
                    'is_official'   => true,
                    'is_active'     => true,
                    'expires_at'    => null,
                ]);
            }

            $tagIds = Tag::whereIn('slug', $def['tags'])->pluck('id');
            $post->tags()->syncWithoutDetaching($tagIds);

            $this->command->info('  ✓ ' . Str::limit($def['content'], 60));
        }

        $this->command->info('');
        $this->command->info('Official rooms: ' . HangoutPost::where('is_official', true)->count());
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
