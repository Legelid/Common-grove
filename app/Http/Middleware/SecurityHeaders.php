<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $viteDevSources = app()->isLocal()
            ? " http://localhost:5173 http://localhost:5194 ws://localhost:5173 ws://localhost:5194"
            : '';

        $response->headers->set('Content-Security-Policy',
            "default-src 'self';" .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteDevSources};" .
            "style-src 'self' 'unsafe-inline'{$viteDevSources};" .
            "img-src 'self' data: " . config('filesystems.disks.s3.url', '') . ";" .
            "connect-src 'self' ws://" . config('app.reverb_host', 'localhost:8080') . " wss://" . config('app.reverb_host', 'localhost:8080') . "{$viteDevSources};" .
            "font-src 'self';" .
            "frame-ancestors 'none';"
        );

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }
}
