@props([
    'message',
    'isMine' => false,
    'myReactions' => null,
    'hideReactions' => false,
    'canPin' => false,
    'showReply' => false,
])

{{--
    Shared reaction trigger + tray + full picker, used by both the room chat
    and direct-message thread so the two can never drift out of sync again.

    Positioning contract (deliberate, do not "simplify" back to a below-bubble
    layout): this sits BESIDE the bubble as a sibling in a flex row that the
    caller reverses via flex-row-reverse when $isMine, so reactions land on
    the correct side regardless of sender. The indicator itself is
    `self-end` within that row, so its vertical position tracks the bottom
    of the bubble rather than the bubble's height — a one-line and a
    three-line message both get the same reaction-row position.
--}}
@php
    $myReactions = $myReactions ?? collect();
@endphp

<div
    class="relative inline-flex flex-shrink-0 self-end"
    x-data="{
        trayOpen: false,
        pickerOpen: false,
        trayPos: {},
        pickerPos: {},
        // Both the tray and the picker are teleported to <body> below —
        // .cg-chat-messages (this sits inside it) is overflow-y:auto, which
        // was clipping either one whenever a message near an edge of the
        // scroll box opened them. Teleporting moves them out from under
        // their natural DOM parent, so position has to be computed here
        // from this component's own rect instead of relying on CSS
        // anchoring to an ancestor.
        //
        // computeTrayPos() and computePickerPos() are deliberately separate
        // (not one shared computePositions()): the more-reactions button
        // that opens the picker lives INSIDE the already-open tray, so
        // openPicker() used to recompute trayPos too, on every click, even
        // though the tray was already open and correctly placed. Any tiny
        // shift between the tray first opening and that click landing
        // (page scroll, a new message arriving and nudging layout) would
        // then snap the already-visible tray to a new spot — reported as
        // the reaction bubble shifting position when opening the picker.
        computeTrayPos() {
            const r = this.$el.getBoundingClientRect();
            this.trayPos = {{ $isMine ? 'true' : 'false' }}
                ? { bottom: (window.innerHeight - r.bottom) + 'px', right: (window.innerWidth - r.left + 6) + 'px' }
                : { bottom: (window.innerHeight - r.bottom) + 'px', left: (r.right + 6) + 'px' };
        },
        computePickerPos() {
            const r = this.$el.getBoundingClientRect();

            // The picker is tall (~235px). It normally opens upward from the
            // button, same as before — but for a message near the very top
            // of the window (not just near the top of the scrollable list,
            // which no longer clips it at all now that it's teleported)
            // opening upward could still run it off the actual top of the
            // browser window. Flip to open downward instead when there
            // isn't room.
            const pickerEstHeight = 235;
            const opensUpward = r.top - pickerEstHeight - 8 > 0;
            const horizontal = {{ $isMine ? 'true' : 'false' }}
                ? { right: (window.innerWidth - r.right) + 'px' }
                : { left: r.left + 'px' };
            this.pickerPos = opensUpward
                ? { ...horizontal, bottom: (window.innerHeight - r.top) + 'px' }
                : { ...horizontal, top: (r.bottom + 8) + 'px' };
        },
        toggleTray() {
            const wasOpen = this.trayOpen;
            this.$dispatch('close-all-reactions');
            if (!wasOpen) this.computeTrayPos();
            this.trayOpen = !wasOpen;
            if (!this.trayOpen) this.pickerOpen = false;
        },
        openPicker() {
            // Captured BEFORE dispatching: this component also listens for
            // close-all-reactions on window (below), so dispatching it
            // resets this instance's own trayOpen to false synchronously,
            // same as everyone else's — checking this.trayOpen after the
            // dispatch would always see it as freshly-closed and recompute
            // trayPos needlessly, which is exactly what caused the tray to
            // visibly jump when opening the picker.
            const wasTrayOpen = this.trayOpen;
            this.$dispatch('close-all-reactions');
            if (!wasTrayOpen) this.computeTrayPos();
            this.computePickerPos();
            this.trayOpen = true;
            this.pickerOpen = true;
        },
        closePicker() { this.pickerOpen = false; }
    }"
    @close-all-reactions.window="trayOpen = false; pickerOpen = false"
    @keydown.escape.window="trayOpen = false; pickerOpen = false"
