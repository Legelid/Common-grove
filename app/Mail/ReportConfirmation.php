<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly ?string $gamertag = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Report received — CommonGrove',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.report-confirmation');
    }
}
