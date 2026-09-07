<x-layouts.app title="Confirm your account | CommonGrove">
    <h1 class="text-2xl font-bold text-center mb-2" style="color:var(--text);">Create your CommonGrove account?</h1>
    <p class="text-sm text-center mb-6" style="color:var(--text-muted);">
        We don't have an account for <strong>{{ $email }}</strong> yet. Continue to create one using this Google account.
    </p>

    <form method="POST" action="{{ route('auth.google.confirm.store') }}" class="space-y-4">
        @csrf
        <x-button type="submit" variant="primary" class="w-full">Yes, create my account</x-button>
    </form>

    <p class="mt-6 text-center text-sm" style="color:var(--text-muted);">
        <a href="{{ route('login') }}" class="underline transition text-accent">Cancel and back to sign in</a>
    </p>
</x-layouts.app>
