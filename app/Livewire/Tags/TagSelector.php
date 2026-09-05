<?php

declare(strict_types=1);

namespace App\Livewire\Tags;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Rules\ValidCustomTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TagSelector extends Component
{
    public string $search = '';

    /** @var list<string> IDs of currently selected tags */
    public array $selectedTagIds = [];

    public ?int $activeCategoryId = null;

    public bool $viewingSuggested = false;

    /** @var list<string> IDs of up to 5 interests marked as "core" */
    public array $coreInterests = [];

    /** @var list<string> IDs of sensitive interests hidden from others */
    public array $hiddenInterests = [];

    public bool $isDirty = false;

    public bool $showResetNotice = false;

    public ?string $customTagMessage = null;

    public ?string $maxTagsMessage = null;

    public ?string $saveMessage = null;

    /** Maximum core interests a user may mark. */
    private const MAX_CORE = 5;

    /** Maximum custom interest tags a user may create across all sessions. */
    private const MAX_CUSTOM_PER_USER = 10;

    /** Maximum total selected tags. */
    private const MAX_SELECTED = 100;

    public function mount(): void
    {
        $user = Auth::user();

        $this->selectedTagIds = $user
            ->tags()
            ->pluck('tags.id')
            ->toArray();

        $prefs = $user->comfort_preferences ?? [];
        $this->coreInterests = $prefs['core_interest_ids'] ?? [];
        $this->hiddenInterests = $prefs['hidden_interest_ids'] ?? [];

        // Anyone whose account predates today may have had interests before
        // the taxonomy overhaul reset user_tags — show them a friendly
        // explanation once, until they dismiss it.
        $this->showResetNotice = $user->created_at < now()->startOfDay()
            && count($this->selectedTagIds) === 0
            && ! ($prefs['interests_reset_acknowledged'] ?? false);
    }

    /**
     * Acknowledge the reset notice permanently — persisted so it never
     * shows again for this user, on this device or any other.
     */
    public function dismissResetNotice(): void
    {
        $prefs = Auth::user()->comfort_preferences ?? [];
        $prefs['interests_reset_acknowledged'] = true;
        Auth::user()->update(['comfort_preferences' => $prefs]);

        $this->showResetNotice = false;
    }

