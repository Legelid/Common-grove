<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class TagModeration extends Component
{
    use WithPagination;

    public string $tab = 'pending';

    public ?int $filterCategoryId = null;

    public bool $filterSensitiveOnly = false;

    /** @var list<string> Tag IDs checked for bulk approval (pending tab only). */
    public array $selectedForBulk = [];

    public function updatedTab(): void
    {
        $this->resetPage();
        $this->selectedForBulk = [];
    }

    public function updatedFilterCategoryId(): void
    {
        $this->resetPage();
        $this->selectedForBulk = [];
    }

    public function updatedFilterSensitiveOnly(): void
    {
        $this->resetPage();
        $this->selectedForBulk = [];
    }

    /**
     * Active categories for the filter dropdown.
     *
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function tags(): LengthAwarePaginator
    {
        $query = Tag::query()->with(['category', 'subcategory']);

        if ($this->tab === 'pending') {
            $query->where('is_curated', false)
                ->where('is_approved', false)
                ->with('createdBy:id,gamertag')
                ->orderBy('created_at', 'asc');
        } elseif ($this->tab === 'approved') {
            $query->where('is_curated', true)
                ->where('is_approved', true)
                ->orderByDesc('usage_count');
        }

        if ($this->filterCategoryId !== null) {
            $query->where('category_id', $this->filterCategoryId);
        }

        if ($this->filterSensitiveOnly) {
            $query->whereHas('subcategory', fn ($q) => $q->where('is_sensitive', true));
        }

        return $query->paginate(25);
    }

    /** @return array<string, int> */
    #[Computed]
    public function tabCounts(): array
    {
        return [
            'pending'  => Tag::where('is_curated', false)->where('is_approved', false)->count(),
            'approved' => Tag::where('is_curated', true)->where('is_approved', true)->count(),
        ];
    }

    /**
     * Check every tag currently visible on this page (respecting the
     * active tab/filters/pagination) for bulk approval.
     */
    public function selectAllVisible(): void
    {
        $this->selectedForBulk = $this->tags->pluck('id')->all();
    }

    public function clearBulkSelection(): void
    {
        $this->selectedForBulk = [];
    }

    /**
     * Approve every currently-checked tag. Reuses the exact same update
     * approve() performs, so bulk and single approval stay identical.
     */
    public function bulkApprove(): void
    {
        foreach ($this->selectedForBulk as $tagId) {
            $this->approveOne($tagId);
        }

        $this->selectedForBulk = [];
        unset($this->tags, $this->tabCounts);
    }

    public function approve(string $tagId): void
    {
        $this->approveOne($tagId);
        unset($this->tags, $this->tabCounts);
    }

    private function approveOne(string $tagId): void
    {
        Tag::where('id', $tagId)->update([
            'is_curated'  => true,
            'is_approved' => true,
            'source'      => 'curated',
            'approved_at' => now(),
        ]);
    }

    public function reject(string $tagId): void
    {
        $tag = Tag::find($tagId);

        if ($tag === null) {
            return;
        }

        // Detach from all users before deleting
        $tag->users()->detach();
        $tag->delete();
        unset($this->tags, $this->tabCounts);
    }

    public function deprecate(string $tagId): void
    {
        Tag::where('id', $tagId)->update(['is_approved' => false]);
        unset($this->tags, $this->tabCounts);
    }

    public function render(): View
    {
        return view('livewire.admin.tag-moderation')
            ->layout('layouts.admin', ['title' => 'Tag Moderation']);
    }
}
