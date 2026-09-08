<x-layouts.app title="Unsubscribed | CommonGrove">
    <h1 class="text-2xl font-bold text-center mb-2" style="color:var(--text);">You're unsubscribed.</h1>
    <p class="text-sm text-center mb-6" style="color:var(--text-muted);">
        You won't get emails like this from CommonGrove again. You'll still get anything tied to your account itself, like password resets.
    </p>

    <p class="text-center text-sm">
        <a href="{{ route('home') }}" class="underline transition text-accent">Back to CommonGrove</a>
    </p>
</x-layouts.app>
