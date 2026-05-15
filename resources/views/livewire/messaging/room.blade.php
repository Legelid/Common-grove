<div class="flex h-full">

    {{-- Participant sidebar --}}
    <aside class="w-48 flex-none border-r flex flex-col" style="background:#161B22;border-color:#30363D;">
        <div class="px-3 py-3 border-b" style="border-color:#30363D;">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">
                @php $activeParticipants = $this->conversation->participants->filter(fn($p) => !$p->pivot->left_at); @endphp
                In this room · {{ $activeParticipants->count() }}
            </p>
        </div>
        <div class="flex-1 overflow-y-auto py-2 space-y-0.5">
            @foreach ($activeParticipants as $participant)
                <a
                    href="{{ route('profile.show', $participant->gamertag) }}"
                    wire:navigate
                    class="flex items-center gap-2 px-3 py-1.5 rounded transition"
                    style="color:#8B949E;" onmouseover="this.style.background='#21262D'" onmouseout="this.style.background=''"
                >
                    <img src="{{ $participant->avatar_url }}" alt="" class="w-6 h-6 rounded-full object-cover" style="background:#21262D;">
                    <span class="text-xs truncate" style="color:#E6EDF3;">{{ $participant->gamertag }}</span>
                    @if ($participant->isOnline())
                        <span class="ml-auto w-1.5 h-1.5 rounded-full flex-none" style="background:#1D9E75;"></span>
                    @endif
                </a>
            @endforeach
        </div>

        @if ($this->conversation->hangoutPost && !$this->conversation->hangoutPost->is_persistent)
            @if ($this->conversation->hangoutPost->isExpired())
                <div class="px-3 py-2 border-t" style="border-color:#30363D;">
                    <p class="text-xs" style="color:#8B949E;">This hangout has ended.</p>
                </div>
            @else
                <div class="px-3 py-2 border-t" style="border-color:#30363D;">
                    <p class="text-xs" style="color:#D29922;">
                        Hangout closes in {{ $this->conversation->hangoutPost->expiresInFormatted() }}
                    </p>
                </div>
            @endif
        @endif

        <div class="px-3 py-2 border-t" style="border-color:#30363D;">
            <button
                type="button"
                wire:click="leaveQuietly"
                wire:confirm="Leave this room quietly? No one will be notified."
                class="w-full text-xs py-1 transition"
                style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
            >Leave quietly</button>
        </div>
    </aside>

    {{-- Chat area --}}
    <div class="flex flex-col flex-1 min-w-0">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3 border-b flex-none" style="background:#161B22;border-color:#30363D;">
            <a href="{{ route('messages.index') }}" wire:navigate class="transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">←</a>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate" style="color:#E6EDF3;">{{ $this->conversation->name ?? 'Hangout Room' }}</p>
                <p class="text-xs" style="color:#8B949E;">{{ $activeParticipants->count() }} participants</p>
            </div>

            {{-- Room pin --}}
            <div class="flex-none flex flex-col items-end gap-0.5">
                <button
                    type="button"
                    wire:click="togglePin"
                    aria-label="{{ $this->isRoomPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                    title="{{ $this->isRoomPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                    class="flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg transition"
                    style="{{ $this->isRoomPinned
                        ? 'color:#1D9E75;background:rgba(29,158,117,0.08);border:1px solid rgba(29,158,117,0.2);'
                        : 'color:#8B949E;border:1px solid #30363D;' }}"
                    onmouseover="{{ $this->isRoomPinned
                        ? "this.style.color='#E24B4A';this.style.background='rgba(226,75,74,0.06)';this.style.borderColor='rgba(226,75,74,0.2)';"
                        : "this.style.color='#C9D1D9';this.style.borderColor='#3d4451';" }}"
                    onmouseout="{{ $this->isRoomPinned
                        ? "this.style.color='#1D9E75';this.style.background='rgba(29,158,117,0.08)';this.style.borderColor='rgba(29,158,117,0.2)';"
                        : "this.style.color='#8B949E';this.style.borderColor='#30363D';" }}"
                >
                    @if ($this->isRoomPinned)
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                        </svg>
                        Pinned
                    @else
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                        </svg>
                        Pin room
                    @endif
                </button>
                @if ($roomPinMessage)
                    <p class="text-xs" style="color:#D29922;">{{ $roomPinMessage }}</p>
                @endif
            </div>
        </div>

        {{-- Hangout type notice --}}
        @if ($this->conversation->hangoutPost)
            @php $hangoutPost = $this->conversation->hangoutPost; @endphp
            @if ($hangoutPost->is_official)
                <div class="flex items-center gap-2 px-4 py-2 border-b flex-none" style="border-color:#21262D;background:rgba(29,158,117,0.04);">
                    <span class="text-xs px-2 py-0.5 rounded font-medium flex-none"
                        style="background:rgba(29,158,117,0.08);color:#1D9E75;border:1px solid rgba(29,158,117,0.15);">CommonGrove room</span>
                    <span class="text-xs" style="color:#8B949E;">An always-open starter space made by CommonGrove.</span>
                    <span class="ml-auto text-xs" style="color:#3d4451;">Messages here don't stick around forever — just long enough to keep things flowing.</span>
                </div>
            @elseif ($hangoutPost->is_persistent)
                <div class="flex items-center gap-2 px-4 py-2 border-b flex-none" style="border-color:#21262D;background:rgba(29,158,117,0.04);">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-none"
                        style="background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.2);">Always-open room</span>
                    <span class="text-xs" style="color:#8B949E;">This room stays open.</span>
                    <span class="ml-auto text-xs" style="color:#3d4451;">Messages here don't stick around forever — just long enough to keep things flowing.</span>
                </div>
            @elseif ($hangoutPost->isExpired())
                <div class="flex items-center gap-2 px-4 py-2 border-b flex-none" style="border-color:#21262D;background:rgba(210,153,34,0.04);">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-none"
                        style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.2);">Temporary hangout</span>
                    <span class="text-xs" style="color:#8B949E;">This hangout has ended.</span>
                </div>
            @else
                <div class="flex items-center gap-2 px-4 py-2 border-b flex-none" style="border-color:#21262D;background:rgba(210,153,34,0.04);">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-none"
                        style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.2);">Temporary hangout</span>
                    <span class="text-xs" style="color:#8B949E;">This hangout closes in <strong style="color:#D29922;">{{ $hangoutPost->expiresInFormatted() }}</strong>.</span>
                </div>
            @endif
        @endif

        {{-- Pinned messages --}}
        @if ($this->pinnedMessages->isNotEmpty())
            <div x-data="{ open: true }" class="border-b flex-none" style="border-color:#30363D;">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-2 text-xs transition"
                    style="background:rgba(22,27,34,0.8);color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                >
                    <span class="font-medium">📌 {{ $this->pinnedMessages->count() }} pinned {{ Str::plural('message', $this->pinnedMessages->count()) }}</span>
                    <span x-text="open ? '▲' : '▼'"></span>
                </button>
                <div x-show="open" class="divide-y" style="background:rgba(28,35,51,0.5);divide-color:#30363D;">
                    @foreach ($this->pinnedMessages as $pinned)
                        <div class="px-4 py-2 flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs mb-0.5" style="color:#8B949E;">{{ $pinned->user?->gamertag }}</p>
                                <p class="text-xs truncate" style="color:#E6EDF3;">{{ Str::limit($pinned->content, 100) }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-none">
                                <a href="#msg-{{ $pinned->id }}" class="text-xs underline transition" style="color:#1D9E75;">Jump</a>
                                @if ($this->conversation->created_by === auth()->id() || auth()->user()->is_admin)
                                    <button
                                        type="button"
                                        wire:click="unpinMessage('{{ $pinned->id }}')"
                                        class="text-xs transition"
                                        style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                                    >Unpin</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Pin error --}}
        @if ($pinError)
            <div class="mx-4 mt-2 px-3 py-2 rounded text-xs border flex-none" style="background:rgba(226,75,74,0.1);border-color:rgba(226,75,74,0.4);color:#E24B4A;">
                {{ $pinError }}
            </div>
        @endif

        {{-- Messages --}}
        <div
            class="flex-1 overflow-y-auto px-4 py-4 space-y-3"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
            x-on:message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
            x-on:message-received.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
        >
            @foreach ($this->chatMessages as $message)
                @php
                    $isMine        = $message->user_id === auth()->id();
                    $reactionCounts = $message->reactions->groupBy('reaction')->map->count();
                    $myReactions   = $message->reactions->where('user_id', auth()->id())->pluck('reaction');
                    $canPin        = $this->conversation->created_by === auth()->id() || auth()->user()->is_admin;
                @endphp
                <div
                    id="msg-{{ $message->id }}"
                    wire:key="rmsg-{{ $message->id }}"
                    x-data="{ showActions: false }"
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

                        <p class="text-xs opacity-60">{{ $message->created_at->format('H:i') }}</p>
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

                    {{-- React + pin buttons --}}
                    <div class="mt-0.5 flex items-center gap-1 relative" x-data="{ reactOpen: false }">
                        <button type="button" @click="reactOpen = !reactOpen"
                            class="text-xs px-1 transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">
                            + react
                        </button>
                        @if ($canPin && !$message->is_pinned)
                            <button type="button" wire:click="pinMessage('{{ $message->id }}')"
                                class="text-xs px-1 transition" style="color:#8B949E;" onmouseover="this.style.color='#D29922'" onmouseout="this.style.color='#8B949E'">
                                📌 pin
                            </button>
                        @endif
                        <div x-show="reactOpen" @click.outside="reactOpen = false"
                            class="absolute z-10 flex flex-wrap gap-1 p-2 rounded-lg shadow-lg"
                            style="background:#21262D;border:1px solid #30363D;bottom:1.5rem;"
                            @class(['right-0' => $isMine, 'left-0' => !$isMine])
                        >
                            @foreach (\App\Models\MessageReaction::ALLOWED as $reaction)
                                <button
                                    type="button"
                                    wire:click="reactToMessage('{{ $message->id }}', '{{ $reaction }}')"
                                    @click="reactOpen = false"
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
        @php $isExpiredHangout = $this->conversation->hangoutPost && !$this->conversation->hangoutPost->is_persistent && $this->conversation->hangoutPost->isExpired(); @endphp
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
                        placeholder="Warning label (e.g. spoilers, sensitive topic…)"
                        class="w-full rounded-lg px-3 py-1.5 text-xs focus:outline-none"
                        style="background:#1C2333;border:1px solid #D29922;color:#E6EDF3;"
                    >
                </div>
            @endif

            @if ($isExpiredHangout)
                <p class="text-xs text-center py-2" style="color:#8B949E;">This hangout has ended — no new messages can be sent.</p>
            @else
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
                    placeholder="Message the room…"
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
            @endif
        </div>

    </div>
</div>
