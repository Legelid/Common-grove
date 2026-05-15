<?php

declare(strict_types=1);

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return $user->id === $id;
});

/*
 * Private channel for a conversation room.
 * Only participants may subscribe.
 */
Broadcast::channel('conversation.{conversationId}', function ($user, string $conversationId): bool {
    return Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($q) use ($user): void {
            $q->where('user_id', $user->id);
        })
        ->exists();
});
