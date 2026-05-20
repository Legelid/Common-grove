<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Log;

/**
 * Replaces the default Laravel password-reset email with a CommonGrove branded one.
 * Inherits token handling and URL generation from the parent.
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * When toMail() returns a Mailable (not a MailMessage), Laravel's MailChannel
     * does not auto-set the To address — we must set it explicitly here.
     */
    public function toMail(mixed $notifiable): ResetPasswordMail
    {
        $email = $notifiable->routeNotificationFor('mail', $this) ?? $notifiable->email ?? null;

        if (empty($email)) {
            Log::warning('ResetPasswordNotification: notifiable has no email address', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id'   => $notifiable->id ?? null,
            ]);
        }

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new ResetPasswordMail($url))->to((string) $email);
    }
}
