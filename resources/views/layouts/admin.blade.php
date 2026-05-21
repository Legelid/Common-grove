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
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex antialiased" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    {{-- Admin sidebar --}}
    <nav class="w-56 flex-none flex flex-col border-r" style="background:#161B22;border-color:#30363D;">
        <div class="px-4 py-5 border-b" style="border-color:#30363D;">
            <img src="{{ asset('images/logo-full.png') }}" alt="CommonGrove" class="h-7 w-auto mb-2">
            <div class="flex items-center gap-2">
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(210,153,34,0.2);color:#D29922;">ADMIN</span>
                <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
            </div>
        </div>

        <div class="flex-1 py-4 space-y-0.5 px-2">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'icon' => '📊', 'label' => 'Dashboard'],
                    ['route' => 'admin.reports',          'icon' => '🚩', 'label' => 'Reports Queue'],
                    ['route' => 'admin.problem-reports', 'icon' => '📋', 'label' => 'Problem Reports'],
                    ['route' => 'admin.users',     'icon' => '👥', 'label' => 'User Management'],
                    ['route' => 'admin.tags',      'icon' => '🏷️', 'label' => 'Tag Moderation'],
                    ['route' => 'admin.stats',     'icon' => '📈', 'label' => 'Platform Stats'],
                    ['route' => 'admin.crisis',    'icon' => '🆘', 'label' => 'Crisis Log'],
                ];
            @endphp
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" wire:navigate
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition"
                   @style([
                       'background:#21262D;color:#E6EDF3;' => request()->routeIs($link['route']),
                       'color:#8B949E;' => !request()->routeIs($link['route']),
                   ])
                   onmouseover="if(!this.classList.contains('active-nav'))this.style.background='#21262D'"
                   onmouseout="if(!{{ request()->routeIs($link['route']) ? 'true' : 'false' }})this.style.background=''"
                >
                    <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="px-4 py-4 border-t" style="border-color:#30363D;">
            <a href="{{ route('feed') }}" wire:navigate class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">
                ← Back to app
            </a>
        </div>
    </nav>

    {{-- Main content --}}
    <main class="flex-1 overflow-y-auto">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
