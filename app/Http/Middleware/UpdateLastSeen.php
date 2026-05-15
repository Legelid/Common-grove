<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps last_seen_at current for authenticated users.
 * Writes to the DB at most once every 5 minutes to avoid hammering it.
 */
class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $stale = $user->last_seen_at === null
                || $user->last_seen_at->isBefore(now()->subMinutes(5));

            if ($stale) {
                $user->timestamps = false;
                $user->update(['last_seen_at' => now()]);
                $user->timestamps = true;
            }
        }

        return $next($request);
    }
}
