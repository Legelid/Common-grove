<?php

declare(strict_types=1);

namespace App\Livewire\Friends;

use App\Models\Friendship;
use App\Models\User;
use App\Models\WeeklyMatch;
use App\Services\FriendshipService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll(60000)]
class FriendsList extends Component
{
    public string $activeTab = 'friends';

    public ?string $flash = null;

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function onlineFriends(): Collection
    {
        return app(FriendshipService::class)
            ->getFriends(Auth::user())
            ->filter(fn (User $u) => $u->isOnline())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function offlineFriends(): Collection
    {
        return app(FriendshipService::class)
            ->getFriends(Auth::user())
            ->filter(fn (User $u) => ! $u->isOnline())
            ->values();
    }

    /**
     * @return Collection<int, Friendship>
     */
    #[Computed]
    public function pendingRequests(): Collection
    {
        return Auth::user()
            ->pendingRequestsReceived()
            ->with('requester')
            ->get();
    }

    /**
     * The current week's match suggestion for the auth user, or null if none exists yet.
     */
    #[Computed]
    public function weeklyMatch(): ?WeeklyMatch
    {
        $weekOf = Carbon::now()->startOfWeek()->toDateString();

        return WeeklyMatch::where('user_id', Auth::id())
            ->where('week_of', $weekOf)
            ->with('matchedUser.tags')
            ->first();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function accept(string $friendshipId): void
    {
        $friendship = Friendship::findOrFail($friendshipId);

        if ($friendship->recipient_id !== Auth::id()) {
            return;
        }

        app(FriendshipService::class)->accept($friendship, Auth::user());

        unset($this->pendingRequests, $this->onlineFriends, $this->offlineFriends);

        $this->flash = $friendship->requester->gamertag . ' is now your friend.';
    }

    public function decline(string $friendshipId): void
    {
        $friendship = Friendship::findOrFail($friendshipId);

        if ($friendship->recipient_id !== Auth::id()) {
            return;
        }

        app(FriendshipService::class)->decline($friendship, Auth::user());

        unset($this->pendingRequests);

        $this->flash = 'Request declined.';
    }

    public function unfriend(string $userId): void
    {
        $friend = User::findOrFail($userId);

        app(FriendshipService::class)->unfriend(Auth::user(), $friend);

        unset($this->onlineFriends, $this->offlineFriends);

        $this->flash = $friend->gamertag . ' has been removed from your friends.';
    }

    public function sendFriendRequestToMatch(): void
    {
        $match = $this->weeklyMatch;

        if (! $match) {
            return;
        }

        try {
            app(FriendshipService::class)->sendRequest(Auth::user(), $match->matchedUser);
            $match->update(['was_contacted' => true]);
        } catch (\InvalidArgumentException) {
            // Silently ignore — already friends, blocked, etc.
        }

        unset($this->weeklyMatch, $this->onlineFriends, $this->offlineFriends);
        $this->flash = 'Friend request sent to ' . $match->matchedUser->gamertag . '.';
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns the name to show for a friend, respecting the viewer's show_names_pref.
     */
    public function visibleName(User $friend): string
    {
        /** @var User $viewer */
        $viewer = Auth::user();

        return $viewer->show_names_pref ? $friend->display_name : $friend->gamertag;
    }

    public function render(): View
    {
        return view('livewire.friends.friends-list')
            ->layout('layouts.app', ['title' => 'Friends — CommonGround']);
    }
}
