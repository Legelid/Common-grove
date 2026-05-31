<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirePasswordReset
{
    /** Routes exempt from the password-reset gate. */
    private const EXEMPT_ROUTES = [
        'password.reset-required',
        'logout',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->password_reset_required) {
            if (! in_array($request->route()?->getName(), self::EXEMPT_ROUTES, true)) {
                return redirect()->route('password.reset-required');
            }
        }

        return $next($request);
    }
}
