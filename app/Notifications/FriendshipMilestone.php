<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class FriendshipMilestone extends Notification
{
    public function __construct(
        private readonly User $friend,
        private readonly int  $days,
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
            'message'  => 'You and ' . $this->friend->gamertag . ' have been friends for ' . $this->days . ' days.',
            'user_id'  => $this->friend->id,
            'gamertag' => $this->friend->gamertag,
            'days'     => $this->days,
            'url'      => route('friends.index'),
        ];
    }
}
