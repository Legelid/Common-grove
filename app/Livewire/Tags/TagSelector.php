<?php

declare(strict_types=1);

namespace App\Livewire\Tags;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TagSelector extends Component
{
    public string $search = '';

    /** @var list<string> IDs of currently selected tags */
    public array $selectedTagIds = [];

    public ?int $activeCategoryId = null;

    public string $customTagName = '';

    public ?string $customTagMessage = null;

    public ?string $maxTagsMessage = null;

    public ?string $saveMessage = null;

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
     * Subcategories with their tags for the active category, or search results.
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
     * Tags the user has already selected, for display in the selection bar.
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
        $this->search = '';
        $this->maxTagsMessage = null;
    }

    public function toggleTag(string $tagId): void
    {
        $this->maxTagsMessage = null;
        $this->saveMessage    = null;

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
            if (count($this->selectedTagIds) >= 30) {
                $this->maxTagsMessage = "You've reached the maximum of 30 tags.";
                return;
            }

            $this->selectedTagIds[] = $tagId;
            Auth::user()->selectTag($tag);
        }
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

    public function submitCustomTag(): void
    {
        $this->customTagMessage = null;

        $key = 'custom-tag.' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->customTagMessage = 'You can submit up to 3 tag suggestions per day.';
            return;
        }
        RateLimiter::hit($key, 86400);

        $this->validate(
            ['customTagName' => ['required', 'string', 'min:2', 'max:50']],
            ['customTagName.required' => 'Please enter a tag name.'],
        );

        $name = trim($this->customTagName);
        $slug = \Illuminate\Support\Str::slug($name);

        if (Tag::where('slug', $slug)->exists()) {
            $this->customTagMessage = 'That tag already exists — look for it in the list above.';
            return;
        }

        Tag::create([
            'name'        => $name,
            'slug'        => $slug,
            'type'        => 'interest',
            'category'    => 'User Submitted',
            'is_curated'  => false,
            'is_approved' => false,
            'usage_count' => 0,
        ]);

        $this->customTagName    = '';
        $this->customTagMessage = 'Your tag has been submitted for review — it will appear once approved.';
    }

    public function render(): View
    {
        return view('livewire.tags.tag-selector');
    }
}
