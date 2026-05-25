<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $cookieName = config('session.cookie');

        // Fires BEFORE StartSession — raw encrypted cookie, before EncryptCookies runs.
        Log::debug('SH.before', [
            'method'             => $request->method(),
            'path'               => $request->path(),
            'has_session_cookie' => $request->cookies->has($cookieName),
        ]);

        try {
            $response = $next($request);
        } catch (TokenMismatchException $e) {
            // Standard Laravel CSRF check failure.
            $this->log419('SH.419.csrf', $request, $e);
            throw $e;
        } catch (HttpException $e) {
            // Catches LivewireReleaseTokenMismatchException (419 HttpException) and any other
            // framework-level HTTP error thrown before the response is built.
            if ($e->getStatusCode() === 419) {
                $this->log419('SH.419.http', $request, $e);
            }
            throw $e;
        }

        // Fires AFTER the response is received from the inner pipeline.
        // Uses WARNING for 4xx/5xx so it appears in standard log searches.
        $sid    = session()->getId();
        $status = $response->getStatusCode();
        $logFn  = $status >= 400 ? 'warning' : 'debug';
        Log::{$logFn}('SH.after', [
            'method'           => $request->method(),
            'path'             => $request->path(),
            'status'           => $status,
            'session_id'       => $sid,
            'csrf_token'       => substr(session()->token() ?? '', 0, 10),
            'db_row_exists'    => DB::table('sessions')->where('id', $sid)->exists(),
            'sets_cookie'      => $response->headers->has('Set-Cookie'),
            'response_snippet' => $status >= 400
                ? substr((string) $response->getContent(), 0, 300)
                : null,
        ]);

        $viteDevSources = app()->isLocal()
            ? " http://localhost:5173 http://localhost:5194 ws://localhost:5173 ws://localhost:5194"
            : '';
        $unsafeEval = app()->isLocal() ? " 'unsafe-eval'" : '';

        $response->headers->set('Content-Security-Policy',
            "default-src 'self';" .
            "script-src 'self' 'unsafe-inline'{$unsafeEval}{$viteDevSources};" .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com{$viteDevSources};" .
            "img-src 'self' data: " . config('filesystems.disks.s3.url', '') . ";" .
            "connect-src 'self' ws://" . config('app.reverb_host', 'localhost:8080') . " wss://" . config('app.reverb_host', 'localhost:8080') . "{$viteDevSources};" .
            "font-src 'self' https://fonts.gstatic.com;" .
            "frame-ancestors 'none';" .
            "form-action 'self';" .
            "base-uri 'self';"
        );

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Cache-Control', 'no-store, no-cache, private, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    private function log419(string $label, Request $request, \Throwable $e): void
    {
        $cookieName   = config('session.cookie');
        $sessionToken = session()->token() ?? '';
        $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN') ?? '';

        $body        = json_decode((string) $request->getContent(), true) ?? [];
        $lwToken     = (string) ($body['_token'] ?? '');
        $lwSnapshot  = $body['components'][0]['snapshot'] ?? null;
        $releaseInSnapshot = $lwSnapshot
            ? (json_decode($lwSnapshot, true)['memo']['release'] ?? '(not set)')
            : '(no snapshot)';

        Log::warning($label, [
            'exception_class'    => get_class($e),
            'exception_message'  => $e->getMessage(),
            'path'               => $request->path(),
            'method'             => $request->method(),
            'request_host'       => $request->getHost(),
            'has_session_cookie' => $request->cookies->has($cookieName),
            'x_csrf_header'      => substr((string) ($request->header('X-CSRF-TOKEN') ?? ''), 0, 10),
            'x_xsrf_header'      => substr((string) ($request->header('X-XSRF-TOKEN') ?? ''), 0, 10),
            'session_id'         => session()->getId(),
            'session_token'      => substr($sessionToken, 0, 10),
            'request_token'      => substr($requestToken, 0, 10),
            'livewire_token'     => substr($lwToken, 0, 10),
            'release_in_snapshot'=> $releaseInSnapshot,
            'auth_id'            => Auth::id(),
            'db_row_exists'      => DB::table('sessions')->where('id', session()->getId())->exists(),
            'route_name'         => $request->route()?->getName(),
            'route_middleware'   => $request->route()?->gatherMiddleware() ?? [],
        ]);
    }
}
