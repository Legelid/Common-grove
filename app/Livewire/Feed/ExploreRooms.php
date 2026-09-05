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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll(60000)]
class ExploreRooms extends Component
{
    /**
     * Real tag slugs (verified against the seeded taxonomy) each intent
     * filters the feed by. 'interests' isn't here — it uses
     * HangoutPost::scopeForUser() instead (see applyIntentFilter()).
     */
    private const INTENT_TAG_SLUGS = [
        'quiet' => [
            'quiet', 'low-key', 'chill', 'low-stimulation-spaces',
            'quiet-spaces', 'calm-spaces', 'introvert-friendly', 'alone-time', 'social-battery',
        ],
        'casual' => ['casual'],
        'support' => ['listener-friendly', 'advice-welcome', 'grief-support', 'grief-friendly'],
    ];

    /**
     * Official starter room titles each intent narrows officialRooms() to.
     * 'interests' has no mapping — curated starter rooms aren't personal
     * interest content, so all officials show unfiltered for that intent.
     */
    private const INTENT_OFFICIAL_TITLES = [
        'quiet' => ['Just Existing', 'Starting Slow'],
        'casual' => ['Casual Chat'],
        'support' => ['Brain Dump'],
    ];

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

    /** Selected intent card id ('quiet'|'casual'|'interests'|'support'), or null for "all rooms". Arrives via ?intent= from Home. */
    public ?string $intent = null;

    /** Set when a query in this component fails, so the view can show a calm error state instead of crashing. */
    public bool $hasError = false;

    /**
     * Deep-links Home's intent cards into Explore with the right filter
     * pre-selected: /explore?intent=quiet etc.
     */
    public function mount(): void
    {
        $intent = request()->query('intent');

        if (in_array($intent, ['quiet', 'casual', 'interests', 'support'], true)) {
            $this->intent = $intent;
        }
    }

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

