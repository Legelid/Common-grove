<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\VerifyEmail;

/**
 * Replaces the default Laravel verification email with a custom CommonGrove branded email.
 * Inherits verificationUrl() from the parent to preserve signed URL behaviour.
 */
class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Return a custom Mailable instead of a MailMessage so we have
     * full control over the HTML template with no Laravel branding.
     */
    public function toMail(mixed $notifiable): VerifyEmailMail
    {
        $url = $this->verificationUrl($notifiable);

        return new VerifyEmailMail($url);
    }
}
