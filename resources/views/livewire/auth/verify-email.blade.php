<div>
    <h1 class="text-2xl font-bold text-center mb-2" style="color:#E6EDF3;">Check your email</h1>

    <p class="text-center text-sm mb-6 leading-relaxed" style="color:#8B949E;">
        We sent you a verification link.<br>
        This helps keep CommonGrove safe from spam and fake accounts.
    </p>

    <div class="rounded-xl border px-4 py-3 mb-6 text-sm leading-relaxed" style="background:#0D1117;border-color:#30363D;color:#3d4451;">
        Once verified, you'll have full access to messaging, rooms, and hangouts.
    </div>

    @if ($message)
        <div class="mb-5 rounded-lg px-4 py-2.5 text-sm text-center" style="background:{{ $rateLimited ? 'rgba(226,75,74,0.08)' : 'rgba(29,158,117,0.12)' }};color:{{ $rateLimited ? '#E24B4A' : '#1D9E75' }};">
            {{ $message }}
        </div>
    @endif

    <button
        type="button"
        wire:click="resend"
        wire:loading.attr="disabled"
        class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition disabled:opacity-50"
        style="background:#1D9E75;color:#fff;"
        onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
    >
        <span wire:loading.remove>Resend verification email</span>
        <span wire:loading>Sending…</span>
    </button>

    <form method="POST" action="/logout" class="mt-5 text-center">
        @csrf
        <button
            type="submit"
            class="text-xs underline transition"
            style="color:#3d4451;"
            onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
        >Sign out</button>
    </form>
</div>
