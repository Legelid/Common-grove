<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetaInvite extends Model
{
    use HasUuids;

    /** @var list<string> */
    protected $fillable = [
        'email',
        'token',
        'claimed_by',
        'claimed_at',
        'sent_at',
        'expires_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'claimed_at' => 'datetime',
            'sent_at'    => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    public static function generate(string $email): self
    {
        return self::create([
            'email' => $email,
            'token' => bin2hex(random_bytes(32)),
        ]);
    }
}
