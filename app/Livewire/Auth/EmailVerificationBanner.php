<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class EmailVerificationBanner extends Component
{
    public ?string $message     = null;
    public bool    $rateLimited = false;

    public function resend(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('feed'));
            return;
        }

        $key = 'resend-banner.' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds           = RateLimiter::availableIn($key);
            $minutes           = (int) ceil($seconds / 60);
            $this->message     = 'Please wait ' . $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes') . ' before requesting another link.';
            $this->rateLimited = true;
            return;
        }

        RateLimiter::hit($key, 600); // 3 attempts per 10 minutes

        $user->sendEmailVerificationNotification();

        $this->message     = 'Sent — check your inbox.';
        $this->rateLimited = false;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.email-verification-banner');
    }
}
