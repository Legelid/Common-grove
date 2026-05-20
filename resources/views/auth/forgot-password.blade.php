<x-layouts.app title="Reset your password — CommonGrove">
    <h1 class="text-2xl font-bold text-center mb-2" style="color:#E6EDF3;">Forgot your password?</h1>
    <p class="text-sm text-center mb-6" style="color:#8B949E;">
        Enter your email and we'll send a reset link.
    </p>

    @if (session('status'))
        <p class="mb-4 text-sm text-center rounded-lg px-3 py-2" style="background:rgba(29,158,117,0.15);color:#1D9E75;">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="/forgot-password" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium mb-1" style="color:#8B949E;">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 @error('email') ring-1 @enderror"
                style="background:#1C2333;border:1px solid {{ $errors->has('email') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'"
                onblur="this.style.boxShadow=''"
            >
            @error('email')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
            Send reset link
        </button>
    </form>

    <p class="mt-6 text-center text-sm" style="color:#8B949E;">
        <a href="{{ route('login') }}" class="underline transition" style="color:#1D9E75;">Back to sign in</a>
    </p>
</x-layouts.app>
