<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Queues one mailable per recipient with an increasing delay between each,
 * so a large batch doesn't trip the mail provider's rate limits. Shared by
 * the win-back command and the admin announcement tool — anything sending
 * to more than a handful of users at once should go through this rather
 * than looping synchronously.
 */
class BulkMailer
{
    public const STAGGER_SECONDS = 3;

    /**
     * Queue $mailable to $email. $index is this recipient's position in the
     * batch (0-based) — the actual send delay is index * STAGGER_SECONDS.
     */
    public function queue(string $email, Mailable $mailable, int $index): void
    {
        Mail::to($email)->later(now()->addSeconds($index * self::STAGGER_SECONDS), $mailable);
    }
}
