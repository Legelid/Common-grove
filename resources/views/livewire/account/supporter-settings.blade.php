<div class="px-6 py-10 max-w-2xl mx-auto space-y-10">

    <h1 class="text-2xl font-bold" style="color:var(--text);">Supporter</h1>

    {{-- ── Flash messages from redirect ──────────────────────────────────── --}}
    @if (session('subscribed'))
        <div class="rounded-xl border px-4 py-3 text-sm bg-accent/10 text-accent" style="border-color:rgb(var(--accent-rgb) / 0.2);">
            Thank you! Your subscription is being confirmed. Supporter status will be active shortly.
        </div>
    @endif
    @if (session('subscribe_cancelled'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(154,143,126,0.07);border-color:var(--border);color:var(--text-muted);">
            No changes made — you can subscribe any time from the <a href="{{ route('support') }}" class="underline">support page</a>.
        </div>
    @endif
    @if (session('paypal_error'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(var(--danger-rgb),0.07);border-color:rgba(var(--danger-rgb),0.2);color:var(--danger);">
            {{ session('paypal_error') }}
        </div>
    @endif

    {{-- ── Current status ──────────────────────────────────────────────────── --}}
    <x-card padding="p-6" class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Status</h2>

        @if (auth()->user()->is_supporter)
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-accent/10 text-accent" style="border:1px solid rgb(var(--accent-rgb) / 0.25);">
                    <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 14s-6-4.35-6-8A3.5 3.5 0 0 1 8 3.55 3.5 3.5 0 0 1 14 6c0 3.65-6 8-6 8z"/></svg>
                    CommonGrove Supporter
                </span>
            </div>

            @if ($subscription)
                <div class="text-xs space-y-1" style="color:var(--text-muted);">
                    @if ($subscription->started_at)
                        <p>Supporting since {{ $subscription->started_at->format('M j, Y') }}</p>
                    @endif
                    @if ($subscription->status === 'payment_failed')
                        <p style="color:var(--danger);">Last payment failed — please update your payment method via PayPal.</p>
                    @elseif ($subscription->status === 'suspended')
                        <p style="color:#D29922;">Subscription suspended — please check your PayPal account.</p>
                    @endif
                </div>
            @endif
        @else
            <p class="text-sm" style="color:var(--text-muted);">You're using the free plan.</p>
            <x-button :href="route('support')" variant="primary">Learn about supporting CommonGrove</x-button>
        @endif
    </x-card>

    {{-- ── Supporter icon toggle (supporters only) ─────────────────────────── --}}
    @if (auth()->user()->is_supporter)
        <x-card padding="p-6" class="space-y-4">
            <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Supporter Icon</h2>

            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show supporter icon next to username</p>
                    <p class="text-xs mt-0.5" style="color:var(--text-muted);">Shows a small ♥ with the tooltip "I believe in CommonGrove".</p>
                </div>
                <x-toggle :checked="$showSupporterIcon" wire:click="$toggle('showSupporterIcon')" class="flex-none ml-4" />
            </div>

            @if ($iconMessage)
                <p class="text-sm text-accent">{{ $iconMessage }}</p>
            @endif

            <x-button type="button" wire:click="saveIconPref" variant="primary" class="px-5 py-2">Save</x-button>
        </x-card>

        {{-- ── Manage / cancel subscription ──────────────────────────────────── --}}
        <x-card padding="p-6" class="space-y-4">
            <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Manage Subscription</h2>

            <p class="text-sm" style="color:var(--text-muted);">
                Your subscription is billed monthly through PayPal. You can cancel at any time — it takes effect immediately.
            </p>

            @if ($cancelMessage)
                <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(154,143,126,0.07);border:1px solid var(--border);color:var(--text-muted);">
                    {{ $cancelMessage }}
                </div>
            @endif

            @if (! $confirmingCancel)
                <button
                    type="button"
                    wire:click="confirmCancel"
                    class="text-sm transition"
                    style="color:var(--text-muted);"
                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                >Cancel subscription</button>
            @else
                <div class="rounded-xl border px-4 py-4 space-y-3" style="background:var(--bg);border-color:rgba(var(--danger-rgb),0.3);">
                    <p class="text-sm" style="color:var(--text);">Cancel your $1/month subscription? Your supporter status will end immediately.</p>
                    <div class="flex gap-3">
                        <x-button type="button" wire:click="cancelSubscription" wire:loading.attr="disabled" variant="destructive" class="px-4 py-2">
                            <span wire:loading.remove>Yes, cancel</span>
                            <span wire:loading>Cancelling…</span>
                        </x-button>
                        <x-button type="button" wire:click="cancelConfirm" variant="secondary" class="px-4 py-2">Keep subscription</x-button>
                    </div>
                </div>
            @endif

            <p class="text-xs" style="color:var(--text-faint);">
                You can also manage your subscription directly in your
                <a href="https://www.paypal.com/myaccount/autopay/" target="_blank" rel="noopener noreferrer" class="underline" style="color:var(--text-muted);">PayPal account</a>.
            </p>
        </x-card>
    @endif

</div>
