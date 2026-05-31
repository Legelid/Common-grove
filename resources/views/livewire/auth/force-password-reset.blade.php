<div>
    <div class="flex items-center justify-center gap-2 mb-6" style="padding-left:32px;">
        <span class="tracking-tight" style="color:#E6EDF3;font-family:'Cormorant Garamond',serif;font-size:2.25rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px #E6EDF3;">Grove</span></span>
        <img src="{{ asset('images/logo-icon.png') }}" alt="" style="height:54px;width:auto;" class="-ml-9">
    </div>

    <div class="mb-6 px-4 py-3.5 rounded-xl" style="background:rgba(29,158,117,0.08);border:1px solid rgba(29,158,117,0.25);">
        <p class="text-sm font-medium mb-1" style="color:#1D9E75;">Welcome back!</p>
        <p class="text-sm" style="color:#8B949E;">For your security we need you to set a new password before continuing.</p>
    </div>

    <form wire:submit="save" class="space-y-5">

        <div>
            <label for="password" class="block text-sm font-medium mb-1" style="color:#8B949E;">New password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="new-password"
                    autofocus
                    class="w-full rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('password') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                <button type="button" @click="show = !show" tabindex="-1" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-3 flex items-center transition" style="color:#8B949E;" onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            <p class="mt-1 text-xs" style="color:#3d4451;">Minimum 10 characters.</p>
            @error('password')
                <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color:#8B949E;">Confirm new password</label>
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

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;"
            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove>Set new password</span>
            <span wire:loading>Saving…</span>
        </button>

    </form>
</div>
