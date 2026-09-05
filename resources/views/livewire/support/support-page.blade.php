<div class="px-6 py-12 max-w-3xl mx-auto space-y-10">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="space-y-3">
        <h1 class="text-2xl font-bold" style="color:var(--text);">Support CommonGrove</h1>
        <p class="text-sm leading-relaxed max-w-xl" style="color:var(--text-muted);">
            CommonGrove is built to stay calm, ad-free, and independent. Supporters help keep it that way.
        </p>
    </div>

    {{-- ── Free vs Supporter ───────────────────────────────────────────────── --}}
    <div class="grid md:grid-cols-2 gap-5">

        {{-- Free --}}
        <x-card padding="p-6" class="flex flex-col gap-5">
            <div>
                <h2 class="text-base font-semibold" style="color:var(--text);">Free</h2>
                <p class="text-xs mt-1" style="color:var(--text-muted);">Always · nothing to sign up for</p>
            </div>

            <ul class="space-y-2.5 flex-1">
                @foreach ([
                    'Chat in rooms',
                    'Create temporary hangouts',
                    'Create up to ' . config('supporter.limits.persistent_rooms.free', 3) . ' active persistent rooms',
                    'Use core profiles',
                    'Use default avatars and gradients',
                    'Report problems',
                    'No ads · ever',
                ] as $item)
                    <li class="flex items-start gap-2.5 text-sm" style="color:var(--text);">
                        <span class="flex-none mt-px" style="color:var(--text-muted);">–</span>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </x-card>

        {{-- Supporter --}}
        <div class="rounded-card border p-6 flex flex-col gap-5 bg-surface shadow-card" style="border-color:rgba(var(--accent-rgb),0.35);">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold" style="color:var(--text);">CommonGrove Supporter</h2>
                    <p class="text-xs mt-1 text-accent">$1 / month · cancel any time</p>
                </div>
                <span class="flex-none text-xs px-2 py-0.5 rounded font-medium mt-0.5 bg-accent/10 text-accent" style="border:1px solid rgba(var(--accent-rgb),0.22);">Optional</span>
            </div>

            <ul class="space-y-2.5 flex-1">
                @foreach ([
                    'Help keep CommonGrove ad-free and independent',
                    'More profile atmospheres',
                    'More curated avatars',
                    'More gradient options',
                    'Room collections',
                    'Higher persistent room limit (up to ' . config('supporter.limits.persistent_rooms.supporter', 10) . ')',
                    'Atmosphere and tone packs',
                    'Extra comfort controls',
                    'Optional tiny supporter icon',
                ] as $perk)
                    <li class="flex items-start gap-2.5 text-sm" style="color:var(--text);">
                        <span class="flex-none mt-px text-accent">✓</span>
                        {{ $perk }}
                    </li>
                @endforeach
            </ul>

            <div class="pt-1">
                @auth
                    @if (auth()->user()->isSupporter())
                        <div class="rounded-btn px-4 py-3 text-sm bg-accent/[0.07] text-accent" style="border:1px solid rgba(var(--accent-rgb),0.18);">
                            You're a supporter. Thank you.... it genuinely helps.
                        </div>
                    @else
                        <x-button :href="route('support.subscribe')" variant="primary" class="!w-full">Become a Supporter</x-button>
                        <p class="mt-2.5 text-xs text-center" style="color:var(--text-faint);">Billed monthly via PayPal. No rank, no status system.</p>
                    @endif
                @else
                    <x-button :href="route('register')" variant="primary" class="!w-full">Create an account to subscribe</x-button>
                    <p class="mt-2.5 text-xs text-center" style="color:var(--text-faint);">Or <a href="{{ route('login') }}" class="underline" style="color:var(--text-muted);">sign in</a> if you already have one.</p>
                @endauth
            </div>
        </div>

    </div>

    {{-- ── Core features note ───────────────────────────────────────────────── --}}
    <p class="text-sm text-center" style="color:var(--text-faint);">
        Core connection features stay free.
    </p>

    {{-- ── Compare link ─────────────────────────────────────────────────────── --}}
    <div class="text-center -mt-4">
        <a href="{{ route('support.compare') }}" aria-label="See full feature comparison"><x-arrow-icon label="See full feature comparison" /></a>
    </div>

    {{-- ── Divider ──────────────────────────────────────────────────────────── --}}
    <div class="border-t" style="border-color:var(--border);"></div>

    {{-- ── One-time donation ───────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div>
            <h2 class="text-sm font-semibold" style="color:var(--text-muted);">Donate once</h2>
            <p class="text-sm mt-1.5 leading-relaxed" style="color:var(--text-faint);">
                If you'd rather just send a one-time thank you, you can do that too.
            </p>
        </div>

        <a
            href="{{ config('services.paypal.donation_url', '#') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-btn transition bg-surface"
            style="color:var(--text-muted);border:1px solid var(--border);"
            onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--text-muted)'" onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)'"
        >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/>
            </svg>
            Donate via PayPal
        </a>
        <p class="text-xs" style="color:var(--text-faint);">Opens PayPal in a new tab. Not tax-deductible. Doesn't grant any status.</p>
    </section>

</div>
