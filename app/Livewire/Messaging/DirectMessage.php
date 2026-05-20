<?php

declare(strict_types=1);

namespace App\Livewire\Messaging;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\MessageRequest;
use App\Services\BlockService;
use App\Services\CrisisDetectionService;
use App\Services\MessageLimitService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DirectMessage extends Component
{
    public string $conversationId = '';

    public string $messageContent = '';

    public ?string $typingUser = null;

    /** Set to true when the last sent message triggered a crisis keyword match. */
    public bool $showCrisisBanner = false;

    // Group 7 — quiet mode
    public bool $quietMode = false;

    // Group 8 — content warning compose state
    public bool   $showCwInput = false;
    public string $cwLabel     = '';

    public ?string $verificationBlock = null;

    // DM gradient (per-user, only you see it)
    public string $dmGradientTheme   = '';
    public bool   $showGradientPicker = false;

    public function mount(string $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        $this->authorize('view', $conversation);

        $this->conversationId = $conversationId;

        // Mark messages as read on open
        $conversation->participants()
            ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        // Load current quiet mode state from pivot
        $pivot = $conversation->participants()
            ->where('user_id', Auth::id())
            ->first()
            ?->pivot;

        $this->quietMode = (bool) ($pivot?->is_muted ?? false);

        // Load DM gradient preference
        $pref = \App\Models\UserConversationPreference::where('user_id', Auth::id())
            ->where('conversation_id', $conversationId)
            ->first();

        $this->dmGradientTheme = $pref?->gradient_theme ?? '';
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    #[Computed]
    public function conversation(): Conversation
    {
        return Conversation::with('participants')->findOrFail($this->conversationId);
    }

    /** @return Collection<int, Message> */
    #[Computed]
    public function messages(): Collection
    {
        return Message::where('conversation_id', $this->conversationId)
            ->with(['user', 'reactions'])
            ->orderBy('created_at')
            ->get();
    }

    #[Computed]
    public function pendingRequest(): ?MessageRequest
    {
        return MessageRequest::where('conversation_id', $this->conversationId)
            ->where('to_user_id', Auth::id())
            ->where('status', 'pending')
            ->with('fromUser')
            ->first();
    }

    // -------------------------------------------------------------------------
    // Send
    // -------------------------------------------------------------------------

    public function sendMessage(): void
    {
        if (! Auth::user()->hasVerifiedEmail()) {
            $this->verificationBlock = 'Please verify your email before chatting.';
            return;
        }

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

        $isRequest = $limiter->isNewAccount(Auth::user());

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'user_id'         => Auth::id(),
            'content'         => $trimmed,
            'is_request'      => $isRequest,
            'has_cw'          => $this->showCwInput && trim($this->cwLabel) !== '',
            'cw_label'        => $this->showCwInput ? (trim($this->cwLabel) ?: null) : null,
        ]);

        // Touch conversation updated_at so list ordering stays correct
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

    // -------------------------------------------------------------------------
    // Typing broadcast
    // -------------------------------------------------------------------------

    public function broadcastTyping(): void
    {
        broadcast(new UserTyping($this->conversation, Auth::user()))->toOthers();
    }

    // -------------------------------------------------------------------------
    // Message request actions
    // -------------------------------------------------------------------------

    public function acceptRequest(): void
    {
        MessageRequest::where('conversation_id', $this->conversationId)
            ->where('to_user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'accepted']);

        // Clear cached pending request
        unset($this->pendingRequest);
    }

    public function declineRequest(): void
    {
        MessageRequest::where('conversation_id', $this->conversationId)
            ->where('to_user_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'declined']);

        // Remove this user from participants and close the conversation view
        $this->conversation->participants()->detach(Auth::id());

        $this->redirect(route('messages.index'), navigate: true);
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

        unset($this->messages);
    }

    // -------------------------------------------------------------------------
    // Group 7 — Quiet mode
    // -------------------------------------------------------------------------

    public function toggleQuietMode(): void
    {
        $this->quietMode = ! $this->quietMode;

        $this->conversation->participants()
            ->updateExistingPivot(Auth::id(), ['is_muted' => $this->quietMode]);
    }

    // -------------------------------------------------------------------------
    // DM gradient (per-user preference, only you see it)
    // -------------------------------------------------------------------------

    public function setDmGradient(string $theme): void
    {
        $allowed = array_keys(config('gradients'));

        if ($theme !== '' && ! in_array($theme, $allowed, true)) {
            return;
        }

        \App\Models\UserConversationPreference::updateOrCreate(
            ['user_id' => Auth::id(), 'conversation_id' => $this->conversationId],
            ['gradient_theme' => $theme ?: null],
        );

        $this->dmGradientTheme   = $theme;
        $this->showGradientPicker = false;
    }

    // -------------------------------------------------------------------------
    // Echo listeners
    // -------------------------------------------------------------------------

    #[On('echo-private:conversation.{conversationId},MessageSent')]
    public function handleMessageSent(array $event): void
    {
        // Bust computed cache so the next render fetches fresh messages
        unset($this->messages);

        // Update read position
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
        return view('livewire.messaging.direct-message')
            ->layout('layouts.app', ['title' => 'Messages — CommonGrove']);
    }
}
