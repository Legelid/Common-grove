<?php

declare(strict_types=1);

namespace App\Livewire\Friends;

use App\Models\Conversation;
use App\Models\Friendship;
use App\Models\Tag;
use App\Models\User;
use App\Services\BlockService;
use App\Services\FriendshipService;
use App\Services\InterestPrivacyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

/**
 * Backs the People page (route name and class name stay "friends"/
 * "FriendsList" for backend compatibility — the codebase's route,
 * navigation patterns ('messages.*', 'friends.*'), and existing
 * FriendshipService all key off "friend" terminology. User-facing copy
 * throughout this component and its view says "People", never "Friends".
 */
#[Poll(60000)]
class FriendsList extends Component
{
    public ?string $flash = null;

    // -------------------------------------------------------------------------
    // Computed — the four sections
    // -------------------------------------------------------------------------

    /**
     * Section 1 — people the user has direct-message history with, most
     * recently active conversation first. No privacy filtering: an existing
     * DM history is consent to be recognized, not a discovery surface.
     *
     * @return Collection<int, array{user: User, conversationId: string}>
     */
    #[Computed]
    public function familiarFaces(): Collection
    {
        $user = Auth::user();

        return Conversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->with(['participants' => fn ($q) => $q->where('users.id', '!=', $user->id)])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get()
            ->map(fn (Conversation $c) => $c->participants->first() !== null
                ? ['user' => $c->participants->first(), 'conversationId' => $c->id]
                : null)
            ->filter()
            ->unique(fn (array $row) => $row['user']->id)
            ->values();
    }

    /**
     * Section 2 — people who shared a room with the user in the last 7
     * days, excluding anyone already connected/pending, blocked, or
     * opted out of connection suggestions.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function recentlyMet(): Collection
    {
        $user = Auth::user();

        // "Recently" is anchored to the user's own recent activity in the
        // room (last_read_at, updated every time Room::mount() runs) rather
        // than the other participant's join date, which could be stale.
        $rows = DB::table('conversation_participants as other')
            ->join('conversations as c', 'c.id', '=', 'other.conversation_id')
            ->join('conversation_participants as me', function ($join) use ($user): void {
                $join->on('me.conversation_id', '=', 'other.conversation_id')
                    ->where('me.user_id', $user->id);
            })
            ->where('c.type', 'room')
            ->where('me.last_read_at', '>=', now()->subDays(7))
            ->where('other.user_id', '!=', $user->id)
            ->select('other.user_id', DB::raw('MAX(me.last_read_at) as last_activity'))
            ->groupBy('other.user_id')
            ->orderByDesc('last_activity')
            ->limit(50)
            ->get();

        $orderedIds = $rows->pluck('user_id');

        if ($orderedIds->isEmpty()) {
            return collect();
        }

        $excludeIds = $this->connectedOrPendingIds($user)
            ->merge($this->blockedIds($user))
            ->push($user->id)
            ->unique();

        $users = User::whereIn('id', $orderedIds)
            ->whereNotIn('id', $excludeIds)
            ->where('show_connection_suggestions', true)
            ->get()
            ->keyBy('id');

        return $orderedIds
            ->map(fn ($id) => $users->get($id))
            ->filter()
            ->take(8)
            ->values();
    }

    /**
     * Section 3 — interest-overlap suggestions (Phase 5: weighted
     * specificity scoring — exact tag = 3, shared subcategory = 2, shared
     * category = 1, summed across all overlaps), highest score first.
     * Excludes anyone already connected/pending, blocked, opted out of
     * connection suggestions, or already shown in an earlier section.
     *
     * Falls back to recently-active users (still respecting every
     * exclusion above) when the current user has no interests selected —
     * never an empty section just because interests aren't set.
     *
     * @return Collection<int, array{user: User, explanation: string}>
     */
    #[Computed]
    public function suggestedPeople(): Collection
    {
        $user       = Auth::user();
        $userTagIds = $user->tags()->pluck('tag_id');

        $excludeIds = $this->connectedOrPendingIds($user)
            ->merge($this->blockedIds($user))
            ->merge($this->familiarFaces->pluck('user.id'))
            ->merge($this->recentlyMet->pluck('id'))
            ->push($user->id)
            ->unique();

        if ($userTagIds->isEmpty()) {
            $candidates = User::whereNotIn('id', $excludeIds)
                ->where('show_connection_suggestions', true)
                ->orderByDesc('last_seen_at')
                ->limit(6)
                ->get();

            return $candidates->map(fn (User $candidate) => [
                'user'        => $candidate,
                'explanation' => '',
            ])->values();
        }

        $userSubcategoryIds = Tag::whereIn('id', $userTagIds)->whereNotNull('subcategory_id')->pluck('subcategory_id')->unique()->values();
        $userCategoryIds    = Tag::whereIn('id', $userTagIds)->whereNotNull('category_id')->pluck('category_id')->unique()->values();

        [$caseSql, $bindings] = Tag::tierScoreExpression(
            $userTagIds,
            $userSubcategoryIds,
            $userCategoryIds,
            'ut.tag_id',
            't.subcategory_id',
            't.category_id',
        );

        $scoreSubquery = DB::table('user_tags as ut')
            ->join('tags as t', 't.id', '=', 'ut.tag_id')
            ->selectRaw("ut.user_id, SUM({$caseSql}) as match_score", $bindings)
            ->groupBy('ut.user_id')
            ->havingRaw('match_score > 0');

        $candidates = User::whereNotIn('id', $excludeIds)
            ->where('show_connection_suggestions', true)
            ->joinSub($scoreSubquery, 'scores', 'users.id', '=', 'scores.user_id')
            ->addSelect('users.*', 'scores.match_score')
            ->orderByDesc('scores.match_score')
            ->limit(6)
            ->get();

        $privacy         = app(InterestPrivacyService::class);
        $userTagIdsArray = $userTagIds->all();

        return $candidates->map(function (User $candidate) use ($user, $userTagIdsArray, $privacy) {
            $candidateTagIds = $candidate->tags()->pluck('tags.id')->all();
            $sharedExactIds  = array_values(array_intersect($userTagIdsArray, $candidateTagIds));

            return [
                'user'        => $candidate,
                'explanation' => $privacy->getMatchExplanation($user, $candidate, $sharedExactIds),
            ];
        })->values();
    }

