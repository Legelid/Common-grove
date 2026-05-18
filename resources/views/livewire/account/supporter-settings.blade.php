<div class="px-6 py-10 max-w-2xl mx-auto space-y-10">

    <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Supporter</h1>

    {{-- ── Flash messages from redirect ──────────────────────────────────── --}}
    @if (session('subscribed'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(29,158,117,0.08);border-color:rgba(29,158,117,0.2);color:#1D9E75;">
            Thank you! Your subscription is being confirmed. Supporter status will be active shortly.
        </div>
    @endif
    @if (session('subscribe_cancelled'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(139,148,158,0.07);border-color:#30363D;color:#8B949E;">
            No changes made — you can subscribe any time from the <a href="{{ route('support') }}" class="underline">support page</a>.
        </div>
    @endif
    @if (session('paypal_error'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(226,75,74,0.07);border-color:rgba(226,75,74,0.2);color:#E24B4A;">
            {{ session('paypal_error') }}
        </div>
    @endif

    {{-- ── Current status ──────────────────────────────────────────────────── --}}
    <section class="rounded-xl border p-6 space-y-4" style="background:#161B22;border-color:#30363D;">
        <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Status</h2>

        @if (auth()->user()->is_supporter)
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium" style="background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.25);">
                    <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 14s-6-4.35-6-8A3.5 3.5 0 0 1 8 3.55 3.5 3.5 0 0 1 14 6c0 3.65-6 8-6 8z"/></svg>
                    CommonGrove Supporter
                </span>
            </div>

            @if ($subscription)
                <div class="text-xs space-y-1" style="color:#8B949E;">
                    @if ($subscription->started_at)
                        <p>Supporting since {{ $subscription->started_at->format('M j, Y') }}</p>
                    @endif
                    @if ($subscription->status === 'payment_failed')
                        <p style="color:#E24B4A;">Last payment failed — please update your payment method via PayPal.</p>
                    @elseif ($subscription->status === 'suspended')
                        <p style="color:#D29922;">Subscription suspended — please check your PayPal account.</p>
                    @endif
                </div>
            @endif
        @else
            <p class="text-sm" style="color:#8B949E;">You're using the free plan.</p>
            <a
                href="{{ route('support') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Learn about supporting CommonGrove</a>
        @endif
    </section>

    {{-- ── Supporter icon toggle (supporters only) ─────────────────────────── --}}
    @if (auth()->user()->is_supporter)
        <section class="rounded-xl border p-6 space-y-4" style="background:#161B22;border-color:#30363D;">
            <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Supporter Icon</h2>

            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium" style="color:#E6EDF3;">Show supporter icon next to username</p>
                    <p class="text-xs mt-0.5" style="color:#8B949E;">Shows a small ♥ with the tooltip "I believe in CommonGrove".</p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('showSupporterIcon')"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition flex-none ml-4"
                    style="background:{{ $showSupporterIcon ? '#1D9E75' : '#21262D' }};"
                    role="switch"
                    aria-checked="{{ $showSupporterIcon ? 'true' : 'false' }}"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showSupporterIcon ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if ($iconMessage)
                <p class="text-sm" style="color:#1D9E75;">{{ $iconMessage }}</p>
            @endif

            <button
                type="button"
                wire:click="saveIconPref"
                class="px-5 py-2 text-sm font-semibold rounded-lg transition"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Save</button>
        </section>

        {{-- ── Manage / cancel subscription ──────────────────────────────────── --}}
        <section class="rounded-xl border p-6 space-y-4" style="background:#161B22;border-color:#30363D;">
            <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Manage Subscription</h2>

            <p class="text-sm" style="color:#8B949E;">
                Your subscription is billed monthly through PayPal. You can cancel at any time — it takes effect immediately.
            </p>

            @if ($cancelMessage)
                <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(139,148,158,0.07);border:1px solid #30363D;color:#8B949E;">
                    {{ $cancelMessage }}
                </div>
            @endif

            @if (! $confirmingCancel)
                <button
                    type="button"
                    wire:click="confirmCancel"
                    class="text-sm transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                >Cancel subscription</button>
            @else
                <div class="rounded-xl border px-4 py-4 space-y-3" style="background:#0D1117;border-color:rgba(226,75,74,0.3);">
                    <p class="text-sm" style="color:#C9D1D9;">Cancel your $1/month subscription? Your supporter status will end immediately.</p>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            wire:click="cancelSubscription"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                            style="background:rgba(226,75,74,0.15);color:#E24B4A;border:1px solid rgba(226,75,74,0.3);"
                        >
                            <span wire:loading.remove>Yes, cancel</span>
                            <span wire:loading>Cancelling…</span>
                        </button>
                        <button
                            type="button"
                            wire:click="cancelConfirm"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                            style="background:#21262D;color:#8B949E;"
                        >Keep subscription</button>
                    </div>
                </div>
            @endif

            <p class="text-xs" style="color:#3d4451;">
                You can also manage your subscription directly in your
                <a href="https://www.paypal.com/myaccount/autopay/" target="_blank" rel="noopener noreferrer" class="underline" style="color:#8B949E;">PayPal account</a>.
            </p>
        </section>
    @endif

</div>
