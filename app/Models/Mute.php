<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mute extends Model
{
    use HasUuids;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['muter_id', 'muted_id', 'created_at'];

    public function muter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'muter_id');
    }

    public function muted(): BelongsTo
    {
        return $this->belongsTo(User::class, 'muted_id');
    }
}
