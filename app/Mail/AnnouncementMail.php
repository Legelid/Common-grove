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
 * A general-purpose announcement to the whole (opted-in) user base — sent
 * only via the admin Announcements page, never automatically. Distinct from
 * WinbackMail, which targets dormant users specifically, but shares the
 * same permanent signed unsubscribe link.
 */
class AnnouncementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subjectLine,
        public readonly string $bodyText,
        public readonly User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.announcement',
            with: [
                'unsubscribeUrl' => URL::signedRoute('email.unsubscribe', ['user' => $this->user->id]),
            ],
        );
    }
}
