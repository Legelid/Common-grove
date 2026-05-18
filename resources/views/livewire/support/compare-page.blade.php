<div class="px-6 py-12 max-w-2xl mx-auto space-y-8">

    <div class="space-y-2">
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Free vs Supporter</h1>
        <p class="text-sm leading-relaxed" style="color:#8B949E;">An honest look at what's included at each level. Core connection features stay free, always.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- ── Free column ─────────────────────────────────────────────────── --}}
        <div class="rounded-xl border p-6 space-y-5" style="background:#161B22;border-color:#30363D;">
            <div>
                <h2 class="text-base font-semibold" style="color:#E6EDF3;">Free</h2>
                <p class="text-xs mt-0.5" style="color:#8B949E;">Always — no expiry, no catch</p>
            </div>

            @foreach ([
                'Connection'  => [
                    'Chat in rooms and hangouts',
                    'Create temporary hangouts',
                    'Create up to {{ config(\'supporter.limits.persistent_rooms.free\') }} active persistent rooms',
                    'Direct messages',
                    'Friend requests',
                ],
                'Profiles'    => [
                    'Core profile and identity options',
                    'Core curated avatars (4 full categories)',
                    '5 gradient themes',
                    'Profile expression fields',
                ],
                'Platform'    => [
                    'Use CommonGrove without ads — ever',
                    'Report problems',
                    'Community guidelines access',
                ],
            ] as $group => $items)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#3d4451;">{{ $group }}</p>
                    <ul class="space-y-1.5">
                        @foreach ($items as $item)
                            <li class="flex items-start gap-2 text-sm" style="color:#C9D1D9;">
                                <span class="mt-0.5 flex-none" style="color:#8B949E;">–</span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- ── Supporter column ─────────────────────────────────────────────── --}}
        <div class="rounded-xl border p-6 space-y-5" style="background:#161B22;border-color:rgba(29,158,117,0.3);">
            <div>
                <h2 class="text-base font-semibold" style="color:#E6EDF3;">Supporter</h2>
                <p class="text-xs mt-0.5" style="color:#1D9E75;">$1 / month · cancel any time</p>
            </div>

            @foreach ([
                'Everything in Free, plus:'  => [
                    'Helps keep CommonGrove ad-free and independent',
                ],
                'Rooms'       => [
                    'Up to {{ config(\'supporter.limits.persistent_rooms.supporter\') }} active persistent rooms',
                ],
                'Atmosphere'  => [
                    'Extra gradient themes (5 additional)',
                    'Atmosphere and tone packs for rooms',
                ],
                'Profiles'    => [
                    'Cozy Digital / Retro avatar category',
                    'Seasonal avatar category',
                    'More avatar options as packs are added',
                ],
                'Comfort'     => [
                    'Advanced comfort controls',
                    'Optional tiny supporter icon (with tooltip "I believe in CommonGrove")',
                ],
            ] as $group => $items)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(29,158,117,0.7);">{{ $group }}</p>
                    <ul class="space-y-1.5">
                        @foreach ($items as $item)
                            <li class="flex items-start gap-2 text-sm" style="color:#C9D1D9;">
                                <span class="mt-0.5 flex-none" style="color:#1D9E75;">✓</span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @auth
                @if (! auth()->user()->isSupporter())
                    <a
                        href="{{ route('support.subscribe') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition"
                        style="background:#1D9E75;color:#fff;"
                        onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                    >Subscribe — $1/month</a>
                @endif
            @endauth
        </div>

    </div>

    {{-- ── Core connection note ──────────────────────────────────────────── --}}
    <div class="rounded-xl border px-6 py-4 text-center" style="background:rgba(29,158,117,0.04);border-color:rgba(29,158,117,0.18);">
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            Core connection features stay free. Supporter perks are comfort, personalization, and atmosphere only —
            no visibility boosts, no social ranking, no second-class free experience.
        </p>
    </div>

    {{-- ── What it's NOT ────────────────────────────────────────────────── --}}
    <div class="rounded-xl border px-6 py-4 space-y-2" style="background:#161B22;border-color:#21262D;">
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#3d4451;">What supporter status is not</p>
        <ul class="grid grid-cols-2 gap-x-6 gap-y-1.5">
            @foreach ([
                'No follower or like systems',
                'No ranked users or leaderboards',
                'No visibility boosts',
                'No priority room placement',
                'No special moderation power',
                'No engagement rewards',
                'No exclusive popularity badges',
                'No second-class free tier',
            ] as $item)
                <li class="flex items-start gap-2 text-xs" style="color:#3d4451;">
                    <span class="flex-none">×</span>{{ $item }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="text-center">
        <a href="{{ route('support') }}" class="text-sm underline transition" style="color:#8B949E;"
            onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
        >← Back to support page</a>
    </div>

</div>
