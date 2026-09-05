<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Handles user logout with full session invalidation.
 */
class LogoutController extends Controller
{
    /**
     * Log out the authenticated user.
     *
     * Invalidates the entire session (not just the auth token) and
     * regenerates the CSRF token to prevent session fixation.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
