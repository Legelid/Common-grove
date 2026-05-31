<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\BetaInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BetaInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $claimUrl;

    public function __construct(public readonly BetaInvite $invite)
    {
        $this->claimUrl = url('/beta/claim/' . $invite->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are invited to the CommonGrove founding beta',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.beta-invite',
            text: 'emails.beta-invite-text',
        );
    }
}
