<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\GeneratesPlaceholderGamertag;
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
 * Handles "Continue with Discord" sign-in/sign-up.
 *
 * Same shape as GoogleController deliberately — see that file's docblock for
 * why this is a plain HTTP controller rather than a Livewire action, and why
 * the "no matching account" case defers to a confirm step instead of
 * creating immediately.
 */
class DiscordController extends Controller
{
    use GeneratesPlaceholderGamertag;

    private const PENDING_SESSION_KEY = 'pending_discord_signup';

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $discordUser = Socialite::driver('discord')->user();
        } catch (InvalidStateException $e) {
            Log::info('Discord OAuth: invalid state (expired or replayed callback)', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'That sign-in link expired. Please try "Continue with Discord" again.']);
        } catch (\Throwable $e) {
            Log::warning('Discord OAuth: callback failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Something went wrong signing in with Discord. Please try again.']);
        }

        $email = $discordUser->getEmail();

        if (empty($email)) {
            Log::warning('Discord OAuth: no email returned by Discord', ['discord_id' => $discordUser->getId()]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Your Discord account did not share an email address, so we could not sign you in.']);
        }

        $user = User::where('discord_id', $discordUser->getId())->first();

        if ($user === null) {
            $existing         = User::where('email', $email)->first();
            $discordVerified  = (bool) ($discordUser->getRaw()['verified'] ?? false);

            if ($existing !== null && $existing->hasVerifiedEmail() && $discordVerified) {
                $existing->update(['discord_id' => $discordUser->getId()]);
                Log::info('Discord OAuth: linked to existing verified account', ['gamertag' => $existing->gamertag]);

                $user = $existing;
            } else {
                session([self::PENDING_SESSION_KEY => [
                    'discord_id' => $discordUser->getId(),
                    'email'      => $email,
                    'name'       => $discordUser->getName(),
                ]]);

                Log::info('Discord OAuth: no verified match, routing to confirm', ['discord_id' => $discordUser->getId()]);

                return redirect()->route('auth.discord.confirm');
            }
        }

        if ($user->isSuspended()) {
            Log::info('Discord OAuth: suspended account attempted sign-in', ['gamertag' => $user->gamertag]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Your account has been suspended. Please contact support if you believe this is an error.']);
        }

        Auth::login($user);
        request()->session()->regenerate();

        $betaToken = session()->pull('beta_invite_token');
        if ($betaToken) {
            app(FirstRootsService::class)->claimInvite((string) $betaToken, $user);
        }

        Log::info('Discord OAuth: sign-in success', ['gamertag' => $user->gamertag]);

        return redirect($this->destinationFor($user));
    }

    /**
     * Placeholder confirmation screen — shown only when the Discord callback
     * found no matching account at all. Real styling is a later phase.
     */
    public function confirmShow(): View|RedirectResponse
    {
        $pending = session(self::PENDING_SESSION_KEY);

        if (! $this->pendingIsValid($pending)) {
            return redirect()->route('login')
                ->withErrors(['login' => 'That sign-up link expired. Please try "Continue with Discord" again.']);
        }

        return view('auth.discord-confirm', [
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
                ->withErrors(['login' => 'That sign-up link expired. Please try "Continue with Discord" again.']);
        }

        // Race guard: a matching account may have appeared since confirmShow()
        // rendered (e.g. a double submit, or the same Discord account signing
        // in from a second tab) — check again rather than letting a unique
        // constraint violation surface as a raw 500.
        if (
            User::where('discord_id', $pending['discord_id'])->exists()
            || User::where('email', $pending['email'])->exists()
        ) {
            session()->forget(self::PENDING_SESSION_KEY);
            Log::info('Discord OAuth: confirm race — account appeared before submit', ['email' => $pending['email']]);

            return redirect()->route('login')
                ->withErrors(['login' => 'An account with that email already exists. Please sign in instead.']);
        }

        $user = $this->createUser($pending['discord_id'], $pending['email']);
        session()->forget(self::PENDING_SESSION_KEY);

        Auth::login($user);
        $request->session()->regenerate();

        $betaToken = session()->pull('beta_invite_token');
        if ($betaToken) {
            app(FirstRootsService::class)->claimInvite((string) $betaToken, $user);
        }

        Log::info('Discord OAuth: account created via confirm', ['gamertag' => $user->gamertag]);

        return redirect($this->destinationFor($user));
    }

    private function createUser(string $discordId, string $email): User
    {
        /** @var PasswordService $passwordService */
        $passwordService = app(PasswordService::class);

        $user = User::create([
            'gamertag'                => $this->generatePlaceholderGamertag(),
            'gamertag_setup_required' => true,
            'email'                   => $email,
            'password'                => $passwordService->hash(Str::random(40)),
            'discord_id'              => $discordId,
        ]);

        // email_verified_at is deliberately not mass-assignable (see User::$fillable) —
        // set it directly rather than widening that allow-list app-wide just for this.
        $user->email_verified_at = now();
        $user->save();

        Log::info('Discord OAuth: new account created', ['user_id' => $user->id]);

        return $user;
    }

    private function pendingIsValid(mixed $pending): bool
    {
        return is_array($pending)
            && ! empty($pending['discord_id'])
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
