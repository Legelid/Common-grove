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
use Illuminate\Support\Collection;

class Tag extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'source',
        'category',
        'category_id',
        'subcategory_id',
        'is_curated',
        'is_approved',
        'usage_count',
        'created_by_user_id',
        'approved_at',
    ];

    protected $casts = [
        'is_curated'     => 'boolean',
        'is_approved'    => 'boolean',
        'usage_count'    => 'integer',
        'category_id'    => 'integer',
        'subcategory_id' => 'integer',
        'approved_at'    => 'datetime',
    ];

    /** User who submitted this tag (null for curated/seeded tags). */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Users who have selected this tag.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tags')
            ->withPivot('created_at');
    }

    /**
     * Additional subcategory placements beyond the tag's canonical
     * category_id/subcategory_id home (Phase 1 of the interests overhaul —
     * lets one tag surface in more than one subcategory).
     */
    public function subcategoryPlacements(): HasMany
    {
        return $this->hasMany(TagSubcategoryPlacement::class)->orderBy('display_order');
    }

    /**
     * Scope: only curated and approved tags visible to users.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true)->where('is_curated', true);
    }

    /**
     * Scope: tags whose canonical subcategory is flagged sensitive.
     */
    public function scopeSensitive(Builder $query): Builder
    {
        return $query->whereHas('subcategory', fn (Builder $q) => $q->where('is_sensitive', true));
    }

    /**
     * Scope: top 10 tags by usage_count (popular picks).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('usage_count', 'desc')->limit(10);
    }

    /**
     * Scope: filter by tag type (interest, shared_experience, vibe).
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Builds a raw SQL `CASE` expression (and its parameter bindings)
     * scoring a joined tag row against a user's exact/subcategory/category
     * interest overlap: exact tag = 3, same subcategory = 2, same
     * category = 1, otherwise 0. Shared by HangoutPost::scopeForUser() and
     * FriendsList::suggestedPeople() (Phase 5 matching) so the three-tier
     * scoring rule lives in exactly one place.
     *
     * All three ID collections may be empty; empty tiers are simply
     * omitted from the CASE rather than emitting an invalid `IN ()`.
     *
     * @param  Collection<int, string>  $exactTagIds
     * @param  Collection<int, int>  $subcategoryIds
     * @param  Collection<int, int>  $categoryIds
     * @param  string  $tagIdColumn  qualified column holding the tag id being scored
     * @param  string  $subcategoryColumn  qualified column holding that tag's subcategory_id
     * @param  string  $categoryColumn  qualified column holding that tag's category_id
     * @return array{0: string, 1: list<mixed>}
     */
    public static function tierScoreExpression(
        Collection $exactTagIds,
        Collection $subcategoryIds,
        Collection $categoryIds,
        string $tagIdColumn,
        string $subcategoryColumn,
        string $categoryColumn,
    ): array {
        $bindings = [];
        $whens    = [];

        if ($exactTagIds->isNotEmpty()) {
            $whens[] = "WHEN {$tagIdColumn} IN (" . implode(',', array_fill(0, $exactTagIds->count(), '?')) . ') THEN 3';
            array_push($bindings, ...$exactTagIds->all());
        }

        if ($subcategoryIds->isNotEmpty()) {
            $whens[] = "WHEN {$subcategoryColumn} IN (" . implode(',', array_fill(0, $subcategoryIds->count(), '?')) . ') THEN 2';
            array_push($bindings, ...$subcategoryIds->all());
        }

        if ($categoryIds->isNotEmpty()) {
            $whens[] = "WHEN {$categoryColumn} IN (" . implode(',', array_fill(0, $categoryIds->count(), '?')) . ') THEN 1';
            array_push($bindings, ...$categoryIds->all());
        }

        $sql = 'CASE ' . implode(' ', $whens) . ' ELSE 0 END';

        return [$sql, $bindings];
    }
}
