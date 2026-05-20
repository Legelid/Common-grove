<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Notifications\ResetPassword;

/**
 * Replaces the default Laravel password-reset email with a CommonGrove branded one.
 * Inherits token handling and URL generation from the parent.
 */
class ResetPasswordNotification extends ResetPassword
{
    public function toMail(mixed $notifiable): ResetPasswordMail
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return new ResetPasswordMail($url);
    }
}
