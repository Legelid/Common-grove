<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\FirstRootsService;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

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
            $user = $this->findOrCreateByEmail($googleUser, $email);
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
     * Link an existing account by verified email match, or create a new one.
     *
     * Per product decision: a Google account proves control of that email
     * address, which we treat as equivalent to the trust an email-verified
     * CommonGrove account already has — so we link rather than block. An
     * unverified existing account is left alone (its owner never proved
     * they control that inbox) and a brand-new account is created instead,
     * to avoid a narrow window where an attacker registers a look-alike
     * email first and waits for the real owner to "link" into it.
     */
    private function findOrCreateByEmail(SocialiteUser $googleUser, string $email): User
    {
        $existing = User::where('email', $email)->first();

        if ($existing !== null && $existing->hasVerifiedEmail()) {
            $existing->update(['google_id' => $googleUser->getId()]);
            Log::info('Google OAuth: linked to existing account', ['gamertag' => $existing->gamertag]);

            return $existing;
        }

        return $this->createUser($googleUser, $email);
    }

    private function createUser(SocialiteUser $googleUser, string $email): User
    {
        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $user = User::create([
            'gamertag'                => $this->generatePlaceholderGamertag(),
            'gamertag_setup_required' => true,
            'email'                   => $email,
            'password'                => $passwordService->hash(Str::random(40)),
            'google_id'               => $googleUser->getId(),
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
