@php
    $bodyBg = 'background:radial-gradient(ellipse 80% 50% at 50% 100%, rgba(29,158,117,0.055) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 75% 0%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — CommonGrove</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="{{ $bodyBg }}color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;min-height:100vh;">

    <header class="sticky top-0 z-50 flex items-center justify-between px-6 h-14 border-b" style="background:#161B22;border-color:#30363D;">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="font-bold text-sm tracking-tight transition" style="color:#E6EDF3;" onmouseover="this.style.color='#1D9E75'" onmouseout="this.style.color='#E6EDF3'">CommonGrove</a>
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
        </div>
        <nav class="flex items-center gap-5">
            <a href="{{ route('privacy') }}" class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Privacy</a>
            <a href="{{ route('terms') }}" class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Terms</a>
            <a href="{{ route('guidelines') }}" class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Guidelines</a>
        </nav>
    </header>

    <main class="py-14 px-6">
        <div class="max-w-2xl mx-auto">
            @yield('content')
        </div>
    </main>

    <footer class="border-t py-8 px-6" style="border-color:#21262D;">
        <div class="max-w-2xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <p class="text-xs" style="color:#3d4451;">&copy; {{ date('Y') }} CommonGrove · Coldev Enterprises</p>
            <nav class="flex gap-5">
                <a href="{{ route('privacy') }}" class="text-xs transition" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Privacy</a>
                <a href="{{ route('terms') }}" class="text-xs transition" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Terms</a>
                <a href="{{ route('guidelines') }}" class="text-xs transition" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Community Guidelines</a>
                <a href="{{ route('report') }}" class="text-xs transition" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Report a problem</a>
            </nav>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
