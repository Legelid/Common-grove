<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\Block;
use App\Models\Friendship;
use App\Models\HangoutPost;
use App\Models\Mute;
use App\Models\User;
use App\Services\BlockService;
use App\Services\FriendshipService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PublicProfile extends Component
{
    public User $profileUser;

    public ?string $actionFlash = null;

    public function mount(string $gamertag): void
    {
        $this->profileUser = User::whereRaw('LOWER(gamertag) = LOWER(?)', [$gamertag])
            ->firstOrFail();
    }

    /**
     * Returns the name to show for the profile owner, respecting the viewer's show_names_pref.
     * If the viewer has disabled friendly names, always show the gamertag.
     */
    public function visibleName(): string
    {
        /** @var User $viewer */
        $viewer = Auth::user();

        if (! $viewer->show_names_pref) {
            return $this->profileUser->gamertag;
        }

        // display_name accessor already applies identity_mode
        return $this->profileUser->display_name;
    }

    // -------------------------------------------------------------------------
    // Computed — block / mute
    // -------------------------------------------------------------------------

    #[Computed]
    public function isBlocked(): bool
    {
        return Auth::user()->hasBlocked($this->profileUser);
    }

    #[Computed]
    public function isMuted(): bool
    {
        return Auth::user()->hasMuted($this->profileUser);
    }

    // -------------------------------------------------------------------------
    // Computed — friendship state
    // -------------------------------------------------------------------------

    /**
     * Returns 'self' | 'friends' | 'pending_sent' | 'pending_received' | 'none'.
     */
    #[Computed]
    public function friendshipState(): string
    {
        /** @var User $viewer */
        $viewer = Auth::user();

        if ($viewer->is($this->profileUser)) {
            return 'self';
        }

        if ($viewer->isFriendWith($this->profileUser)) {
            return 'friends';
        }

        if ($viewer->hasSentRequestTo($this->profileUser)) {
            return 'pending_sent';
        }

        if ($viewer->hasPendingRequestFrom($this->profileUser)) {
            return 'pending_received';
        }

        return 'none';
    }

    // -------------------------------------------------------------------------
    // Computed — shared interests (Group 4)
    // -------------------------------------------------------------------------

    /**
     * @return Collection<int, array{tag: \App\Models\Tag, is_shared: bool}>
     */
    #[Computed]
    public function profileTags(): Collection
    {
        $viewerTagIds = Auth::user()->tags()->pluck('tags.id');

        return $this->profileUser->tags()->orderByDesc('usage_count')->get()
            ->map(fn ($tag) => [
                'tag'       => $tag,
                'is_shared' => $viewerTagIds->contains($tag->id),
            ])
            ->sortByDesc('is_shared')
            ->values();
    }

    #[Computed]
    public function sharedTagCount(): int
    {
        return $this->profileTags->where('is_shared', true)->count();
    }

    /**
     * Up to 3 active persistent rooms the profile owner has created.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, HangoutPost>
     */
    #[Computed]
    public function usualRooms(): \Illuminate\Database\Eloquent\Collection
    {
        return HangoutPost::where('user_id', $this->profileUser->id)
            ->where('is_persistent', true)
            ->where('is_active', true)
            ->where('is_official', false)
            ->whereNotNull('title')
            ->orderByDesc('joined_count')
            ->limit(3)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Block / mute actions
    // -------------------------------------------------------------------------

    public function block(): void
    {
        if ($this->profileUser->is($this->viewer())) {
            return;
        }

        /** @var BlockService $service */
        $service = app(BlockService::class);
        $service->block(Auth::user(), $this->profileUser);

        unset($this->isBlocked);
        $this->actionFlash = $this->profileUser->gamertag . ' has been blocked.';
    }

    public function unblock(): void
    {
        /** @var BlockService $service */
        $service = app(BlockService::class);
        $service->unblock(Auth::user(), $this->profileUser);

        unset($this->isBlocked);
        $this->actionFlash = $this->profileUser->gamertag . ' has been unblocked.';
    }

    public function mute(): void
    {
        if ($this->profileUser->is($this->viewer())) {
            return;
        }

        Mute::firstOrCreate([
            'muter_id' => Auth::id(),
            'muted_id' => $this->profileUser->id,
        ]);

        unset($this->isMuted);
        $this->actionFlash = $this->profileUser->gamertag . '\'s messages will be hidden.';
    }

    public function unmute(): void
    {
        Mute::where('muter_id', Auth::id())
            ->where('muted_id', $this->profileUser->id)
            ->delete();

        unset($this->isMuted);
        $this->actionFlash = $this->profileUser->gamertag . ' has been unmuted.';
    }

    // -------------------------------------------------------------------------
    // Friendship actions
    // -------------------------------------------------------------------------

    public function sendFriendRequest(): void
    {
        $viewer = $this->viewer();

        if ($viewer->is($this->profileUser)) {
            return;
        }

        if (app(BlockService::class)->isBlocked($viewer, $this->profileUser)) {
            $this->actionFlash = 'Cannot send a friend request to this user.';
            return;
        }

        try {
            app(FriendshipService::class)->sendRequest($viewer, $this->profileUser);
        } catch (\InvalidArgumentException $e) {
            $this->actionFlash = $e->getMessage();
            return;
        }

        unset($this->friendshipState);
        $this->actionFlash = 'Friend request sent to ' . $this->profileUser->gamertag . '.';
    }

    public function cancelFriendRequest(): void
    {
        Friendship::where('requester_id', Auth::id())
            ->where('recipient_id', $this->profileUser->id)
            ->where('status', 'pending')
            ->delete();

        unset($this->friendshipState);
        $this->actionFlash = 'Friend request cancelled.';
    }

    public function acceptFriendRequest(): void
    {
        $friendship = Friendship::where('requester_id', $this->profileUser->id)
            ->where('recipient_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        app(FriendshipService::class)->accept($friendship, $this->viewer());

        unset($this->friendshipState);
        $this->actionFlash = 'You are now friends with ' . $this->profileUser->gamertag . '.';
    }

    public function unfriend(): void
    {
        app(FriendshipService::class)->unfriend($this->viewer(), $this->profileUser);

        unset($this->friendshipState);
        $this->actionFlash = $this->profileUser->gamertag . ' has been removed from your friends.';
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    private function viewer(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    public function render(): View
    {
        return view('livewire.profile.public-profile', [
            'visibleName' => $this->visibleName(),
        ])->layout('layouts.app', [
            'title' => $this->profileUser->gamertag . ' — CommonGround',
        ]);
    }
}
