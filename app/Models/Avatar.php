<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Avatar extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'category',
        'image_path',
        'status',
    ];

    // -------------------------------------------------------------------------
    // Category display labels
    // -------------------------------------------------------------------------

    /**
     * Extend this map when new categories are added via migration/seeder.
     * Falls back to title-cased slug if a label is missing.
     */
    private const CATEGORY_LABELS = [
        'cozy_objects'  => 'Cozy Objects',
        'nature'        => 'Nature & Atmosphere',
        'retro_digital' => 'Cozy Digital / Retro',
        'mood_vibe'     => 'Mood & Vibe',
        'minimal'       => 'Minimal',
        'seasonal'      => 'Seasonal',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category]
            ?? Str::title(str_replace('_', ' ', $this->category));
    }

    /** Absolute URL for use in img src attributes. */
    public function getUrlAttribute(): string
    {
        return asset($this->image_path);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Only avatars that appear in the picker.
     *
     * @param  Builder<Avatar>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Avatars that may still be shown on user profiles (active + retired).
     * Retired avatars are hidden from the picker but preserved on profiles.
     *
     * @param  Builder<Avatar>  $query
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'retired']);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /** @return HasMany<User> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
