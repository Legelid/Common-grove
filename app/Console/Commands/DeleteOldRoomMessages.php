<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\HangoutPost;
use App\Models\Message;
use Illuminate\Console\Command;

class DeleteOldRoomMessages extends Command
{
    protected $signature   = 'rooms:prune-messages';
    protected $description = 'Soft-delete messages older than 14 days from persistent rooms.';

    public function handle(): int
    {
        // Collect conversation IDs for all persistent rooms.
        $conversationIds = HangoutPost::where('hangout_posts.is_persistent', true)
            ->join('conversations', 'hangout_posts.id', '=', 'conversations.hangout_post_id')
            ->whereNull('conversations.deleted_at')
            ->pluck('conversations.id');

        if ($conversationIds->isEmpty()) {
            $this->info('No persistent room conversations found. Nothing pruned.');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays(14);

        // Soft-delete in chunks to avoid locking large tables.
        $pruned = 0;
        Message::whereIn('conversation_id', $conversationIds)
            ->where('created_at', '<', $cutoff)
            ->chunkById(500, function ($messages) use (&$pruned): void {
                foreach ($messages as $message) {
                    $message->delete();
                    $pruned++;
                }
            });

        $this->info("Pruned {$pruned} messages older than 14 days from persistent rooms.");

        return self::SUCCESS;
    }
}
