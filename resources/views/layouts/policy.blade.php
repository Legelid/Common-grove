@php
    $bodyBg = 'background:radial-gradient(ellipse 80% 50% at 50% 100%, rgba(29,158,117,0.055) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 75% 0%, rgba(8,32,58,0.20) 0%, transparent 55%), #0D1117;';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | CommonGrove</title>
    @include('partials.fonts')
    @include('partials.theme-init')
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="{{ $bodyBg }}color:var(--text);font-family:'Source Sans 3',system-ui,sans-serif;min-height:100vh;">

    <header class="sticky top-0 z-50 flex items-center justify-between px-6 h-14 border-b" style="background:var(--surface);border-color:var(--border);">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="font-display font-bold text-sm tracking-tight transition" style="color:var(--text);" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">CommonGrove</a>
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold bg-accent/15 text-accent">BETA</span>
        </div>
        <nav class="flex items-center gap-5">
            <a href="{{ route('privacy') }}" class="text-xs transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">Privacy</a>
            <a href="{{ route('terms') }}" class="text-xs transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">Terms</a>
            <a href="{{ route('guidelines') }}" class="text-xs transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">Guidelines</a>
        </nav>
    </header>

    <main class="py-14 px-6">
        <div class="max-w-2xl mx-auto">
            @yield('content')
        </div>
    </main>

    <footer class="border-t py-8 px-6" style="border-color:var(--border);">
        <div class="max-w-2xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <p class="text-xs" style="color:var(--text-faint);">&copy; {{ date('Y') }} CommonGrove · Coldev Enterprises · Logo by <a href="https://atccreative.com/" target="_blank" rel="noopener" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">AC Creative</a></p>
            <nav class="flex gap-5">
                <a href="{{ route('privacy') }}" class="text-xs transition" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">Privacy</a>
                <a href="{{ route('terms') }}" class="text-xs transition" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">Terms</a>
                <a href="{{ route('guidelines') }}" class="text-xs transition" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">Community Guidelines</a>
                <a href="{{ route('report') }}" class="text-xs transition" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">Report a problem</a>
            </nav>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