    /**
     * All active categories, ordered for display.
     *
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    /**
     * Curated subcategories + their approved interest tags matching the
     * current search term. Empty when not searching — category browsing is
     * handled by activeCategoryData() instead.
     *
     * @return Collection<int, Subcategory>
     */
    #[Computed]
    public function subcategoriesWithTags(): Collection
    {
        if (trim($this->search) === '') {
            return collect();
        }

        return Subcategory::where('is_active', true)
            ->whereHas('tags', fn ($q) => $q
                ->approved()
                ->ofType('interest')
                ->where('name', 'like', '%'.$this->search.'%')
            )
            ->with([
                'category',
                'tags' => fn ($q) => $q
                    ->approved()
                    ->ofType('interest')
                    ->where('name', 'like', '%'.$this->search.'%')
                    ->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Search results reshaped for display: flattened out of their
     * subcategories and regrouped by top-level category name, each tag
     * carrying its subcategory name for the "Name · Subcategory" row label.
     * Purely a display transform over subcategoriesWithTags() — does not
     * change what search matches.
     *
     * @return Collection<string, Collection<int, Tag>>
     */
    #[Computed]
    public function searchResultsByCategory(): Collection
    {
        if (trim($this->search) === '') {
            return collect();
        }

        $categoryOrder = $this->categories->pluck('sort_order', 'name');

        return $this->subcategoriesWithTags
            ->flatMap(fn (Subcategory $subcat) => $subcat->tags->map(function (Tag $tag) use ($subcat) {
                $tag->setAttribute('_subcategoryName', $subcat->name);
                $tag->setAttribute('_categoryName', $subcat->category?->name ?? 'Other');

                return $tag;
            }))
            ->groupBy('_categoryName')
            ->sortBy(fn ($tags, $name) => $categoryOrder[$name] ?? PHP_INT_MAX);
    }

    /**
     * Subcategories + approved interest tags for the currently browsed
     * category (desktop right pane / mobile list). Separate from search.
     *
     * @return Collection<int, Subcategory>
     */
    #[Computed]
    public function activeCategoryData(): Collection
    {
        if ($this->activeCategoryId === null) {
            return collect();
        }

        return Subcategory::where('category_id', $this->activeCategoryId)
            ->where('is_active', true)
            ->with(['tags' => fn ($q) => $q->approved()->ofType('interest')->orderBy('name')])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Popular approved tags from categories the user already has interests
     * in, excluding what's already selected. Only meaningful once the user
     * has picked at least 3 interests.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function suggestedTags(): Collection
    {
        if (count($this->selectedTagIds) < 3) {
            return collect();
        }

        $categoryIds = Tag::whereIn('id', $this->selectedTagIds)
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique();

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Tag::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $this->selectedTagIds)
            ->approved()
            ->ofType('interest')
            ->orderByDesc('usage_count')
            ->limit(12)
            ->get();
    }

    /**
     * Count of the user's selected tags per category_id, for the left
     * pane's selected-count badges. Display-only.
     *
     * @return Collection<int, int>
     */
    #[Computed]
    public function selectedCountsByCategory(): Collection
    {
        if (empty($this->selectedTagIds)) {
            return collect();
        }

        return Tag::whereIn('id', $this->selectedTagIds)
            ->whereNotNull('category_id')
            ->get()
            ->groupBy('category_id')
            ->map->count();
    }

    /**
     * The current user's own custom interest tags that match the search query.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function myCustomTagResults(): Collection
    {
        $q = trim($this->search);

        if (mb_strlen($q) < 2) {
            return collect();
        }

        return Tag::where('source', 'custom')
            ->where('created_by_user_id', Auth::id())
            ->where('type', 'interest')
            ->where('name', 'like', '%'.$q.'%')
            ->orderBy('name')
            ->get();
    }

    /**
     * Tags the user has already selected, for the selection bar.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function selectedTags(): Collection
    {
        if (empty($this->selectedTagIds)) {
            return collect();
        }

        return Tag::whereIn('id', $this->selectedTagIds)->orderBy('name')->get();
    }

    /**
     * The user's core-interest tags, restricted to ones still actually
     * selected (in-memory selection can drop a tag before save() prunes
     * the stale core/hidden id lists).
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function coreInterestTags(): Collection
    {
        $ids = array_values(array_intersect($this->coreInterests, $this->selectedTagIds));

        if (empty($ids)) {
            return collect();
        }

        return Tag::whereIn('id', $ids)->orderBy('name')->get();
    }

    /**
     * Selected tags grouped by parent category name, ordered to match the
     * category browser's sort_order. Tags with no category_id (custom /
     * user-submitted interests) fall back to their legacy `category`
     * string and sort last. Display-only — does not affect how interests
     * are stored, loaded, or saved.
     *
     * @return Collection<string, Collection<int, Tag>>
     */
    #[Computed]
    public function selectedTagsByCategory(): Collection
    {
        if (empty($this->selectedTagIds)) {
            return collect();
        }

        $categoryOrder = $this->categories->pluck('sort_order', 'name');

        return Tag::whereIn('id', $this->selectedTagIds)
            ->with(['category', 'subcategory'])
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Tag $tag) => $tag->category?->name ?? $tag->category ?? 'Other')
            ->sortBy(fn ($tags, $name) => $categoryOrder[$name] ?? PHP_INT_MAX);
    }

    public function setCategory(?int $categoryId): void
    {
        $this->activeCategoryId = $this->activeCategoryId === $categoryId ? null : $categoryId;
        $this->viewingSuggested = false;
        $this->search = '';
        $this->maxTagsMessage = null;
        $this->customTagMessage = null;
    }

    /**
     * Toggle the "Suggested" left-pane item, mutually exclusive with
     * browsing a real category.
     */
    public function selectSuggested(): void
    {
        $this->viewingSuggested = ! $this->viewingSuggested;
        $this->activeCategoryId = null;
        $this->search = '';
        $this->maxTagsMessage = null;
        $this->customTagMessage = null;
    }

    public function toggleTag(string $tagId): void
    {
        $this->maxTagsMessage = null;
        $this->saveMessage = null;
        $this->customTagMessage = null;

        $tag = Tag::find($tagId);

        if ($tag === null) {
            return;
        }

        if (in_array($tagId, $this->selectedTagIds, true)) {
            $this->selectedTagIds = array_values(
                array_diff($this->selectedTagIds, [$tagId])
            );
            Auth::user()->deselectTag($tag);
        } else {
            if (count($this->selectedTagIds) >= self::MAX_SELECTED) {
                $this->maxTagsMessage = 'You\'ve reached the maximum of '.self::MAX_SELECTED.' tags.';

                return;
            }

            $this->selectedTagIds[] = $tagId;
            Auth::user()->selectTag($tag);
        }

        unset($this->selectedTags);

        $this->isDirty = true;
    }

    /**
     * Mark/unmark a tag as a "core" interest — up to MAX_CORE at a time.
     * A no-op past the limit (button simply won't add a 6th).
     */
    public function toggleCore(string $tagId): void
    {
        if (in_array($tagId, $this->coreInterests, true)) {
            $this->coreInterests = array_values(
                array_filter($this->coreInterests, fn ($id) => $id !== $tagId)
            );
            $this->isDirty = true;
        } elseif (count($this->coreInterests) < self::MAX_CORE) {
            $this->coreInterests[] = $tagId;
            $this->isDirty = true;
        }
    }

    /**
     * Toggle whether a (typically sensitive) interest is hidden from
     * others in matching/commonality explanations.
     */
    public function toggleHidden(string $tagId): void
    {
        if (in_array($tagId, $this->hiddenInterests, true)) {
            $this->hiddenInterests = array_values(
                array_filter($this->hiddenInterests, fn ($id) => $id !== $tagId)
            );
        } else {
            $this->hiddenInterests[] = $tagId;
        }

        $this->isDirty = true;
    }

    public function save(): void
    {
        if (count($this->selectedTagIds) < 3) {
            $this->saveMessage = 'Please select at least 3 tags before saving.';

            return;
        }

        $current = Auth::user()->tags()->pluck('tags.id')->toArray();
        $toAttach = array_diff($this->selectedTagIds, $current);
        $toDetach = array_diff($current, $this->selectedTagIds);

        foreach ($toAttach as $id) {
            $tag = Tag::find($id);
            if ($tag) {
                Auth::user()->selectTag($tag);
            }
        }

        foreach ($toDetach as $id) {
            $tag = Tag::find($id);
            if ($tag) {
                Auth::user()->deselectTag($tag);
            }
        }

        // Drop core/hidden references to tags no longer selected before persisting.
        $this->coreInterests = array_values(array_intersect($this->coreInterests, $this->selectedTagIds));
        $this->hiddenInterests = array_values(array_intersect($this->hiddenInterests, $this->selectedTagIds));

        $prefs = Auth::user()->comfort_preferences ?? [];
        $prefs['core_interest_ids'] = $this->coreInterests;
        $prefs['hidden_interest_ids'] = $this->hiddenInterests;
        Auth::user()->update(['comfort_preferences' => $prefs]);

        $this->isDirty = false;
        $this->saveMessage = 'Your interests have been saved!';
    }

    /**
     * Create a personal custom interest from the current search term and immediately
     * select it. The tag is held in review (is_approved=false) until an admin
     * promotes it to curated.
     */
    public function addCustomTag(): void
    {
        $this->customTagMessage = null;
        $this->maxTagsMessage = null;

        $name = (string) preg_replace('/\s+/', ' ', trim($this->search));

        if ($name === '') {
            return;
        }

        // Per-user lifetime limit
        if (Tag::where('source', 'custom')->where('created_by_user_id', Auth::id())->count() >= self::MAX_CUSTOM_PER_USER) {
            $this->customTagMessage = 'You\'ve reached the limit of '.self::MAX_CUSTOM_PER_USER.' personal interests. Remove one to add another.';

            return;
        }

        // Daily rate limit — secondary anti-abuse guard
        $rateKey = 'custom-tag.'.Auth::id();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->customTagMessage = 'You\'ve added too many personal interests today. Try again tomorrow.';

            return;
        }

        // Selection cap
        if (count($this->selectedTagIds) >= self::MAX_SELECTED) {
            $this->maxTagsMessage = 'You\'ve reached the maximum of '.self::MAX_SELECTED.' tags.';

            return;
        }

        // Validate name
        $validator = Validator::make(
            ['interest' => $name],
            ['interest' => ['required', 'string', new ValidCustomTag]],
        );
        if ($validator->fails()) {
            $this->customTagMessage = $validator->errors()->first('interest');

            return;
        }

        $slug = Str::slug($name);
        $existing = Tag::where('slug', $slug)->first();

        if ($existing) {
            // Tag already exists — just select it rather than creating a duplicate.
            if (! in_array($existing->id, $this->selectedTagIds, true)) {
                $this->selectedTagIds[] = $existing->id;
                Auth::user()->selectTag($existing);
                $this->customTagMessage = '"'.$existing->name.'" already exists and has been added to your interests.';
            } else {
                $this->customTagMessage = '"'.$existing->name.'" is already in your interests.';
            }
            $this->search = '';
            unset($this->subcategoriesWithTags, $this->myCustomTagResults, $this->selectedTags);

            return;
        }

        RateLimiter::hit($rateKey, 86400);

        $tag = Tag::create([
            'name' => $name,
            'slug' => $slug,
            'type' => 'interest',
            'source' => 'custom',
            'category' => 'User Submitted',
            'created_by_user_id' => Auth::id(),
            'is_curated' => false,
            'is_approved' => false,
            'usage_count' => 0,
        ]);

        $this->selectedTagIds[] = $tag->id;
        Auth::user()->selectTag($tag);

        $this->search = '';
        unset($this->subcategoriesWithTags, $this->myCustomTagResults, $this->selectedTags);

        $this->customTagMessage = '"'.$name.'" added as your personal interest.';
    }

    public function render(): View
    {
        return view('livewire.tags.tag-selector')
            ->layout('layouts.app', ['title' => 'Your Interests | CommonGrove']);
    }
}
