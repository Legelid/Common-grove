<x-layouts.app title="Verify your email | CommonGrove">
    <div class="text-center">
        <div class="text-4xl mb-4">✉️</div>
        <h1 class="text-2xl font-bold mb-3" style="color:var(--text);">Check your email</h1>
        <p class="text-sm mb-6" style="color:var(--text-muted);">
            We sent a verification link to your email address.<br>
            Click it to activate your account.
        </p>

        @if (session('status') === 'verification-link-sent')
            <p class="mb-4 text-sm rounded-lg px-3 py-2 bg-accent/15 text-accent">
                A fresh verification link has been sent to your email address.
            </p>
        @endif

        <form method="POST" action="/email/verification-notification">
            @csrf
            <x-button type="submit" variant="primary" class="px-6">
                Resend verification email
            </x-button>
        </form>

        <form method="POST" action="/logout" class="mt-4">
            @csrf
            <button type="submit" class="text-sm transition underline" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                Sign out
            </button>
        </form>
    </div>
</x-layouts.app>
