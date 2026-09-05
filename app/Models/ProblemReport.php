<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemReport extends Model
{
    use HasUuids;

    protected $table = 'problem_reports';

    protected $fillable = [
        'user_id',
        'report_type',
        'subject',
        'description',
        'page_url',
        'screenshot_path',
        'related_user',
        'related_room',
        'related_message',
        'contact_email',
        'status',
        'priority',
        'admin_note',
    ];

    public const TYPES = [
        'bug'         => 'Bug or broken feature',
        'safety'      => 'Safety concern',
        'user-report' => 'Report a user or room',
        'account'     => 'Account issue',
        'feedback'    => 'Feedback or suggestion',
        'other'       => 'Something else',
    ];

    public const STATUSES = ['open', 'reviewing', 'resolved', 'dismissed'];

    public const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->report_type] ?? $this->report_type;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
