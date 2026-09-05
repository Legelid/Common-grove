<?php

declare(strict_types=1);

namespace App\Livewire\Rooms;

use App\Models\RoomCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RoomCollectionsSidebar extends Component
{
    public string  $newName   = '';
    public bool    $showForm  = false;
    public ?string $message   = null;

    private function maxCollections(): int
    {
        return (int) config('supporter.limits.room_collections.max_collections', 10);
    }

    /**
     * @return Collection<int, RoomCollection>
     */
    #[Computed]
    public function collections(): Collection
    {
        return RoomCollection::where('user_id', Auth::id())
            ->with(['items' => function ($q): void {
                $q->with(['conversation', 'conversation.hangoutPost'])
                  ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();
    }

    public function create(): void
    {
        $name = trim($this->newName);

        if ($name === '' || ! Auth::user()->isSupporter()) {
            return;
        }

        $count = RoomCollection::where('user_id', Auth::id())->count();

        if ($count >= $this->maxCollections()) {
            $this->message  = 'Maximum ' . $this->maxCollections() . ' collections reached.';
            $this->showForm = false;
            return;
        }

        RoomCollection::create([
            'user_id'    => Auth::id(),
            'name'       => $name,
            'sort_order' => $count,
        ]);

        $this->newName  = '';
        $this->showForm = false;
        $this->message  = null;
        unset($this->collections);
    }

    public function rename(string $id, string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            return;
        }

        RoomCollection::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['name' => $name]);

        unset($this->collections);
    }

    public function delete(string $id): void
    {
        RoomCollection::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        unset($this->collections);
    }

    public function removeRoom(string $collectionId, string $roomId): void
    {
        RoomCollection::where('id', $collectionId)
            ->where('user_id', Auth::id())
            ->firstOrFail()
            ->items()
            ->where('room_id', $roomId)
            ->delete();

        unset($this->collections);
    }

    #[On('collection-updated')]
    public function refresh(): void
    {
        unset($this->collections);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.rooms.room-collections-sidebar');
    }
}
