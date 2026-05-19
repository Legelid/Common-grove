<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
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
        // Fires BEFORE StartSession — captures raw browser-sent cookie state.
        // Compare has_raw_session_cookie between GET and POST to detect missing cookies.
        if ($request->is('login')) {
            $cookieName = config('session.cookie');
            Log::info('CSRF diag: login request', [
                'method'                 => $request->method(),
                'has_raw_session_cookie' => $request->cookies->has($cookieName),
                'raw_post_token_prefix'  => $request->isMethod('POST')
                    ? substr((string) ($request->request->get('_token') ?? ''), 0, 10)
                    : null,
            ]);
        }

        try {
            $response = $next($request);
        } catch (TokenMismatchException $e) {
            $cookieName   = config('session.cookie');
            $sessionToken = session()->token() ?? '';
            $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN') ?? '';
            Log::warning('CSRF 419: token mismatch', [
                'path'                 => $request->path(),
                'session_driver'       => config('session.driver'),
                'session_lifetime'     => config('session.lifetime'),
                'app_url'              => config('app.url'),
                'request_host'         => $request->getHost(),
                'request_scheme'       => $request->getScheme(),
                // has_raw_session_cookie: browser sent cookie BEFORE StartSession ran.
                // session_id_prefix: compare this with the Login render log's session_id_prefix.
                // If they differ, different sessions used for GET and POST → session not persisting.
                'has_raw_session_cookie' => $request->cookies->has($cookieName),
                'session_id_prefix'      => substr(session()->getId(), 0, 10),
                'session_token_prefix'   => substr($sessionToken, 0, 10),
                'request_token_prefix'   => substr($requestToken, 0, 10),
                'has_session_token'      => $sessionToken !== '',
                'has_request_token'      => $requestToken !== '',
                'tokens_match'           => $sessionToken !== '' && $requestToken !== '' && hash_equals($sessionToken, $requestToken),
                'session_files_on_disk'  => count(glob(storage_path('framework/sessions/*')) ?: []),
            ]);
            throw $e;
        }

        // After a successful GET /login: log the session state that was saved and the
        // cookie that will be sent to the browser.  Compare session_id_prefix here
        // with the session_id_prefix in the subsequent CSRF 419 log.
        if ($request->is('login') && $request->isMethod('GET')) {
            Log::info('CSRF diag: GET /login complete', [
                'session_id_prefix'     => substr(session()->getId(), 0, 10),
                'csrf_token_prefix'     => substr(csrf_token(), 0, 10),
                'session_files_on_disk' => count(glob(storage_path('framework/sessions/*')) ?: []),
            ]);
        }

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

        return $response;
    }
}
