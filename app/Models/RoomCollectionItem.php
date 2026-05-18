<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCollectionItem extends Model
{
    use HasUuids;

    protected $fillable = ['room_collection_id', 'room_id', 'sort_order'];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(RoomCollection::class, 'room_collection_id');
    }

    /**
     * The conversation (room or hangout) this item points to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'room_id');
    }
}
