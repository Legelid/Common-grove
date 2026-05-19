<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Prevent any reverse proxy or CDN from caching dynamic pages.
        // A cached authenticated page would carry a stale CSRF token that no
        // longer matches the live session, causing 419 on the first form submit.
        $response->headers->set('Cache-Control', 'no-store, no-cache, private, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');

        // Temporary 419 diagnostic — remove once the production CSRF issue is resolved.
        if ($response->getStatusCode() === 419) {
            $sessionToken = session()->token() ?? '';
            $requestToken = $request->header('X-CSRF-TOKEN') ?? $request->input('_token') ?? '';
            Log::warning('CSRF 419', [
                'path'                 => $request->path(),
                'session_driver'       => config('session.driver'),
                'app_url'              => config('app.url'),
                'request_host'         => $request->getHost(),
                'request_scheme'       => $request->getScheme(),
                'session_id_prefix'    => substr(session()->getId(), 0, 10),
                'session_token_prefix' => substr($sessionToken, 0, 10),
                'request_token_prefix' => substr($requestToken, 0, 10),
                'tokens_match'         => $sessionToken !== '' && hash_equals($sessionToken, $requestToken),
                'has_session_token'    => $sessionToken !== '',
                'has_request_token'    => $requestToken !== '',
            ]);
        }

        return $response;
    }
}
