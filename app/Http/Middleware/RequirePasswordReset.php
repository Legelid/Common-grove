<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirePasswordReset
{
    private const EXEMPT_ROUTES = [
        'password.reset-required',
        'logout',
        'logout.get',
        'login',
        'home',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        if (
            Auth::check()
            && ! Auth::user()->is_admin
            && Auth::user()->password_reset_required
            && ! $this->isExempt($request)
        ) {
            return redirect()->route('password.reset-required');
        }

        return $next($request);
    }

    private function isExempt(Request $request): bool
    {
        if (in_array($request->route()?->getName(), self::EXEMPT_ROUTES, true)) {
            return true;
        }

        $path = '/' . ltrim($request->path(), '/');

        return $path === '/' || str_starts_with($path, '/livewire');
    }
}
