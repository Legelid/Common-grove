<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $cookieName = config('session.cookie');

        // Fires BEFORE StartSession — raw encrypted cookie value, before EncryptCookies runs.
        Log::info('SH.before', [
            'method'          => $request->method(),
            'path'            => $request->path(),
            'has_session_cookie' => $request->cookies->has($cookieName),
            'raw_cookie_len'  => strlen((string) $request->cookies->get($cookieName)),
        ]);

        try {
            $response = $next($request);
        } catch (TokenMismatchException $e) {
            $sid          = session()->getId();
            $sessionToken = session()->token() ?? '';
            $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN') ?? '';

            // Livewire sends the token inside the JSON body, not as a form field.
            $body         = json_decode((string) $request->getContent(), true) ?? [];
            $lwToken      = (string) ($body['_token'] ?? '');

            Log::warning('SH.419', [
                'path'               => $request->path(),
                'session_driver'     => config('session.driver'),
                'request_host'       => $request->getHost(),
                'has_session_cookie' => $request->cookies->has($cookieName),
                'session_id'         => $sid,
                'session_token'      => substr($sessionToken, 0, 10),
                'request_token'      => substr($requestToken, 0, 10),
                'livewire_token'     => substr($lwToken, 0, 10),
                'has_session_token'  => $sessionToken !== '',
                'db_row_exists'      => DB::table('sessions')->where('id', $sid)->exists(),
            ]);
            throw $e;
        }

        // Fires AFTER StartSession has saved the session and added the Set-Cookie header.
        $sid = session()->getId();
        Log::info('SH.after', [
            'method'           => $request->method(),
            'path'             => $request->path(),
            'status'           => $response->getStatusCode(),
            'session_id'       => $sid,
            'csrf_token'       => substr(session()->token() ?? '', 0, 10),
            'db_row_exists'    => DB::table('sessions')->where('id', $sid)->exists(),
            'sets_cookie'      => $response->headers->has('Set-Cookie'),
        ]);

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
        $response->headers->set('Cache-Control', 'no-store, no-cache, private, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
