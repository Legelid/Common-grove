<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $primaryKey  = 'user_id';
    public    $incrementing = false;
    protected $keyType     = 'string';
    public    $timestamps  = false;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'friend_request',
        'friend_accepted',
        'new_message',
        'message_request',
        'hangout_from_friend',
        'weekly_match',
        'milestone',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'friend_request'     => 'boolean',
            'friend_accepted'    => 'boolean',
            'new_message'        => 'boolean',
            'message_request'    => 'boolean',
            'hangout_from_friend' => 'boolean',
            'weekly_match'       => 'boolean',
            'milestone'          => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
