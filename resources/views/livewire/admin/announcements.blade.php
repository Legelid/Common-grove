<div class="p-8 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold font-display" style="color:var(--text);">Announcements</h1>
        <p class="text-xs mt-1" style="color:var(--text-muted);">
            Send a one-off email to the entire user base. Excludes anyone who has unsubscribed.
        </p>
    </div>

    {{-- Feedback --}}
    @if ($feedback)
        <div class="mb-5 rounded-lg px-4 py-2.5 text-sm" style="background:{{ $feedbackError ? 'rgba(var(--danger-rgb),0.1)' : 'rgb(var(--accent-rgb) / 0.1)' }};color:{{ $feedbackError ? 'var(--danger)' : 'var(--accent)' }};">
            {{ $feedback }}
        </div>
    @endif

    @if (! $previewing)
        {{-- ── Compose ─────────────────────────────────────────────────────── --}}
        <x-card padding="p-5" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Subject</label>
                <x-input type="text" wire:model="subject" placeholder="What's new at CommonGrove" maxlength="191" :error="$errors->first('subject')" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Message</label>
                <x-textarea wire:model="body" rows="10" placeholder="Write the announcement here…" :error="$errors->first('body')" />
                <p class="text-xs mt-1" style="color:var(--text-faint);">Plain text — line breaks are preserved. {{ $this->recipientCount }} user(s) currently eligible to receive it.</p>
            </div>

            <x-button type="button" wire:click="preview" wire:loading.attr="disabled" variant="primary">
                Preview &amp; Send
            </x-button>
        </x-card>
    @else
        {{-- ── Preview & confirm ───────────────────────────────────────────── --}}
        <x-card padding="p-5" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold" style="color:var(--text);">Ready to send</h2>
                    <p class="text-xs mt-0.5" style="color:var(--text-muted);">This will go to <strong style="color:var(--text);">{{ $this->recipientCount }}</strong> user(s). This is exactly what they'll see.</p>
                </div>
            </div>

            <iframe
                srcdoc="{{ $this->previewHtml }}"
                title="Email preview"
                style="width:100%;height:560px;border:1px solid var(--border);border-radius:8px;background:#fff;"
            ></iframe>

            <div class="flex items-center gap-3">
                <x-button type="button" wire:click="confirmSend" wire:loading.attr="disabled" wire:target="confirmSend" variant="primary">
                    <span wire:loading.remove wire:target="confirmSend">Confirm Send</span>
                    <span wire:loading wire:target="confirmSend">Queuing…</span>
                </x-button>
                <button type="button" wire:click="cancelPreview" class="text-sm transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                    Back to edit
                </button>
            </div>
        </x-card>
    @endif
</div>
