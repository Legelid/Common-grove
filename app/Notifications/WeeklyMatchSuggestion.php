<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class WeeklyMatchSuggestion extends Notification
{
    public function __construct(
        private readonly User $matchedUser,
        private readonly int  $sharedTagCount,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'message'         => 'You have a new match suggestion this week: ' . $this->matchedUser->gamertag . '.',
            'matched_user_id' => $this->matchedUser->id,
            'gamertag'        => $this->matchedUser->gamertag,
            'shared_tags'     => $this->sharedTagCount,
            'url'             => route('friends.index'),
        ];
    }
}
