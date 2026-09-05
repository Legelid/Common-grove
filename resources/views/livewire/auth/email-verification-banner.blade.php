<div class="flex-none border-b" style="background:rgba(194,140,12,0.09);border-color:rgba(194,140,12,0.2);" role="status" aria-live="polite">
    <div class="flex items-center justify-between gap-4 px-5 py-2.5">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <span class="text-xs leading-relaxed" style="color:#C9A83C;">
                Verify your email to unlock messaging, rooms, and hangouts.
            </span>
            @if ($message)
                <span class="text-xs flex-none" style="color:{{ $rateLimited ? 'var(--danger)' : 'var(--accent)' }};">{{ $message }}</span>
            @endif
        </div>
        <div class="flex items-center gap-3 flex-none">
            <button
                type="button"
                wire:click="resend"
                wire:loading.attr="disabled"
                class="text-xs font-medium px-3 py-1.5 rounded-lg transition disabled:opacity-50"
                style="background:rgba(194,140,12,0.18);color:#C9A83C;border:1px solid rgba(194,140,12,0.30);"
                onmouseover="this.style.background='rgba(194,140,12,0.28)'" onmouseout="this.style.background='rgba(194,140,12,0.18)'"
            >
                <span wire:loading.remove>Resend email</span>
                <span wire:loading>Sending…</span>
            </button>
            <a
                href="{{ url()->current() }}"
                class="text-xs transition hidden sm:inline"
                style="color:var(--text-muted);"
                onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
            >I verified — refresh</a>
        </div>
    </div>
</div>
