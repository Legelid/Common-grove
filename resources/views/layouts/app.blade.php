@php
    $isAuthPage = request()->routeIs('login', 'register', 'password.*', 'verification.*', 'home');
    $isLowStim  = auth()->check() && auth()->user()->low_stimulation_mode;

    // Advanced comfort settings (supporter-only)
    $advancedComfort = [];
    if (auth()->check() && !$isAuthPage) {
        $cgUser = auth()->user();
        if ($cgUser->is_admin || $cgUser->isSupporter()) {
            $advancedComfort = $cgUser->advanced_comfort_settings ?? [];
        }
    }
    $cgHideGradients = in_array('hide_gradients', $advancedComfort, true);

    // $isBirthday = it IS their birthday and the theme is enabled (regardless of low-stim)
    $isBirthday = auth()->check()
        && (auth()->user()->birthday_theme_enabled ?? true)
        && auth()->user()->isBirthday();

    // Full visual experience: birthday + not low-stim
    $showBirthdayDecorations = $isBirthday && !$isLowStim;

    // Holiday atmosphere (opt-out, disabled in low-stim)
    $holiday                = null;
    $showHolidayDecorations = false;
    if (!$isLowStim && auth()->check() && (auth()->user()->holiday_themes_enabled ?? true)) {
        $holiday                = app(\App\Services\HolidayThemeService::class)->currentHoliday();
        $showHolidayDecorations = $holiday !== null;
    }

    // Tone pack phrases (personalised rotating tagline for the sidebar)
    $tonePackPhrases = auth()->check() && !$isAuthPage
        ? app(\App\Services\TonePackService::class)->getPhrases(auth()->user())
        : null;

    $defaultGradientCss  = 'radial-gradient(ellipse 80% 50% at 50% 100%, rgba(29,158,117,0.055) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 75% 0%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117';
    $birthdayGradientCss = 'radial-gradient(ellipse 65% 45% at 50% 0%, rgba(160,80,200,0.14) 0%, transparent 65%), radial-gradient(ellipse 50% 40% at 80% 90%, rgba(200,130,160,0.10) 0%, transparent 55%), #0D1117';

    if ($isLowStim || $cgHideGradients) {
        $bodyBg = 'background:#0D1117;';
    } elseif ($showBirthdayDecorations) {
        $bodyBg = 'background:' . $birthdayGradientCss . ';';
    } elseif ($showHolidayDecorations) {
        $bodyBg = 'background:' . $holiday['gradient'] . ';';
    } elseif (auth()->check() && auth()->user()->personal_gradient_theme) {
        $themeKey    = auth()->user()->personal_gradient_theme;
        $gradientDef = config('gradients.' . $themeKey);
        $isSupporter = auth()->user()->is_supporter || auth()->user()->is_admin;
        $isLocked    = in_array($themeKey, config('supporter.gradient_packs', []), true);
        if ($gradientDef && (! $isLocked || $isSupporter)) {
            $bodyBg = 'background:' . $gradientDef['css'] . ';';
        } else {
            $bodyBg = 'background:' . $defaultGradientCss . ';';
        }
    } else {
        $bodyBg = 'background:' . $defaultGradientCss . ';';
    }

    // Build body classes
    $cgBodyClasses = ['h-full', 'antialiased'];
    if ($isAuthPage) $cgBodyClasses[] = 'overflow-hidden';
    if ($isLowStim)  $cgBodyClasses[] = 'low-stimulation';
    $cgClassMap = [
        'ultra_minimal'    => 'cg-ultra-minimal',
        'extra_spacing'    => 'cg-extra-spacing',
        'simple_room_cards'=> 'cg-simple-room-cards',
        'reduce_sidebar'   => 'cg-reduce-sidebar',
        'hide_suggestions' => 'cg-hide-suggestions',
        'hide_phrases'     => 'cg-hide-phrases',
        'compact_chat'     => 'cg-compact-chat',
        'larger_text'      => 'cg-larger-text',
    ];
    foreach ($cgClassMap as $settingKey => $cssClass) {
        if (in_array($settingKey, $advancedComfort, true)) {
            $cgBodyClasses[] = $cssClass;
        }
    }

    $navItems = [];
    if (auth()->check() && !$isAuthPage) {
        $navItems = [
            ['href' => route('feed'),             'patterns' => ['feed', 'feed.post'],                 'label' => 'Feed'],
            ['href' => route('friends.index'),    'patterns' => ['friends.*'],                         'label' => 'Friends'],
            ['href' => route('messages.index'),   'patterns' => ['messages.*', 'room.*'],              'label' => 'Messages'],
            ['href' => route('tags.select'),      'patterns' => ['tags.*'],                            'label' => 'Interests'],
            ['href' => route('profile.settings'), 'patterns' => ['profile.settings', 'profile.show'], 'label' => 'Settings'],
        ];
    }
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
    <style>
        /* Advanced comfort controls */
        body.cg-hide-phrases .cg-tagline,
        body.cg-ultra-minimal .cg-tagline { display: none; }
        body.cg-hide-suggestions .cg-suggestion-preview,
        body.cg-ultra-minimal .cg-suggestion-preview { display: none; }
        body.cg-simple-room-cards .cg-room-badge,
        body.cg-ultra-minimal .cg-room-badge { display: none; }
        body.cg-simple-room-cards .cg-room-card-tags,
        body.cg-ultra-minimal .cg-room-card-tags { display: none; }
        body.cg-extra-spacing .cg-chat-messages > :not([hidden]) ~ :not([hidden]) { margin-top: 1.25rem; }
        body.cg-reduce-sidebar .cg-tagline-wrap { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        body.cg-reduce-sidebar .cg-sidebar-content a { padding-top: 0.15rem; padding-bottom: 0.15rem; }
        body.cg-reduce-sidebar .cg-sidebar-content { gap: 0.25rem; }
        body.cg-compact-chat .cg-chat-messages > :not([hidden]) ~ :not([hidden]) { margin-top: 0.1875rem; }
        body.cg-compact-chat .cg-msg-bubble { padding-top: 0.375rem; padding-bottom: 0.375rem; }
        body.low-stimulation .cg-reaction-tray,
        body.low-stimulation .cg-reaction-picker { transition: none !important; }
        @media (hover: none) and (pointer: coarse) {
            .cg-room-card:active { background: #1C2333 !important; border-color: #3d4451 !important; transition: background 0.1s, border-color 0.1s; }
            body.low-stimulation .cg-room-card:active { background: #161B22 !important; border-color: #30363D !important; }
        }
    </style>
</head>
<body class="{{ implode(' ', $cgBodyClasses) }}" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">
<script>if(document.body.classList.contains('cg-larger-text')){document.documentElement.style.fontSize='17px';}</script>

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
    <div class="flex flex-col h-full" x-data="{ navOpen: false }">

        {{-- ── Top header: branding only ───────────────────────────────────── --}}
        <header class="h-14 flex-none flex items-center justify-between px-4 sm:px-6 border-b z-30" style="background:#161B22;border-color:#30363D;box-shadow:0 1px 0 rgba(0,0,0,0.2);">
            <div class="flex items-center gap-3">
                {{-- Hamburger — mobile only --}}
                @auth
                    <button
                        type="button"
                        @click="navOpen = true"
                        class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg transition -ml-1"
                        style="color:#8B949E;"
                        aria-label="Open navigation"
                        :aria-expanded="navOpen.toString()"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                @endauth
                <a href="{{ route('feed') }}" wire:navigate class="flex items-center gap-2">
                    <span class="font-bold tracking-tight" style="color:#E6EDF3;">CommonGround</span>
                </a>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
                @if ($isBirthday && !$isAuthPage)
                    <span class="text-xs hidden sm:inline" style="color:#C4A0D4;">· Happy birthday!</span>
                @endif
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
                        <a
                            href="{{ route('settings.supporter') }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#E6EDF3';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='#8B949E';this.style.background=''"
                        >Supporter</a>
                        <div class="my-1 border-t" style="border-color:#21262D;"></div>
                        <form method="POST" action="/logout">
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

        {{-- ── Email verification banner ──────────────────────────────────── --}}
        @auth
            @if(! auth()->user()->hasVerifiedEmail())
                <livewire:auth.email-verification-banner />
            @endif
        @endauth

        {{-- ── Holiday atmosphere banner ───────────────────────────────────── --}}
        @if ($showHolidayDecorations && !$isAuthPage)
            @include('partials.holidays.' . $holiday['id'], ['holiday' => $holiday])
        @endif

        {{-- ── Birthday banner (full experience only) ─────────────────────── --}}
        @if ($showBirthdayDecorations && !$isAuthPage)
            <div class="flex-none" style="background:rgba(155,75,195,0.07);border-bottom:1px solid rgba(155,75,195,0.16);" aria-live="polite">
                <div class="flex items-center justify-center px-4 py-2" style="min-height:2.2rem;">
                    <span style="color:#C2A0D8;font-size:0.8rem;letter-spacing:0.01em;">Hope today is kind to you.</span>
                </div>
            </div>
        @endif

        {{-- ── 3-column body ────────────────────────────────────────────────── --}}
        <div class="flex flex-1 overflow-hidden">

            {{-- ── LEFT: Discovery ──────────────────────────────────────────── --}}
            <aside class="cg-discovery-sidebar hidden md:flex md:flex-col w-52 flex-none border-r overflow-y-auto" style="background:#161B22;border-color:#30363D;">

                {{-- Rotating tagline --}}
                <div
                    x-data="tagline({{ $isLowStim ? 'true' : 'false' }}, {{ $tonePackPhrases ? \Illuminate\Support\Js::from($tonePackPhrases) : 'null' }})"
                    class="cg-tagline-wrap px-4 pt-5 pb-4 border-b flex-none"
                    style="border-color:#21262D;"
                >
                    <p
                        x-text="phrases[idx]"
                        :style="{ opacity: visible ? '1' : '0', transition: 'opacity 0.6s ease' }"
                        class="cg-tagline text-xs leading-relaxed"
                        style="color:#8B949E;min-height:2.5rem;"
                        aria-live="polite"
                        aria-atomic="true"
                    ></p>
                </div>

                {{-- Your rooms + discovery cards --}}
                @auth
                    <div class="cg-sidebar-content px-3 py-3 space-y-2.5 overflow-y-auto">
                        <livewire:rooms.pinned-rooms-sidebar />
                        @if(auth()->user()->isSupporter())
                            <livewire:rooms.room-collections-sidebar />
                        @endif
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
            <nav class="hidden md:flex md:flex-col w-44 flex-none border-l" style="background:#161B22;border-color:#30363D;box-shadow:-1px 0 0 rgba(0,0,0,0.15);">

                {{-- Nav section label --}}
                <div class="px-5 pt-6 pb-2">
                    <p class="text-xs font-medium uppercase tracking-widest" style="color:#3d4451;">Navigate</p>
                </div>

                {{-- Nav links --}}
                @auth
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
                        <a
                            href="{{ route('support') }}"
                            wire:navigate
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition"
                            style="color:#3d4451;"
                            onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                            onmouseout="this.style.color='#3d4451';this.style.background=''"
                        >Support CommonGrove</a>
                    </div>
                @endauth

            </nav>

        </div>

        {{-- ── Mobile nav drawer ───────────────────────────────────────────────── --}}
        @auth
            {{-- Backdrop --}}
            <div
                x-show="navOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="navOpen = false"
                class="fixed inset-0 z-40 md:hidden"
                style="display:none;background:rgba(0,0,0,0.6);"
                aria-hidden="true"
            ></div>

            {{-- Drawer panel --}}
            <div
                x-show="navOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col md:hidden"
                style="display:none;background:#161B22;border-right:1px solid #30363D;"
            >
                {{-- Drawer header --}}
                <div class="flex items-center justify-between px-5 py-4 flex-none border-b" style="border-color:#21262D;">
                    <div class="flex items-center gap-2">
                        <span class="font-bold tracking-tight" style="color:#E6EDF3;">CommonGround</span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
                    </div>
                    <button
                        type="button"
                        @click="navOpen = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg transition"
                        style="color:#8B949E;"
                        aria-label="Close menu"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                {{-- Nav links --}}
                <div class="flex-1 overflow-y-auto px-3 py-4">
                    <p class="text-xs font-medium uppercase tracking-widest px-3 pb-3" style="color:#3d4451;">Navigate</p>
                    <div class="space-y-0.5">
                        @foreach ($navItems as $item)
                            @php $drawerActive = collect($item['patterns'])->contains(fn ($p) => request()->routeIs($p)); @endphp
                            <a
                                href="{{ $item['href'] }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition"
                                style="{{ $drawerActive
                                    ? 'background:rgba(29,158,117,0.1);color:#E6EDF3;'
                                    : 'color:#8B949E;' }}"
                                onmouseover="{{ $drawerActive ? '' : "this.style.color='#C9D1D9';this.style.background='rgba(255,255,255,0.04)'" }}"
                                onmouseout="{{ $drawerActive ? '' : "this.style.color='#8B949E';this.style.background=''" }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full flex-none" style="background:{{ $drawerActive ? '#1D9E75' : 'transparent' }};"></span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-5 border-t" style="border-color:#21262D;">
                        <p class="text-xs font-medium uppercase tracking-widest px-3 pb-3" style="color:#3d4451;">Community</p>
                        <div class="space-y-0.5">
                            <a
                                href="{{ route('guidelines') }}"
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='#3d4451';this.style.background=''"
                            >Community Guidelines</a>
                            <a
                                href="{{ route('report') }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='#3d4451';this.style.background=''"
                            >Report a problem</a>
                            <a
                                href="{{ route('support') }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='#3d4451';this.style.background=''"
                            >Support CommonGrove</a>
                        </div>
                    </div>

                    @if (auth()->user()->is_admin)
                        <div class="mt-5 pt-5 border-t" style="border-color:#21262D;">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:#D29922;"
                                onmouseover="this.style.background='rgba(210,153,34,0.06)'" onmouseout="this.style.background=''"
                            >Admin Panel</a>
                        </div>
                    @endif
                </div>

                {{-- Drawer footer: avatar + sign out --}}
                <div class="flex-none px-4 py-4 border-t" style="border-color:#21262D;">
                    <div class="flex items-center justify-between gap-3">
                        <a
                            href="{{ route('profile.show', auth()->user()->gamertag) }}"
                            wire:navigate
                            @click="navOpen = false"
                            class="flex items-center gap-3 min-w-0"
                        >
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover flex-none" style="background:#21262D;">
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate" style="color:#C9D1D9;">{{ auth()->user()->display_name ?? auth()->user()->gamertag }}</p>
                                <p class="text-xs truncate" style="color:#8B949E;">{{ auth()->user()->gamertag }}</p>
                            </div>
                        </a>
                        <form method="POST" action="/logout" class="flex-none">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs px-2.5 py-1.5 rounded-lg transition"
                                style="color:#8B949E;border:1px solid #30363D;"
                                onmouseover="this.style.color='#E24B4A';this.style.borderColor='rgba(226,75,74,0.4)'"
                                onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D'"
                            >Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth

    </div>

@endif

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tagline', (lowStim, customPhrases) => ({
        phrases: customPhrases || [
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
