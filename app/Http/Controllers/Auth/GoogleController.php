<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\FirstRootsService;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

/**
 * Handles "Continue with Google" sign-in/sign-up.
 *
 * A plain HTTP controller (not a Livewire action) — this whole flow is a
 * full-page redirect round trip through Google, so there is no Livewire
 * AJAX/cookie-propagation concern here (see LoginController's docblock for
 * why that concern exists elsewhere in this codebase).
 *
 * Matching behaviour to Register.php / Login.php: Auth::login() + session
 * regenerate, beta invite claiming, and the same post-login destination
 * logic (verification / password reset / onboarding / feed).
 */
class GoogleController extends Controller
{
    private const PENDING_SESSION_KEY = 'pending_google_signup';

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            Log::info('Google OAuth: invalid state (expired or replayed callback)', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'That sign-in link expired. Please try "Continue with Google" again.']);
        } catch (\Throwable $e) {
            Log::warning('Google OAuth: callback failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Something went wrong signing in with Google. Please try again.']);
        }

        $email = $googleUser->getEmail();

        if (empty($email)) {
            Log::warning('Google OAuth: no email returned by Google', ['google_id' => $googleUser->getId()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Your Google account did not share an email address, so we could not sign you in.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user === null) {
            $existing        = User::where('email', $email)->first();
            $googleVerified  = (bool) ($googleUser->getRaw()['verified_email'] ?? false);

            if ($existing !== null && $existing->hasVerifiedEmail() && $googleVerified) {
                $existing->update(['google_id' => $googleUser->getId()]);
                Log::info('Google OAuth: linked to existing verified account', ['gamertag' => $existing->gamertag]);

                $user = $existing;
            } else {
                session([self::PENDING_SESSION_KEY => [
                    'google_id' => $googleUser->getId(),
                    'email'     => $email,
                    'name'      => $googleUser->getName(),
                ]]);

                Log::info('Google OAuth: no verified match, routing to confirm', ['google_id' => $googleUser->getId()]);

                return redirect()->route('auth.google.confirm');
            }
        }

        if ($user->isSuspended()) {
            Log::info('Google OAuth: suspended account attempted sign-in', ['gamertag' => $user->gamertag]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Your account has been suspended. Please contact support if you believe this is an error.']);
        }

        Auth::login($user);
        request()->session()->regenerate();

        $betaToken = session()->pull('beta_invite_token');
        if ($betaToken) {
            app(FirstRootsService::class)->claimInvite((string) $betaToken, $user);
        }

        Log::info('Google OAuth: sign-in success', ['gamertag' => $user->gamertag]);

        return redirect($this->destinationFor($user));
    }

    /**
     * Placeholder confirmation screen — shown only when the Google callback
     * found no matching account at all. Real styling is a later phase.
     */
    public function confirmShow(): View|RedirectResponse
    {
        $pending = session(self::PENDING_SESSION_KEY);

        if (! $this->pendingIsValid($pending)) {
            return redirect()->route('login')
                ->withErrors(['login' => 'That sign-up link expired. Please try "Continue with Google" again.']);
        }

        return view('auth.google-confirm', [
            'email' => $pending['email'],
            'name'  => $pending['name'],
        ]);
    }

    /**
     * Only here — not in the callback — does a new account actually get
     * created, and only on an explicit submit.
     */
    public function confirmStore(Request $request): RedirectResponse
    {
        $pending = session(self::PENDING_SESSION_KEY);

        if (! $this->pendingIsValid($pending)) {
            return redirect()->route('login')
                ->withErrors(['login' => 'That sign-up link expired. Please try "Continue with Google" again.']);
        }

        // Race guard: a matching account may have appeared since confirmShow()
        // rendered (e.g. a double submit, or the same Google account signing
        // in from a second tab) — check again rather than letting a unique
        // constraint violation surface as a raw 500.
        if (
            User::where('google_id', $pending['google_id'])->exists()
            || User::where('email', $pending['email'])->exists()
        ) {
            session()->forget(self::PENDING_SESSION_KEY);
            Log::info('Google OAuth: confirm race — account appeared before submit', ['email' => $pending['email']]);

            return redirect()->route('login')
                ->withErrors(['login' => 'An account with that email already exists. Please sign in instead.']);
        }

        $user = $this->createUser($pending['google_id'], $pending['email']);
        session()->forget(self::PENDING_SESSION_KEY);

        Auth::login($user);
        $request->session()->regenerate();

        $betaToken = session()->pull('beta_invite_token');
        if ($betaToken) {
            app(FirstRootsService::class)->claimInvite((string) $betaToken, $user);
        }

        Log::info('Google OAuth: account created via confirm', ['gamertag' => $user->gamertag]);

        return redirect($this->destinationFor($user));
    }

    private function createUser(string $googleId, string $email): User
    {
        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $user = User::create([
            'gamertag'                => $this->generatePlaceholderGamertag(),
            'gamertag_setup_required' => true,
            'email'                   => $email,
            'password'                => $passwordService->hash(Str::random(40)),
            'google_id'               => $googleId,
        ]);

        // email_verified_at is deliberately not mass-assignable (see User::$fillable) —
        // set it directly rather than widening that allow-list app-wide just for this.
        $user->email_verified_at = now();
        $user->save();

        Log::info('Google OAuth: new account created', ['user_id' => $user->id]);

        return $user;
    }

    /**
     * A short-lived, unique placeholder — replaced on first login via the
     * gamertag_setup_required flag / CollectGamertag step. Still built to
     * satisfy the normal gamertag shape so nothing downstream (nav, profile)
     * breaks if it's ever displayed before setup completes.
     */
    private function generatePlaceholderGamertag(): string
    {
        do {
            $candidate = 'NewMember' . random_int(10000, 999999);
        } while (User::withTrashed()->whereRaw('LOWER(gamertag) = ?', [strtolower($candidate)])->exists());

        return $candidate;
    }

    private function pendingIsValid(mixed $pending): bool
    {
        return is_array($pending)
            && ! empty($pending['google_id'])
            && ! empty($pending['email']);
    }

    private function destinationFor(User $user): string
    {
        if ($user->password_reset_required) {
            return route('password.reset-required');
        }

        return $user->onboarding_completed
            ? route('feed')
            : route('onboarding');
    }
}
