<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CommonGrove | A quiet place for introverts and gamers</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex flex-col" style="background:#0D1117;color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;">

    {{-- Nav --}}
    <header class="border-b px-6 py-4 flex items-center justify-between" style="border-color:#30363D;background:#161B22;">
        <div class="flex items-center gap-2.5">
            <span class="font-bold text-lg" style="color:#E6EDF3;">CommonGrove</span>
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">BETA</span>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('feed') }}" class="text-sm font-semibold px-4 py-2 rounded-lg transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
                    Go to feed →
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Sign in</a>
                <a href="{{ route('register') }}" class="text-sm font-semibold px-4 py-2 rounded-lg transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
                    Join free
                </a>
            @endauth
        </div>
    </header>

    {{-- Hero --}}
    <main class="flex-1">
        <section class="max-w-4xl mx-auto px-6 py-24 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-6 leading-tight" style="color:#E6EDF3;">
                A quieter corner of the internet,<br>
                <span style="color:#1D9E75;">just for gamers and introverts.</span>
            </h1>
            <p class="text-lg mb-10 max-w-xl mx-auto" style="color:#8B949E;">
                No ads. No bots. No fake engagement. CommonGrove is a closed, judgment-free space to find your people.
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-block text-base font-semibold px-8 py-3.5 rounded-xl transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
                    Create your account — it's free
                </a>
                <p class="mt-4 text-sm" style="color:#8B949E;">
                    Already a member? <a href="{{ route('login') }}" class="underline transition" style="color:#1D9E75;">Sign in</a>
                </p>
            @endguest
        </section>

        {{-- Feature grid --}}
        <section class="max-w-5xl mx-auto px-6 pb-24 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['icon' => '🎮', 'title' => 'Interest-based matching',       'desc' => 'Tag what you love. Find people who get it.'],
                    ['icon' => '💬', 'title' => 'Private messaging',             'desc' => 'DMs and group rooms — no public timeline, no noise.'],
                    ['icon' => '🕐', 'title' => 'Hangout posts',                 'desc' => 'Short-lived posts that expire after 6 hours. No clutter.'],
                    ['icon' => '🔒', 'title' => 'Strictly private',              'desc' => 'Nothing is visible to people outside the platform.'],
                    ['icon' => '🤝', 'title' => 'Friendship milestones',         'desc' => 'Celebrate 7, 30, and 90-day friendships.'],
                    ['icon' => '🆘', 'title' => 'Built-in crisis support',       'desc' => 'Sensitive keyword detection with discreet support resources.'],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="rounded-xl border p-6" style="background:#161B22;border-color:#30363D;">
                    <div class="text-2xl mb-3">{{ $f['icon'] }}</div>
                    <h3 class="font-semibold text-sm mb-1.5" style="color:#E6EDF3;">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed" style="color:#8B949E;">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </section>
    </main>

    {{-- Footer --}}
    <footer class="border-t px-6 py-6 text-center text-xs" style="border-color:#30363D;color:#8B949E;">
        <p>© {{ date('Y') }} CommonGrove · Coldev Enterprises · Logo by <a href="https://atccreative.com/" target="_blank" rel="noopener" class="underline">AC Creative</a> · Platonic friendships only · <a href="#" class="underline">Privacy</a> · <a href="#" class="underline">Terms</a></p>
    </footer>

</body>
</html>
