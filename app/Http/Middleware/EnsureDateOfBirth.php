<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * After email verification, require all users to have a date_of_birth on file
 * before accessing any protected page. Redirects to the DOB collection page.
 * Idempotent: does nothing for unauthenticated or unverified users.
 */
class EnsureDateOfBirth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Livewire component update requests must not be redirected — the route
        // is /livewire/update, not account.birthday, so they would otherwise be
        // caught and the form on that page could never submit.
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        if (
            Auth::check()
            && Auth::user()->email_verified_at !== null
            && Auth::user()->date_of_birth === null
            && ! $request->routeIs(
                'account.birthday',
                'logout',
                'login',
                'register',
                'home',
                'password.*',
                'verification.*',
            )
        ) {
            return redirect()->route('account.birthday');
        }

        return $next($request);
    }
}
