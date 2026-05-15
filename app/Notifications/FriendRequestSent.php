<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class FriendRequestSent extends Notification
{
    public function __construct(
        private readonly User $from,
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
            'message'      => $this->from->gamertag . ' sent you a friend request.',
            'from_user_id' => $this->from->id,
            'gamertag'     => $this->from->gamertag,
            'url'          => route('friends.index'),
        ];
    }
}
