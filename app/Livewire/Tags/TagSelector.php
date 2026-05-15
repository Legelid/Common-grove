<?php

declare(strict_types=1);

namespace App\Livewire\Tags;

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
     * Top 10 approved curated tags by usage_count.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function popularTags(): Collection
    {
        return Tag::approved()->popular()->get();
    }

    /**
     * Approved curated tags grouped by category, filtered by search.
     *
     * @return Collection<string, Collection<int, Tag>>
     */
    #[Computed]
    public function tagsByCategory(): Collection
    {
        $query = Tag::approved()->orderBy('name');

        if (trim($this->search) !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->get()->groupBy('category');
    }

    /**
     * Toggle a tag selected or unselected.
     */
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

    /**
     * Persist the current selection and surface a success message.
     */
    public function save(): void
    {
        if (count($this->selectedTagIds) < 3) {
            $this->saveMessage = 'Please select at least 3 tags before saving.';
            return;
        }

        // Final reconcile — syncs pivot to match selectedTagIds without touching usage_count
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

        $this->saveMessage = 'Your interests have been saved!';
    }

    /**
     * Submit a user-created tag for moderation.
     */
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