        $userTagIds = Auth::check() ? Auth::user()->tags()->pluck('tag_id')->flip()->all() : [];

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
        try {
            if (Auth::check() && ! (Auth::user()->show_official_rooms ?? true)) {
                return collect();
            }

            $query = HangoutPost::where('is_official', true)
                ->where('is_active', true)
                ->where('is_persistent', true)
                ->whereNotNull('title')
                ->with(['tags', 'conversation']);
            $this->withActivity($query);

            if ($this->intent !== null && isset(self::INTENT_OFFICIAL_TITLES[$this->intent])) {
                $query->whereIn('title', self::INTENT_OFFICIAL_TITLES[$this->intent]);
            }

            return $query->orderBy('created_at')->limit(4)->get();
        } catch (\Throwable $e) {
            Log::error('ExploreRooms::officialRooms failed', ['exception' => $e]);
            $this->hasError = true;
            return collect();
        }
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
        try {
            // User-created rooms are hidden from guests; they see only the 4 official starter rooms.
            if (! Auth::check()) {
                return collect();
            }

            $isAdmin = Auth::user()->is_admin;

            if (empty($this->selectedFilterTagIds)) {
                $query = HangoutPost::active()
                    ->where('is_official', false)
                    ->with(['user', 'tags', 'conversation'])
                    ->latest()
                    ->limit(30);
                $this->withActivity($query);

                if (! $isAdmin) {
                    $query->forUser(Auth::user());
                }

                $this->applyIntentFilter($query);

                return $query->get();
            }

            $scoreSubquery = DB::table('hangout_post_tags')
                ->selectRaw('hangout_post_id, COUNT(*) as match_score')
                ->whereIn('tag_id', $this->selectedFilterTagIds)
                ->groupBy('hangout_post_id');

            $query = HangoutPost::active()
                ->where('is_official', false)
                ->with(['user', 'tags', 'conversation'])
                ->joinSub($scoreSubquery, 'scores', 'hangout_posts.id', '=', 'scores.hangout_post_id')
                ->select('hangout_posts.*', 'scores.match_score');
            $this->withActivity($query);

            $this->applyIntentFilter($query);

            $posts = $query
                ->orderByDesc('scores.match_score')
                ->orderByDesc('hangout_posts.created_at')
                ->limit(40)
                ->get();

            if ($isAdmin) {
                return $posts;
            }

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
        } catch (\Throwable $e) {
            Log::error('ExploreRooms::posts failed', ['exception' => $e]);
            $this->hasError = true;
            return collect();
        }
    }

    /**
     * Narrows a HangoutPost query by the selected intent. Never touches
     * the query when $intent is null — behavior is unchanged from before
     * intent cards existed in that case.
     */
    private function applyIntentFilter(Builder $query): void
    {
        if ($this->intent === null) {
            return;
        }

        if ($this->intent === 'interests') {
            $query->forUser(Auth::user());
            return;
        }

        $slugs = self::INTENT_TAG_SLUGS[$this->intent] ?? null;

        if ($slugs !== null) {
            $query->whereHas('tags', fn (Builder $q) => $q->whereIn('tags.slug', $slugs));
        }
    }

    public function setIntent(?string $value): void
    {
        $this->intent = $this->intent === $value ? null : $value;
    }

    /**
     * Adds a real, cheap-at-scale activity signal to a HangoutPost query —
     * current participant count (left_at IS NULL, so it reflects who's
     * actually still in the room, unlike joined_count which only ever
     * increments) and the conversation's latest message. Pattern lifted from
     * the existing withCount join in Admin/AllRooms.php and the
     * latestOfMany() eager-load already used in Messaging/ConversationList.php
     * — proven to be a handful of queries total, not N+1.
     */
    private function withActivity(Builder $query): Builder
    {
        return $query
            ->withCount(['conversation as participant_count' => function (Builder $q): void {
                $q->join('conversation_participants', 'conversations.id', '=', 'conversation_participants.conversation_id')
                    ->whereNull('conversation_participants.left_at');
            }])
            ->with('conversation.latestMessage');
    }

    /**
     * Honest, natural-language room state — never a raw count, never
     * invented. Prefers the real current participant count over
     * joined_count (a lifetime counter that never decrements); falls back
     * to 'Open' when neither is available rather than guessing.
     */
    public function roomStateLabel(HangoutPost $post): string
    {
        $count = $post->participant_count ?? $post->joined_count ?? null;

        if ($count === null) {
            return 'Open';
        }

        return match (true) {
            $count === 0 => 'Quiet right now',
            $count <= 2  => 'A few people here',
            $count <= 8  => 'Conversation moving slowly',
            default      => 'Active right now',
        };
    }

    /**
     * The five Explore discovery sections. Only meaningful in pure browse
     * mode — the moment an intent or tag filter is active, the existing
     * scored posts()/showingFallback() flow already gives a focused result
     * set and takes over in the view, so this returns an all-empty
     * structure rather than doing five queries nobody will see.
     *
     * Guest privacy: user-created room content stays hidden from guests
     * everywhere in this app (see posts()); only the official-rooms section
     * is ever populated for a guest.
     *
     * @return array{
     *     officialShown: Collection<int, HangoutPost>,
     *     officialAll: Collection<int, HangoutPost>,
     *     officialMoreCount: int,
     *     interestMatches: Collection<int, HangoutPost>,
     *     secondaryMatches: Collection<int, HangoutPost>,
     *     newRooms: Collection<int, HangoutPost>,
     *     quietRooms: Collection<int, HangoutPost>,
     * }
     */
    #[Computed]
    public function discoverySections(): array
    {
        $empty = [
            'officialShown' => collect(), 'officialAll' => collect(), 'officialMoreCount' => 0,
            'interestMatches' => collect(), 'secondaryMatches' => collect(),
            'newRooms' => collect(), 'quietRooms' => collect(),
        ];

        if ($this->intent !== null || ! empty($this->selectedFilterTagIds)) {
            return $empty;
        }

        try {
            // Tracks every room ID already placed in an earlier section so no
            // room is ever shown twice on the page — each subsequent section's
            // query excludes everything collected so far.
            $usedIds = collect();

            if (Auth::check() && ! (Auth::user()->show_official_rooms ?? true)) {
                $officialAll = collect();
            } else {
                $officialQuery = HangoutPost::where('is_official', true)
                    ->where('is_active', true)
                    ->where('is_persistent', true)
                    ->whereNotNull('title')
                    ->with(['tags', 'conversation']);
                $this->withActivity($officialQuery);
                $officialAll = $officialQuery->orderBy('created_at')->limit(4)->get();
            }

            $result = $empty;
            $result['officialAll']       = $officialAll;
            $result['officialShown']     = $officialAll->take(2);
            $result['officialMoreCount'] = max(0, $officialAll->count() - 2);
            $usedIds = $usedIds->merge($officialAll->pluck('id'));

            if (! Auth::check()) {
                return $result;
            }

            $blocked = collect(
                DB::table('blocks')->where('blocker_id', Auth::id())->pluck('blocked_id')
            )->merge(
                DB::table('blocks')->where('blocked_id', Auth::id())->pluck('blocker_id')
            )->unique();

            $userTagIds = Auth::user()->tags()->pluck('tag_id');

            if ($userTagIds->isNotEmpty()) {
                $scoreSubquery = DB::table('hangout_post_tags')
                    ->selectRaw('hangout_post_id, COUNT(*) as match_score')
                    ->whereIn('tag_id', $userTagIds)
                    ->groupBy('hangout_post_id');

                $scoredQuery = HangoutPost::active()
                    ->where('is_official', false)
                    ->whereNotIn('hangout_posts.id', $usedIds)
                    ->with(['user', 'tags', 'conversation'])
                    ->joinSub($scoreSubquery, 'scores', 'hangout_posts.id', '=', 'scores.hangout_post_id')
                    ->select('hangout_posts.*', 'scores.match_score')
                    ->orderByDesc('scores.match_score')
                    ->orderByDesc('hangout_posts.created_at');
                $this->withActivity($scoredQuery);

                $allMatches = $scoredQuery->limit(20)->get()
                    ->reject(fn ($p) => $blocked->contains($p->user_id))
                    ->values();

                $result['interestMatches'] = $allMatches->take(6)->values();
                $usedIds = $usedIds->merge($result['interestMatches']->pluck('id'));

                if ($result['interestMatches']->count() >= 3) {
                    $result['secondaryMatches'] = $allMatches
                        ->whereNotIn('id', $usedIds)
                        ->take(6)
                        ->values();
                    $usedIds = $usedIds->merge($result['secondaryMatches']->pluck('id'));
                }
            }

            $newQuery = HangoutPost::active()
                ->where('is_official', false)
                ->where('created_at', '>=', now()->subHours(48))
                ->whereNotIn('id', $usedIds)
                ->with(['user', 'tags', 'conversation'])
                ->latest();
            $this->withActivity($newQuery);
            $newRooms = $newQuery->limit(8)->get()->reject(fn ($p) => $blocked->contains($p->user_id))->values();
            $result['newRooms'] = $newRooms;
            $usedIds = $usedIds->merge($newRooms->pluck('id'));

            $quietQuery = HangoutPost::active()
                ->where('is_official', false)
                ->whereNotIn('id', $usedIds)
                ->with(['user', 'tags', 'conversation'])
                ->latest();
            $this->withActivity($quietQuery);
            $quietCandidates = $quietQuery->limit(20)->get()->reject(fn ($p) => $blocked->contains($p->user_id));
            $result['quietRooms'] = $quietCandidates
                ->filter(fn ($p) => ($p->participant_count ?? $p->joined_count ?? 0) <= 2)
                ->take(3)
                ->values();
            $usedIds = $usedIds->merge($result['quietRooms']->pluck('id'));

            return $result;
        } catch (\Throwable $e) {
            Log::error('ExploreRooms::discoverySections failed', ['exception' => $e]);
            $this->hasError = true;
            return $empty;
        }
    }

    /** @return list<string> */
    #[Computed]
    public function pinnedConversationIds(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return PinnedRoom::where('user_id', Auth::id())
            ->pluck('conversation_id')
            ->all();
    }

    #[Computed]
    public function showingFallback(): bool
    {
        return (! empty($this->selectedFilterTagIds) || $this->intent !== null) && $this->posts->isEmpty();
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
        $post = HangoutPost::active()->find($postId);

        if ($post === null) {
            $this->joinMessage = 'That hangout has already expired.';
            return;
        }

        if (! Auth::check()) {
            // Official starter rooms are previewable by guests — route them in directly.
            if ($post->is_official) {
                $conversation = Conversation::where('hangout_post_id', $post->id)->first();
                if ($conversation) {
                    $this->redirect(route('room.show', $conversation->id), navigate: true);
                    return;
                }
            }
            $this->redirect(route('register'), navigate: true);
            return;
        }

        if (! Auth::user()->hasVerifiedEmail()) {
            $this->joinMessage = 'Please verify your email before joining hangouts.';
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
        if (! Auth::check()) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

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
        return view('livewire.feed.explore-rooms')
            ->layout('layouts.app', ['title' => 'Explore | CommonGrove']);
    }
}
