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

    public ?string $customTagMessage = null;

    public ?string $maxTagsMessage = null;

    public ?string $saveMessage = null;

    /** Maximum custom interest tags a user may create across all sessions. */
    private const MAX_CUSTOM_PER_USER = 10;

    /** Maximum total selected tags. */
    private const MAX_SELECTED = 100;

    public function mount(): void
    {
        $this->selectedTagIds = Auth::user()
            ->tags()
            ->pluck('tags.id')
            ->toArray();
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
     * Curated subcategories + their approved interest tags, for browse or search.
     *
     * @return Collection<int, Subcategory>
     */
    #[Computed]
    public function subcategoriesWithTags(): Collection
    {
        $searching = trim($this->search) !== '';

        if (! $searching && $this->activeCategoryId === null) {
            return collect();
        }

        if ($searching) {
            return Subcategory::where('is_active', true)
                ->whereHas('tags', fn ($q) => $q
                    ->approved()
                    ->ofType('interest')
                    ->where('name', 'like', '%' . $this->search . '%')
                )
                ->with(['tags' => fn ($q) => $q
                    ->approved()
                    ->ofType('interest')
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orderBy('name')
                ])
                ->orderBy('sort_order')
                ->get();
        }

        return Subcategory::where('category_id', $this->activeCategoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['tags' => fn ($q) => $q->approved()->ofType('interest')->orderBy('name')])
            ->get();
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
            ->where('name', 'like', '%' . $q . '%')
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

    public function setCategory(?int $categoryId): void
    {
        $this->activeCategoryId = $this->activeCategoryId === $categoryId ? null : $categoryId;
        $this->search           = '';
        $this->maxTagsMessage   = null;
        $this->customTagMessage = null;
    }

    public function toggleTag(string $tagId): void
    {
        $this->maxTagsMessage   = null;
        $this->saveMessage      = null;
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
                $this->maxTagsMessage = 'You\'ve reached the maximum of ' . self::MAX_SELECTED . ' tags.';
                return;
            }

            $this->selectedTagIds[] = $tagId;
            Auth::user()->selectTag($tag);
        }

        unset($this->selectedTags);
    }

    public function save(): void
    {
        if (count($this->selectedTagIds) < 3) {
            $this->saveMessage = 'Please select at least 3 tags before saving.';
            return;
        }

        $current  = Auth::user()->tags()->pluck('tags.id')->toArray();
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
        $this->maxTagsMessage   = null;

        $name = (string) preg_replace('/\s+/', ' ', trim($this->search));

        if ($name === '') {
            return;
        }

        // Per-user lifetime limit
        if (Tag::where('source', 'custom')->where('created_by_user_id', Auth::id())->count() >= self::MAX_CUSTOM_PER_USER) {
            $this->customTagMessage = 'You\'ve reached the limit of ' . self::MAX_CUSTOM_PER_USER . ' personal interests. Remove one to add another.';
            return;
        }

        // Daily rate limit — secondary anti-abuse guard
        $rateKey = 'custom-tag.' . Auth::id();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->customTagMessage = 'You\'ve added too many personal interests today. Try again tomorrow.';
            return;
        }

        // Selection cap
        if (count($this->selectedTagIds) >= self::MAX_SELECTED) {
            $this->maxTagsMessage = 'You\'ve reached the maximum of ' . self::MAX_SELECTED . ' tags.';
            return;
        }

        // Validate name
        $validator = Validator::make(
            ['interest' => $name],
            ['interest' => ['required', 'string', new ValidCustomTag()]],
        );
        if ($validator->fails()) {
            $this->customTagMessage = $validator->errors()->first('interest');
            return;
        }

        $slug     = Str::slug($name);
        $existing = Tag::where('slug', $slug)->first();

        if ($existing) {
            // Tag already exists — just select it rather than creating a duplicate.
            if (! in_array($existing->id, $this->selectedTagIds, true)) {
                $this->selectedTagIds[] = $existing->id;
                Auth::user()->selectTag($existing);
                $this->customTagMessage = '"' . $existing->name . '" already exists and has been added to your interests.';
            } else {
                $this->customTagMessage = '"' . $existing->name . '" is already in your interests.';
            }
            $this->search = '';
            unset($this->subcategoriesWithTags, $this->myCustomTagResults, $this->selectedTags);
            return;
        }

        RateLimiter::hit($rateKey, 86400);

        $tag = Tag::create([
            'name'               => $name,
            'slug'               => $slug,
            'type'               => 'interest',
            'source'             => 'custom',
            'category'           => 'User Submitted',
            'created_by_user_id' => Auth::id(),
            'is_curated'         => false,
            'is_approved'        => false,
            'usage_count'        => 0,
        ]);

        $this->selectedTagIds[] = $tag->id;
        Auth::user()->selectTag($tag);

        $this->search = '';
        unset($this->subcategoriesWithTags, $this->myCustomTagResults, $this->selectedTags);

        $this->customTagMessage = '"' . $name . '" added as your personal interest.';
    }

    public function render(): View
    {
        return view('livewire.tags.tag-selector')
            ->layout('layouts.app', ['title' => 'Your Interests — CommonGrove']);
    }
}
