<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;

/**
 * Replaces the default Laravel verification email with a custom CommonGrove branded email.
 * Inherits verificationUrl() from the parent to preserve signed URL behaviour.
 */
class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Return a custom Mailable instead of a MailMessage so we have
     * full control over the HTML template with no Laravel branding.
     *
     * When toMail() returns a Mailable (not a MailMessage), Laravel's MailChannel
     * does not auto-set the To address — we must set it explicitly here.
     */
    public function toMail(mixed $notifiable): VerifyEmailMail
    {
        $email = $notifiable->routeNotificationFor('mail', $this) ?? $notifiable->email ?? null;

        if (empty($email)) {
            Log::warning('VerifyEmailNotification: notifiable has no email address', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id'   => $notifiable->id ?? null,
            ]);
        }

        $url = $this->verificationUrl($notifiable);

        return (new VerifyEmailMail($url))->to((string) $email);
    }
}
