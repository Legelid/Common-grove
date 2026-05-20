<div class="px-6 py-12 max-w-3xl mx-auto space-y-10">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="space-y-3">
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Support CommonGrove</h1>
        <p class="text-sm leading-relaxed max-w-xl" style="color:#8B949E;">
            CommonGrove is built to stay calm, ad-free, and independent. Supporters help keep it that way.
        </p>
    </div>

    {{-- ── Free vs Supporter ───────────────────────────────────────────────── --}}
    <div class="grid md:grid-cols-2 gap-5">

        {{-- Free --}}
        <div class="rounded-xl border p-6 flex flex-col gap-5" style="background:#161B22;border-color:#30363D;">
            <div>
                <h2 class="text-base font-semibold" style="color:#E6EDF3;">Free</h2>
                <p class="text-xs mt-1" style="color:#8B949E;">Always — nothing to sign up for</p>
            </div>

            <ul class="space-y-2.5 flex-1">
                @foreach ([
                    'Chat in rooms',
                    'Create temporary hangouts',
                    'Create up to ' . config('supporter.limits.persistent_rooms.free', 3) . ' active persistent rooms',
                    'Use core profiles',
                    'Use default avatars and gradients',
                    'Report problems',
                    'No ads — ever',
                ] as $item)
                    <li class="flex items-start gap-2.5 text-sm" style="color:#C9D1D9;">
                        <span class="flex-none mt-px" style="color:#8B949E;">–</span>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Supporter --}}
        <div class="rounded-xl border p-6 flex flex-col gap-5" style="background:#161B22;border-color:rgba(29,158,117,0.35);">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold" style="color:#E6EDF3;">CommonGrove Supporter</h2>
                    <p class="text-xs mt-1" style="color:#1D9E75;">$1 / month · cancel any time</p>
                </div>
                <span class="flex-none text-xs px-2 py-0.5 rounded font-medium mt-0.5" style="background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.22);">Optional</span>
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
                    <li class="flex items-start gap-2.5 text-sm" style="color:#C9D1D9;">
                        <span class="flex-none mt-px" style="color:#1D9E75;">✓</span>
                        {{ $perk }}
                    </li>
                @endforeach
            </ul>

            <div class="pt-1">
                @auth
                    @if (auth()->user()->isSupporter())
                        <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(29,158,117,0.07);border:1px solid rgba(29,158,117,0.18);color:#1D9E75;">
                            You're a supporter. Thank you.... it genuinely helps.
                        </div>
                    @else
                        <a
                            href="{{ route('support.subscribe') }}"
                            class="inline-flex items-center justify-center w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition"
                            style="background:#1D9E75;color:#fff;"
                            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                        >Become a Supporter</a>
                        <p class="mt-2.5 text-xs text-center" style="color:#3d4451;">Billed monthly via PayPal. No rank, no status system.</p>
                    @endif
                @else
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center justify-center w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition"
                        style="background:#1D9E75;color:#fff;"
                        onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                    >Create an account to subscribe</a>
                    <p class="mt-2.5 text-xs text-center" style="color:#3d4451;">Or <a href="{{ route('login') }}" class="underline" style="color:#8B949E;">sign in</a> if you already have one.</p>
                @endauth
            </div>
        </div>

    </div>

    {{-- ── Core features note ───────────────────────────────────────────────── --}}
    <p class="text-sm text-center" style="color:#3d4451;">
        Core connection features stay free.
    </p>

    {{-- ── Compare link ─────────────────────────────────────────────────────── --}}
    <div class="text-center -mt-4">
        <a href="{{ route('support.compare') }}" class="text-xs transition" style="color:#3d4451;"
            onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
        >See full feature comparison →</a>
    </div>

    {{-- ── Divider ──────────────────────────────────────────────────────────── --}}
    <div class="border-t" style="border-color:#21262D;"></div>

    {{-- ── One-time donation ───────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div>
            <h2 class="text-sm font-semibold" style="color:#8B949E;">Donate once</h2>
            <p class="text-sm mt-1.5 leading-relaxed" style="color:#3d4451;">
                If you'd rather just send a one-time thank you, you can do that too.
            </p>
        </div>

        <a
            href="{{ config('services.paypal.donation_url', '#') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition"
            style="background:#161B22;color:#8B949E;border:1px solid #30363D;"
            onmouseover="this.style.color='#C9D1D9';this.style.borderColor='#8B949E'" onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D'"
        >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/>
            </svg>
            Donate via PayPal
        </a>
        <p class="text-xs" style="color:#3d4451;">Opens PayPal in a new tab. Not tax-deductible. Doesn't grant any status.</p>
    </section>

</div>
