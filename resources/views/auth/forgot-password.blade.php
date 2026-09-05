<x-layouts.app title="Let's get you back in | CommonGrove">
    <h1 class="text-2xl font-bold text-center mb-2" style="color:var(--text);">Let's get you back in</h1>
    <p class="text-sm text-center mb-6" style="color:var(--text-muted);">
        Enter your email and we'll send you a secure reset link.
    </p>

    @if (session('status'))
        <p class="mb-4 text-sm text-center rounded-lg px-3 py-2 bg-accent/15 text-accent">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="/forgot-password" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Email address</label>
            <x-input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                :error="$errors->first('email')"
            />
        </div>

        <x-button type="submit" variant="primary" class="w-full">
            Send reset link
        </x-button>
    </form>

    <p class="mt-6 text-center text-sm" style="color:var(--text-muted);">
        <a href="{{ route('login') }}" class="underline transition text-accent">Back to sign in</a>
    </p>
</x-layouts.app>
