<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\HangoutPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AllRooms extends Component
{
    use WithPagination;

    public string $search = '';

    /** all | active | expired */
    public string $statusFilter = 'all';

    /** all | official | user */
    public string $typeFilter = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    /** @return LengthAwarePaginator<HangoutPost> */
    #[Computed]
    public function rooms(): LengthAwarePaginator
    {
        return HangoutPost::withTrashed()
            ->with(['user', 'tags', 'conversation'])
            ->withCount(['conversation as participant_count' => function ($q): void {
                $q->join('conversation_participants', 'conversations.id', '=', 'conversation_participants.conversation_id')
                  ->whereNull('conversation_participants.left_at');
            }])
            ->withCount(['conversation as message_count' => function ($q): void {
                $q->join('messages', 'conversations.id', '=', 'messages.conversation_id')
                  ->whereNull('messages.deleted_at');
            }])
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($inner): void {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', fn ($u) => $u->where('gamertag', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->active())
            ->when($this->statusFilter === 'expired', function ($q): void {
                $q->where('is_active', true)
                  ->where('is_persistent', false)
                  ->where('expires_at', '<=', now());
            })
            ->when($this->typeFilter === 'official', fn ($q) => $q->where('is_official', true))
            ->when($this->typeFilter === 'user', fn ($q) => $q->where('is_official', false))
            ->orderByDesc('created_at')
            ->paginate(30);
    }

    public function render(): View
    {
        return view('livewire.admin.all-rooms')
            ->layout('layouts.admin', ['title' => 'All Rooms']);
    }
}
