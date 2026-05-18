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
                    <x-user-name :user="$participant" class="text-xs truncate" style="color:#E6EDF3;" />
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

        @if(auth()->user()->isSupporter())
            <div class="px-3 py-2 border-t" style="border-color:#30363D;">
                <livewire:rooms.add-to-collection :conversationId="$conversationId" :key="'atc-'.$conversationId" />
            </div>
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
    @php
        $isLowStim          = auth()->user()?->low_stimulation_mode;
        $cgAdvanced         = auth()->user()?->advanced_comfort_settings ?? [];
        $cgHideGradients    = in_array('hide_gradients', $cgAdvanced, true);
        $hideReactions      = (bool) (auth()->user()?->hide_reactions ?? false);
        $roomGradientDef    = (!$isLowStim && !$cgHideGradients && $roomGradientTheme)
            ? config('gradients.' . $roomGradientTheme)
            : null;
        $chatAreaBg = $roomGradientDef
            ? 'background:' . $roomGradientDef['css'] . ';'
            : '';
    @endphp
    <div class="flex flex-col flex-1 min-w-0" style="{{ $chatAreaBg }}">

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

            {{-- Room appearance panel (owner / admin only) --}}
            @if ($this->isRoomOwner)
                <div
                    class="flex-none relative"
                    x-data="{ open: @entangle('showGradientPicker') }"
                    @click.outside="open = false"
                >
                    <button
                        type="button"
                        @click="open = !open"
                        title="Room appearance"
                        class="flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg transition"
                        style="{{ $roomGradientTheme ? 'color:#8B949E;border:1px solid #30363D;background:rgba(255,255,255,0.04);' : 'color:#8B949E;border:1px solid #30363D;' }}"
                        onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                    >
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                        Appearance
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 top-full mt-2 rounded-xl z-50"
                        style="display:none;width:18rem;background:#161B22;border:1px solid #30363D;box-shadow:0 8px 24px rgba(0,0,0,0.5);"
                    >
                        {{-- Panel header --}}
                        <div class="px-3 pt-3 pb-2 border-b" style="border-color:#21262D;">
                            <p class="text-xs font-semibold" style="color:#C9D1D9;">Room atmosphere</p>
                            <p class="text-xs mt-0.5" style="color:#3d4451;">Choose how this room feels. Everyone inside sees it.</p>
                        </div>

                        <div class="p-3 space-y-3">
                            @php
                                $allGradients       = config('gradients');
                                $supporterKeys      = config('supporter.gradient_packs', []);
                                $freeGradients      = array_filter($allGradients, fn ($k) => ! in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                                $supporterGradients = array_filter($allGradients, fn ($k) => in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                                $hasLockedAtmosphere = count($this->lockedRoomAtmosphereKeys) > 0;
                            @endphp

                            {{-- Default + free atmospheres --}}
                            <div>
                                <p class="text-xs mb-1.5" style="color:#3d4451;">Default</p>
                                <div class="grid grid-cols-4 gap-1.5">
                                    <button type="button" wire:click="setRoomGradient('')" class="flex flex-col items-center gap-1">
                                        <div class="w-full h-7 rounded border-2 transition" style="background:#0D1117;{{ $roomGradientTheme === '' ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                                        <span class="text-xs" style="color:{{ $roomGradientTheme === '' ? '#E6EDF3' : '#8B949E' }};">None</span>
                                    </button>
                                    @foreach ($freeGradients as $key => $gradient)
                                        <button type="button" wire:click="setRoomGradient('{{ $key }}')" class="flex flex-col items-center gap-1">
                                            <div class="w-full h-7 rounded border-2 transition" style="background:{{ $gradient['css'] }};{{ $roomGradientTheme === $key ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                                            <span class="text-xs text-center leading-tight" style="color:{{ $roomGradientTheme === $key ? '#E6EDF3' : '#8B949E' }};">{{ $gradient['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Supporter atmospheres --}}
                            <div class="pt-2 border-t" style="border-color:#21262D;">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <p class="text-xs" style="color:#3d4451;">Supporter</p>
                                    @if ($hasLockedAtmosphere)
                                        <a href="{{ route('support') }}" wire:navigate class="text-xs transition" style="color:#1D9E75;" onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'">Learn more</a>
                                    @endif
                                </div>
                                <div class="grid grid-cols-4 gap-1.5">
                                    @foreach ($supporterGradients as $key => $gradient)
                                        @if (! $hasLockedAtmosphere)
                                            <button type="button" wire:click="setRoomGradient('{{ $key }}')" class="flex flex-col items-center gap-1">
                                                <div class="w-full h-7 rounded border-2 transition" style="background:{{ $gradient['css'] }};{{ $roomGradientTheme === $key ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                                                <span class="text-xs text-center leading-tight" style="color:{{ $roomGradientTheme === $key ? '#E6EDF3' : '#8B949E' }};">{{ $gradient['label'] }}</span>
                                            </button>
                                        @else
                                            <div class="flex flex-col items-center gap-1 cursor-default">
                                                <div class="w-full h-7 rounded border-2 relative overflow-hidden" style="background:{{ $gradient['css'] }};border-color:#21262D;opacity:0.40;">
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <svg width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="#8B949E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="1"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
                                                    </div>
                                                </div>
                                                <span class="text-xs text-center leading-tight" style="color:#3d4451;">{{ $gradient['label'] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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

        {{-- Conversation prompt card --}}
        @if ($this->enabledPrompts)
            <div
                x-data="{
                    prompts: {{ \Illuminate\Support\Js::from($this->enabledPrompts) }},
                    storageKey: {{ \Illuminate\Support\Js::from('prompts_' . $conversationId) }},
                    current: '',
                    dismissed: false,
                    idx: 0,
                    init() {
                        this.dismissed = sessionStorage.getItem(this.storageKey) === '1';
                        if (this.prompts.length > 0) {
                            this.idx = Math.floor(Math.random() * this.prompts.length);
                            this.current = this.prompts[this.idx];
                        }
                    },
                    next() {
                        if (this.prompts.length <= 1) return;
                        let n = this.idx;
                        while (n === this.idx) n = Math.floor(Math.random() * this.prompts.length);
                        this.idx = n;
                        this.current = this.prompts[n];
                    },
                    dismiss() {
                        sessionStorage.setItem(this.storageKey, '1');
                        this.dismissed = true;
                    }
                }"
                x-show="!dismissed"
                class="flex items-start gap-3 px-4 py-2.5 border-b flex-none"
                style="border-color:#21262D;background:rgba(22,27,34,0.6);"
            >
                <div class="flex-1 min-w-0">
                    <p class="text-xs mb-0.5" style="color:#21262D;">Conversation starter</p>
                    <p class="text-sm leading-snug" style="color:#C9D1D9;" x-text="current"></p>
                </div>
                <div class="flex items-center gap-0.5 flex-none mt-0.5">
                    <button type="button" @click="next()"
                        class="w-6 h-6 flex items-center justify-center rounded transition text-sm"
                        style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        title="New prompt"
                        aria-label="New prompt"
                    >↻</button>
                    <button type="button" @click="dismiss()"
                        class="w-6 h-6 flex items-center justify-center rounded transition text-sm"
                        style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        title="Dismiss"
                        aria-label="Dismiss prompt"
                    >×</button>
                </div>
            </div>
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
            class="cg-chat-messages flex-1 overflow-y-auto px-4 py-4"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
            x-on:message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
            x-on:message-received.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
        >
            {{-- Quiet room empty state --}}
            @if ($this->chatMessages->isEmpty())
                <div class="h-full flex flex-col items-center justify-center text-center gap-2 pb-8">
                    <p class="text-sm" style="color:#3d4451;">Quiet room right now.</p>
                    <p class="text-xs" style="color:#21262D;">No rush — messages can start whenever they're ready.</p>
                </div>
            @endif

            @foreach ($this->chatMessages as $message)
                @php
                    $isMine         = $message->user_id === auth()->id();
                    $reactionCounts = $message->reactions->groupBy('reaction')->map->count();
                    $myReactions    = $message->reactions->where('user_id', auth()->id())->pluck('reaction');
                    $canPin         = $this->conversation->created_by === auth()->id() || auth()->user()->is_admin;
                    $prevMsg        = $loop->index > 0 ? $this->chatMessages[$loop->index - 1] : null;
                    $isGrouped      = $prevMsg
                        && $prevMsg->user_id === $message->user_id
                        && $message->created_at->diffInMinutes($prevMsg->created_at) <= 5;
                    $topMargin      = $loop->first ? '0.25rem' : ($isGrouped ? '0.2rem' : '0.75rem');
                @endphp
                <div
                    id="msg-{{ $message->id }}"
                    wire:key="rmsg-{{ $message->id }}"
                    x-data="{ reactOpen: false, pickerOpen: false, _lp: null }"
                    @mouseenter="reactOpen = true"
                    @mouseleave="if (!pickerOpen) reactOpen = false"
                    @focusin="reactOpen = true"
                    @focusout="setTimeout(() => { if (!$el.contains(document.activeElement)) { reactOpen = false; pickerOpen = false; } }, 150)"
                    @click.outside="reactOpen = false; pickerOpen = false"
                    @class(['flex flex-col', 'items-end' => $isMine, 'items-start' => !$isMine])
                    style="margin-top:{{ $topMargin }};"
                >
                    {{-- Bubble + tray wrapper (relative context for tray/picker) --}}
                    <div class="relative">
                        <div class="cg-msg-bubble max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl text-sm space-y-0.5"
                            style="{{ $isMine ? 'background:#1D9E75;color:#fff;' : 'background:#1C2333;color:#E6EDF3;' }}"
                            @touchstart.passive="_lp = setTimeout(() => { reactOpen = true }, 500)"
                            @touchend.passive="clearTimeout(_lp)"
                            @touchmove.passive="clearTimeout(_lp)"
                        >
                            @if (!$isMine && !$isGrouped)
                                @if ($message->user)
                                    <x-user-name :user="$message->user" class="text-xs font-semibold" style="color:#8B949E;" />
                                @else
                                    <p class="text-xs font-semibold" style="color:#8B949E;">{{ $message->author_name }}</p>
                                @endif
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

                            @if (!$isGrouped)
                                <p class="text-xs opacity-50">{{ $message->created_at->format('H:i') }}</p>
                            @endif
                        </div>

                        @if (!$hideReactions)
                            {{-- Reaction tray: floats at the bottom edge of the bubble --}}
                            <div
                                x-show="reactOpen || pickerOpen"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-10 flex items-center gap-0.5 px-1.5 py-1 rounded-full cg-reaction-tray"
                                style="display:none;bottom:-1.1rem;background:#1C2333;border:1px solid #30363D;box-shadow:0 2px 8px rgba(0,0,0,0.3);"
                                @class(['right-1.5' => $isMine, 'left-1.5' => !$isMine])
                            >
                                @foreach (\App\Models\MessageReaction::TRAY as $emoji)
                                    <button
                                        type="button"
                                        wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                                        @click="reactOpen = false; pickerOpen = false"
                                        class="text-base leading-none px-1 py-0.5 rounded transition"
                                        style="background:transparent;{{ $myReactions->contains($emoji) ? 'filter:drop-shadow(0 0 4px rgba(29,158,117,0.7));' : '' }}"
                                        onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'"
                                        aria-label="{{ $emoji }}"
                                        title="{{ $emoji }}"
                                    >{{ $emoji }}</button>
                                @endforeach
                                <span class="text-xs select-none" style="color:#2d3340;margin:0 2px;">│</span>
                                <button
                                    type="button"
                                    @click.stop="pickerOpen = !pickerOpen"
                                    class="text-xs px-1.5 py-0.5 rounded-full transition font-medium"
                                    style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    aria-label="More reactions"
                                    :aria-expanded="pickerOpen.toString()"
                                >+</button>
                            </div>

                            {{-- Full curated picker: floats above the tray --}}
                            <div
                                x-show="pickerOpen"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-20 p-2.5 rounded-xl cg-reaction-picker"
                                style="display:none;background:#161B22;border:1px solid #30363D;box-shadow:0 8px 24px rgba(0,0,0,0.5);bottom:2.5rem;min-width:11rem;"
                                @class(['right-0' => $isMine, 'left-0' => !$isMine])
                            >
                                @foreach (\App\Models\MessageReaction::PICKER_GROUPS as $groupLabel => $groupEmojis)
                                    <p class="text-xs mb-1.5 {{ $loop->first ? '' : 'mt-2.5' }}" style="color:#3d4451;">{{ $groupLabel }}</p>
                                    <div class="flex gap-0.5">
                                        @foreach ($groupEmojis as $emoji)
                                            <button
                                                type="button"
                                                wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                                                @click="pickerOpen = false; reactOpen = false"
                                                class="text-xl px-1.5 py-1 rounded-lg transition"
                                                style="background:transparent;line-height:1;{{ $myReactions->contains($emoji) ? 'filter:drop-shadow(0 0 4px rgba(29,158,117,0.7));' : '' }}"
                                                onmouseover="this.style.background='rgba(29,158,117,0.10)'" onmouseout="this.style.background='transparent'"
                                                aria-label="{{ $emoji }}"
                                                title="{{ $emoji }}"
                                            >{{ $emoji }}</button>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Reaction pills: mt-4 clears the floating tray --}}
                    @if ($reactionCounts->isNotEmpty() && !$hideReactions)
                        <div class="flex flex-wrap gap-1 mt-4">
                            @foreach ($reactionCounts as $emoji => $count)
                                <button
                                    type="button"
                                    wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                                    class="text-xs px-1.5 py-0.5 rounded-full border transition leading-none"
                                    style="{{ $myReactions->contains($emoji) ? 'background:rgba(29,158,117,0.18);border-color:rgba(29,158,117,0.5);color:#E6EDF3;' : 'background:rgba(22,27,34,0.8);border-color:#30363D;color:#E6EDF3;' }}"
                                    aria-label="{{ $emoji }} reaction{{ $count > 1 ? ', '.$count.' people' : '' }}"
                                >{{ $emoji }}{{ $count > 1 ? ' '.$count : '' }}</button>
                            @endforeach
                        </div>
                    @else
                        {{-- Small spacer so the floating tray doesn't bleed into the next message --}}
                        <div class="h-3 flex-none"></div>
                    @endif

                    {{-- Pin button: appears with the tray on hover --}}
                    @if ($canPin && !$message->is_pinned)
                        <button
                            type="button"
                            wire:click="pinMessage('{{ $message->id }}')"
                            x-show="reactOpen || pickerOpen"
                            class="mt-0.5 text-xs px-1 transition"
                            style="display:none;color:#8B949E;"
                            onmouseover="this.style.color='#D29922'" onmouseout="this.style.color='#8B949E'"
                            aria-label="Pin message"
                        >📌 pin</button>
                    @endif
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
            <p x-show="typingUser" x-text="typingUser + ' is writing…'" class="text-xs italic" style="color:#8B949E;"></p>
        </div>

        {{-- Crisis banner --}}
        @include('livewire.partials.crisis-banner')

        {{-- Compose --}}
        @php $isExpiredHangout = $this->conversation->hangoutPost && !$this->conversation->hangoutPost->is_persistent && $this->conversation->hangoutPost->isExpired(); @endphp
        <div class="px-4 pb-4 pt-2 border-t flex-none" style="border-color:#30363D;">
            @if ($verificationBlock)
                <p class="mb-1.5 text-xs" style="color:#C9A83C;">{{ $verificationBlock }}</p>
            @endif
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
