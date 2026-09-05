<?php

declare(strict_types=1);

namespace App\Livewire\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ConversationList extends Component
{
    /** 'messages' | 'rooms' | 'archive' | 'requests' (requests reachable via the badge button, not a pill). */
    public string $tab = 'messages';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Direct-message conversations only — active (not left, not archived).
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function messagesTabConversations(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->where('type', 'direct')
            ->whereHas('participants', function (Builder $q): void {
                $q->where('user_id', Auth::id())
                    ->whereNull('conversation_participants.left_at')
                    ->whereNull('conversation_participants.archived_at');
            })
            ->whereDoesntHave('messageRequests', function (Builder $q): void {
                $q->where('to_user_id', Auth::id())
                    ->where('status', 'pending');
            })
            ->with(['latestMessage.user', 'participants'])
            ->latest('updated_at')
            ->get();
    }

    /**
     * Room conversations only — active (not left, not archived). Adds a live
     * participant count (left_at IS NULL) for the "X people here" status.
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function roomsTabConversations(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->where('type', 'room')
            ->whereHas('participants', function (Builder $q): void {
                $q->where('user_id', Auth::id())
                    ->whereNull('conversation_participants.left_at')
                    ->whereNull('conversation_participants.archived_at');
            })
            ->withCount(['participants as people_count' => fn (Builder $q) => $q->whereNull('conversation_participants.left_at')])
            ->with(['latestMessage.user', 'participants'])
            ->latest('updated_at')
            ->get();
    }

    /**
     * Everything this user has archived — rooms and DMs mixed, regardless
     * of left_at (an archived+left conversation is a permanent delete and
     * is excluded — see deleteConversation()).
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function archivedConversations(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->whereHas('participants', function (Builder $q): void {
                $q->where('user_id', Auth::id())
                    ->whereNotNull('conversation_participants.archived_at')
                    ->whereNull('conversation_participants.left_at');
            })
            ->with(['latestMessage.user', 'participants'])
            ->get()
            ->sortByDesc(fn (Conversation $c) => $c->participants->find(Auth::id())?->pivot->archived_at)
            ->values();
    }

    /**
     * Count of DM conversations (Messages tab) with an unread latest
     * message — drives the small badge on the Messages pill. Muted
     * conversations (quiet mode) never count toward this, even with new
     * messages waiting — that's the whole point of muting one.
     */
    #[Computed]
    public function unreadMessagesCount(): int
    {
        return $this->messagesTabConversations
            ->filter(function (Conversation $c) {
                $pivot = $c->participants->find(Auth::id())?->pivot;

                if ($pivot?->is_muted) {
                    return false;
                }

                $lastMsg = $c->latestMessage;

                return $lastMsg && ($pivot?->last_read_at === null || $lastMsg->created_at->isAfter($pivot->last_read_at));
            })
            ->count();
    }

    /**
     * Pending direct message requests sent to this user. Unchanged from
     * before the tab restructure — reachable via the "X requests" button
     * in the Messages tab rather than its own pill.
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function requests(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->where('type', 'direct')
            ->whereHas('messageRequests', function ($q): void {
                $q->where('to_user_id', Auth::id())
                    ->where('status', 'pending');
            })
            ->with([
                'latestMessage.user',
                'participants',
                'messageRequests' => fn ($q) => $q->where('to_user_id', Auth::id()),
            ])
            ->latest('updated_at')
            ->get();
    }

    /**
     * Unread message count for one conversation, relative to this user's
     * last_read_at on its pivot. Same pattern as ActiveRoomDock. Requires
     * $conversation->participants to already be eager-loaded (true for
     * every tab query above). Always 0 while muted — quiet mode means the
     * conversation shouldn't draw the eye with an unread badge either, not
     * just skip the aggregate count on the Messages pill.
     */
    public function unreadCountFor(Conversation $conversation): int
    {
        $pivot = $conversation->participants->find(Auth::id())?->pivot;

        if ($pivot?->is_muted) {
            return 0;
        }

        $lastReadAt = $pivot?->last_read_at;

        return Message::where('conversation_id', $conversation->id)
            ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
            ->count();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * Per-user archive — only removes the conversation from this user's own
     * tabs; other participants are unaffected.
     */
    public function archiveConversation(string $conversationId): void
    {
        Auth::user()->conversations()
            ->updateExistingPivot($conversationId, [
                'archived_at' => now(),
            ]);

        $this->clearConversationCaches();
    }

    public function unarchiveConversation(string $conversationId): void
    {
        Auth::user()->conversations()
            ->updateExistingPivot($conversationId, [
                'archived_at' => null,
            ]);

        $this->clearConversationCaches();
    }

    /**
     * Permanent delete from this user's perspective only — sets both
     * left_at and archived_at so it drops out of every tab. The
     * conversation itself is never soft-deleted; other participants keep
     * full access.
     */
    public function deleteConversation(string $conversationId): void
    {
        Auth::user()->conversations()
            ->updateExistingPivot($conversationId, [
                'left_at'     => now(),
                'archived_at' => now(),
            ]);

        $this->clearConversationCaches();
    }

    private function clearConversationCaches(): void
    {
        unset(
            $this->messagesTabConversations,
            $this->roomsTabConversations,
            $this->archivedConversations,
            $this->unreadMessagesCount,
        );
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.messaging.conversation-list')
            ->layout('layouts.app', ['title' => 'Messages | CommonGrove']);
    }
}
