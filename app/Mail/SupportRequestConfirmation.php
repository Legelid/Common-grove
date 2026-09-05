<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportRequestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string  $typeLabel,
        public readonly ?string $gamertag = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We\'ve received your message — CommonGrove',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.support-confirmation');
    }
}
