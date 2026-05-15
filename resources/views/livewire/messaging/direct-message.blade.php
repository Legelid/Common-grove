<div class="flex flex-col h-full">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 py-3 border-b flex-none" style="background:#161B22;border-color:#30363D;">
        <a href="{{ route('messages.index') }}" wire:navigate class="transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">←</a>
        <div class="flex-1">
            @if ($this->conversation->isDirect())
                @php $other = $this->conversation->participants->firstWhere('id', '!=', auth()->id()); @endphp
                <p class="font-semibold text-sm" style="color:#E6EDF3;">{{ $other?->display_name ?? 'Unknown' }}</p>
                @if ($other?->isOnline())
                    <p class="text-xs" style="color:#1D9E75;">Online now</p>
                @endif
            @else
                <p class="font-semibold text-sm" style="color:#E6EDF3;">{{ $this->conversation->name }}</p>
            @endif
        </div>

        {{-- Quiet mode toggle --}}
        <button
            type="button"
            wire:click="toggleQuietMode"
            title="{{ $quietMode ? 'Quiet mode on — click to disable' : 'Enable quiet mode' }}"
            class="text-xs px-3 py-1.5 rounded-lg font-medium transition"
            style="{{ $quietMode ? 'background:rgba(210,153,34,0.2);color:#D29922;' : 'background:#21262D;color:#8B949E;' }}"
        >{{ $quietMode ? 'Quiet mode on' : 'Quiet mode' }}</button>
    </div>

    {{-- Message request banner --}}
    @if ($this->pendingRequest)
        <div class="mx-4 mt-3 rounded-lg border px-4 py-3 space-y-2 flex-none" style="background:rgba(210,153,34,0.08);border-color:rgba(210,153,34,0.4);">
            <p class="text-sm" style="color:#D29922;">
                This is a message request from
                <span class="font-semibold">{{ $this->pendingRequest->fromUser?->gamertag }}</span>.
                Accept or decline?
            </p>
            <div class="flex gap-3">
                <button type="button" wire:click="acceptRequest"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                    style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">
                    Accept
                </button>
                <button type="button" wire:click="declineRequest"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                    style="background:rgba(226,75,74,0.2);color:#E24B4A;">
                    Decline
                </button>
            </div>
        </div>
    @endif

    {{-- Messages --}}
    @php $bothShowReceipts = $this->conversation->participants->every(fn($p) => $p->show_read_receipts ?? false); @endphp
    <div
        class="flex-1 overflow-y-auto px-4 py-4 space-y-3"
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
                x-data="{ showReactPicker: false }"
                @class(['flex flex-col', 'items-end' => $isMine, 'items-start' => !$isMine])
            >
                <div class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl text-sm space-y-0.5"
                    style="{{ $isMine ? 'background:#1D9E75;color:#fff;' : 'background:#1C2333;color:#E6EDF3;' }}"
                >
                    @if (!$isMine)
                        <p class="text-xs font-semibold" style="color:#8B949E;">{{ $message->author_name }}</p>
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

                {{-- Reaction counts --}}
                @if ($reactionCounts->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach ($reactionCounts as $reaction => $count)
                            <button
                                type="button"
                                wire:click="reactToMessage('{{ $message->id }}', '{{ $reaction }}')"
                                class="text-xs px-2 py-0.5 rounded-full border transition"
                                style="{{ $myReactions->contains($reaction) ? 'background:rgba(29,158,117,0.2);border-color:#1D9E75;color:#1D9E75;' : 'background:#1C2333;border-color:#30363D;color:#8B949E;' }}"
                            >{{ $reaction }} {{ $count }}</button>
                        @endforeach
                    </div>
                @endif

                {{-- Add reaction --}}
                <div class="mt-0.5 relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="text-xs px-1 transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">
                        + react
                    </button>
                    <div x-show="open" @click.outside="open = false"
                        class="absolute z-10 flex flex-wrap gap-1 p-2 rounded-lg shadow-lg"
                        style="background:#21262D;border:1px solid #30363D;bottom:1.5rem;"
                        @class(['right-0' => $isMine, 'left-0' => !$isMine])
                    >
                        @foreach (\App\Models\MessageReaction::ALLOWED as $reaction)
                            <button
                                type="button"
                                wire:click="reactToMessage('{{ $message->id }}', '{{ $reaction }}')"
                                @click="open = false"
                                class="text-xs px-2 py-1 rounded transition capitalize"
                                style="background:#1C2333;color:#8B949E;" onmouseover="this.style.background='#1D9E75';this.style.color='#fff'" onmouseout="this.style.background='#1C2333';this.style.color='#8B949E'"
                            >{{ $reaction }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Typing indicator --}}
    <div
        class="px-4 pb-1 min-h-[1.25rem] flex-none"
        x-data="{ typingUser: null, timer: null }"
        x-on:typing-received.window="
            typingUser = $event.detail.displayName;
            clearTimeout(timer);
            timer = setTimeout(() => typingUser = null, 3000)
        "
    >
        <p x-show="typingUser" x-text="typingUser + ' is typing...'" class="text-xs italic" style="color:#8B949E;"></p>
    </div>

    {{-- Crisis banner --}}
    @include('livewire.partials.crisis-banner')

    {{-- Compose --}}
    <div class="px-4 pb-4 pt-2 border-t flex-none" style="border-color:#30363D;">
        @error('messageContent')
            <p class="mb-1 text-xs" style="color:#E24B4A;">{{ $message }}</p>
        @enderror
        @error('cwLabel')
            <p class="mb-1 text-xs" style="color:#E24B4A;">{{ $message }}</p>
        @enderror

        @if ($showCwInput)
            <div class="mb-2">
                <input
                    type="text"
                    wire:model="cwLabel"
                    maxlength="50"
                    placeholder="Warning label (e.g. spoilers, mental health…)"
                    class="w-full rounded-lg px-3 py-1.5 text-xs focus:outline-none"
                    style="background:#1C2333;border:1px solid #D29922;color:#E6EDF3;"
                >
            </div>
        @endif

        <form wire:submit="sendMessage" class="flex gap-2">
            <button
                type="button"
                wire:click="$toggle('showCwInput')"
                title="Add content warning"
                class="px-2.5 py-2.5 rounded-xl text-xs font-medium transition"
                style="{{ $showCwInput ? 'background:rgba(210,153,34,0.25);color:#D29922;' : 'background:#21262D;color:#8B949E;' }}"
            >CW</button>
            <input
                type="text"
                wire:model="messageContent"
                wire:keydown.debounce.500ms="broadcastTyping"
                maxlength="2000"
                placeholder="Message…"
                class="flex-1 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.borderColor='#1D9E75'" onblur="this.style.borderColor='#30363D'"
                autocomplete="off"
            >
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-4 py-2.5 text-sm font-semibold rounded-xl transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Send</button>
        </form>
    </div>

</div>
