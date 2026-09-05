<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReaction extends Model
{
    use HasUuids;

    public $timestamps = false;

    /** Full curated set — validated server-side. */
    public const ALLOWED = [
        '💚', '👋', '🤗', '😂', '🫂',
        '☕', '🌙', '🤔', '✨', '😭',
        '🎧', '👍', '💛', '❤️', '🙏',
        '😊', '🥹', '😔', '🫶', '💙',
        '🌿', '🍃', '🌱', '⭐', '🌸',
    ];

    /** Quick-tray subset shown on hover/tap before opening the full picker. */
    public const TRAY = ['💚', '👋', '🤗', '😂', '🫂'];

    /**
     * Curated picker sections.
     *
     * @var array<string, list<string>>
     */
    public const PICKER_GROUPS = [
        'Warm'       => ['💚', '🫂', '🤗', '🫶', '🙏', '❤️'],
        'Friendly'   => ['👋', '😊', '😂', '🥹', '👍', '💛'],
        'Cozy'       => ['☕', '🌙', '🎧', '✨', '🌸', '⭐'],
        'Thoughtful' => ['🤔', '😔', '😭', '💙', '🌿', '🍃'],
    ];

    protected $fillable = [
        'message_id',
        'user_id',
        'reaction',
        'created_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
