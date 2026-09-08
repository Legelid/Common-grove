<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Sent manually via `php artisan users:send-winback` — never dispatched
 * automatically. See that command for the eligibility criteria (30+ days
 * inactive, never sent before, not opted out of marketing email).
 */
class WinbackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We miss you at CommonGrove',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.winback',
            with: [
                // Permanent (non-expiring) signed link — this may sit in an
                // inbox for months before someone clicks it, unlike a
                // password-reset or verification link.
                'unsubscribeUrl' => URL::signedRoute('email.unsubscribe', ['user' => $this->user->id]),
            ],
        );
    }
}
