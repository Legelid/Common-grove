<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementLog extends Model
{
    use HasUuids;

    /** @var list<string> */
    protected $fillable = [
        'sent_by',
        'subject',
        'body',
        'recipient_count',
    ];

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
