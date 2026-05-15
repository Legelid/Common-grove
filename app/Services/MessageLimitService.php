<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class MessageLimitService
{
    private const NEW_ACCOUNT_HOURS   = 24;
    private const NEW_ACCOUNT_MSG_MAX = 5;

    /**
     * True if the user registered within the last 24 hours.
     */
    public function isNewAccount(User $user): bool
    {
        return $user->created_at->isAfter(now()->subHours(self::NEW_ACCOUNT_HOURS));
    }

    /**
     * True if the user is allowed to send another message.
     * New accounts are capped at 5 outbound messages in their first 24 hours.
     */
    public function canSendMessage(User $user): bool
    {
        if (! $this->isNewAccount($user)) {
            return true;
        }

        return Message::where('user_id', $user->id)->count() < self::NEW_ACCOUNT_MSG_MAX;
    }
}
