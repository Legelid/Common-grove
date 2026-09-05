<div>
    <div class="flex items-center justify-center gap-2 mb-6" style="padding-left:32px;">
        <span class="tracking-tight font-display" style="color:var(--text);font-size:2.25rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px var(--text);">Grove</span></span>
        <img src="{{ asset('images/logo-icon.png') }}" alt="" style="height:54px;width:auto;" class="-ml-9">
    </div>

    <form wire:submit="submit" class="space-y-5">

        <div>
            <label for="login" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                Gamertag or email
            </label>
            <x-input
                id="login"
                type="text"
                wire:model="login"
                autocomplete="username"
                autofocus
                :error="$errors->first('login')"
            />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium" style="color:var(--text-muted);">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm underline transition text-accent">Need help getting back in?</a>
            </div>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="current-password"
                    class="w-full rounded-btn px-4 py-2.5 pr-10 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('password') ? 'var(--danger)' : 'var(--border)' }};"
                >
                <button type="button" @click="show = !show" tabindex="-1" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-3 flex items-center transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm" style="color:var(--danger);">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input
                id="remember"
                type="checkbox"
                wire:model.live="remember"
                class="w-4 h-4 rounded"
                style="background:var(--surface);border-color:var(--border);accent-color:var(--accent);"
            >
            <label for="remember" class="text-sm" style="color:var(--text-muted);">Remember me</label>
        </div>

        <x-button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove>Sign in</span>
            <span wire:loading>Signing in…</span>
        </x-button>

    </form>

    <p class="mt-6 text-center text-sm" style="color:var(--text-muted);">
        Don't have an account?
        <a href="{{ route('register') }}" class="underline transition text-accent">Create one</a>
    </p>
</div>
