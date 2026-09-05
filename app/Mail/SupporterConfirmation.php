<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupporterConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re a CommonGrove Supporter — thank you',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.supporter-confirmation',
            with: ['gamertag' => $this->user->gamertag],
        );
    }
}
