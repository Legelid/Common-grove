<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Livewire\Component;

/**
 * Renders the login page.
 * Authentication is handled by LoginController::store() via a standard HTML form POST
 * so that Laravel's StartSession middleware can attach the session cookie to the 302
 * redirect response reliably, regardless of server/browser configuration.
 */
class Login extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Sign in — CommonGround']);
    }
}
