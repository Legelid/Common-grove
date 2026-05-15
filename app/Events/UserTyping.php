<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Conversation $conversation,
        public readonly User $user,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.' . $this->conversation->id);
    }

    /** @return array<string, string> */
    public function broadcastWith(): array
    {
        return [
            'display_name' => $this->user->display_name,
        ];
    }
}
