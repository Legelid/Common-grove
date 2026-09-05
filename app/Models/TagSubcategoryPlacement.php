<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagSubcategoryPlacement extends Model
{
    protected $fillable = [
        'tag_id',
        'subcategory_id',
        'is_primary',
        'display_order',
    ];

    protected $casts = [
        'is_primary'     => 'boolean',
        'display_order'  => 'integer',
        'subcategory_id' => 'integer',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
