<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\HangoutPost;
use Illuminate\Console\Command;

class ExpireHangoutPosts extends Command
{
    protected $signature   = 'hangout:expire';
    protected $description = 'Mark expired hangout posts as inactive and soft-delete posts expired over 24 hours ago.';

    public function handle(): int
    {
        $deactivated = HangoutPost::query()
            ->where('is_active', true)
            ->where('expires_at', '<=', now())
            ->update(['is_active' => false]);

        $pruned = HangoutPost::query()
            ->where('is_active', false)
            ->where('expires_at', '<=', now()->subHours(24))
            ->whereNull('deleted_at')
            ->get()
            ->each(fn (HangoutPost $post) => $post->delete());

        $this->info("Deactivated: {$deactivated} posts. Pruned: {$pruned->count()} posts.");

        return self::SUCCESS;
    }
}
