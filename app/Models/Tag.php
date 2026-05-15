<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'category',
        'is_curated',
        'is_approved',
        'usage_count',
    ];

    protected $casts = [
        'is_curated'  => 'boolean',
        'is_approved' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Users who have selected this tag.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tags')
            ->withPivot('created_at');
    }

    /**
     * Scope: only curated and approved tags visible to users.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true)->where('is_curated', true);
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
}
