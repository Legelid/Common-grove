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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public ?string $verificationBlock = null;

    // Group 8 — content warning compose state
    public bool   $showCwInput = false;
    public string $cwLabel     = '';

    // Replies
    public ?string $replyingToMessageId = null;
    public ?string $replyingToPrompt    = null;

    // Room gradient
    public string $roomGradientTheme  = '';
    public bool   $showGradientPicker = false;

    public function mount(string $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        if (Auth::check()) {
            // Authenticated users must be participants of DMs; rooms are open to all members.
            $this->authorize('view', $conversation);

            // Mark messages as read on open
            $conversation->participants()
                ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

            // Track recent visit
            RecentRoom::updateOrCreate(
                ['user_id' => Auth::id(), 'conversation_id' => $conversationId],
                ['last_visited_at' => now()],
            );
        } else {
            // Guests may only browse room-type conversations, not DMs.
            abort_if($conversation->type !== 'room', 403);
            // Only official starter rooms are previewable — send all others to sign-up.
            if (! ($conversation->hangoutPost?->is_official ?? false)) {
                $this->redirect(route('register'), navigate: true);
                return;
            }
        }

        $this->conversationId = $conversationId;
        $this->roomGradientTheme = $conversation->hangoutPost?->gradient_theme ?? '';
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
        // Guests receive a capped preview — no reaction data needed since they can't interact.
        if (! Auth::check()) {
            return Message::where('conversation_id', $this->conversationId)
                ->with(['user', 'replyToMessage.user'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->reverse()
                ->values();
        }

        return Message::where('conversation_id', $this->conversationId)
            ->with(['user', 'reactions', 'replyToMessage.user'])
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
        try {
            $this->doSendMessage();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Room.sendMessage: uncaught exception', [
                'conversation_id' => $this->conversationId,
                'user_id'         => Auth::id(),
                'error'           => $e->getMessage(),
                'class'           => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => $e->getTraceAsString(),
            ]);
            $this->addError('messageContent', 'Something went wrong sending your message. Please try again.');
        }
    }

    private function doSendMessage(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

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

        // Validate reply target belongs to this conversation before saving.
        $replyMsgId = null;
        if ($this->replyingToMessageId !== null) {
            $replyExists = Message::where('id', $this->replyingToMessageId)
                ->where('conversation_id', $this->conversationId)
                ->exists();
            if ($replyExists) {
                $replyMsgId = $this->replyingToMessageId;
            }
        }

        $message = Message::create([
            'conversation_id'     => $this->conversationId,
            'user_id'             => Auth::id(),
            'content'             => $trimmed,
            'is_request'          => false,
            'has_cw'              => $this->showCwInput && trim($this->cwLabel) !== '',
            'cw_label'            => $this->showCwInput ? (trim($this->cwLabel) ?: null) : null,
            'reply_to_message_id' => $replyMsgId,
            'reply_to_prompt'     => $replyMsgId === null ? (
                $this->replyingToPrompt !== null ? mb_substr($this->replyingToPrompt, 0, 500) : null
            ) : null,
        ]);

        $this->conversation->touch();

        try {
            broadcast(new MessageSent($message->load('user')))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Room.sendMessage: broadcast failed', [
                'conversation_id' => $this->conversationId,
                'message_id'      => $message->id,
                'error'           => $e->getMessage(),
            ]);
        }

        try {
            /** @var CrisisDetectionService $crisis */
            $crisis = app(CrisisDetectionService::class);
            $this->showCrisisBanner = $crisis->check($trimmed, Auth::user(), $this->conversationId) !== null;
        } catch (\Throwable $e) {
            Log::warning('Room.sendMessage: crisis detection failed', ['error' => $e->getMessage()]);
            $this->showCrisisBanner = false;
        }

        $this->messageContent      = '';
        $this->showCwInput         = false;
        $this->cwLabel             = '';
        $this->replyingToMessageId = null;
        $this->replyingToPrompt    = null;
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
        if (! Auth::check()) {
            return;
        }

        broadcast(new UserTyping($this->conversation, Auth::user()))->toOthers();
    }

    // -------------------------------------------------------------------------
    // Replies
    // -------------------------------------------------------------------------

    public function setReply(string $messageId): void
    {
        $this->replyingToMessageId = $messageId;
        $this->replyingToPrompt    = null;
    }

    public function setPromptReply(string $promptText): void
    {
        $this->replyingToPrompt    = mb_substr($promptText, 0, 500);
        $this->replyingToMessageId = null;
    }

    public function cancelReply(): void
    {
        $this->replyingToMessageId = null;
        $this->replyingToPrompt    = null;
    }

    // -------------------------------------------------------------------------
    // Group 5 — Reactions
    // -------------------------------------------------------------------------

    public function reactToMessage(string $messageId, string $reaction): void
    {
        if (! Auth::check()) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

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
        if (! Auth::check()) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

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

    public function deleteRoom(): void
    {
        if (! $this->canDeleteRoom) {
            return;
        }

        DB::transaction(function (): void {
            $post = $this->conversation->hangoutPost;
            $this->conversation->delete();
            $post?->delete();
        });

        $this->redirect(route('feed'), navigate: true);
    }

    // -------------------------------------------------------------------------
    // Room atmosphere (owner / admin only)
    // -------------------------------------------------------------------------

    #[Computed]
    public function isRoomOwner(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->conversation->hangoutPost?->user_id === Auth::id()
            || $this->conversation->created_by === Auth::id()
            || Auth::user()->is_admin;
    }

    /** Room owners can delete user-created rooms; official rooms are excluded. */
    #[Computed]
    public function canDeleteRoom(): bool
    {
        return $this->isRoomOwner
            && ! ($this->conversation->hangoutPost?->is_official ?? false);
    }

    /**
     * Gradient keys that are locked for the current user as room owner.
     * Empty for supporters and admins.
     *
     * @return list<string>
     */
    #[Computed]
    public function lockedRoomAtmosphereKeys(): array
    {
        if (! Auth::check()) {
            return [];
        }

        if (Auth::user()->is_admin || Auth::user()->isSupporter()) {
            return [];
        }

        return config('supporter.gradient_packs', []);
    }

    /**
     * Flat array of prompts to show in this room.
     * Empty when low-stim mode is on or the user has prompts disabled.
     *
     * @return list<string>
     */
    #[Computed]
    public function enabledPrompts(): array
    {
        $user = Auth::user();

        // Prompts have reply buttons — not shown to guests in preview mode.
        if ($user === null) {
            return [];
        }

        if ($user->low_stimulation_mode || ! ($user->show_conversation_prompts ?? true)) {
            return [];
        }

        $allPacks    = config('prompts.packs', []);
        $isSupporter = $user->is_admin || $user->isSupporter();

        $selectedPacks = ! empty($user->enabled_prompt_packs) ? $user->enabled_prompt_packs : ['general'];

        $prompts = [];

        foreach ($selectedPacks as $packKey) {
            if (! isset($allPacks[$packKey])) {
                continue;
            }

            $pack = $allPacks[$packKey];

            if (($pack['supporter_only'] ?? false) && ! $isSupporter) {
                continue;
            }

            foreach ($pack['prompts'] as $prompt) {
                $prompts[] = $prompt;
            }
        }

        if (empty($prompts) && isset($allPacks['general'])) {
            $prompts = $allPacks['general']['prompts'];
        }

        return $prompts;
    }

    public function setRoomGradient(string $theme): void
    {
        if (! $this->isRoomOwner) {
            return;
        }

        $allowed = array_keys(config('gradients'));

        if ($theme !== '' && ! in_array($theme, $allowed, true)) {
            return;
        }

        // Silently reject if the user is not eligible for a supporter atmosphere.
        if ($theme !== '' && in_array($theme, $this->lockedRoomAtmosphereKeys, true)) {
            return;
        }

        $this->conversation->hangoutPost?->update([
            'gradient_theme' => $theme ?: null,
        ]);

        $this->roomGradientTheme  = $theme;
        $this->showGradientPicker = false;
        unset($this->lockedRoomAtmosphereKeys);
    }

    // -------------------------------------------------------------------------
    // Echo listeners
    // -------------------------------------------------------------------------

    #[On('echo-private:conversation.{conversationId},MessageSent')]
    public function handleMessageSent(array $event): void
    {
        unset($this->chatMessages);

        if (Auth::check()) {
            $this->conversation->participants()
                ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);
        }

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
            ->layout('layouts.app', ['title' => ($this->conversation->name ?? 'Room') . ' — CommonGrove']);
    }
}
