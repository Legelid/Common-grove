@php
    $isAuthPage = request()->routeIs('login', 'register', 'password.*', 'verification.*', 'home', 'auth.google.*', 'auth.discord.*');
    $isLowStim  = auth()->check() && auth()->user()->low_stimulation_mode;

    // Advanced comfort settings (supporter-only)
    $advancedComfort = [];
    if (auth()->check() && !$isAuthPage) {
        $cgUser = auth()->user();
        if ($cgUser->is_admin || $cgUser->isSupporter()) {
            $advancedComfort = $cgUser->advanced_comfort_settings ?? [];
        }
    }

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

    $bodyBg = 'background:var(--bg);';

    // Glass UI theme system (Phase 4 of 6) — applies on the routes listed in
    // $glassRoutes (forest photo + glass panels). Every other route keeps
    // the solid $bodyBg above. Add a route name here to extend the theme.
    $glassRoutes  = ['feed', 'explore', 'messages.show', 'room.show', 'messages.index', 'friends.index', 'profile.settings', 'profile.edit', 'tags.select'];
    $isGlassRoute = request()->routeIs($glassRoutes);

    // Glass UI theme (Phase 6): which forest-photo-and-tint theme to show
    // behind the glass panels — resolved per-user via GlassThemeService,
    // falling back to the free default for guests.
    $glassThemeService = app(\App\Services\GlassThemeService::class);
    $glassThemeKey     = auth()->check() ? $glassThemeService->resolveKey(auth()->user()) : 'forest-default';
    $glassTheme        = config("glass_themes.{$glassThemeKey}") ?? config('glass_themes.forest-default', []);

    // Glass UI blur toggle (Settings > Vibe > Theme) — user opt-out of the
    // backdrop-filter/photo blur, on by default.
    $glassBlurDisabled = auth()->check() && ! (auth()->user()->glass_blur_enabled ?? true);

    // Build body classes
    $cgBodyClasses = $isAuthPage ? ['min-h-screen', 'antialiased'] : ['h-full', 'antialiased'];
    if ($isLowStim)  $cgBodyClasses[] = 'low-stimulation';
    if ($glassBlurDisabled) $cgBodyClasses[] = 'cg-no-glass-blur';
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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $isAuthPage ? '' : 'h-full' }}" data-glass-theme="{{ $glassThemeKey }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon2.png') }}">
    @include('partials.fonts')
    @include('partials.theme-init')
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
            .cg-room-card:active { background: var(--surface-raised) !important; border-color: var(--text-faint) !important; transition: background 0.1s, border-color 0.1s; }
            body.low-stimulation .cg-room-card:active { background: var(--surface) !important; border-color: var(--border) !important; }
        }
        @keyframes cg-banner-in {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .cg-guest-banner { animation: cg-banner-in 0.3s ease-out both; }

        /* ── Oval nav pills (Layer 2) — straddle the identity bar's bottom divider ── */
        .cg-nav-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            min-height: 36px;
            padding: 6px 18px;
            border-radius: var(--radius-pill);
            overflow: hidden;
            font-family: var(--font-body);
            font-size: 0.875rem;
            background: var(--surface);
            color: var(--text-muted);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 150ms ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .cg-nav-pill:not(.cg-nav-pill--active):hover {
            background: var(--surface-raised);
            color: var(--text);
            transform: translateY(-2px);
        }
        .cg-nav-pill--active {
            background: var(--accent);
            color: var(--bg);
            font-weight: 600;
            border-color: transparent;
        }
        {{-- Desktop pills show only their icon at rest; the label lives inside a
             max-width:0 clip and slides out to the icon's left on hover/focus. --}}
        .cg-nav-pill-label {
            display: inline-block;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
            vertical-align: middle;
            margin-right: 0;
            transition: max-width 260ms ease, opacity 200ms ease, margin-right 260ms ease;
        }
        .cg-nav-pill:hover .cg-nav-pill-label,
        .cg-nav-pill:focus .cg-nav-pill-label,
        .cg-nav-pill:focus-visible .cg-nav-pill-label {
            max-width: 160px;
            opacity: 1;
            margin-right: 6px;
        }
        {{-- The mobile trigger is the only <button> using .cg-nav-pill (desktop pills are <a> tags), so this selector can't touch desktop nav. --}}
        button.cg-nav-pill:focus {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
            border-radius: var(--radius-pill);
            box-shadow: none;
        }
        .cg-mobile-pill-item {
            display: inline-flex;
            width: auto;
            margin: 4px auto;
            align-self: center;
            padding: 8px 24px;
            border-radius: var(--radius-pill);
            text-align: center;
            font-family: var(--font-body);
            font-size: 0.875rem;
            transition: all 150ms ease;
            background: var(--surface-raised);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .cg-mobile-pill-item:not(.cg-mobile-pill-item--active):hover {
            background: var(--border);
            color: var(--text);
        }
        .cg-mobile-pill-item--active {
            background: var(--accent);
            color: var(--bg);
            font-weight: 600;
            border: none;
            padding: 10px 28px;
            font-size: 0.9375rem;
        }
    </style>
</head>
<body class="{{ implode(' ', $cgBodyClasses) }}" style="{{ $bodyBg }}color:var(--text);font-family:'Source Sans 3',system-ui,sans-serif;">
<script>if(document.body.classList.contains('cg-larger-text')){document.documentElement.style.fontSize='17px';}</script>

@if ($isGlassRoute)
    {{-- Glass UI (Phase 4): forest photo replaces the solid $bodyBg fill on
         glass routes. Fixed + full-viewport, so it sits behind the header and
         main content regardless of scroll — those become glass panels (see
         below) that let it show through rather than a masked/hidden strip. --}}
    <div aria-hidden="true" style="position:fixed;inset:-20px;z-index:0;background-image:url('{{ asset($glassTheme['image'] ?? 'images/forest-path.jpg') }}');background-size:cover;background-position:center 25%;filter:blur(var(--bg-photo-blur)) brightness(var(--bg-photo-brightness));"></div>
@endif

@if ($isAuthPage)

    {{-- ── Auth shell ─────────────────────────────────────────────────────── --}}
    <div class="cg-app-shell min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="relative w-full max-w-md">
            <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-10">
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold bg-accent/15 text-accent">BETA</span>
            </div>
            <x-card padding="p-8" class="w-full shadow-2xl">
                {{ $slot }}
            </x-card>
        </div>
    </div>

@else

    {{--
        Phase 1 note: $pillNavItems is a NEW, separate array for the Layer 2 oval
        nav pills — deliberately not merged into $navItems above, which must stay
        exactly as-is. "Explore" reuses the feed route but has no active-match
        patterns, so it never highlights (Home owns the active state on /feed).
    --}}
    @php
        $pillNavItems = [];
        if (auth()->check() && !$isAuthPage) {
            $pillNavItems = [
                ['href' => route('feed'),             'patterns' => ['feed', 'home'],          'label' => 'Home',        'icon' => 'nav-icon-home.png'],
                ['href' => route('explore'),          'patterns' => ['explore'],               'label' => 'Explore',     'icon' => 'nav-icon-explore.png'],
                ['href' => route('friends.index'),    'patterns' => ['friends.*'],             'label' => 'Connections', 'icon' => 'nav-icon-connections.png'],
                ['href' => route('messages.index'),   'patterns' => ['messages.*', 'room.*'],  'label' => 'Messages',    'icon' => 'nav-icon-messages.png'],
                ['href' => route('tags.select'),      'patterns' => ['tags.*'],                'label' => 'Interests',   'icon' => 'nav-icon-interests.png'],
            ];
        }

        // Glass UI (Phase 4): on glass routes, the header becomes a light-tier
        // glass panel (translucent scrim + blur + border) instead of its
        // solid surface fill, so the fixed photo above shows through it.
        // Every other route keeps this exact original class/style string.
        $headerClass = 'relative h-16 flex-none flex items-center justify-between px-4 sm:px-6 z-30'
            . ($isGlassRoute ? ' cg-glass-panel cg-glass-panel--light' : '');
        $headerStyle = $isGlassRoute
            ? ''
            : 'background:var(--surface);border-bottom:1px solid var(--border);box-shadow:0 1px 0 rgba(0,0,0,0.2);';
    @endphp

    {{-- ── App shell: header + 3-column body ──────────────────────────────── --}}
    <div class="cg-app-shell flex flex-col h-full" x-data="{ navOpen: false }">

        {{-- ── Top header: identity bar (Layer 1) + oval nav pills (Layer 2) ─── --}}
        <header class="{{ $headerClass }}" style="{{ $headerStyle }}">
            <div class="flex items-center gap-3">
                {{-- Hamburger — mobile only --}}
                @auth
                    <button
                        type="button"
                        @click="navOpen = true"
                        class="md:hidden flex items-center justify-center w-11 h-11 rounded-lg transition -ml-2"
                        style="color:var(--text-muted);"
                        aria-label="Open navigation"
                        :aria-expanded="navOpen.toString()"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                @endauth
                <a href="{{ route('feed') }}" wire:navigate class="flex items-center gap-2">
                    <span class="tracking-tight font-display" style="color:var(--text);font-size:1.5rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px var(--text);">Grove</span></span>
                    <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-9 w-auto -ml-6">
                </a>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold -ml-4 bg-accent/15 text-accent">BETA</span>
                @if ($isBirthday && !$isAuthPage)
                    <span class="text-xs hidden sm:inline" style="color:#C4A0D4;">· Happy birthday!</span>
                @endif
            </div>

            {{-- ── Layer 2: oval nav pills, straddling the identity bar's bottom divider ── --}}
            @auth
            <nav aria-label="Primary">
                {{--
                    Desktop: full 5-pill row. Absolutely positioned/centered
                    independent of the logo (left) and avatar (right) flex
                    groups, so it doesn't reserve space from them — at high
                    zoom or a narrow "desktop" width it can run out of room
                    before those groups do. max-width + overflow-x lets it
                    scroll internally instead of overlapping either side.
                --}}
                <div class="hidden md:flex items-center gap-2 absolute left-1/2 bottom-0 overflow-x-auto" style="transform:translate(-50%, calc(50% - 8px)); z-index:40; max-width:min(90vw, 640px); padding:8px 0;">
                    @foreach ($pillNavItems as $item)
                        @php $pillActive = collect($item['patterns'])->contains(fn ($p) => request()->routeIs($p)); @endphp
                        <a
                            href="{{ $item['href'] }}"
                            wire:navigate
                            class="cg-nav-pill {{ $pillActive ? 'cg-nav-pill--active' : '' }}"
                            aria-label="{{ $item['label'] }}"
                            @if ($pillActive) aria-current="page" @endif
                        ><span aria-hidden="true" class="cg-nav-pill-label">{{ $item['label'] }}</span><span
                                aria-hidden="true"
                                style="display:inline-block;flex-shrink:0;width:1.9em;height:2.6em;margin-left:2px;background-color:currentColor;-webkit-mask-image:url('{{ asset('images/' . $item['icon']) }}');mask-image:url('{{ asset('images/' . $item['icon']) }}');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"
                            ></span></a>
                    @endforeach
                </div>

                {{-- Mobile: single active pill, expands to a full-width list on tap --}}
                <div
                    class="md:hidden absolute left-1/2 bottom-0"
                    style="transform:translate(-50%, 50%); z-index:40;"
                    x-data="{ pillOpen: false }"
                    @keydown.escape.window="pillOpen = false"
                    @click.outside="pillOpen = false"
                >
                    @php
                        $activePill = collect($pillNavItems)->first(
                            fn ($item) => collect($item['patterns'])->contains(fn ($p) => request()->routeIs($p))
                        ) ?? ($pillNavItems[0] ?? null);
                    @endphp
                    @if ($activePill)
                        <button
                            type="button"
                            @click="pillOpen = !pillOpen"
                            :aria-expanded="pillOpen.toString()"
                            aria-haspopup="true"
                            class="cg-nav-pill cg-nav-pill--active inline-flex items-center gap-1.5"
                            style="min-height:44px;"
                        >
                            {{ $activePill['label'] }}
                            <span
                                aria-hidden="true"
                                style="display:inline-block;flex-shrink:0;width:1.9em;height:2.6em;background-color:currentColor;-webkit-mask-image:url('{{ asset('images/' . $activePill['icon']) }}');mask-image:url('{{ asset('images/' . $activePill['icon']) }}');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"
                            ></span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" :style="pillOpen ? 'transform:rotate(180deg);transition:transform 150ms ease;' : 'transition:transform 150ms ease;'"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>

                        {{-- Scrim --}}
                        <div
                            x-show="pillOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @click="pillOpen = false"
                            class="fixed inset-0"
                            style="display:none;background:rgba(0,0,0,0.4);z-index:35;"
                            aria-hidden="true"
                        ></div>

                        {{-- Expanded destination list — full-width, unfolds below the identity bar --}}
                        <div
                            x-show="pillOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="fixed top-16 left-1/2 -translate-x-1/2 flex flex-col"
                            style="display:none;min-width:200px;max-width:280px;background:transparent;border:none;border-radius:var(--radius-lg);box-shadow:none;padding:0;margin-top:8px;overflow:hidden;z-index:40;align-items:center;gap:6px;"
                        >
                            @foreach ($pillNavItems as $item)
                                @php $itemActive = collect($item['patterns'])->contains(fn ($p) => request()->routeIs($p)); @endphp
                                <a
                                    href="{{ $item['href'] }}"
                                    wire:navigate
                                    @click="pillOpen = false"
                                    class="cg-mobile-pill-item {{ $itemActive ? 'cg-mobile-pill-item--active' : '' }}"
                                    @if ($itemActive) aria-current="page" @endif
                                >{{ $item['label'] }}<span
                                        aria-hidden="true"
                                        style="display:inline-block;flex-shrink:0;width:1.9em;height:2.6em;margin-left:2px;vertical-align:middle;background-color:currentColor;-webkit-mask-image:url('{{ asset('images/' . $item['icon']) }}');mask-image:url('{{ asset('images/' . $item['icon']) }}');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"
                                    ></span></a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </nav>
            @endauth

            <div class="flex items-center gap-3">
                {{-- Light/dark toggle hidden pending a different feature — logic left intact in partials.theme-toggle. --}}
                {{-- @include('partials.theme-toggle') --}}
            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        type="button"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        class="flex items-center gap-2 transition"
                        style="color:var(--text-muted);"
                        onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                    >
                        <x-avatar :user="auth()->user()" size="sm" />
                        <span class="hidden sm:block text-xs">{{ auth()->user()->gamertag }}</span>
                        <svg x-show="!open" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        <svg x-show="open" style="display:none;" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
                    </button>

                    <div
                        x-show="open"
                        class="account-dropdown fixed right-6 w-44 rounded-xl py-1"
                        style="display:none;top:60px;border:1px solid var(--border);background:var(--surface-raised);box-shadow:0 8px 24px rgba(0,0,0,0.5);z-index:9999;"
                    >
                        <a
                            href="{{ route('profile.show', auth()->user()->gamertag) }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Profile</a>
                        <a
                            href="{{ route('profile.settings') }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Settings</a>
                        <a
                            href="{{ route('guidelines') }}"
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Community Guidelines</a>
                        <a
                            href="{{ route('report') }}"
                            wire:navigate
                            @click="open = false"
                            class="flex items-center px-4 py-2.5 text-sm transition"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Report a problem</a>
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
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Supporter</a>
                        <div class="my-1 border-t" style="border-color:var(--border);"></div>
                        <a
                            href="{{ route('logout.get') }}"
                            class="flex items-center w-full px-4 py-2.5 text-sm transition text-left"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--danger)';this.style.background='rgba(var(--danger-rgb),0.08)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.background=''"
                        >Sign out</a>
                    </div>
                </div>
            @endauth
            </div>
        </header>

        {{-- ── Guest join banner ──────────────────────────────────────────── --}}
        {{-- @guest ensures this is never evaluated for authenticated users.   --}}
        {{-- Placed inside the app-shell @else block so it never touches auth  --}}
        {{-- pages (login / register / etc).                                   --}}
        @guest
            <div class="cg-guest-banner flex-none border-b" style="background:var(--surface);border-color:var(--border);" role="banner" aria-label="Join CommonGrove">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-6 px-4 sm:px-6 py-2.5">
                    <p class="text-xs sm:text-sm text-center sm:text-left flex-1" style="color:var(--text-muted);line-height:1.5;">
                        @if (request()->routeIs('room.show'))
                            Join the conversation in this room.
                        @else
                            Join the conversations &mdash; create your free account
                        @endif
                    </p>
                    <div class="flex items-center gap-2 flex-none">
                        <x-button :href="route('login')" wire:navigate variant="secondary" class="!px-3 !py-1.5 !text-xs">Log In</x-button>
                        <x-button :href="route('register')" wire:navigate variant="primary" class="!px-3.5 !py-1.5 !text-xs">Join the Conversation</x-button>
                    </div>
                </div>
            </div>
        @endguest

        {{-- ── Email verification banner ──────────────────────────────────── --}}
        @auth
            @if(! auth()->user()->hasVerifiedEmail())
                <livewire:auth.email-verification-banner />
            @endif
        @endauth

        {{-- ── Holiday atmosphere banner ───────────────────────────────────── --}}
        @if ($showHolidayDecorations && !$isAuthPage && ($holiday['banner']['show'] ?? true))
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

        {{-- ── Main content (Layer 3) ───────────────────────────────────────── --}}
        <div class="flex flex-1 overflow-hidden">

            {{-- Phase 1: left discovery sidebar retired from the desktop shell — kept disabled (not deleted) below for Phase 2 restoration. Disabled via @if(false) rather than a comment, since the block contains inline comments of its own that a wrapping comment can't safely contain. The old right-side nav rail is NOT kept: its content was fully redistributed — primary destinations now live in the Layer 2 pill row above, and its Support-section links (Guidelines/Report/Support CommonGrove) now live in the user avatar dropdown. --}}
            @if (false)
            <aside class="cg-discovery-sidebar hidden md:flex md:flex-col w-52 flex-none border-r overflow-y-auto" style="background:var(--surface);border-color:var(--border);">

                {{-- Rotating tagline --}}
                <div
                    x-data="tagline({{ $isLowStim ? 'true' : 'false' }}, {{ $tonePackPhrases ? \Illuminate\Support\Js::from($tonePackPhrases) : 'null' }})"
                    class="cg-tagline-wrap px-4 pt-5 pb-4 border-b flex-none"
                    style="border-color:var(--border);"
                >
                    <p
                        x-text="phrases[idx]"
                        :style="{ opacity: visible ? '1' : '0', transition: 'opacity 0.6s ease' }"
                        class="cg-tagline text-xs leading-relaxed"
                        style="color:var(--text-muted);min-height:2.5rem;"
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
                        <div class="flex-none px-4 py-2 border-t" style="border-color:var(--border);">
                            <span class="text-xs px-2 py-0.5 rounded font-medium"
                                style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.25);">
                                Admin Mode Active
                            </span>
                        </div>
                    @endif
                @endauth

            </aside>
            @endif

            {{-- ── Main content ─────────────────────────────────────────────── --}}
            <main class="flex-1 min-w-0 overflow-y-auto">
                {{ $slot }}
            </main>

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
                style="display:none;background:var(--surface);border-right:1px solid var(--border);"
                role="dialog" aria-modal="true" aria-label="Navigation menu"
                :aria-hidden="(!navOpen).toString()"
            >
                {{-- Drawer header --}}
                <div class="flex items-center justify-between px-5 py-4 flex-none border-b" style="border-color:var(--border);">
                    <div class="flex items-center gap-2">
                        <span class="tracking-tight font-display" style="color:var(--text);font-size:1.5rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px var(--text);">Grove</span></span>
                        <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-9 w-auto -ml-6">
                        <span class="text-xs px-1.5 py-0.5 rounded font-semibold bg-accent/15 text-accent">BETA</span>
                    </div>
                    <button
                        type="button"
                        @click="navOpen = false"
                        class="w-11 h-11 flex items-center justify-center rounded-lg transition"
                        style="color:var(--text-muted);"
                        aria-label="Close menu"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                {{--
                    Phase 1: the drawer's old "Navigate" section (primary
                    destinations) was removed — those now live in the Layer 2
                    pill row / its mobile expand-list. This drawer covers
                    secondary items only (Community, Admin, sign out).
                --}}
                <div class="flex-1 overflow-y-auto px-3 py-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-widest px-3 pb-3" style="color:var(--text-faint);">Community</p>
                        <div class="space-y-0.5">
                            <a
                                href="{{ route('guidelines') }}"
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:var(--text-faint);"
                                onmouseover="this.style.color='var(--text-muted)';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='var(--text-faint)';this.style.background=''"
                            >Community Guidelines</a>
                            <a
                                href="{{ route('report') }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:var(--text-faint);"
                                onmouseover="this.style.color='var(--text-muted)';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='var(--text-faint)';this.style.background=''"
                            >Report a problem</a>
                            <a
                                href="{{ route('support') }}"
                                wire:navigate
                                @click="navOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition"
                                style="color:var(--text-faint);"
                                onmouseover="this.style.color='var(--text-muted)';this.style.background='rgba(255,255,255,0.03)'"
                                onmouseout="this.style.color='var(--text-faint)';this.style.background=''"
                            >Support CommonGrove</a>
                        </div>
                    </div>

                    @if (auth()->user()->is_admin)
                        <div class="mt-5 pt-5 border-t" style="border-color:var(--border);">
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
                <div class="flex-none px-4 py-4 border-t" style="border-color:var(--border);">
                    <div class="flex items-center justify-between gap-3">
                        <a
                            href="{{ route('profile.show', auth()->user()->gamertag) }}"
                            wire:navigate
                            @click="navOpen = false"
                            class="flex items-center gap-3 min-w-0"
                        >
                            <x-avatar :user="auth()->user()" size="md" />
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate" style="color:var(--text);">{{ auth()->user()->display_name ?? auth()->user()->gamertag }}</p>
                                <p class="text-xs truncate" style="color:var(--text-muted);">{{ auth()->user()->gamertag }}</p>
                            </div>
                        </a>
                        <a
                            href="{{ route('logout.get') }}"
                            class="flex-none text-xs px-2.5 py-1.5 rounded-lg transition"
                            style="color:var(--text-muted);border:1px solid var(--border);"
                            onmouseover="this.style.color='var(--danger)';this.style.borderColor='rgba(var(--danger-rgb),0.4)'"
                            onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)'"
                        >Sign out</a>
                    </div>
                </div>
            </div>
        @endauth

        {{-- ── Active room dock (Phase 5) ──────────────────────────────────── --}}
        @auth
            <livewire:rooms.active-room-dock />
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
            "Take your time. There's no pressure.",
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
