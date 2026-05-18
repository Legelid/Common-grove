<?php

declare(strict_types=1);

namespace App\Livewire\Rooms;

use App\Models\RoomCollection;
use App\Models\RoomCollectionItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AddToCollection extends Component
{
    public string $conversationId = '';

    public string  $newName  = '';
    public bool    $showForm = false;
    public ?string $message  = null;

    private function maxCollections(): int
    {
        return (int) config('supporter.limits.room_collections.max_collections', 10);
    }

    private function maxRooms(): int
    {
        return (int) config('supporter.limits.room_collections.max_rooms_per_collection', 20);
    }

    /**
     * All collections for this user, each decorated with an `in_collection` flag.
     *
     * @return Collection<int, RoomCollection>
     */
    #[Computed]
    public function collections(): Collection
    {
        $inIds = RoomCollectionItem::whereHas('collection', function ($q): void {
            $q->where('user_id', Auth::id());
        })
            ->where('room_id', $this->conversationId)
            ->pluck('room_collection_id')
            ->flip();

        return RoomCollection::where('user_id', Auth::id())
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->each(function (RoomCollection $c) use ($inIds): void {
                $c->in_collection = $inIds->has($c->id);
            });
    }

    public function toggle(string $collectionId): void
    {
        $collection = RoomCollection::where('id', $collectionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $existing = RoomCollectionItem::where('room_collection_id', $collectionId)
            ->where('room_id', $this->conversationId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $count = $collection->items()->count();
            if ($count >= $this->maxRooms()) {
                $this->message = 'That collection is full (max ' . $this->maxRooms() . ' rooms).';
                return;
            }
            RoomCollectionItem::create([
                'room_collection_id' => $collectionId,
                'room_id'            => $this->conversationId,
                'sort_order'         => $count,
            ]);
        }

        $this->message = null;
        unset($this->collections);
        $this->dispatch('collection-updated');
    }

    public function create(): void
    {
        $name = trim($this->newName);

        if ($name === '') {
            return;
        }

        $user = Auth::user();

        if (! $user->isSupporter()) {
            return;
        }

        $count = RoomCollection::where('user_id', $user->id)->count();

        if ($count >= $this->maxCollections()) {
            $this->message  = 'You can have up to ' . $this->maxCollections() . ' collections.';
            $this->showForm = false;
            return;
        }

        $collection = RoomCollection::create([
            'user_id'    => $user->id,
            'name'       => $name,
            'sort_order' => $count,
        ]);

        $roomCount = $collection->items()->count();

        if ($roomCount < $this->maxRooms()) {
            RoomCollectionItem::create([
                'room_collection_id' => $collection->id,
                'room_id'            => $this->conversationId,
                'sort_order'         => 0,
            ]);
        }

        $this->newName  = '';
        $this->showForm = false;
        $this->message  = null;
        unset($this->collections);
        $this->dispatch('collection-updated');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.rooms.add-to-collection');
    }
}
