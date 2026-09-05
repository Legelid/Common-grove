<div class="text-center">

    {{-- Icon --}}
    <div class="mb-5 flex justify-center">
        <span class="inline-flex items-center justify-center w-14 h-14 rounded-full" style="background:rgba(var(--accent-rgb),0.12);border:1px solid rgba(var(--accent-rgb),0.3);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2a7 7 0 0 1 7 7c0 4-3.5 8-7 11C8.5 17 5 13 5 9a7 7 0 0 1 7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
        </span>
    </div>

    {{-- Invalid token --}}
    @if (! $tokenValid && ! $tokenExpired)
        <h1 class="text-2xl font-bold mb-3" style="color:var(--text);">Invite not found</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:var(--text-muted);">
            This invite link doesn't look right. It may have been mistyped or it was never issued.
        </p>
        <a href="{{ route('home') }}" wire:navigate class="text-sm underline" style="color:var(--accent);">Back to CommonGrove</a>

    {{-- Expired --}}
    @elseif ($tokenExpired)
        <h1 class="text-2xl font-bold mb-3" style="color:var(--text);">Invite expired</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:var(--text-muted);">
            This invite link has expired. Reach out to the person who sent it and ask for a fresh one.
        </p>
        <a href="{{ route('home') }}" wire:navigate class="text-sm underline" style="color:var(--accent);">Back to CommonGrove</a>

    {{-- Already claimed — celebration --}}
    @elseif ($claimed)
        <div class="mb-5 flex justify-center">
            <span class="text-4xl" aria-hidden="true">🌿</span>
        </div>
        <h1 class="text-2xl font-bold mb-3" style="color:var(--text);">Welcome to the FirstRoots.</h1>
        <p class="text-sm leading-relaxed mb-6" style="color:var(--text-muted);">
            You helped shape CommonGrove from the very beginning.<br>
            Your founding badge and aura are now permanently part of your profile.
        </p>
        <div class="rounded-xl border px-5 py-4 mb-6 text-left" style="background:rgba(var(--accent-rgb),0.06);border-color:rgba(var(--accent-rgb),0.25);">
            <p class="text-xs font-semibold mb-1" style="color:var(--accent);">What you've earned</p>
            <ul class="text-sm space-y-1" style="color:var(--text-muted);">
                <li>· A permanent <strong style="color:var(--text);">FirstRoots</strong> badge on your profile</li>
                <li>· A soft teal glow ring on your avatar — visible everywhere on the platform</li>
                <li>· Your name in the founding generation of CommonGrove</li>
            </ul>
        </div>
        <x-button :href="route('feed')" wire:navigate variant="primary" class="w-full">Go to your feed</x-button>

    {{-- Already has FirstRoots --}}
    @elseif ($alreadyOwns)
        <h1 class="text-2xl font-bold mb-3" style="color:var(--text);">You're already a founding member.</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:var(--text-muted);">
            Your FirstRoots badge is already permanently part of your profile.
        </p>
        <a href="{{ route('feed') }}" wire:navigate class="text-sm underline" style="color:var(--accent);">Go to your feed</a>

    {{-- Valid, unclaimed --}}
    @else
        <h1 class="text-2xl font-bold mb-2" style="color:var(--text);">You've been invited.</h1>
        <p class="text-xs font-semibold mb-4 uppercase tracking-widest" style="color:var(--accent);">FirstRoots · Founding Member</p>

        <div class="rounded-xl border px-5 py-4 mb-6 text-left" style="background:var(--surface);border-color:var(--border);">
            <p class="text-sm leading-relaxed mb-3" style="color:var(--text-muted);">
                CommonGrove is a quiet, judgment-free space for introverts and gamers to find real connection — no feeds designed to addict you, no ads, no fake activity.
            </p>
            <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                As a <strong style="color:var(--text);">FirstRoots</strong> founding member, you'll carry a permanent badge and a soft teal aura on your avatar — visible everywhere on the platform, forever. You were here before it was anything.
            </p>
        </div>

        @if (auth()->check())
            {{-- Logged in — one-click claim --}}
            <p class="text-xs mb-4" style="color:var(--text-muted);">
                Claiming as <strong style="color:var(--text);">{{ auth()->user()->gamertag }}</strong>
            </p>
            <x-button type="button" wire:click="claim" wire:loading.attr="disabled" variant="primary" class="w-full">
                <span wire:loading.remove>Claim my FirstRoots badge</span>
                <span wire:loading>Claiming…</span>
            </x-button>
        @else
            {{-- Guest — store token in session then redirect --}}
            <div class="flex flex-col gap-3">
                <x-button type="button" wire:click="redirectToRegister" wire:loading.attr="disabled" variant="primary" class="w-full">Create account to claim</x-button>
                <x-button type="button" wire:click="redirectToLogin" wire:loading.attr="disabled" variant="secondary" class="w-full">Already have an account? Log in to claim</x-button>
            </div>
        @endif
    @endif

</div>
