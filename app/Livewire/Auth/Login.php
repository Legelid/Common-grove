<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Log;
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
        Log::info('Login render', [
            'session_id_prefix' => substr(session()->getId(), 0, 10),
            'csrf_token_prefix' => substr(csrf_token(), 0, 10),
        ]);

        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Sign in — CommonGround']);
    }
}
