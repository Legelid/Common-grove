<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class FriendRequestAccepted extends Notification
{
    public function __construct(
        private readonly User $acceptor,
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
            'message'      => $this->acceptor->gamertag . ' accepted your friend request.',
            'user_id'      => $this->acceptor->id,
            'gamertag'     => $this->acceptor->gamertag,
            'url'          => route('friends.index'),
        ];
    }
}