>
    <button
        type="button"
        x-data="{ hovering: false }"
        @mouseenter="hovering = true"
        @mouseleave="hovering = false"
        @click.stop="toggleTray()"
        style="width:28px;height:28px;min-width:28px;min-height:28px;border-radius:50%;background:var(--surface-raised);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--accent);transition:opacity 150ms ease;flex-shrink:0;align-self:flex-end;margin-bottom:2px;"
        :style="{ opacity: (trayOpen || pickerOpen || hovering) ? '1' : '0.35' }"
        aria-label="React to message"
        :aria-expanded="trayOpen.toString()"
    >
        <span
            aria-hidden="true"
            style="display:inline-block;width:14px;height:14px;pointer-events:none;background-color:currentColor;-webkit-mask-image:url('{{ asset('images/leaf-plus-icon.png') }}');mask-image:url('{{ asset('images/leaf-plus-icon.png') }}');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"
        ></span>
    </button>

    {{--
        Quick-tray — a single horizontal row, not a grid/box. It flies out
        SIDEWAYS from the reaction button toward the center of the thread
        (left of the button for $isMine, since the button already sits to
        the left of your own right-aligned bubble; right of the button
        otherwise), rather than stacking above the message. Positioned with
        bottom:0 (not a transform-based vertical center) deliberately — the
        enter/leave transition below already owns `transform` for its
        scale animation, and an inline `transform` would silently override
        Tailwind's scale-95/scale-100 classes since inline style always
        wins over class-based CSS.
    --}}
    <template x-teleport="body">
    <div
        x-show="trayOpen || pickerOpen"
        @click.outside="trayOpen = false"
        @keydown.escape.window="trayOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="cg-reaction-tray flex items-center"
        :style="trayPos"
        style="display:none;position:fixed;z-index:9999;flex-shrink:0;background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-md);gap:0.15rem;width:max-content;padding:0.25rem;white-space:nowrap;"
    >
        @unless ($hideReactions)
            @foreach (\App\Models\MessageReaction::TRAY as $emoji)
                <button
                    type="button"
                    wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                    @click="trayOpen = false; pickerOpen = false"
                    style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:4px;font-size:0.8rem;line-height:1;cursor:pointer;transition:background 100ms ease;background:transparent;border:none;{{ $myReactions->contains($emoji) ? 'filter:drop-shadow(0 0 4px rgba(var(--accent-rgb),0.7));' : '' }}"
                    onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'"
                    aria-label="{{ $emoji }}"
                    title="{{ $emoji }}"
                >{{ $emoji }}</button>
            @endforeach
            <button
                type="button"
                @click.stop="openPicker()"
                style="width:22px;height:22px;background:transparent;border:none;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-faint);transition:all 150ms ease;"
                onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--accent)';"
                onmouseout="this.style.background='transparent';this.style.color='var(--text-faint)';"
                aria-label="More reactions"
                :aria-expanded="pickerOpen.toString()"
            >
                <span
                    aria-hidden="true"
                    style="display:inline-block;width:14px;height:14px;background-color:currentColor;-webkit-mask-image:url('{{ asset('images/leaf-plus-icon.png') }}');mask-image:url('{{ asset('images/leaf-plus-icon.png') }}');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"
                ></span>
            </button>
        @endunless

        @if ($canPin || $showReply)
            {{-- Same row as the emojis above — a thin vertical rule instead
                 of a second row's border-top, since the tray is one line now. --}}
            @unless ($hideReactions)
                <div style="width:1px;align-self:stretch;background:var(--border);flex-shrink:0;margin:0 0.05rem;"></div>
            @endunless

            @if ($canPin)
                <span
                    class="relative inline-flex"
                    x-data="{ pinHover: false }"
                    @mouseenter="pinHover = true"
                    @mouseleave="pinHover = false"
                >
                    <button
                        type="button"
                        wire:click="{{ $message->is_pinned ? 'unpinMessage' : 'pinMessage' }}('{{ $message->id }}')"
                        @click="trayOpen = false; pickerOpen = false"
                        style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:4px;cursor:pointer;transition:color 100ms ease;background:transparent;border:none;color:{{ $message->is_pinned ? 'var(--accent)' : 'var(--text-faint)' }};"
                        onmouseover="this.style.color='var(--accent)'"
                        onmouseout="this.style.color='{{ $message->is_pinned ? 'var(--accent)' : 'var(--text-faint)' }}'"
                        aria-label="{{ $message->is_pinned ? 'Unpin message' : 'Pin message' }}"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $message->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="12" y1="17" x2="12" y2="22"/>
                            <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V17z"/>
                        </svg>
                    </button>
                    <div
                        x-show="pinHover"
                        style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.25rem 0.625rem;font-size:0.6875rem;color:var(--text);white-space:nowrap;pointer-events:none;z-index:30;"
                    >{{ $message->is_pinned ? 'Unpin message' : 'Pin message' }}</div>
                </span>
            @endif

            @if ($showReply && !$hideReactions)
                <button
                    type="button"
                    wire:click="setReply('{{ $message->id }}')"
                    @click="trayOpen = false; pickerOpen = false"
                    style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:4px;cursor:pointer;transition:color 100ms ease;background:transparent;border:none;color:var(--text-faint);"
                    onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-faint)'"
                    aria-label="Reply to message"
                    title="Reply"
                >↩</button>
            @endif
        @endif
    </div>
    </template>

    {{-- Full curated picker — its own top-level teleport (a sibling of the
         tray's above, not nested inside it — nesting two x-teleport
         templates works mechanically in Alpine, but both end up flattened
         to body-level siblings anyway once teleported, so nesting them here
         just obscures that). Renders above absolutely everything and can
         never be clipped by an ancestor's overflow again (.cg-chat-messages
         is overflow-y:auto; the settings-menu dropdowns, other message
         bubbles, anything with its own stacking context — none of it can
         clip a body-level node). Position comes from `pickerPos` (computed
         in openPicker() above) via an OBJECT-form :style binding — not a
         string — since a string there would replace this element's entire
         inline style, wiping out position:fixed and z-index along with it
         (the exact bug fixed earlier in the badge tooltips and the
         "similar rooms" panel). --}}
    @unless ($hideReactions)
        <template x-teleport="body">
            <div
                x-show="pickerOpen"
                @click.outside="closePicker()"
                @keydown.escape.window="closePicker()"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="cg-reaction-picker"
                :style="pickerPos"
                style="display:none;position:fixed;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:0.6rem;box-shadow:var(--shadow-lg);width:232px;z-index:9999;"
            >
                {{-- Each PICKER_GROUPS category has exactly 6 emojis (see
                     App\Models\MessageReaction) — sized so all 6 sit on one
                     row instead of wrapping a lone 6th emoji onto its own
                     line, which was most of why this felt oversized. --}}
                @foreach (\App\Models\MessageReaction::PICKER_GROUPS as $groupLabel => $groupEmojis)
                    <p style="font-size:0.625rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-faint);margin-bottom:0.3rem;{{ $loop->first ? '' : 'margin-top:0.4rem;' }}">{{ $groupLabel }}</p>
                    <div style="display:flex;gap:0.2rem;">
                        @foreach ($groupEmojis as $emoji)
                            <button
                                type="button"
                                wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                                @click="trayOpen = false; pickerOpen = false"
                                style="width:28px;height:28px;font-size:1.05rem;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 100ms ease;background:transparent;border:none;{{ $myReactions->contains($emoji) ? 'filter:drop-shadow(0 0 4px rgba(var(--accent-rgb),0.7));' : '' }}"
                                onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"
                                aria-label="{{ $emoji }}"
                                title="{{ $emoji }}"
                            >{{ $emoji }}</button>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </template>
    @endunless
</div>
