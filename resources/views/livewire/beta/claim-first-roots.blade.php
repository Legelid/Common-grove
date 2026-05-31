<div class="text-center">

    {{-- Icon --}}
    <div class="mb-5 flex justify-center">
        <span class="inline-flex items-center justify-center w-14 h-14 rounded-full" style="background:rgba(29,158,117,0.12);border:1px solid rgba(29,158,117,0.3);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2a7 7 0 0 1 7 7c0 4-3.5 8-7 11C8.5 17 5 13 5 9a7 7 0 0 1 7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
        </span>
    </div>

    {{-- Invalid token --}}
    @if (! $tokenValid && ! $tokenExpired)
        <h1 class="text-2xl font-bold mb-3" style="color:#E6EDF3;">Invite not found</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:#8B949E;">
            This invite link doesn't look right. It may have been mistyped or it was never issued.
        </p>
        <a href="{{ route('home') }}" wire:navigate class="text-sm underline" style="color:#1D9E75;">Back to CommonGrove</a>

    {{-- Expired --}}
    @elseif ($tokenExpired)
        <h1 class="text-2xl font-bold mb-3" style="color:#E6EDF3;">Invite expired</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:#8B949E;">
            This invite link has expired. Reach out to the person who sent it and ask for a fresh one.
        </p>
        <a href="{{ route('home') }}" wire:navigate class="text-sm underline" style="color:#1D9E75;">Back to CommonGrove</a>

    {{-- Already claimed — celebration --}}
    @elseif ($claimed)
        <div class="mb-5 flex justify-center">
            <span class="text-4xl" aria-hidden="true">🌿</span>
        </div>
        <h1 class="text-2xl font-bold mb-3" style="color:#E6EDF3;">Welcome to the FirstRoots.</h1>
        <p class="text-sm leading-relaxed mb-6" style="color:#8B949E;">
            You helped shape CommonGrove from the very beginning.<br>
            Your founding badge and aura are now permanently part of your profile.
        </p>
        <div class="rounded-xl border px-5 py-4 mb-6 text-left" style="background:rgba(29,158,117,0.06);border-color:rgba(29,158,117,0.25);">
            <p class="text-xs font-semibold mb-1" style="color:#1D9E75;">What you've earned</p>
            <ul class="text-sm space-y-1" style="color:#8B949E;">
                <li>· A permanent <strong style="color:#C9D1D9;">FirstRoots</strong> badge on your profile</li>
                <li>· A soft teal glow ring on your avatar — visible everywhere on the platform</li>
                <li>· Your name in the founding generation of CommonGrove</li>
            </ul>
        </div>
        <a
            href="{{ route('feed') }}"
            wire:navigate
            class="inline-block w-full py-2.5 px-6 text-sm font-semibold rounded-lg transition"
            style="background:#1D9E75;color:#fff;"
            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >Go to your feed</a>

    {{-- Already has FirstRoots --}}
    @elseif ($alreadyOwns)
        <h1 class="text-2xl font-bold mb-3" style="color:#E6EDF3;">You're already a founding member.</h1>
        <p class="text-sm mb-6 leading-relaxed" style="color:#8B949E;">
            Your FirstRoots badge is already permanently part of your profile.
        </p>
        <a href="{{ route('feed') }}" wire:navigate class="text-sm underline" style="color:#1D9E75;">Go to your feed</a>

    {{-- Valid, unclaimed --}}
    @else
        <h1 class="text-2xl font-bold mb-2" style="color:#E6EDF3;">You've been invited.</h1>
        <p class="text-xs font-semibold mb-4 uppercase tracking-widest" style="color:#1D9E75;">FirstRoots — Founding Member</p>

        <div class="rounded-xl border px-5 py-4 mb-6 text-left" style="background:#0D1117;border-color:#30363D;">
            <p class="text-sm leading-relaxed mb-3" style="color:#8B949E;">
                CommonGrove is a quiet, judgment-free space for introverts and gamers to find real connection — no feeds designed to addict you, no ads, no fake activity.
            </p>
            <p class="text-sm leading-relaxed" style="color:#8B949E;">
                As a <strong style="color:#C9D1D9;">FirstRoots</strong> founding member, you'll carry a permanent badge and a soft teal aura on your avatar — visible everywhere on the platform, forever. You were here before it was anything.
            </p>
        </div>

        @if (auth()->check())
            {{-- Logged in — one-click claim --}}
            <p class="text-xs mb-4" style="color:#8B949E;">
                Claiming as <strong style="color:#C9D1D9;">{{ auth()->user()->gamertag }}</strong>
            </p>
            <button
                type="button"
                wire:click="claim"
                wire:loading.attr="disabled"
                class="w-full py-2.5 px-6 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >
                <span wire:loading.remove>Claim my FirstRoots badge</span>
                <span wire:loading>Claiming…</span>
            </button>
        @else
            {{-- Guest — store token in session then redirect --}}
            <div class="flex flex-col gap-3">
                <button
                    type="button"
                    wire:click="redirectToRegister"
                    wire:loading.attr="disabled"
                    class="w-full py-2.5 px-6 text-sm font-semibold rounded-lg transition disabled:opacity-50 text-center"
                    style="background:#1D9E75;color:#fff;"
                    onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                >Create account to claim</button>
                <button
                    type="button"
                    wire:click="redirectToLogin"
                    wire:loading.attr="disabled"
                    class="w-full py-2.5 px-6 text-sm font-semibold rounded-lg transition disabled:opacity-50 text-center"
                    style="border:1px solid #30363D;color:#8B949E;"
                    onmouseover="this.style.borderColor='rgba(29,158,117,0.4)';this.style.color='#E6EDF3'" onmouseout="this.style.borderColor='#30363D';this.style.color='#8B949E'"
                >Already have an account? Log in to claim</button>
            </div>
        @endif
    @endif

</div>
