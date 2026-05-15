<div>
    <h1 class="text-2xl font-bold text-center mb-8" style="color:#E6EDF3;">Create your account</h1>

    <form wire:submit="register" class="space-y-5">

        {{-- Gamertag --}}
        <div>
            <label for="gamertag" class="block text-sm font-medium mb-1" style="color:#8B949E;">Gamertag</label>
            <div class="relative">
                <input
                    id="gamertag"
                    type="text"
                    wire:model.live.debounce.400ms="gamertag"
                    autocomplete="username"
                    maxlength="20"
                    placeholder="YourGamertag"
                    class="w-full rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('gamertag') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @if ($gamertagStatus === 'available')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:#1D9E75;">✓</span>
                @elseif ($gamertagStatus === 'taken')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:#E24B4A;">✗</span>
                @endif
            </div>
            @error('gamertag')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs" style="color:#8B949E;">3–20 characters · starts with a letter · letters, numbers, _ and - only</p>

            @if ($gamertagStatus === 'taken')
                <div class="mt-2">
                    <p class="text-sm mb-1" style="color:#E24B4A;">That gamertag is taken. Try one of these:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($suggestions as $suggestion)
                            <button
                                type="button"
                                wire:click="useSuggestion('{{ $suggestion }}')"
                                class="px-3 py-1 text-sm rounded-full transition"
                                style="background:#21262D;color:#1D9E75;"
                                onmouseover="this.style.background='#1D9E75';this.style.color='#fff'"
                                onmouseout="this.style.background='#21262D';this.style.color='#1D9E75'"
                            >{{ $suggestion }}</button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                Email address
                <span class="font-normal" style="color:#8B949E;opacity:0.7;">(for account recovery only — never shown)</span>
            </label>
            <input
                id="email"
                type="email"
                wire:model="email"
                autocomplete="email"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('email') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            @error('email')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium mb-1" style="color:#8B949E;">Password</label>
            <input
                id="password"
                type="password"
                wire:model="password"
                autocomplete="new-password"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('password') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            @error('password')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs" style="color:#8B949E;">Minimum 10 characters. Checked against known breaches.</p>
        </div>

        {{-- Confirm password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color:#8B949E;">Confirm password</label>
            <input
                id="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                autocomplete="new-password"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;"
            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove>Create account</span>
            <span wire:loading>Creating account…</span>
        </button>

    </form>

    <p class="mt-6 text-center text-sm" style="color:#8B949E;">
        Already have an account?
        <a href="{{ route('login') }}" class="underline transition" style="color:#1D9E75;" wire:navigate>Sign in</a>
    </p>
</div>
