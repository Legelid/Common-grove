<?php

declare(strict_types=1);

namespace App\Livewire\Rooms;

use App\Models\PinnedRoom;
use App\Models\RecentRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RecentRoomsSidebar extends Component
{
    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Up to 3 recently visited rooms, excluding any already shown in Your Rooms,
     * ordered by most recently visited first.
     *
     * @return Collection<int, RecentRoom>
     */
    #[Computed]
    public function recentRooms(): Collection
    {
        $pinnedIds = PinnedRoom::where('user_id', Auth::id())
            ->pluck('conversation_id');

        return RecentRoom::where('user_id', Auth::id())
            ->whereNotIn('conversation_id', $pinnedIds)
            ->with(['conversation', 'conversation.hangoutPost'])
            ->whereHas('conversation', function ($q): void {
                $q->where('is_active', true);
            })
            ->orderByDesc('last_visited_at')
            ->limit(3)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Event listeners
    // -------------------------------------------------------------------------

    #[On('room-pin-updated')]
    public function refresh(): void
    {
        unset($this->recentRooms);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.rooms.recent-rooms-sidebar');
    }
}
