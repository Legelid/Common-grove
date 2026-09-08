<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $viteDevSources = app()->isLocal()
            ? " http://localhost:5173 http://localhost:5194 ws://localhost:5173 ws://localhost:5194"
            : '';
        $unsafeEval = " 'unsafe-eval'";

        // /dev/preview-email (local-only, see routes/web.php) renders email
        // HTML — which hardcodes absolute production image URLs, since email
        // clients need those regardless of environment — as an actual page
        // on this app's local origin. 'self' doesn't cover a different
        // origin, so without this the logo 404s in the preview tool only;
        // production never hits this, since there the page and the image
        // already share the same origin.
        $emailPreviewImgSources = app()->isLocal() ? ' https://www.common-grove.com' : '';

        $reverbHost = config('app.reverb_host', 'localhost:8080');
        $reverbWs   = "ws://{$reverbHost} wss://{$reverbHost}";

        $response->headers->set('Content-Security-Policy',
            "default-src 'self';" .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://static.cloudflareinsights.com{$viteDevSources};" .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com{$viteDevSources};" .
            "img-src 'self' data: blob: " . config('filesystems.disks.s3.url', '') . "{$emailPreviewImgSources};" .
            "connect-src 'self' {$reverbWs} https://cloudflareinsights.com{$viteDevSources};" .
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
}
