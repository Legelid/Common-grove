<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly Message $message) {}

    /**
     * The private channel for this conversation.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    /**
     * Payload delivered to subscribed clients.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'content'         => $this->message->content,
            'author_name'     => $this->message->author_name,
            'gamertag'        => $this->message->user?->gamertag,
            'identity_mode'   => $this->message->user?->identity_mode,
            'created_at'      => $this->message->created_at->toISOString(),
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}
