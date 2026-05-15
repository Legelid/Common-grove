<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'is_request',
        'read_at',
        // Group 6 — pinned messages
        'is_pinned',
        'pinned_at',
        'pinned_by',
        // Group 8 — content warnings
        'has_cw',
        'cw_label',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_request' => 'boolean',
            'read_at'    => 'datetime',
            'is_pinned'  => 'boolean',
            'pinned_at'  => 'datetime',
            'has_cw'     => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Reactions on this message. */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * The sender's display name, respecting their identity_mode.
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->display_name ?? 'Unknown';
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns reaction type → count pairs for non-zero counts.
     *
     * @return array<string, int>
     */
    public function reactionCounts(): array
    {
        return $this->reactions()
            ->selectRaw('reaction, COUNT(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction')
            ->map(fn ($c) => (int) $c)
            ->all();
    }
}
