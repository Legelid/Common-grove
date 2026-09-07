<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Accounts created via "Continue with Google" start with a placeholder
 * gamertag (gamertag_setup_required = true) — this forces them to pick a
 * real one before touching any other protected page. Runs before
 * EnsureDateOfBirth in the middleware stack so the flow is: gamertag, then
 * date of birth, then onboarding.
 */
class RequireGamertagSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        // Livewire component update requests must not be redirected — the route
        // is /livewire/update, not account.gamertag, so the form on that page
        // could never submit (mirrors EnsureDateOfBirth's own exemption).
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        if (
            Auth::check()
            && Auth::user()->gamertag_setup_required
            && ! $request->routeIs(
                'account.gamertag',
                'logout',
                'login',
                'register',
                'home',
                'password.*',
                'verification.*',
            )
        ) {
            return redirect()->route('account.gamertag');
        }

        return $next($request);
    }
}
