<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'hangout_post_id',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * All participants in this conversation.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_muted']);
    }

    /**
     * All messages in chronological order.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * The most recent message — used for conversation list previews.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * The user who created this conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The hangout post this room was created from (rooms only).
     */
    public function hangoutPost(): BelongsTo
    {
        return $this->belongsTo(HangoutPost::class);
    }

    /**
     * Message requests associated with this conversation.
     */
    public function messageRequests(): HasMany
    {
        return $this->hasMany(MessageRequest::class);
    }

    /**
     * Users who have pinned this conversation.
     */
    public function pinnedByUsers(): HasMany
    {
        return $this->hasMany(PinnedRoom::class);
    }

    /**
     * Tags attached directly to this conversation/room.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'conversation_tags')
            ->withPivot('created_at');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Only conversations the given user is a participant of.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('participants', function (Builder $q) use ($user): void {
            $q->where('user_id', $user->id);
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isRoom(): bool
    {
        return $this->type === 'room';
    }

    public function isDirect(): bool
    {
        return $this->type === 'direct';
    }
}
