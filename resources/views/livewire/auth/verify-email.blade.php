<div>
    <h1 class="text-2xl font-bold text-center mb-2" style="color:var(--text);">Check your email</h1>

    <p class="text-center text-sm mb-6 leading-relaxed" style="color:var(--text-muted);">
        We sent you a verification link.<br>
        This helps keep CommonGrove safe from spam and fake accounts.
    </p>

    <div class="rounded-xl border px-4 py-3 mb-6 text-sm leading-relaxed" style="background:var(--bg);border-color:var(--border);color:var(--text-faint);">
        Once verified, you'll have full access to messaging, rooms, and hangouts.
    </div>

    <div class="flex items-center gap-2 mb-6">
        <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(154,143,126,0.12);color:var(--text-faint);">Bot Deterrent</span>
        <p class="text-xs" style="color:var(--text-faint);">Email verification keeps bots and fake accounts off the platform.</p>
    </div>

    @if ($message)
        <div class="mb-5 rounded-lg px-4 py-2.5 text-sm text-center {{ $rateLimited ? '' : 'bg-accent/15 text-accent' }}" style="{{ $rateLimited ? 'background:rgba(var(--danger-rgb),0.1);color:var(--danger);' : '' }}">
            {{ $message }}
        </div>
    @endif

    <x-button type="button" wire:click="resend" wire:loading.attr="disabled" variant="primary" class="w-full">
        <span wire:loading.remove>Resend verification email</span>
        <span wire:loading>Sending…</span>
    </x-button>

    <div class="mt-5 text-center">
        <button
            type="button"
            wire:click="logout"
            wire:loading.attr="disabled"
            class="text-xs underline transition disabled:opacity-50"
            style="color:var(--text-faint);"
            onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
        >Sign out</button>
    </div>
</div>
