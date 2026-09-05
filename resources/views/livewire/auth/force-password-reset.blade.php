<div>
    <div class="flex items-center justify-center gap-2 mb-6" style="padding-left:32px;">
        <span class="tracking-tight font-display" style="color:var(--text);font-size:2.25rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px var(--text);">Grove</span></span>
        <img src="{{ asset('images/logo-icon.png') }}" alt="" style="height:54px;width:auto;" class="-ml-9">
    </div>

    <div class="mb-6 px-4 py-3.5 rounded-xl bg-accent/10" style="border:1px solid rgb(var(--accent-rgb) / 0.25);">
        <p class="text-sm font-medium mb-1 text-accent">Welcome back!</p>
        <p class="text-sm" style="color:var(--text-muted);">For your security we need you to set a new password before continuing.</p>
    </div>

    <form wire:submit="save" class="space-y-5">

        <div>
            <label for="password" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">New password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="new-password"
                    autofocus
                    class="w-full rounded-btn px-4 py-2.5 pr-10 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('password') ? 'var(--danger)' : 'var(--border)' }};"
                >
                <button type="button" @click="show = !show" tabindex="-1" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-3 flex items-center transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            <p class="mt-1 text-xs" style="color:var(--text-faint);">Minimum 10 characters.</p>
            @error('password')
                <p class="mt-1 text-sm" style="color:var(--danger);">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Confirm new password</label>
            <x-input
                id="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                autocomplete="new-password"
            />
        </div>

        <x-button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove>Set new password</span>
            <span wire:loading>Saving…</span>
        </x-button>

    </form>
</div>
