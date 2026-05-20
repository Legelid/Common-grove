<x-layouts.app title="Verify your email — CommonGrove">
    <div class="text-center">
        <div class="text-4xl mb-4">✉️</div>
        <h1 class="text-2xl font-bold mb-3" style="color:#E6EDF3;">Check your email</h1>
        <p class="text-sm mb-6" style="color:#8B949E;">
            We sent a verification link to your email address.<br>
            Click it to activate your account.
        </p>

        @if (session('status') === 'verification-link-sent')
            <p class="mb-4 text-sm rounded-lg px-3 py-2" style="background:rgba(29,158,117,0.15);color:#1D9E75;">
                A fresh verification link has been sent to your email address.
            </p>
        @endif

        <form method="POST" action="/email/verification-notification">
            @csrf
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-lg transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="/logout" class="mt-4">
            @csrf
            <button type="submit" class="text-sm transition underline" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">
                Sign out
            </button>
        </form>
    </div>
</x-layouts.app>
