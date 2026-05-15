<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->onboarding_completed) {
            if (! $request->routeIs('onboarding')) {
                return redirect()->route('onboarding');
            }
        }

        return $next($request);
    }
}
