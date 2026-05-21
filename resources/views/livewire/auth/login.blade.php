<div>
    <div class="flex justify-center mb-6">
        <div style="border-radius:18px;overflow:hidden;box-shadow:inset 0 0 55px 22px #161B22;">
            <img src="{{ asset('images/logo-full2.png') }}" alt="CommonGrove" style="height:140px;width:auto;display:block;filter:drop-shadow(0 0 18px rgba(29,158,117,0.45)) drop-shadow(0 0 40px rgba(29,158,117,0.18));">
        </div>
    </div>

    <form wire:submit="submit" class="space-y-5">

        <div>
            <label for="login" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                Gamertag or email
            </label>
            <input
                id="login"
                type="text"
                wire:model="login"
                autocomplete="username"
                autofocus
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('login') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            @error('login')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium" style="color:#8B949E;">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm underline transition" style="color:#1D9E75;">Need help getting back in?</a>
            </div>
            <input
                id="password"
                type="password"
                wire:model="password"
                autocomplete="current-password"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('password') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            @error('password')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input
                id="remember"
                type="checkbox"
                wire:model="remember"
                class="w-4 h-4 rounded"
                style="background:#1C2333;border-color:#30363D;accent-color:#1D9E75;"
            >
            <label for="remember" class="text-sm" style="color:#8B949E;">Remember me</label>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;"
            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove>Sign in</span>
            <span wire:loading>Signing in…</span>
        </button>

    </form>

    <p class="mt-6 text-center text-sm" style="color:#8B949E;">
        Don't have an account?
        <a href="{{ route('register') }}" class="underline transition" style="color:#1D9E75;">Create one</a>
    </p>
</div>
