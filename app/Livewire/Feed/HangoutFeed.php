<?php

declare(strict_types=1);

namespace App\Livewire\Feed;

use App\Livewire\Rooms\PinnedRoomsSidebar;
use App\Models\Conversation;
use App\Models\HangoutPost;
use App\Models\PinnedRoom;
use App\Models\Tag;
use App\Services\BlockService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll(60000)]
class HangoutFeed extends Component
{
    /** @var list<string> Tag IDs currently selected in the filter panel. */
    public array $selectedFilterTagIds = [];

    public ?string $joinMessage = null;

    public ?string $pinToast = null;

    // ── Computed ──────────────────────────────────────────────────────────────

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function filterInterestTags(): Collection
    {
        return Tag::approved()->ofType('interest')->orderByDesc('usage_count')->orderBy('name')->limit(24)->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function filterExperienceTags(): Collection
    {
        return Tag::approved()->ofType('shared_experience')->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function filterVibeTags(): Collection
    {
        return Tag::approved()->ofType('vibe')->orderBy('name')->get();
    }

    /**
     * Official CommonGrove starter rooms, shown when the user hasn't opted out.
     *
     * @return Collection<int, HangoutPost>
     */
    #[Computed]
    public function officialRooms(): Collection
    {
        if (! (Auth::user()->show_official_rooms ?? true)) {
            return collect();
        }

        return HangoutPost::where('is_official', true)
            ->where('is_active', true)
            ->where('is_persistent', true)
            ->with(['tags', 'conversation'])
            ->limit(4)
            ->get();
    }

    /**
     * Posts scored and sorted by how many selected filter tags they match.
     * Falls back to the user's interest-filtered feed when no filters are active.
     *
     * Eagerly loads the associated conversation so pin state can be checked per card.
     *
     * @return Collection<int, HangoutPost>
     */
    #[Computed]
    public function posts(): Collection
    {
        if (empty($this->selectedFilterTagIds)) {
            return HangoutPost::active()
                ->forUser(Auth::user())
                ->with(['user', 'tags', 'conversation'])
                ->latest()
                ->limit(30)
                ->get();
        }

        // Score posts by number of matching filter tags using a subquery.
        $scoreSubquery = DB::table('hangout_post_tags')
            ->selectRaw('hangout_post_id, COUNT(*) as match_score')
            ->whereIn('tag_id', $this->selectedFilterTagIds)
            ->groupBy('hangout_post_id');

        $posts = HangoutPost::active()
            ->with(['user', 'tags', 'conversation'])
            ->joinSub($scoreSubquery, 'scores', 'hangout_posts.id', '=', 'scores.hangout_post_id')
            ->select('hangout_posts.*', 'scores.match_score')
            ->orderByDesc('scores.match_score')
            ->orderByDesc('hangout_posts.created_at')
            ->limit(40)
            ->get();

        $blocked = collect(
            DB::table('blocks')
                ->where('blocker_id', Auth::id())
                ->pluck('blocked_id')
        )->merge(
            DB::table('blocks')
                ->where('blocked_id', Auth::id())
                ->pluck('blocker_id')
        )->unique();

        return $posts->reject(fn ($p) => $blocked->contains($p->user_id))->values();
    }

    /**
     * Conversation IDs the current user has pinned, for fast per-card lookup.
     *
     * @return list<string>
     */
    #[Computed]
    public function pinnedConversationIds(): array
    {
        return PinnedRoom::where('user_id', Auth::id())
            ->pluck('conversation_id')
            ->all();
    }

    /**
     * True when filters are active but produced no results.
     */
    #[Computed]
    public function showingFallback(): bool
    {
        return ! empty($this->selectedFilterTagIds) && $this->posts->isEmpty();
    }

    /**
     * True when filters are active and showing scored results.
     */
    #[Computed]
    public function filtersActive(): bool
    {
        return ! empty($this->selectedFilterTagIds);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function toggleFilter(string $tagId): void
    {
        if (in_array($tagId, $this->selectedFilterTagIds, true)) {
            $this->selectedFilterTagIds = array_values(
                array_diff($this->selectedFilterTagIds, [$tagId])
            );
        } else {
            $this->selectedFilterTagIds[] = $tagId;
        }
    }

    public function clearFilters(): void
    {
        $this->selectedFilterTagIds = [];
    }

    public function joinHangout(string $postId): void
    {
        $post = HangoutPost::active()->find($postId);

        if ($post === null) {
            $this->joinMessage = 'That hangout has already expired.';
            return;
        }

        if (app(BlockService::class)->isBlocked(Auth::user(), $post->user)) {
            $this->joinMessage = 'You cannot join this hangout.';
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['hangout_post_id' => $post->id],
            [
                'type'       => 'room',
                'name'       => Str::limit($post->content, 80),
                'created_by' => $post->user_id,
                'is_active'  => true,
            ]
        );

        if (! $conversation->participants()->where('user_id', Auth::id())->exists()) {
            $conversation->participants()->attach(Auth::id(), ['joined_at' => now()]);
            $post->increment('joined_count');
        }

        $this->redirect(route('room.show', $conversation->id), navigate: true);
    }

    /**
     * Pin or unpin a room from a feed card.
     * Creates the conversation (and makes the user a participant) if it doesn't exist yet,
     * so the room is accessible immediately when navigated to from the sidebar.
     */
    public function toggleCardPin(string $postId): void
    {
        $this->pinToast = null;

        $post = HangoutPost::active()->find($postId);

        if ($post === null) {
            return;
        }

        if (app(BlockService::class)->isBlocked(Auth::user(), $post->user)) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['hangout_post_id' => $post->id],
            [
                'type'       => 'room',
                'name'       => Str::limit($post->content, 80),
                'created_by' => $post->user_id,
                'is_active'  => true,
            ]
        );

        // Ensure the user is a participant so ConversationPolicy::view passes when they navigate.
        $isParticipant = $conversation->participants()
            ->where('conversation_participants.user_id', Auth::id())
            ->exists();

        if (! $isParticipant) {
            $conversation->participants()->attach(Auth::id(), ['joined_at' => now()]);
        } else {
            // Clear left_at if they had previously left
            $conversation->participants()->updateExistingPivot(Auth::id(), ['left_at' => null]);
        }

        $existing = PinnedRoom::where('user_id', Auth::id())
            ->where('conversation_id', $conversation->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->pinToast = 'Removed from Your Rooms';
        } else {
            $count = PinnedRoom::where('user_id', Auth::id())->count();

            if ($count >= PinnedRoomsSidebar::MAX_PINS) {
                $this->pinToast = 'You can save up to ' . PinnedRoomsSidebar::MAX_PINS . ' rooms.';
                return;
            }

            PinnedRoom::create([
                'user_id'         => Auth::id(),
                'conversation_id' => $conversation->id,
            ]);

            $this->pinToast = 'Saved to Your Rooms';
        }

        unset($this->pinnedConversationIds, $this->posts);
        $this->dispatch('room-pin-updated');
    }

    public function render(): View
    {
        return view('livewire.feed.hangout-feed')
            ->layout('layouts.app', ['title' => 'Hangout Feed — CommonGround']);
    }
}
