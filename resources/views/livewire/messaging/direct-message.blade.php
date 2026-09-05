@php
    $isLowStim     = auth()->user()?->low_stimulation_mode;
    $dmGradientDef = (!$isLowStim && $dmGradientTheme)
        ? config('gradients.' . $dmGradientTheme)
        : null;
    $dmBg          = $dmGradientDef ? 'background:' . $dmGradientDef['css'] . ';' : '';
    $hideReactions = (bool) (auth()->user()->hide_reactions ?? false);
@endphp
<div class="flex flex-col h-full max-w-3xl w-full mx-auto">

    {{-- Header --}}
    {{-- Glass UI fix: the header sits OUTSIDE .cg-room-entry (added below), at
         the same DOM level as the sitewide forest photo layer, matching Home
         and Explore. backdrop-filter blurs whatever is immediately behind an
         element — nested inside .cg-room-entry, it was blurring that div's own
         opaque gradient fill instead of the photo, which made the glass look
         solid no matter what the scrim/blur tokens were set to. The whole
         column is also capped at max-w-3xl + centered (same width Home uses),
         so the photo shows through on both sides instead of the chat
         stretching edge-to-edge. --}}
    {{-- Glass UI (Phase 4): light tier — chrome only, the thread below stays flat/opaque. --}}
    {{-- position:relative + z-index:10 (not just the "relative" on the
         gradient-picker's own inner wrapper below): without it, this header
         was a plain non-positioned block, which — per CSS stacking rules —
         always paints BEHIND any position:relative element elsewhere on the
         page regardless of DOM order or z-index value. Each message's
         reaction-button wrapper is position:relative, so those buttons'
         icons were bleeding through on top of the gradient dropdown. Same
         fix already applied to the room header for the same reason. --}}
    <x-glass-panel tier="light" class="relative flex items-center gap-2 px-4 py-3 flex-none" style="border-radius:var(--radius-lg) var(--radius-lg) 0 0;z-index:10;">
        <a href="{{ route('messages.index') }}" wire:navigate class="transition flex-none" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">←</a>
        <div class="flex-1 min-w-0">
            @if ($this->conversation->isDirect())
                @php $other = $this->conversation->participants->firstWhere('id', '!=', auth()->id()); @endphp
                <h1 class="font-semibold text-sm truncate" style="color:var(--text);margin:0;">{{ $other?->display_name ?? 'Unknown' }}</h1>
                @if ($other?->isOnline())
                    <p class="text-xs" style="color:var(--accent);">Online now</p>
                @endif
            @else
                <h1 class="font-semibold text-sm truncate" style="color:var(--text);margin:0;">{{ $this->conversation->name }}</h1>
            @endif
        </div>

        {{--
            DM gradient picker — hidden for now at Andrew's request (code
            kept, just not rendered): changing the gradient wasn't working
            right and he isn't sure yet whether to keep the feature at all.
            Disabled with a real @if(false) below rather than commenting
            the block out, same reason as the disabled discovery sidebar in
            layouts/app.blade.php: this block has its own Blade comments
            inside it, and Blade comments don't nest, so a wrapping comment
            would close itself at the first closing marker it hits deep
            inside the block, leaking literal text onto the page (which is
            exactly what happened here the first time this was disabled —
            fixed now). Flip it back on by deleting the @if/@endif pair
            immediately below, leaving everything between them untouched.
        --}}
        @if (false)
        <div class="flex-none relative" x-data="{ open: @entangle('showGradientPicker') }" @click.outside="open = false">
            <button
                type="button"
                @click="open = !open"
                title="Set your chat gradient"
                class="flex items-center justify-center w-8 h-8 sm:w-auto sm:h-auto sm:px-2.5 sm:py-1.5 rounded-lg transition text-xs"
                style="{{ $dmGradientTheme ? 'background:rgba(255,255,255,0.06);color:var(--text-muted);border:1px solid var(--border);' : 'color:var(--text-muted);border:1px solid var(--border);' }}"
                onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
            >
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline;vertical-align:middle;"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                <span class="hidden sm:inline ml-1">Gradient</span>
            </button>

            <div
                x-show="open"
                @keydown.escape.window="open = false"
                class="absolute right-0 top-full mt-2 w-64 rounded-xl p-3 z-50"
                style="display:none;background:var(--surface);border:1px solid var(--border);box-shadow:0 8px 24px rgba(0,0,0,0.5);"
            >
                    <p class="text-xs font-semibold mb-1" style="color:var(--text-muted);">Your chat gradient</p>
                    <p class="text-xs mb-2" style="color:var(--text-faint);">Only you will see this.</p>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button
                            type="button"
                            wire:click="setDmGradient('')"
                            @click="open = false"
                            class="flex flex-col items-center gap-1"
                        >
                            <div class="w-full h-8 rounded border-2 transition" style="background:#0D1117;{{ $dmGradientTheme === '' ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"></div>
                            <span class="text-xs" style="color:{{ $dmGradientTheme === '' ? 'var(--text)' : 'var(--text-muted)' }};">None</span>
                        </button>
                        @foreach (config('gradients') as $key => $gradient)
                            <button
                                type="button"
                                wire:click="setDmGradient('{{ $key }}')"
                                @click="open = false"
                                class="flex flex-col items-center gap-1"
                            >
                                <div class="w-full h-8 rounded border-2 transition" style="background:{{ $gradient['css'] }};{{ $dmGradientTheme === $key ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"></div>
                                <span class="text-xs text-center leading-tight" style="color:{{ $dmGradientTheme === $key ? 'var(--text)' : 'var(--text-muted)' }};">{{ $gradient['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
        </div>
        @endif

        {{-- Quiet mode toggle — styled hover tooltip instead of the native
             browser title tooltip, matching the pin/reply tooltips in
             message-reaction-box.blade.php. Not teleported: this header has
             its own z-index:10 and no overflow:hidden ancestor, so it never
             hits the clipping issues those other tooltips needed to escape. --}}
        <span class="relative inline-flex flex-none" x-data="{ quietHover: false }" @mouseenter="quietHover = true" @mouseleave="quietHover = false">
            <button
                type="button"
                wire:click="toggleQuietMode"
                class="flex items-center justify-center w-8 h-8 sm:w-auto sm:h-auto sm:px-3 sm:py-1.5 rounded-lg font-medium transition text-xs flex-none"
                style="{{ $quietMode ? 'background:rgba(210,153,34,0.2);color:#D29922;' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                aria-label="{{ $quietMode ? 'Unmute this conversation' : 'Mute this conversation' }}"
            >
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="sm:hidden"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                <span class="hidden sm:inline">{{ $quietMode ? 'Quiet mode on' : 'Quiet mode' }}</span>
            </button>
            <span
                x-show="quietHover"
                style="display:none;position:absolute;top:calc(100% + 6px);right:0;background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.25rem 0.625rem;font-size:0.6875rem;color:var(--text);white-space:nowrap;pointer-events:none;z-index:30;"
            >{{ $quietMode ? 'Unmute this conversation' : 'Mute this conversation' }}</span>
        </span>
    </x-glass-panel>

    {{-- Glass UI fix: .cg-room-entry (the per-conversation gradient theme fill)
         now wraps only the thread content area — banner, messages, typing
         indicator, crisis banner — between the header and compose bar, not
         the glass chrome itself. The "Gradient" picker above keeps setting
         $dmGradientTheme/$dmBg exactly as before; only where it's painted
         changed. --}}
    <div class="cg-room-entry flex flex-col flex-1 min-w-0" style="{{ $dmBg }}">

    {{-- Message request banner --}}
    @if ($this->pendingRequest)
        <div class="mx-4 mt-3 rounded-lg border px-4 py-3 space-y-2 flex-none" style="background:rgba(210,153,34,0.08);border-color:rgba(210,153,34,0.4);">
            <p class="text-sm" style="color:#D29922;">
                This is a message request from
                <span class="font-semibold">{{ $this->pendingRequest->fromUser?->gamertag }}</span>.
                Accept or decline?
            </p>
            <div class="flex gap-3">
                <x-button type="button" variant="primary" wire:click="acceptRequest" class="!py-1.5">Accept</x-button>
                <x-button type="button" variant="destructive" wire:click="declineRequest" class="!py-1.5">Decline</x-button>
            </div>
        </div>
    @endif

    {{-- Messages --}}
    {{-- Glass UI (Phase 4): deliberately NOT glass — matches room chat's own
         .cg-chat-messages, which keeps an explicit opaque background so dense
         reading content never sits on the photo layer. Without this, the
         thread would inherit transparency now that this route has the fixed
         photo behind everything, and the photo would show through the gaps
         between bubbles. --}}
    @php $bothShowReceipts = $this->conversation->participants->every(fn($p) => $p->show_read_receipts ?? false); @endphp
    <div
        class="flex-1 overflow-y-auto px-4 py-4 space-y-1"
        style="background:var(--bg);"
        x-data
        x-init="$el.scrollTop = $el.scrollHeight"
        x-on:message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
        x-on:message-received.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
    >
        @foreach ($this->messages as $message)
            @php
                $isMine       = $message->user_id === auth()->id();
                $reactionCounts = $message->reactions->groupBy('reaction')->map->count();
                $myReactions  = $message->reactions->where('user_id', auth()->id())->pluck('reaction');
            @endphp
            <div
                wire:key="msg-{{ $message->id }}"
                @class(['flex flex-col', 'items-end' => $isMine, 'items-start' => !$isMine])
            >
                {{-- Bubble + reaction indicator sit side by side (reversed for $isMine) so
                     the indicator lands on the correct side and stays pinned to the bottom
                     of the bubble via self-end — consistent position regardless of how many
                     lines the message wraps to. Shared with room chat: x-message-reaction-box. --}}
                <div @class(['flex items-end', 'flex-row-reverse' => $isMine]) style="gap:0.375rem;">
                    <div class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl text-sm space-y-0.5"
                        style="{{ $isMine ? 'background:var(--accent);color:var(--on-accent);' : 'background:var(--surface);color:var(--text);' }}"
                    >
                        @if (!$isMine)
                            <p class="text-xs font-semibold" style="color:var(--text-muted);">{{ $message->author_name }}</p>
                        @endif

                        {{-- Content warning --}}
                        @if ($message->has_cw)
                            <div x-data="{ revealed: false }">
                                <div x-show="!revealed">
                                    <p class="text-xs font-semibold mb-1 opacity-80">⚠ {{ $message->cw_label ?? 'Content warning' }}</p>
                                    <button type="button" @click="revealed = true" class="text-xs underline opacity-70 hover:opacity-100">
                                        Click to reveal
                                    </button>
                                </div>
                                <p x-show="revealed" class="leading-snug">{{ $message->content }}</p>
                            </div>
                        @else
                            <p class="leading-snug">{{ $message->content }}</p>
                        @endif

                        <div class="flex items-center justify-between">
                            <p class="text-xs opacity-60">{{ $message->created_at->format('H:i') }}</p>
                            {{-- Read receipt --}}
                            @if ($isMine && $bothShowReceipts && $message->read_at)
                                <p class="text-xs opacity-50 ml-2">Read {{ $message->read_at->format('H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    @if (!$hideReactions)
                        <x-message-reaction-box
                            :message="$message"
                            :is-mine="$isMine"
                            :my-reactions="$myReactions"
                            :hide-reactions="$hideReactions"
                        />
                    @endif
                </div>

                {{-- Reaction counts --}}
                @if ($reactionCounts->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach ($reactionCounts as $reaction => $count)
                            <button
                                type="button"
                                wire:click="reactToMessage('{{ $message->id }}', '{{ $reaction }}')"
                                class="text-xs px-2 py-0.5 rounded-full border transition"
                                style="{{ $myReactions->contains($reaction) ? 'background:rgba(var(--accent-rgb),0.2);border-color:var(--accent);color:var(--accent);' : 'background:var(--surface);border-color:var(--border);color:var(--text-muted);' }}"
                            >{{ $reaction }} {{ $count }}</button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Typing indicator --}}
    <div
        class="px-4 pb-1 min-h-[1.25rem] flex-none"
        style="background:var(--bg);"
        x-data="{ typingUser: null, timer: null }"
        x-on:typing-received.window="
            typingUser = $event.detail.displayName;
            clearTimeout(timer);
            timer = setTimeout(() => typingUser = null, 3000)
        "
    >
        <p x-show="typingUser" x-text="typingUser + ' is typing...'" class="text-xs italic" style="color:var(--text-muted);"></p>
    </div>

    {{-- Crisis banner --}}
    @include('livewire.partials.crisis-banner')

    </div>

    {{-- Compose --}}
    {{-- Glass UI (Phase 4): light tier — chrome only, the thread above stays flat/opaque.
         position+z-index (Part 1 fix, unrelated to theming): the global active-room dock
         (livewire:rooms.active-room-dock) floats fixed at bottom:1.25rem;right:1.25rem;
         z-index:40 on every authenticated page. Without an explicit stacking order here,
         the dock's pill would render on top of and swallow clicks meant for the Send
         button — win the stacking order explicitly regardless of background treatment. --}}
    <x-glass-panel tier="light" class="px-4 pb-4 pt-2 flex-none" style="position:relative;z-index:41;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
        @if ($verificationBlock)
            <p class="mb-1.5 text-xs" style="color:#C9A83C;">{{ $verificationBlock }}</p>
        @endif
        @error('messageContent')
            <p class="mb-1 text-xs" style="color:var(--danger);">{{ $message }}</p>
        @enderror
        @error('cwLabel')
            <p class="mb-1 text-xs" style="color:var(--danger);">{{ $message }}</p>
        @enderror

        @if ($showCwInput)
            <div class="mb-2">
                <input
                    type="text"
                    wire:model="cwLabel"
                    maxlength="50"
                    placeholder="Warning label (e.g. spoilers, mental health…)"
                    class="w-full rounded-lg px-3 py-1.5 text-xs"
                    style="background:var(--surface);border:1px solid #D29922;color:var(--text);outline:none;"
                    onfocus="this.style.outline='2px solid var(--accent)';this.style.outlineOffset='2px'"
                    onblur="this.style.outline='none'"
                >
            </div>
        @endif

        <form wire:submit="sendMessage" class="flex gap-2">
            <button
                type="button"
                wire:click="$toggle('showCwInput')"
                title="Add content warning"
                class="px-2.5 py-2.5 rounded-xl text-xs font-medium transition"
                style="{{ $showCwInput ? 'background:rgba(210,153,34,0.25);color:#D29922;' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
            >CW</button>
            <x-input
                type="text"
                wire:model="messageContent"
                wire:keydown.debounce.500ms="broadcastTyping"
                maxlength="2000"
                placeholder="Message…"
                class="flex-1 !rounded-xl"
                autocomplete="off"
            />
            <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="!rounded-xl disabled:opacity-50">Send</x-button>
        </form>
    </x-glass-panel>

</div>
