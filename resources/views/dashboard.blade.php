<x-layouts.app title="Dashboard — CommonGrove">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <h1 class="text-3xl font-bold mb-2" style="color:#E6EDF3;">
            Welcome, {{ Auth::user()->gamertag }}
        </h1>
        <p class="mb-8" style="color:#8B949E;">
            You're in. Head to the feed to get started.
        </p>
        <a href="{{ route('feed') }}" class="text-sm font-semibold px-6 py-3 rounded-xl transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
            Go to feed →
        </a>
    </div>
</x-layouts.app>
