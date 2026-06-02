@php
    $isLowStim = auth()->check() && auth()->user()->low_stimulation_mode;
    $bodyBg    = $isLowStim
        ? 'background:#0D1117;'
        : 'background:radial-gradient(ellipse 80% 50% at 50% 100%, rgba(29,158,117,0.055) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 75% 0%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} — CommonGrove</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon2.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="h-full flex antialiased"
    style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;"
    x-data="{ navOpen: false }"
>

    {{-- Mobile overlay — sits above content, below the drawer --}}
    <div
        x-show="navOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="navOpen = false"
        class="md:hidden fixed inset-0 z-40"
        style="background:rgba(0,0,0,0.6);display:none;"
    ></div>

    {{-- Admin sidebar
         Mobile : fixed drawer, slides in/out with translate
         Desktop: static flex column (md:relative resets the fixed positioning) --}}
    <nav
        class="fixed inset-y-0 left-0 z-50 w-56 flex-none flex flex-col border-r
               transition-transform duration-200 ease-in-out
               md:relative md:translate-x-0"
        :class="navOpen ? 'translate-x-0' : '-translate-x-full'"
        style="background:#161B22;border-color:#30363D;"
    >
        {{-- Sidebar header --}}
        <div class="px-4 py-5 border-b flex-none" style="border-color:#30363D;">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="tracking-tight" style="color:#E6EDF3;font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px #E6EDF3;">Grove</span></span>
                    <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-9 w-auto -ml-6">
                </div>
                {{-- Close button — mobile only --}}
                <button
                    type="button"
                    @click="navOpen = false"
                    class="md:hidden p-1 rounded transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(210,153,34,0.2);color:#D29922;">ADMIN</span>
            </div>
        </div>

        {{-- Nav links --}}
        <div class="flex-1 py-4 space-y-0.5 px-2 overflow-y-auto">
            @php
                $links = [
                    ['route' => 'admin.dashboard',       'icon' => '📊', 'label' => 'Dashboard'],
                    ['route' => 'admin.reports',          'icon' => '🚩', 'label' => 'Reports Queue'],
                    ['route' => 'admin.problem-reports',  'icon' => '📋', 'label' => 'Problem Reports'],
                    ['route' => 'admin.users',            'icon' => '👥', 'label' => 'User Management'],
                    ['route' => 'admin.rooms',            'icon' => '🏠', 'label' => 'All Rooms'],
                    ['route' => 'admin.tags',             'icon' => '🏷️', 'label' => 'Tag Moderation'],
                    ['route' => 'admin.stats',            'icon' => '📈', 'label' => 'Platform Stats'],
                    ['route' => 'admin.crisis',           'icon' => '🆘', 'label' => 'Crisis Log'],
                ];
            @endphp
            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    wire:navigate
                    @click="navOpen = false"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition"
                    @style([
                        'background:#21262D;color:#E6EDF3;' => request()->routeIs($link['route']),
                        'color:#8B949E;'                    => ! request()->routeIs($link['route']),
                    ])
                    onmouseover="if(!this.classList.contains('active-nav'))this.style.background='#21262D'"
                    onmouseout="if(!{{ request()->routeIs($link['route']) ? 'true' : 'false' }})this.style.background=''"
                >
                    <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="px-4 py-4 border-t flex-none" style="border-color:#30363D;">
            <a
                href="{{ route('feed') }}"
                wire:navigate
                @click="navOpen = false"
                class="text-xs transition"
                style="color:#8B949E;"
                onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
            >← Back to app</a>
        </div>
    </nav>

    {{-- Main content area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Mobile top bar — hidden on desktop --}}
        <div class="md:hidden flex items-center gap-3 px-4 py-3 border-b flex-none" style="background:#161B22;border-color:#30363D;">
            <button
                type="button"
                @click="navOpen = true"
                class="p-1.5 rounded-lg transition flex-none"
                style="color:#8B949E;"
                onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                aria-label="Open navigation"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="6"  x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="flex items-center gap-1">
                <span class="tracking-tight" style="color:#E6EDF3;font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.5px #E6EDF3;">Grove</span></span>
                <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-7 w-auto -ml-4">
            </div>
            <div class="ml-auto flex items-center gap-1.5">
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(210,153,34,0.2);color:#D29922;">ADMIN</span>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
