<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Only participants may view a conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * The hangout post creator, conversation creator, or an admin may delete a room.
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->hangoutPost?->user_id === $user->id
            || $conversation->created_by === $user->id
            || $user->is_admin;
    }
}
