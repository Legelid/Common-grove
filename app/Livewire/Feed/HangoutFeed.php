<?php

declare(strict_types=1);

namespace App\Livewire\Feed;

use App\Livewire\Rooms\PinnedRoomsSidebar;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\HangoutPost;
use App\Models\PinnedRoom;
use App\Models\Subcategory;
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

    /** Category drill-down navigation (panel UI state only, not part of the feed query). */
    public ?int $filterCategoryId = null;

    /** Subcategory filter within the selected category. */
    public ?int $filterSubcategoryId = null;

    /** Free-text search query inside the filter panel. */
    public string $filterSearch = '';

    public ?string $joinMessage = null;

    public ?string $pinToast = null;

    // ── Filter panel — categories & tags ─────────────────────────────────────

    /** @return Collection<int, Category> */
    #[Computed]
    public function filterCategories(): Collection
    {
        return Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    /** @return Collection<int, Subcategory> */
    #[Computed]
    public function filterSubcategories(): Collection
    {
        if ($this->filterCategoryId === null) {
            return collect();
        }

        return Subcategory::where('category_id', $this->filterCategoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Tags to display in the panel.
     * — Non-empty search  → global search results (≤ 24 tags)
     * — Subcategory set   → that subcategory's tags
     * — Category set      → top tags for that category (≤ 40)
     * — Nothing set       → empty (category list is shown instead)
     *
     * Sorted: currently-selected first, then user's profile interests, then usage desc, then alpha.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function filterTags(): Collection
    {
        if (strlen($this->filterSearch) < 2 && $this->filterCategoryId === null) {
            return collect();
        }

        $userTagIds = Auth::user()->tags()->pluck('tag_id')->flip()->all();

        if (strlen($this->filterSearch) >= 2) {
            $tags = Tag::approved()
                ->where('name', 'like', '%' . $this->filterSearch . '%')
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->limit(24)
                ->get();
        } elseif ($this->filterSubcategoryId !== null) {
            $tags = Tag::approved()
                ->where('subcategory_id', $this->filterSubcategoryId)
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->get();
        } else {
            $tags = Tag::approved()
                ->where('category_id', $this->filterCategoryId)
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->limit(40)
                ->get();
        }

        return $tags->sortBy(fn (Tag $tag): array => [
            in_array($tag->id, $this->selectedFilterTagIds, true) ? 0 : (isset($userTagIds[$tag->id]) ? 1 : 2),
            -$tag->usage_count,
            $tag->name,
        ])->values();
    }

    /**
     * Tags currently active as feed filters — used to display and remove them.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function activeFilterTags(): Collection
    {
        if (empty($this->selectedFilterTagIds)) {
            return collect();
        }

        return Tag::whereIn('id', $this->selectedFilterTagIds)->orderBy('name')->get();
    }

    // ── Feed ──────────────────────────────────────────────────────────────────

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
     * @return Collection<int, HangoutPost>
     */
    #[Computed]
    public function posts(): Collection
    {
        if (empty($this->selectedFilterTagIds)) {
            return HangoutPost::active()
                ->where('is_official', false)
                ->forUser(Auth::user())
                ->with(['user', 'tags', 'conversation'])
                ->latest()
                ->limit(30)
                ->get();
        }

        $scoreSubquery = DB::table('hangout_post_tags')
            ->selectRaw('hangout_post_id, COUNT(*) as match_score')
            ->whereIn('tag_id', $this->selectedFilterTagIds)
            ->groupBy('hangout_post_id');

        $posts = HangoutPost::active()
            ->where('is_official', false)
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

    /** @return list<string> */
    #[Computed]
    public function pinnedConversationIds(): array
    {
        return PinnedRoom::where('user_id', Auth::id())
            ->pluck('conversation_id')
            ->all();
    }

    #[Computed]
    public function showingFallback(): bool
    {
        return ! empty($this->selectedFilterTagIds) && $this->posts->isEmpty();
    }

    #[Computed]
    public function filtersActive(): bool
    {
        return ! empty($this->selectedFilterTagIds);
    }

    // ── Filter panel actions ──────────────────────────────────────────────────

    public function setFilterCategory(int $categoryId): void
    {
        $this->filterCategoryId    = $this->filterCategoryId === $categoryId ? null : $categoryId;
        $this->filterSubcategoryId = null;
        $this->filterSearch        = '';
    }

    public function setFilterSubcategory(int $subcategoryId): void
    {
        $this->filterSubcategoryId = $this->filterSubcategoryId === $subcategoryId ? null : $subcategoryId;
    }

    public function clearFilterNav(): void
    {
        $this->filterCategoryId    = null;
        $this->filterSubcategoryId = null;
        $this->filterSearch        = '';
    }

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

    // ── Room actions ──────────────────────────────────────────────────────────

    public function joinHangout(string $postId): void
    {
        if (! Auth::user()->hasVerifiedEmail()) {
            $this->joinMessage = 'Please verify your email before joining hangouts.';
            return;
        }

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

        $isParticipant = $conversation->participants()
            ->where('conversation_participants.user_id', Auth::id())
            ->exists();

        if (! $isParticipant) {
            $conversation->participants()->attach(Auth::id(), ['joined_at' => now()]);
        } else {
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
