<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRequest extends Model
{
    use HasFactory;
    use HasUuids;

    /** No updated_at — pivot-style table. */
    public $timestamps = false;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'conversation_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
