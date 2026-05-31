<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\BetaInvite;
use App\Models\UserSubscription;
use App\Models\RoomCollection;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'gamertag',
        'display_name',
        'email',
        'password',
        'identity_mode',
        'show_names_pref',
        'password_reset_required',
        'bio',
        'avatar_path',
        'avatar_id',
        'last_seen_at',
        'last_gamertag_changed_at',
        'suspended_at',
        'dismiss_count',
        // Group 2 — status
        'status_text',
        'status_mood',
        'status_expires_at',
        // Group 3 — currently into
        'currently_playing',
        'currently_reading',
        'currently_watching',
        // Group 9 — read receipts
        'show_read_receipts',
        // Group 11 — discovery
        'show_connection_suggestions',
        'low_stimulation_mode',
        'is_supporter',
        'show_supporter_icon',
        'show_official_rooms',
        // Group 10 — onboarding
        'onboarding_completed',
        'comfort_preferences',
        // Age gate & birthday
        'date_of_birth',
        'birthday_theme_enabled',
        'holiday_themes_enabled',
        'tone_pack',
        // Profile expression
        'profile_status',
        'accent_color',
        'banner_style',
        'personal_gradient_theme',
        'comfort_things',
        'open_to',
        'social_styles',
        'show_conversation_prompts',
        'enabled_prompt_packs',
        'advanced_comfort_settings',
        'hide_reactions',
        // FirstRoots
        'is_first_roots',
        'first_roots_awarded_at',
        'beta_invite_id',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'show_names_pref'            => 'boolean',
            'password_reset_required'    => 'boolean',
            'identity_mode'              => 'integer',
            'last_seen_at'               => 'datetime',
            'last_gamertag_changed_at'   => 'datetime',
            'suspended_at'               => 'datetime',
            'is_admin'                   => 'boolean',
            'status_expires_at'          => 'datetime',
            'show_read_receipts'             => 'boolean',
            'show_connection_suggestions'   => 'boolean',
            'low_stimulation_mode'          => 'boolean',
            'is_supporter'                  => 'boolean',
            'show_supporter_icon'           => 'boolean',
            'show_official_rooms'           => 'boolean',
            'onboarding_completed'          => 'boolean',
            'comfort_preferences'        => 'array',
            'date_of_birth'              => 'date',
            'birthday_theme_enabled'     => 'boolean',
            'holiday_themes_enabled'     => 'boolean',
            'tone_pack'                  => 'string',
            'show_conversation_prompts'   => 'boolean',
            'enabled_prompt_packs'        => 'array',
            'advanced_comfort_settings'   => 'array',
            'hide_reactions'              => 'boolean',
            'comfort_things'             => 'array',
            'open_to'                    => 'array',
            'social_styles'              => 'array',
            'is_first_roots'             => 'boolean',
            'first_roots_awarded_at'     => 'datetime',
        ];
    }

    /**
     * Send the email verification notification using the custom CommonGrove branded email.
     */
    public function sendEmailVerificationNotification(): void
    {
        if (empty($this->email)) {
            Log::warning('sendEmailVerificationNotification: user has no email — skipping', ['user_id' => $this->id]);
            return;
        }

        $this->notify(new VerifyEmailNotification());
    }

    /**
     * Send the password reset notification using the custom CommonGrove branded email.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Returns the appropriate name to show others, based on identity_mode.
     *
     * Mode 1 — gamertag only
     * Mode 2 — gamertag only (preferred name shown as a tooltip via preferred_name)
     * Mode 3 — display_name if set, else gamertag
     *
     * Reads the raw database value via $this->attributes to avoid recursion.
     */
    public function getDisplayNameAttribute(): string
    {
        $raw = $this->attributes['display_name'] ?? null;

        return match ($this->identity_mode) {
            2       => $this->gamertag,
            3       => $raw ?? $this->gamertag,
            default => $this->gamertag,
        };
    }

    /**
     * Returns the stored display name for mode-2 users only.
     * Consumed by the x-user-name component to render a "Prefers X" tooltip.
     */
    public function getPreferredNameAttribute(): ?string
    {
        if ($this->identity_mode !== 2) {
            return null;
        }

        return $this->attributes['display_name'] ?? null;
    }

    /**
     * Returns the absolute URL to the user's avatar, or a placeholder.
     *
     * Resolution order:
     *  1. avatar_id → Avatar record: show if active or retired; fall back to
     *     default if disabled (hidden everywhere) or record missing.
     *  2. avatar_path starting with "curated:" — legacy pre-migration path.
     *  3. avatar_path as an S3 key — legacy file upload (no longer created).
     *  4. Default placeholder SVG.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_id !== null) {
            $avatar = $this->avatar;

            if ($avatar === null || $avatar->status === 'disabled') {
                return asset('images/default-avatar.svg');
            }

            return asset($avatar->image_path);
        }

        // Legacy: curated: path written before the avatar_id migration
        if ($this->avatar_path && str_starts_with($this->avatar_path, 'curated:')) {
            return asset('avatars/curated/' . substr($this->avatar_path, 8) . '.svg');
        }

        if ($this->avatar_path) {
            return Storage::disk('s3')->url($this->avatar_path);
        }

        return asset('images/default-avatar.svg');
    }

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    /**
     * True when the user has an active supporter subscription.
     * Prefer this over reading is_supporter directly so gating logic is one call.
     */
    public function isSupporter(): bool
    {
        return (bool) $this->is_supporter;
    }

    /**
     * Maximum number of active persistent rooms this user may own.
     * Admins are unlimited. Read from config so the value has one source of truth.
     */
    public function persistentRoomLimit(): int
    {
        if ($this->is_admin) {
            return PHP_INT_MAX;
        }

        $key = $this->is_supporter ? 'supporter' : 'free';

        return (int) config("supporter.limits.persistent_rooms.{$key}", $this->is_supporter ? 10 : 3);
    }

    /**
     * Returns true if today is the user's birthday (month + day match).
     */
    public function isBirthday(): bool
    {
        return $this->date_of_birth !== null
            && $this->date_of_birth->format('m-d') === now()->format('m-d');
    }

    /**
     * Returns true if the user was seen within the last 15 minutes.
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at !== null
            && $this->last_seen_at->isAfter(now()->subMinutes(15));
    }

    /**
     * Returns true if the user has an unexpired status set.
     */
    public function hasActiveStatus(): bool
    {
        return $this->status_expires_at !== null
            && $this->status_expires_at->isAfter(now());
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tags the user has selected.
     * No withTimestamps() — created_at is filled by the DB default on the pivot.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_tags')
            ->withPivot('created_at');
    }

    /**
     * Hangout posts authored by this user.
     */
    public function hangoutPosts(): HasMany
    {
        return $this->hasMany(HangoutPost::class);
    }

    /**
     * Conversations this user is a participant of.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_muted', 'left_at']);
    }

    /**
     * Notification preferences for this user.
     */
    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /** Selected curated avatar. */
    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Avatar::class);
    }

    /** Per-conversation display preferences (gradient, etc.) for this user. */
    public function conversationPreferences(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserConversationPreference::class);
    }

    /** PayPal and other payment subscriptions. */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /** Room collections (supporter feature). */
    public function roomCollections(): HasMany
    {
        return $this->hasMany(RoomCollection::class)->orderBy('sort_order');
    }

    /** Returns the active subscription if one exists. */
    public function activeSubscription(): ?UserSubscription
    {
        return $this->subscriptions()->where('status', 'active')->latest()->first();
    }

    // -------------------------------------------------------------------------
    // Safety relationships
    // -------------------------------------------------------------------------

    /** Users this user has blocked. */
    public function blockedUsers(): HasMany
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    /** Users who have blocked this user. */
    public function blockedByUsers(): HasMany
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    /** Users this user has muted. */
    public function mutedUsers(): HasMany
    {
        return $this->hasMany(Mute::class, 'muter_id');
    }

    /** Reports filed by this user. */
    public function reportsFiled(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /** Reports filed against this user. */
    public function reportsReceived(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    /** Strikes issued to this user. */
    public function strikes(): HasMany
    {
        return $this->hasMany(Strike::class);
    }

    // -------------------------------------------------------------------------
    // Safety helpers
    // -------------------------------------------------------------------------

    public function hasBlocked(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_id', $user->id)->exists();
    }

    public function isBlockedBy(User $user): bool
    {
        return $this->blockedByUsers()->where('blocker_id', $user->id)->exists();
    }

    public function hasMuted(User $user): bool
    {
        return $this->mutedUsers()->where('muted_id', $user->id)->exists();
    }

    /**
     * Returns the highest active strike level (1–3), or 0 if none.
     */
    public function activeStrikeLevel(): int
    {
        $strike = $this->strikes()
            ->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('level')
            ->first();

        return $strike ? $strike->level : 0;
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /**
     * Restricted users (strike level 2+) can read but not send messages.
     */
    public function isRestricted(): bool
    {
        return $this->activeStrikeLevel() >= 2;
    }

    /**
     * Users whose reports are dismissed at a high rate — potential bad actors.
     *
     * @param  Builder<User>  $query
     */
    public function scopeSerialReporters(Builder $query): Builder
    {
        return $query->where('dismiss_count', '>=', 5);
    }

    // -------------------------------------------------------------------------
    // Tag helpers
    // -------------------------------------------------------------------------

    /**
     * Attach a tag and increment its usage count.
     * Safe to call even if already attached — will not double-attach.
     */
    public function selectTag(Tag $tag): void
    {
        if ($this->tags()->where('tag_id', $tag->id)->doesntExist()) {
            $this->tags()->attach($tag->id);
            Tag::where('id', $tag->id)->increment('usage_count');
        }
    }

    /**
     * Detach a tag and decrement its usage count, never below zero.
     */
    public function deselectTag(Tag $tag): void
    {
        $detached = $this->tags()->detach($tag->id);

        if ($detached > 0) {
            Tag::where('id', $tag->id)->where('usage_count', '>', 0)->decrement('usage_count');
        }
    }

    // -------------------------------------------------------------------------
    // Pinned rooms
    // -------------------------------------------------------------------------

    public function pinnedRooms(): HasMany
    {
        return $this->hasMany(PinnedRoom::class)->latest();
    }

    // -------------------------------------------------------------------------
    // Friendship relationships
    // -------------------------------------------------------------------------

    /** Friendships where this user sent the request. */
    public function sentFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    /** Friendships where this user received the request. */
    public function receivedFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'recipient_id');
    }

    /** All accepted Friendship records in either direction. */
    public function friendships(): Collection
    {
        return $this->sentFriendships()->where('status', 'accepted')->get()
            ->merge($this->receivedFriendships()->where('status', 'accepted')->get());
    }

    /**
     * All User models who are accepted friends.
     *
     * @return Collection<int, User>
     */
    public function friends(): Collection
    {
        $sentIds     = $this->sentFriendships()->where('status', 'accepted')->pluck('recipient_id');
        $receivedIds = $this->receivedFriendships()->where('status', 'accepted')->pluck('requester_id');

        return User::whereIn('id', $sentIds->merge($receivedIds))->get();
    }

    /** Pending friend requests received by this user. */
    public function pendingRequestsReceived(): HasMany
    {
        return $this->hasMany(Friendship::class, 'recipient_id')->where('status', 'pending');
    }

    /** Pending friend requests sent by this user. */
    public function pendingRequestsSent(): HasMany
    {
        return $this->hasMany(Friendship::class, 'requester_id')->where('status', 'pending');
    }

    // -------------------------------------------------------------------------
    // Friendship helpers
    // -------------------------------------------------------------------------

    public function isFriendWith(User $user): bool
    {
        return Friendship::where('status', 'accepted')
            ->where(function ($q) use ($user): void {
                $q->where(function ($inner) use ($user): void {
                    $inner->where('requester_id', $this->id)->where('recipient_id', $user->id);
                })->orWhere(function ($inner) use ($user): void {
                    $inner->where('requester_id', $user->id)->where('recipient_id', $this->id);
                });
            })
            ->exists();
    }

    public function hasPendingRequestFrom(User $user): bool
    {
        return Friendship::where('requester_id', $user->id)
            ->where('recipient_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasSentRequestTo(User $user): bool
    {
        return Friendship::where('requester_id', $this->id)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function betaInvite(): BelongsTo
    {
        return $this->belongsTo(BetaInvite::class);
    }
}
