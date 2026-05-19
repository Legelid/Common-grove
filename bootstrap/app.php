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
        // Trust all upstream proxy headers so Laravel sees the real client
        // IP, protocol (HTTPS), and host when behind Nginx or any load balancer.
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL);

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
        //
    })->create();
