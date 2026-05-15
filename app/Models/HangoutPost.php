<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HangoutPost extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'content',
        'expires_at',
        'is_active',
        'is_persistent',
        'is_official',
        'joined_count',
    ];

    protected $casts = [
        'expires_at'    => 'datetime',
        'is_active'     => 'boolean',
        'is_persistent' => 'boolean',
        'is_official'   => 'boolean',
        'joined_count'  => 'integer',
    ];

    /**
     * The user who created this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The conversation (room) created from this post, if one exists.
     */
    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'hangout_post_id');
    }

    /**
     * Tags attached to this hangout post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'hangout_post_tags');
    }

    /**
     * Scope: posts that are active and not yet expired.
     * Persistent rooms (is_persistent = true) are always included while is_active.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->where('is_persistent', true)
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: posts that share at least one tag with the given user's interests.
     * Returns all active posts if the user has no tags selected.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        $blockedIds = \App\Models\Block::where('blocker_id', $user->id)
            ->pluck('blocked_id')
            ->merge(
                \App\Models\Block::where('blocked_id', $user->id)
                    ->pluck('blocker_id')
            )
            ->unique()
            ->values();

        if ($blockedIds->isNotEmpty()) {
            $query->whereNotIn('user_id', $blockedIds);
        }

        $userTagIds = $user->tags()->pluck('tags.id');

        if ($userTagIds->isEmpty()) {
            return $query;
        }

        return $query->whereHas('tags', function (Builder $q) use ($userTagIds): void {
            $q->whereIn('tags.id', $userTagIds);
        });
    }

    /**
     * Returns true if the post's expiry time has passed.
     * Persistent rooms never expire.
     */
    public function isExpired(): bool
    {
        if ($this->is_persistent || $this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Returns a human-readable countdown string, e.g. "4h 12m" or "45m".
     * Returns empty string for persistent rooms (no timer to show).
     */
    public function expiresInFormatted(): string
    {
        if ($this->is_persistent || $this->expires_at === null) {
            return '';
        }

        if ($this->isExpired()) {
            return 'ended';
        }

        $diff = now()->diff($this->expires_at);

        if ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m";
    }
}
