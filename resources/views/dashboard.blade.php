<x-layouts.app title="Dashboard | CommonGrove">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <h1 class="text-3xl font-bold mb-2" style="color:var(--text);">
            Welcome, {{ Auth::user()->gamertag }}
        </h1>
        <p class="mb-8" style="color:var(--text-muted);">
            You're in. Head to the feed to get started.
        </p>
        <x-button :href="route('feed')" variant="primary" class="!px-6 !py-3" aria-label="Go to feed">
            <x-arrow-icon label="Go to feed" />
        </x-button>
    </div>
</x-layouts.app>
