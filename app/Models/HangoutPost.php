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
use Illuminate\Support\Facades\DB;

class HangoutPost extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'expires_at',
        'is_active',
        'is_persistent',
        'is_official',
        'icon',
        'gradient_theme',
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
     * Scope: posts matched to the given user's interests via three-tier
     * scoring (Phase 5) — exact tag = 3, same subcategory = 2, same
     * category = 1, summed across every tag the post carries. Only posts
     * scoring above zero at any tier are returned, ordered by score desc
     * then activity (updated_at desc). Returns all active posts, untouched
     * and unordered by this scope, if the user has no tags selected —
     * never an empty result just because interests aren't set.
     *
     * Deliberately calls reorder() before applying its own ordering, so
     * this scope's score-first ordering wins regardless of whether a
     * caller chained .latest() before or after calling this scope.
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

        $userSubcategoryIds = Tag::whereIn('id', $userTagIds)->whereNotNull('subcategory_id')->pluck('subcategory_id')->unique()->values();
        $userCategoryIds    = Tag::whereIn('id', $userTagIds)->whereNotNull('category_id')->pluck('category_id')->unique()->values();

        [$caseSql, $bindings] = Tag::tierScoreExpression(
            $userTagIds,
            $userSubcategoryIds,
            $userCategoryIds,
            'hpt.tag_id',
            't.subcategory_id',
            't.category_id',
        );

        $scoreSubquery = DB::table('hangout_post_tags as hpt')
            ->join('tags as t', 't.id', '=', 'hpt.tag_id')
            ->selectRaw("hpt.hangout_post_id, SUM({$caseSql}) as interest_match_score", $bindings)
            ->groupBy('hpt.hangout_post_id')
            ->havingRaw('interest_match_score > 0');

        $query->joinSub($scoreSubquery, 'interest_scores', 'hangout_posts.id', '=', 'interest_scores.hangout_post_id')
            ->addSelect('hangout_posts.*', 'interest_scores.interest_match_score')
            ->reorder()
            ->orderByDesc('interest_scores.interest_match_score')
            ->orderByDesc('hangout_posts.updated_at');

        return $query;
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
