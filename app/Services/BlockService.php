<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Block;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BlockService
{
    /**
     * Block $target from $blocker's perspective.
     * Removes any shared direct conversations and pending message requests.
     */
    public function block(User $blocker, User $target): void
    {
        if ($blocker->hasBlocked($target)) {
            return;
        }

        DB::transaction(function () use ($blocker, $target): void {
            Block::create([
                'blocker_id' => $blocker->id,
                'blocked_id' => $target->id,
            ]);

            // Remove both users from shared direct conversations
            $sharedDirectIds = Conversation::where('type', 'direct')
                ->whereHas('participants', fn ($q) => $q->where('user_id', $blocker->id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $target->id))
                ->pluck('id');

            foreach ($sharedDirectIds as $convId) {
                $conv = Conversation::find($convId);
                $conv?->participants()->detach([$blocker->id, $target->id]);
                $conv?->messageRequests()
                    ->whereIn('from_user_id', [$blocker->id, $target->id])
                    ->delete();
            }
        });
    }

    public function unblock(User $blocker, User $target): void
    {
        Block::where('blocker_id', $blocker->id)
            ->where('blocked_id', $target->id)
            ->delete();
    }

    /**
     * Returns true if either user has blocked the other.
     */
    public function isBlocked(User $a, User $b): bool
    {
        return Block::where(function ($q) use ($a, $b): void {
            $q->where('blocker_id', $a->id)->where('blocked_id', $b->id);
        })->orWhere(function ($q) use ($a, $b): void {
            $q->where('blocker_id', $b->id)->where('blocked_id', $a->id);
        })->exists();
    }
}
