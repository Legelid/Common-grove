@php
    $isAuthPage = request()->routeIs('login', 'register', 'password.*', 'verification.*', 'home');
    $isLowStim  = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg     = $isLowStim
        ? 'background:#0D1117;'
        : 'background:radial-gradient(ellipse 80% 50% at 50% 100%, rgba(29,158,117,0.055) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 75% 0%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full {{ $isAuthPage ? 'overflow-hidden' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased {{ $isAuthPage ? 'overflow-hidden' : '' }} {{ $isLowStim ? 'low-stimulation' : '' }}" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

@if ($isAuthPage)

    {{-- ── Auth shell ─────────────────────────────────────────────────────── --}}
    <div class="min-h-full flex flex-col items-center justify-center px-4 py-12">
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="text-xl font-bold" style="color:#E6EDF3;">CommonGround</a>
            <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-semibold align-middle" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
        </div>
        <div class="w-full max-w-md rounded-xl border p-8 shadow-2xl" style="background:#161B22;border-color:#30363D;">
            {{ $slot }}
        </div>
    </div>

@else

    {{-- ── App shell: header + 3-column body ──────────────────────────────── --}}
    <div class="flex flex-col h-full">

        {{-- ── Top header: branding only ───────────────────────────────────── --}}
        <header class="h-14 flex-none flex items-center justify-between px-6 border-b z-30" style="background:#161B22;border-color:#30363D;box-shadow:0 1px 0 rgba(0,0,0,0.2);">
            <div class="flex items-center gap-3">
                <a href="{{ route('feed') }}" wire:navigate class="flex items-center gap-2">
                    <span class="font-bold tracking-tight" style="color:#E6EDF3;">CommonGround</span>
                </a>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
            </div>
            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        type="button"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        class="flex items-center gap-2 transition"
                        style="color:#8B949E;"
                        onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                    >
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full object-cover" style="background:#21262D;">
                        <span class="hidden sm:block text-xs">{{ auth()->user()->gamertag }}</span>
                        <svg x-show="!open" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        <svg x-show="open" style="display:none;" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
                    </button>

                    <div
                        x-show="open"
                        class="account-dropdown fixed right-6 w-44 rounded-xl py-1"
                        style="display:none;top:60px;border:1px solid #30363D;box-shadow:0 8px 24px rgba(0,0,0,0.5);z-index:9999;"
                    >
                        <a
                            href="{{ route('profile.show', auth()->user()->gamertag) }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#E6EDF3';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='#8B949E';this.style.background=''"
                        >Profile</a>
                        <a
                            href="{{ route('profile.settings') }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#E6EDF3';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='#8B949E';this.style.background=''"
                        >Settings</a>
                        @if (auth()->user()->is_admin)
                            <a
                                href="{{ route('admin.dashboard') }}"
                                wire:navigate
                                @click="open = false"
                                class="flex items-center px-4 py-2.5 text-sm transition"
                                style="color:#D29922;"
                                onmouseover="this.style.color='#E6B84A';this.style.background='rgba(210,153,34,0.05)'"
                                onmouseout="this.style.color='#D29922';this.style.background=''"
                            >Admin Panel</a>
                        @endif
                        <div class="my-1 border-t" style="border-color:#21262D;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex items-center w-full px-4 py-2.5 text-sm transition text-left"
                                style="color:#8B949E;"
                                onmouseover="this.style.color='#E24B4A';this.style.background='rgba(226,75,74,0.06)'"
                                onmouseout="this.style.color='#8B949E';this.style.background=''"
                            >Sign out</button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        {{-- ── 3-column body ────────────────────────────────────────────────── --}}
        <div class="flex flex-1 overflow-hidden">

            {{-- ── LEFT: Discovery ──────────────────────────────────────────── --}}
            <aside class="w-52 flex-none flex flex-col border-r overflow-y-auto" style="background:#161B22;border-color:#30363D;">

                {{-- Rotating tagline --}}
                <div
                    x-data="tagline({{ (auth()->check() && auth()->user()->low_stimulation_mode) ? 'true' : 'false' }})"
                    class="px-4 pt-5 pb-4 border-b flex-none"
                    style="border-color:#21262D;"
                >
                    <p
                        x-text="phrases[idx]"
                        :style="{ opacity: visible ? '1' : '0', transition: 'opacity 0.6s ease' }"
                        class="text-xs leading-relaxed"
                        style="color:#8B949E;min-height:2.5rem;"
                        aria-live="polite"
                        aria-atomic="true"
                    ></p>
                </div>

                {{-- Your rooms + discovery cards --}}
                @auth
                    <div class="px-3 py-3 space-y-2.5 overflow-y-auto">
                        <livewire:rooms.pinned-rooms-sidebar />
                        <livewire:rooms.recent-rooms-sidebar />
                        <livewire:sidebar.sidebar-discovery />
                    </div>
                @endauth

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Admin mode indicator --}}
                @auth
                    @if (auth()->user()->is_admin && request()->routeIs('admin.*'))
                        <div class="flex-none px-4 py-2 border-t" style="border-color:#30363D;">
                            <span class="text-xs px-2 py-0.5 rounded font-medium"
                                style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.25);">
                                Admin Mode Active
                            </span>
                        </div>
                    @endif
                @endauth


            </aside>

            {{-- ── CENTER: Main content ──────────────────────────────────────── --}}
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>

            {{-- ── RIGHT: Navigation rail ────────────────────────────────────── --}}
            <nav class="w-44 flex-none flex flex-col border-l" style="background:#161B22;border-color:#30363D;box-shadow:-1px 0 0 rgba(0,0,0,0.15);">

                {{-- Nav section label --}}
                <div class="px-5 pt-6 pb-2">
                    <p class="text-xs font-medium uppercase tracking-widest" style="color:#3d4451;">Navigate</p>
                </div>

                {{-- Nav links --}}
                @auth
                    @php
                        $navItems = [
                            [
                                'href'     => route('feed'),
                                'patterns' => ['feed', 'feed.post'],
                                'label'    => 'Feed',
                            ],
                            [
                                'href'     => route('friends.index'),
                                'patterns' => ['friends.*'],
                                'label'    => 'Friends',
                            ],
                            [
                                'href'     => route('messages.index'),
                                'patterns' => ['messages.*', 'room.*'],
                                'label'    => 'Messages',
                            ],
                            [
                                'href'     => route('tags.select'),
                                'patterns' => ['tags.*'],
                                'label'    => 'Interests',
                            ],
                            [
                                'href'     => route('profile.settings'),
                                'patterns' => ['profile.settings', 'profile.show'],
                                'label'    => 'Settings',
                            ],
                        ];
                    @endphp

                    <div class="flex-1 overflow-y-auto px-3 space-y-0.5">
                        @foreach ($navItems as $item)
                            @php
                                $active = collect($item['patterns'])->contains(fn ($p) => request()->routeIs($p));
                            @endphp
                            <a
                                href="{{ $item['href'] }}"
                                wire:navigate
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition"
                                style="{{ $active
                                    ? 'background:rgba(29,158,117,0.1);color:#E6EDF3;'
                                    : 'color:#8B949E;' }}"
                                onmouseover="{{ $active ? '' : "this.style.color='#C9D1D9';this.style.background='rgba(255,255,255,0.04)'" }}"
                                onmouseout="{{ $active ? '' : "this.style.color='#8B949E';this.style.background=''" }}"
                            >
                                @if ($active)
                                    <span class="w-1 h-1 rounded-full flex-none" style="background:#1D9E75;"></span>
                                @else
                                    <span class="w-1 h-1 rounded-full flex-none" style="background:transparent;"></span>
                                @endif
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                    </div>

                    {{-- Support section --}}
                    <div class="flex-none px-3 pb-5 border-t" style="border-color:#21262D;">
                        <div class="px-3 pt-4 pb-1">
                            <p class="text-xs font-medium uppercase tracking-widest" style="color:#3d4451;">Support</p>
                        </div>
                        <a
                            href="{{ route('guidelines') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition"
                            style="color:#3d4451;"
                            onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                            onmouseout="this.style.color='#3d4451';this.style.background=''"
                        >Community Guidelines</a>
                        <a
                            href="{{ route('report') }}"
                            wire:navigate
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition"
                            style="color:#3d4451;"
                            onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                            onmouseout="this.style.color='#3d4451';this.style.background=''"
                        >Report a problem</a>
                    </div>
                @endauth

            </nav>

        </div>
    </div>

@endif

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tagline', (lowStim) => ({
        phrases: [
            'A place to find your people',
            "You don't have to rush here",
            'Just being here is enough',
            "Take your time — there's no pressure",
            'A quieter corner of the internet',
            'Find people who feel familiar',
            'Come as you are',
            "It's okay to just exist here",
            'No expectations, just connection',
            'A place to feel a little less alone',
            'You can take things slow here',
            'Not everything has to be said right away',
            'Stay as long as you like',
            'A calm place to connect',
            "You're welcome here, however you show up",
            'No pressure to be anything but yourself',
            'Find your pace here',
            "You don't have to perform here",
            'A space that moves at your speed',
            'You can just listen if you want',
        ],
        idx: 0,
        visible: true,
        init() {
            if (!lowStim) {
                this.idx = Math.floor(Math.random() * this.phrases.length);
            }
            const noMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (lowStim || noMotion) return;
            setInterval(() => {
                this.visible = false;
                setTimeout(() => {
                    this.idx = (this.idx + 1) % this.phrases.length;
                    this.visible = true;
                }, 600);
            }, 12000);
        },
    }));
});
</script>
@livewireScripts
</body>
</html>
