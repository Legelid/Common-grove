<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Services\PasswordService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Handles the password reset form submission.
 *
 * Uses PasswordService to hash the new password with the pepper,
 * rather than calling Hash:: directly.
 */
class NewPasswordController extends Controller
{
    /**
     * Show the reset password form.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request): \Illuminate\View\View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->uncompromised()],
        ]);

        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request, $passwordService): void {
                $user->forceFill([
                    'password'               => $passwordService->hash($request->string('password')->value()),
                    'remember_token'         => Str::random(60),
                    'password_reset_required' => false,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => __($status)]);
    }
}
