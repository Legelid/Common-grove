<div class="px-6 py-12 max-w-2xl mx-auto space-y-8">

    <div class="space-y-2">
        <h1 class="text-2xl font-bold" style="color:var(--text);">Free vs Supporter</h1>
        <p class="text-sm leading-relaxed" style="color:var(--text-muted);">An honest look at what's included at each level. Core connection features stay free, always.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- ── Free column ─────────────────────────────────────────────────── --}}
        <x-card padding="p-6" class="space-y-5">
            <div>
                <h2 class="text-base font-semibold" style="color:var(--text);">Free</h2>
                <p class="text-xs mt-0.5" style="color:var(--text-muted);">Always · no expiry, no catch</p>
            </div>

            @foreach ([
                'Connection'  => [
                    'Chat in rooms and hangouts',
                    'Create temporary hangouts',
                    'Create up to ' . config('supporter.limits.persistent_rooms.free', 3) . ' active persistent rooms',
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
                    'Use CommonGrove without ads · ever',
                    'Report problems',
                    'Community guidelines access',
                ],
            ] as $group => $items)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-faint);">{{ $group }}</p>
                    <ul class="space-y-1.5">
                        @foreach ($items as $item)
                            <li class="flex items-start gap-2 text-sm" style="color:var(--text);">
                                <span class="mt-0.5 flex-none" style="color:var(--text-muted);">–</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </x-card>

        {{-- ── Supporter column ─────────────────────────────────────────────── --}}
        <div class="rounded-card border p-6 space-y-5 bg-surface shadow-card" style="border-color:rgba(var(--accent-rgb),0.3);">
            <div>
                <h2 class="text-base font-semibold" style="color:var(--text);">Supporter</h2>
                <p class="text-xs mt-0.5 text-accent">$1 / month · cancel any time</p>
            </div>

            @foreach ([
                'Everything in Free, plus:'  => [
                    'Helps keep CommonGrove ad-free and independent',
                ],
                'Rooms'       => [
                    'Up to ' . config('supporter.limits.persistent_rooms.supporter', 10) . ' active persistent rooms',
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
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2 text-accent/70">{{ $group }}</p>
                    <ul class="space-y-1.5">
                        @foreach ($items as $item)
                            <li class="flex items-start gap-2 text-sm" style="color:var(--text);">
                                <span class="mt-0.5 flex-none text-accent">✓</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @auth
                @if (! auth()->user()->isSupporter())
                    <x-button :href="route('support.subscribe')" variant="primary">Subscribe · $1/month</x-button>
                @endif
            @endauth
        </div>

    </div>

    {{-- ── Core connection note ──────────────────────────────────────────── --}}
    <div class="rounded-card border px-6 py-4 text-center bg-accent/[0.04]" style="border-color:rgba(var(--accent-rgb),0.18);">
        <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
            Core connection features stay free. Supporter perks are comfort, personalization, and atmosphere only —
            no visibility boosts, no social ranking, no second-class free experience.
        </p>
    </div>

    {{-- ── What it's NOT ────────────────────────────────────────────────── --}}
    <x-card padding="px-6 py-4" class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--text-faint);">What supporter status is not</p>
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
                <li class="flex items-start gap-2 text-xs" style="color:var(--text-faint);">
                    <span class="flex-none">×</span>{{ $item }}
                </li>
            @endforeach
        </ul>
    </x-card>

    <div class="text-center">
        <a href="{{ route('support') }}" class="text-sm underline transition" style="color:var(--text-muted);"
            onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
        >← Back to support page</a>
    </div>

</div>
