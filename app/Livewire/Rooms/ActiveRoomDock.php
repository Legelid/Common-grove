<?php

declare(strict_types=1);

namespace App\Livewire\Rooms;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActiveRoomDock extends Component
{
    /**
     * The room the visitor is currently viewing, captured once at mount from
     * the page's own route — a wire:poll request has no route parameters of
     * its own, so this must not be re-derived from request() on every poll.
     */
    public ?string $currentRoomId = null;

    public function mount(): void
    {
        $this->currentRoomId = request()->route('conversationId');
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Rooms the user currently has an active (non-left) seat in, ordered by
     * most recently active first.
     *
     * @return Collection<int, array{
     *     id: string, name: string, unreadCount: int, unreadDisplay: string,
     *     isCurrent: bool, expiresSoon: bool,
     * }>
     */
    #[Computed]
    public function rooms(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return Conversation::query()
            ->where('type', 'room')
            ->where('is_active', true)
            ->whereHas('participants', function ($q): void {
                $q->where('user_id', Auth::id())
                    ->whereNull('conversation_participants.left_at');
            })
            ->with('hangoutPost')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Conversation $conversation) {
                $pivot = $conversation->participants()
                    ->where('user_id', Auth::id())
                    ->first()
                    ?->pivot;

                $lastReadAt = $pivot?->last_read_at;

                $unreadCount = Message::where('conversation_id', $conversation->id)
                    ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
                    ->count();

                $hangoutPost = $conversation->hangoutPost;
                $expiresSoon = $hangoutPost
                    && ! $hangoutPost->is_persistent
                    && $hangoutPost->expires_at
                    && $hangoutPost->expires_at->isFuture()
                    && $hangoutPost->expires_at->diffInHours(now()) <= 2;

                return [
                    'id'            => $conversation->id,
                    'name'          => $conversation->name ?? 'Hangout Room',
                    'unreadCount'   => $unreadCount,
                    'unreadDisplay' => $unreadCount > 99 ? '99+' : (string) $unreadCount,
                    'isCurrent'     => $this->currentRoomId === $conversation->id,
                    'expiresSoon'   => $expiresSoon,
                ];
            })
            ->values();
    }

    #[Computed]
    public function newCount(): int
    {
        return $this->rooms->filter(fn (array $room) => $room['unreadCount'] > 0)->count();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * No-op target for wire:poll — accessing $this->rooms in the view after
     * this runs recomputes it fresh, since the computed property isn't
     * persistently cached across requests.
     */
    public function refresh(): void
    {
        //
    }

    /**
     * Soft-leaves a room from the dock, using the exact same pivot update
     * Room::leaveQuietly() uses — no new leave endpoint.
     */
    public function leaveRoom(string $conversationId): void
    {
        $conversation = Conversation::query()
            ->whereHas('participants', function ($q) use ($conversationId): void {
                $q->where('user_id', Auth::id())
                    ->whereNull('conversation_participants.left_at');
            })
            ->find($conversationId);

        if (! $conversation) {
            return;
        }

        $conversation->participants()
            ->updateExistingPivot(Auth::id(), ['left_at' => now()]);

        unset($this->rooms);

        if ($this->currentRoomId === $conversationId) {
            $this->redirect(route('feed'), navigate: true);
        }
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.rooms.active-room-dock');
    }
}