    /**
     * Section 4 — pending incoming friend requests.
     *
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
     * Whether the page has anything at all to show — drives the
     * whole-page empty state (Part E).
     */
    #[Computed]
    public function hasAnyContent(): bool
    {
        return $this->familiarFaces->isNotEmpty()
            || $this->recentlyMet->isNotEmpty()
            || $this->suggestedPeople->isNotEmpty()
            || $this->pendingRequests->isNotEmpty();
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

        unset($this->pendingRequests, $this->recentlyMet, $this->suggestedPeople);

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

    /**
     * "Stay connected" — sends a friend request from Sections 2 or 3.
     */
    public function sendRequest(string $userId): void
    {
        $target = User::findOrFail($userId);

        if (app(BlockService::class)->isBlocked(Auth::user(), $target)) {
            return;
        }

        try {
            app(FriendshipService::class)->sendRequest(Auth::user(), $target);
            $this->flash = 'Request sent to ' . $target->gamertag . '.';
        } catch (\InvalidArgumentException) {
            // Already friends, blocked, or duplicate — silently skip
        }

        unset($this->recentlyMet, $this->suggestedPeople);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * All user ids the given user has a non-declined friendship row with
     * (pending in either direction, or accepted).
     *
     * @return Collection<int, string>
     */
    private function connectedOrPendingIds(User $user): Collection
    {
        return DB::table('friendships')
            ->where(function ($q) use ($user): void {
                $q->where('requester_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->where('status', '!=', 'declined')
            ->get(['requester_id', 'recipient_id'])
            ->flatMap(fn ($f) => [$f->requester_id, $f->recipient_id])
            ->reject(fn ($id) => $id === $user->id)
            ->unique();
    }

    /**
     * All user ids blocked by, or blocking, the given user.
     *
     * @return Collection<int, string>
     */
    private function blockedIds(User $user): Collection
    {
        return DB::table('blocks')
            ->where('blocker_id', $user->id)
            ->orWhere('blocked_id', $user->id)
            ->get(['blocker_id', 'blocked_id'])
            ->flatMap(fn ($b) => [$b->blocker_id, $b->blocked_id])
            ->reject(fn ($id) => $id === $user->id)
            ->unique();
    }

    public function render(): View
    {
        return view('livewire.friends.friends-list')
            ->layout('layouts.app', ['title' => 'People | CommonGrove']);
    }
}
