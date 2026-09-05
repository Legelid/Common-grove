<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dev-only seeder — connects a handful of demo friends to the TestAdmin
 * account with accepted friendships and back-and-forth DM history, purely
 * so layout/UI changes can be eyeballed against realistic-looking data.
 *
 * Usage: php artisan db:seed --class=TestAdminDemoSeeder
 * NEVER run on a production database.
 */
class TestAdminDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('gamertag', 'TestAdmin')->first();

        if (! $admin) {
            $this->command->warn('TestAdmin account not found — skipping.');
            return;
        }

        $this->command->info('Ensuring demo friends...');
        $friends = $this->ensureFriends();

        $this->command->info('Connecting friends to TestAdmin...');
        foreach ($friends as $index => $friend) {
            $this->ensureFriendship($admin, $friend);
            $conversation = $this->ensureConversation($admin, $friend);
            // Leave the first conversation fully read; the rest end on an
            // unread incoming message so the unread badge has something to show.
            $this->ensureMessages($conversation, $admin, $friend, fullyRead: $index === 0);
        }

        $this->command->info('✓ TestAdmin now has ' . count($friends) . ' connected friends with message history.');
    }

    // -------------------------------------------------------------------------
    // Friends
    // -------------------------------------------------------------------------

    /**
     * @return list<User>
     */
    private function ensureFriends(): array
    {
        $password = app(PasswordService::class)->hash('demopassword123');

        $definitions = [
            ['gamertag' => 'Thalindra',  'email' => 'thalindra@example.test',  'display_name' => 'Thal'],
            ['gamertag' => 'Bramblekit', 'email' => 'bramblekit@example.test', 'display_name' => null],
            ['gamertag' => 'Ashwren',    'email' => 'ashwren@example.test',    'display_name' => 'Ash'],
            ['gamertag' => 'Ivosong',    'email' => 'ivosong@example.test',    'display_name' => null],
        ];

        $users = [];
        foreach ($definitions as $def) {
            $users[] = User::firstOrCreate(
                ['gamertag' => $def['gamertag']],
                [
                    'email'                => $def['email'],
                    'display_name'         => $def['display_name'],
                    'password'             => $password,
                    'email_verified_at'    => now(),
                    'date_of_birth'        => now()->subYears(25)->toDateString(),
                    'identity_mode'        => 1,
                    'show_names_pref'      => true,
                    'onboarding_completed' => true,
                    'is_admin'             => false,
                ]
            );
        }

        return $users;
    }

    // -------------------------------------------------------------------------
    // Friendship
    // -------------------------------------------------------------------------

    private function ensureFriendship(User $admin, User $friend): void
    {
        $exists = Friendship::where(function ($q) use ($admin, $friend) {
            $q->where('requester_id', $admin->id)->where('recipient_id', $friend->id);
        })->orWhere(function ($q) use ($admin, $friend) {
            $q->where('requester_id', $friend->id)->where('recipient_id', $admin->id);
        })->first();

        if ($exists) {
            if ($exists->status !== 'accepted') {
                $exists->update(['status' => 'accepted', 'accepted_at' => now()]);
            }
            return;
        }

        Friendship::create([
            'requester_id' => $friend->id,
            'recipient_id' => $admin->id,
            'status'       => 'accepted',
            'created_at'   => now()->subDays(rand(2, 30)),
            'accepted_at'  => now()->subDays(rand(1, 29)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Conversation
    // -------------------------------------------------------------------------

    private function ensureConversation(User $admin, User $friend): Conversation
    {
        $existing = Conversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $admin->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $friend->id))
            ->first();

        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::create([
            'type'       => 'direct',
            'created_by' => $friend->id,
            'is_active'  => true,
        ]);

        $this->ensureParticipant($conversation->id, $admin->id);
        $this->ensureParticipant($conversation->id, $friend->id);

        return $conversation;
    }

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
                'joined_at'       => now()->subDays(rand(1, 29)),
                'last_read_at'    => null,
                'is_muted'        => false,
                'left_at'         => null,
                'archived_at'     => null,
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Messages
    // -------------------------------------------------------------------------

    private function ensureMessages(Conversation $conversation, User $admin, User $friend, bool $fullyRead): void
    {
        if ($conversation->messages()->exists()) {
            return;
        }

        // Each script ends on the friend's turn, so there's always a genuine
        // "latest incoming message" to mark read/unread for TestAdmin below.
        $scripts = [
            [
                'Hey! Saw we matched on quiet gaming nights, figured I\'d say hi.',
                'Oh nice, hey! Yeah I\'ve been looking for people who don\'t need voice chat lol',
                'Same honestly. Text or nothing for me most days.',
                'Same. What have you been playing lately?',
                'Mostly cozy stuff, Stardew again for the 5th time',
                'Haha classic. I just started a new farm too, no pressure',
                'Nice, we should compare farms sometime, no rush',
            ],
            [
                'Hey, thanks for accepting the request!',
                'Of course, your profile seemed really chill',
                'Appreciate that. What kind of stuff are you into?',
                'Mostly books and quiet hangouts, you?',
                'Books too actually, mostly fantasy',
                'Nice, any recs? I\'m in a reading slump',
                'I can put a list together, give me a day',
            ],
            [
                'Hi! Just wanted to say your room sounded really welcoming',
                'Thank you, that means a lot',
                'Do you host it often?',
                'Most nights honestly, it\'s pretty low-key',
                'That sounds nice, I might drop in sometime',
                'You\'re welcome anytime, no pressure to talk',
                'Appreciate that, I get nervous joining new places',
            ],
            [
                'Hey, been meaning to reach out for a bit',
                'Hey! Glad you did',
                'How\'s your week been?',
                'Pretty quiet, honestly kind of nice',
                'Same here, needed the slow down',
                'Yeah. Anyway just wanted to check in',
                'Means a lot, thank you',
            ],
        ];

        $script = $scripts[array_rand($scripts)];

        $timestamp = now()->subDays(rand(1, 6))->subHours(rand(0, 5));
        $secondToLastTimestamp = $timestamp;

        foreach ($script as $i => $content) {
            $sender = $i % 2 === 0 ? $friend : $admin;

            $secondToLastTimestamp = $timestamp;
            $timestamp = $timestamp->copy()->addMinutes(rand(2, 45));

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id'         => $sender->id,
                'content'         => $content,
                'is_request'      => false,
                'read_at'         => $timestamp,
            ]);
            $message->forceFill(['created_at' => $timestamp, 'updated_at' => $timestamp])->save();
        }

        // TestAdmin has read everything up to (and possibly including) the
        // friend's final message, depending on $fullyRead.
        $adminReadUpTo = $fullyRead ? $timestamp : $secondToLastTimestamp;

        DB::table('conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $admin->id)
            ->update(['last_read_at' => $adminReadUpTo]);

        DB::table('conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $friend->id)
            ->update(['last_read_at' => $timestamp]);

        $conversation->forceFill(['updated_at' => $timestamp])->save();
    }
}
