<?php

declare(strict_types=1);

use App\Http\Middleware\CheckSuspended;
use App\Http\Middleware\EnsureDateOfBirth;
use App\Http\Middleware\EnsureOnboardingComplete;
use App\Http\Middleware\RequireAdmin;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\UpdateLastSeen;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust only the configured upstream proxy (Nginx on the same machine = 127.0.0.1).
        // Set TRUSTED_PROXIES in .env to a CIDR range if behind a cloud load balancer.
        // Never use '*' — it allows any client to spoof X-Forwarded-For and bypass rate limits.
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '127.0.0.1'), headers:
            \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR
            | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST
            | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT
            | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->append(SecurityHeaders::class);
        $middleware->web(append: [UpdateLastSeen::class, CheckSuspended::class, EnsureDateOfBirth::class]);
        $middleware->alias([
            'admin'      => RequireAdmin::class,
            'onboarded'  => EnsureOnboardingComplete::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/paypal/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // renderable() fires for ALL exceptions regardless of $internalDontReport.
        // (report() is skipped for TokenMismatchException and HttpException — both are
        // in $internalDontReport — so we must hook into the render phase instead.)
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null;
            $is419  = $status === 419 || $e instanceof \Illuminate\Session\TokenMismatchException;

            if (! $is419) {
                return null; // continue with default rendering for non-419 exceptions
            }

            $cookie     = config('session.cookie');
            $body       = json_decode((string) $request->getContent(), true) ?? [];
            $lwToken    = (string) ($body['_token'] ?? '');
            $lwSnapshot = $body['components'][0]['snapshot'] ?? null;
            $release    = $lwSnapshot
                ? (json_decode($lwSnapshot, true)['memo']['release'] ?? '(not set)')
                : '(no snapshot)';

            \Illuminate\Support\Facades\Log::warning('Global.419', [
                'exception_class'     => get_class($e),
                'exception_message'   => $e->getMessage(),
                'path'                => $request->path(),
                'method'              => $request->method(),
                'has_session_cookie'  => $request->cookies->has($cookie),
                'x_csrf_header'       => substr((string) ($request->header('X-CSRF-TOKEN') ?? ''), 0, 10),
                'x_xsrf_header'       => substr((string) ($request->header('X-XSRF-TOKEN') ?? ''), 0, 10),
                'x_livewire_header'   => $request->hasHeader('X-Livewire') ? 'yes' : 'no',
                'session_id'          => session()->getId(),
                'session_token'       => substr(session()->token() ?? '', 0, 10),
                'livewire_token'      => substr($lwToken, 0, 10),
                'release_in_snapshot' => $release,
                'auth_id'             => \Illuminate\Support\Facades\Auth::id(),
                'route_name'          => $request->route()?->getName(),
                'db_row_exists'       => \Illuminate\Support\Facades\DB::table('sessions')
                                             ->where('id', session()->getId())
                                             ->exists(),
            ]);

            return null; // continue with default rendering (return the actual 419 response)
        });
    })->create();
