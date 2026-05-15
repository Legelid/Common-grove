<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class TagModeration extends Component
{
    use WithPagination;

    public string $tab = 'pending';

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tags(): LengthAwarePaginator
    {
        return match ($this->tab) {
            'pending'  => Tag::where('is_curated', false)
                ->where('is_approved', false)
                ->orderBy('created_at', 'asc')
                ->paginate(25),
            'approved' => Tag::where('is_curated', true)
                ->where('is_approved', true)
                ->orderByDesc('usage_count')
                ->paginate(25),
            default => Tag::query()->paginate(25),
        };
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

    public function approve(string $tagId): void
    {
        Tag::where('id', $tagId)->update(['is_curated' => true, 'is_approved' => true]);
        unset($this->tags, $this->tabCounts);
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
