<?php

declare(strict_types=1);

namespace App\Livewire\Rooms;

use App\Models\PinnedRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PinnedRoomsSidebar extends Component
{
    public const MAX_PINS = 7;

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Pinned rooms the user still has active access to, ordered newest first.
     * Filters out rooms where the user has left or the conversation is inactive.
     *
     * @return Collection<int, PinnedRoom>
     */
    #[Computed]
    public function pinnedRooms(): Collection
    {
        return PinnedRoom::where('user_id', Auth::id())
            ->with(['conversation', 'conversation.hangoutPost'])
            ->whereHas('conversation', function ($q): void {
                $q->where('is_active', true)
                  ->whereHas('participants', function ($q2): void {
                      $q2->where('user_id', Auth::id())
                         ->whereNull('conversation_participants.left_at');
                  });
            })
            ->latest()
            ->limit(self::MAX_PINS)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function unpin(string $conversationId): void
    {
        PinnedRoom::where('user_id', Auth::id())
            ->where('conversation_id', $conversationId)
            ->delete();

        unset($this->pinnedRooms);
    }

    // -------------------------------------------------------------------------
    // Event listeners
    // -------------------------------------------------------------------------

    #[On('room-pin-updated')]
    public function refresh(): void
    {
        unset($this->pinnedRooms);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.rooms.pinned-rooms-sidebar');
    }
}
