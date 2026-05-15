<?php

declare(strict_types=1);

namespace App\Livewire\Messaging;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\PinnedRoom;
use App\Models\RecentRoom;
use App\Livewire\Rooms\PinnedRoomsSidebar;
use App\Services\BlockService;
use App\Services\CrisisDetectionService;
use App\Services\MessageLimitService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Room extends Component
{
    public string $conversationId = '';

    public string $messageContent = '';

    public ?string $typingUser = null;

    public bool $showCrisisBanner = false;

    // Group 6 — pin error (message pins)
    public ?string $pinError = null;

    // Room pin
    public ?string $roomPinMessage = null;

    // Group 8 — content warning compose state
    public bool   $showCwInput = false;
    public string $cwLabel     = '';

    public function mount(string $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        $this->authorize('view', $conversation);

        $this->conversationId = $conversationId;

        // Mark messages as read on open
        $conversation->participants()
            ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        // Track recent visit
        RecentRoom::updateOrCreate(
            ['user_id' => Auth::id(), 'conversation_id' => $conversationId],
            ['last_visited_at' => now()],
        );
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    #[Computed]
    public function conversation(): Conversation
    {
        return Conversation::with(['participants', 'hangoutPost'])
            ->findOrFail($this->conversationId);
    }

    /** @return Collection<int, Message> */
    #[Computed]
    public function chatMessages(): Collection
    {
        return Message::where('conversation_id', $this->conversationId)
            ->with(['user', 'reactions'])
            ->orderBy('created_at')
            ->get();
    }

    /** @return Collection<int, Message> */
    #[Computed]
    public function pinnedMessages(): Collection
    {
        return Message::where('conversation_id', $this->conversationId)
            ->where('is_pinned', true)
            ->with('user')
            ->orderBy('pinned_at')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Send
    // -------------------------------------------------------------------------

    public function sendMessage(): void
    {
        $rules = ['messageContent' => ['required', 'string', 'max:2000']];

        if ($this->showCwInput) {
            $rules['cwLabel'] = ['nullable', 'string', 'max:50'];
        }

        $this->validate($rules);

        $trimmed = trim($this->messageContent);

        if ($trimmed === '') {
            return;
        }

        $blocked = $this->conversation->participants()
            ->get()
            ->some(fn ($p) => app(BlockService::class)->isBlocked(Auth::user(), $p));

        if ($blocked) {
            $this->addError('messageContent', 'You cannot send messages in this conversation.');
            return;
        }

        /** @var MessageLimitService $limiter */
        $limiter = app(MessageLimitService::class);

        if (! $limiter->canSendMessage(Auth::user())) {
            $this->addError('messageContent', 'New accounts are limited to 5 messages in the first 24 hours.');
            return;
        }

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'user_id'         => Auth::id(),
            'content'         => $trimmed,
            'is_request'      => false,
            'has_cw'          => $this->showCwInput && trim($this->cwLabel) !== '',
            'cw_label'        => $this->showCwInput ? (trim($this->cwLabel) ?: null) : null,
        ]);

        $this->conversation->touch();

        broadcast(new MessageSent($message->load('user')))->toOthers();

        /** @var CrisisDetectionService $crisis */
        $crisis = app(CrisisDetectionService::class);
        $this->showCrisisBanner = $crisis->check($trimmed, Auth::user(), $this->conversationId) !== null;

        $this->messageContent = '';
        $this->showCwInput    = false;
        $this->cwLabel        = '';
        $this->dispatch('message-sent');
    }

    public function dismissCrisisBanner(): void
    {
        /** @var CrisisDetectionService $crisis */
        $crisis = app(CrisisDetectionService::class);
        $crisis->markDismissed(Auth::user(), $this->conversationId);
        $this->showCrisisBanner = false;
    }

    public function broadcastTyping(): void
    {
        broadcast(new UserTyping($this->conversation, Auth::user()))->toOthers();
    }

    // -------------------------------------------------------------------------
    // Group 5 — Reactions
    // -------------------------------------------------------------------------

    public function reactToMessage(string $messageId, string $reaction): void
    {
        if (! in_array($reaction, MessageReaction::ALLOWED, true)) {
            return;
        }

        $existing = MessageReaction::where('message_id', $messageId)
            ->where('user_id', Auth::id())
            ->where('reaction', $reaction)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            MessageReaction::create([
                'message_id' => $messageId,
                'user_id'    => Auth::id(),
                'reaction'   => $reaction,
            ]);
        }

        unset($this->chatMessages);
    }

    // -------------------------------------------------------------------------
    // Group 6 — Pinned messages
    // -------------------------------------------------------------------------

    public function pinMessage(string $messageId): void
    {
        $this->pinError = null;

        $canPin = $this->conversation->created_by === Auth::id() || Auth::user()->is_admin;

        if (! $canPin) {
            $this->pinError = 'Only the room owner can pin messages.';
            return;
        }

        $pinnedCount = Message::where('conversation_id', $this->conversationId)
            ->where('is_pinned', true)
            ->count();

        if ($pinnedCount >= 3) {
            $this->pinError = 'You can only pin 3 messages at a time. Unpin one first.';
            return;
        }

        Message::where('id', $messageId)
            ->where('conversation_id', $this->conversationId)
            ->update([
                'is_pinned' => true,
                'pinned_at' => now(),
                'pinned_by' => Auth::id(),
            ]);

        unset($this->pinnedMessages);
    }

    public function unpinMessage(string $messageId): void
    {
        $this->pinError = null;

        $canPin = $this->conversation->created_by === Auth::id() || Auth::user()->is_admin;

        if (! $canPin) {
            $this->pinError = 'Only the room owner can unpin messages.';
            return;
        }

        Message::where('id', $messageId)
            ->where('conversation_id', $this->conversationId)
            ->update([
                'is_pinned' => false,
                'pinned_at' => null,
                'pinned_by' => null,
            ]);

        unset($this->pinnedMessages);
    }

    // -------------------------------------------------------------------------
    // Room pinning
    // -------------------------------------------------------------------------

    #[Computed]
    public function isRoomPinned(): bool
    {
        return PinnedRoom::where('user_id', Auth::id())
            ->where('conversation_id', $this->conversationId)
            ->exists();
    }

    public function togglePin(): void
    {
        $this->roomPinMessage = null;

        $existing = PinnedRoom::where('user_id', Auth::id())
            ->where('conversation_id', $this->conversationId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $count = PinnedRoom::where('user_id', Auth::id())->count();

            if ($count >= PinnedRoomsSidebar::MAX_PINS) {
                $this->roomPinMessage = 'You can pin up to ' . PinnedRoomsSidebar::MAX_PINS . ' rooms for now.';
                return;
            }

            PinnedRoom::create([
                'user_id'         => Auth::id(),
                'conversation_id' => $this->conversationId,
            ]);
        }

        unset($this->isRoomPinned);
        $this->dispatch('room-pin-updated');
    }

    // -------------------------------------------------------------------------
    // Group 7 — Soft leave
    // -------------------------------------------------------------------------

    public function leaveQuietly(): void
    {
        $this->conversation->participants()
            ->updateExistingPivot(Auth::id(), ['left_at' => now()]);

        $this->redirect(route('messages.index'), navigate: true);
    }

    // -------------------------------------------------------------------------
    // Echo listeners
    // -------------------------------------------------------------------------

    #[On('echo-private:conversation.{conversationId},MessageSent')]
    public function handleMessageSent(array $event): void
    {
        unset($this->chatMessages);

        $this->conversation->participants()
            ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        $this->dispatch('message-received');
    }

    #[On('echo-private:conversation.{conversationId},UserTyping')]
    public function handleTyping(array $event): void
    {
        $this->typingUser = $event['display_name'] ?? null;
        $this->dispatch('typing-received', displayName: $this->typingUser);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.messaging.room')
            ->layout('layouts.app', ['title' => ($this->conversation->name ?? 'Room') . ' — CommonGround']);
    }
}
