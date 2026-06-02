<div class="flex h-full" x-data="{ showParticipants: false }">

    {{-- Participant sidebar — desktop only --}}
    @php $activeParticipants = $this->conversation->participants->filter(fn($p) => !$p->pivot->left_at); @endphp
    <aside class="hidden md:flex md:flex-col w-48 flex-none border-r" style="background:#161B22;border-color:#30363D;">
        <div class="px-3 py-3 border-b" style="border-color:#30363D;">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">
                In this room · {{ $activeParticipants->count() }}
            </p>
        </div>
        <div class="flex-1 overflow-y-auto py-2 space-y-0.5">
            @foreach ($activeParticipants as $participant)
                @if (auth()->check())
                    <a
                        href="{{ route('profile.show', $participant->gamertag) }}"
                        wire:navigate
                        class="flex items-center gap-2 px-3 py-1.5 rounded transition"
                        style="color:#8B949E;" onmouseover="this.style.background='#21262D'" onmouseout="this.style.background=''"
                    >
                        <x-avatar :user="$participant" size="xs" />
                        <x-user-name :user="$participant" class="text-xs truncate" style="color:#E6EDF3;" />
                        @if ($participant->isOnline())
                            <span class="ml-auto w-1.5 h-1.5 rounded-full flex-none" style="background:#1D9E75;"></span>
                        @endif
                    </a>
                @else
                    <div class="flex items-center gap-2 px-3 py-1.5">
                        <span class="w-6 h-6 rounded-full flex-none" style="background:#21262D;"></span>
                        <span class="text-xs truncate" style="color:#6B737C;">Someone</span>
                    </div>
                @endif
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

        @if(auth()->check() && auth()->user()->isSupporter())
            <div class="px-3 py-2 border-t" style="border-color:#30363D;">
                <livewire:rooms.add-to-collection :conversationId="$conversationId" :key="'atc-'.$conversationId" />
            </div>
        @endif

        @if (auth()->check())
            <div class="px-3 py-2 border-t" style="border-color:#30363D;">
                <button
                    type="button"
                    wire:click="leaveQuietly"
                    wire:confirm="Leave this room quietly? No one will be notified."
                    class="w-full text-xs py-1 transition"
                    style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                >Leave quietly</button>
            </div>
        @endif
    </aside>

    {{-- Chat area --}}
    @php
        $isLowStim          = auth()->user()?->low_stimulation_mode;
        $cgAdvanced         = auth()->user()?->advanced_comfort_settings ?? [];
        $cgHideGradients    = in_array('hide_gradients', $cgAdvanced, true);
        $hideReactions      = auth()->check() && (bool) (auth()->user()?->hide_reactions ?? false);
        $roomGradientDef    = (!$isLowStim && !$cgHideGradients && $roomGradientTheme)
            ? config('gradients.' . $roomGradientTheme)
            : null;
        $chatAreaBg = $roomGradientDef
            ? 'background:' . $roomGradientDef['css'] . ';'
            : '';
    @endphp
    <div class="flex flex-col flex-1 min-w-0" style="{{ $chatAreaBg }}">

        {{-- Header --}}
        <div class="flex items-center gap-2 px-4 py-3 border-b flex-none" style="background:#161B22;border-color:#30363D;">
            <a href="{{ auth()->check() ? route('messages.index') : route('feed') }}" wire:navigate class="transition flex-none" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">←</a>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate" style="color:#E6EDF3;">{{ $this->conversation->name ?? 'Hangout Room' }}</p>
                <p class="hidden md:block text-xs" style="color:#8B949E;">{{ $activeParticipants->count() }} participants</p>
            </div>

            {{-- Mobile: People button --}}
            <button
                type="button"
                @click="showParticipants = true"
                class="md:hidden flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg transition flex-none"
                style="color:#8B949E;border:1px solid #30363D;"
                onmouseover="this.style.color='#C9D1D9';this.style.borderColor='#3d4451'"
                onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D'"
            >
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                {{ $activeParticipants->count() }}
            </button>

            {{-- Desktop: Room pin --}}
            <div class="hidden md:flex md:flex-col md:items-end gap-0.5 flex-none">
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

            {{-- Desktop: Room appearance panel (owner / admin only) --}}
            @if ($this->isRoomOwner)
                <div
                    class="hidden md:block flex-none relative"
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

                @if ($this->canDeleteRoom)
                    <button
                        type="button"
                        wire:click="deleteRoom"
                        wire:confirm="Delete this room? This will permanently remove it and all its messages for everyone."
                        class="hidden md:flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg transition flex-none"
                        style="color:#E24B4A;border:1px solid rgba(226,75,74,0.25);"
                        onmouseover="this.style.background='rgba(226,75,74,0.08)'" onmouseout="this.style.background=''"
                    >
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        Delete room
                    </button>
                @endif
            @endif
        </div>

        {{-- Hangout type notice --}}
        @if ($this->conversation->hangoutPost)
            @php
                $hangoutPost = $this->conversation->hangoutPost;
                if ($hangoutPost->is_official) {
                    $noticeBadge  = 'CommonGrove room';
                    $noticeBadgeSt = 'background:rgba(29,158,117,0.08);color:#1D9E75;border:1px solid rgba(29,158,117,0.15);';
                    $noticeBg     = 'border-color:#21262D;background:rgba(29,158,117,0.04);';
                    $noticeDetail = 'An always-open starter space made by CommonGrove.';
                    $noticeExtra  = 'Messages here don\'t stick around forever — just long enough to keep things flowing.';
                } elseif ($hangoutPost->is_persistent) {
                    $noticeBadge  = 'Always-open room';
                    $noticeBadgeSt = 'background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.2);';
                    $noticeBg     = 'border-color:#21262D;background:rgba(29,158,117,0.04);';
                    $noticeDetail = 'This room stays open.';
                    $noticeExtra  = 'Messages here don\'t stick around forever — just long enough to keep things flowing.';
                } elseif ($hangoutPost->isExpired()) {
                    $noticeBadge  = 'Temporary hangout';
                    $noticeBadgeSt = 'background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.2);';
                    $noticeBg     = 'border-color:#21262D;background:rgba(210,153,34,0.04);';
                    $noticeDetail = 'This hangout has ended.';
                    $noticeExtra  = null;
                } else {
                    $noticeBadge  = 'Temporary hangout';
                    $noticeBadgeSt = 'background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.2);';
                    $noticeBg     = 'border-color:#21262D;background:rgba(210,153,34,0.04);';
                    $noticeDetail = 'This hangout closes in ' . $hangoutPost->expiresInFormatted() . '.';
                    $noticeExtra  = null;
                }
            @endphp
            <div x-data="{ showRoomInfo: false }">

                {{-- Mobile: badge only + info icon --}}
                <div class="md:hidden flex items-center gap-2 px-4 py-1.5 border-b flex-none" style="{{ $noticeBg }}">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-none" style="{{ $noticeBadgeSt }}">{{ $noticeBadge }}</span>
                    <button
                        type="button"
                        @click="showRoomInfo = true"
                        class="ml-auto w-6 h-6 flex items-center justify-center rounded-full transition flex-none"
                        style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        aria-label="Room info"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </button>
                </div>

                {{-- Desktop: full banner unchanged --}}
                <div class="hidden md:flex items-center gap-2 px-4 py-2 border-b flex-none" style="{{ $noticeBg }}">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-none" style="{{ $noticeBadgeSt }}">{{ $noticeBadge }}</span>
                    <span class="text-xs" style="color:#8B949E;">{{ $noticeDetail }}</span>
                    @if ($noticeExtra)
                        <span class="ml-auto text-xs" style="color:#3d4451;">{{ $noticeExtra }}</span>
                    @endif
                </div>

                {{-- Mobile info bottom sheet --}}
                <div
                    x-show="showRoomInfo"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex flex-col justify-end md:hidden"
                    style="display:none;"
                >
                    <div class="absolute inset-0" @click="showRoomInfo = false" style="background:rgba(0,0,0,0.6);"></div>
                    <div class="relative rounded-t-2xl px-5 py-5 space-y-3" style="background:#161B22;border-top:1px solid #30363D;">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="{{ $noticeBadgeSt }}">{{ $noticeBadge }}</span>
                            <button type="button" @click="showRoomInfo = false"
                                class="w-7 h-7 flex items-center justify-center rounded-lg transition text-lg leading-none flex-none"
                                style="color:#8B949E;" aria-label="Close">×</button>
                        </div>
                        <p class="text-sm" style="color:#C9D1D9;">{{ $noticeDetail }}</p>
                        @if ($noticeExtra)
                            <p class="text-sm" style="color:#8B949E;">{{ $noticeExtra }}</p>
                        @endif
                    </div>
                </div>

            </div>
        @endif

        {{-- Guest preview notice — authenticated users in official rooms only, shown once --}}
        @if (auth()->check() && ($this->conversation->hangoutPost?->is_official ?? false))
            <div
                x-data="{ show: !localStorage.getItem('cg_guest_preview_notice') }"
                x-show="show"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="flex items-center gap-3 px-4 py-2 border-b flex-none"
                style="background:rgba(29,158,117,0.035);border-color:#1A2028;"
            >
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#3d4451" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-none" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="flex-1 text-xs leading-relaxed" style="color:#6B737C;">
                    Visitors previewing CommonGrove can see the last 10 messages in this room before they sign up.
                </p>
                <button
                    type="button"
                    @click="show = false; localStorage.setItem('cg_guest_preview_notice', '1')"
                    class="flex-none text-xs px-2.5 py-1 rounded-lg border transition-colors duration-150"
                    style="color:#3d4451;border-color:#1E2730;"
                    onmouseover="this.style.color='#8B949E';this.style.borderColor='#30363D';"
                    onmouseout="this.style.color='#3d4451';this.style.borderColor='#1E2730';"
                    aria-label="Dismiss notice"
                >Got it</button>
            </div>
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
                class="flex items-start gap-2 px-4 py-1.5 md:py-2.5 border-b flex-none"
                style="border-color:#21262D;background:rgba(22,27,34,0.6);"
            >
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm leading-snug line-clamp-2 md:line-clamp-none" style="color:#C9D1D9;" x-text="current"></p>
                </div>
                <div class="flex items-center gap-0 flex-none mt-0.5">
                    <button type="button" @click="$wire.setPromptReply(current)"
                        class="w-7 h-6 flex items-center justify-center rounded transition text-xs"
                        style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        title="Reply to this prompt"
                        aria-label="Reply to this prompt"
                    >↩</button>
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
                                <p class="text-xs mb-0.5" style="color:#8B949E;">{{ auth()->check() ? $pinned->user?->gamertag : 'Someone' }}</p>
                                <p class="text-xs truncate" style="color:#E6EDF3;">{{ Str::limit($pinned->content, 100) }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-none">
                                <a href="#msg-{{ $pinned->id }}" class="text-xs underline transition" style="color:#1D9E75;">Jump</a>
                                @if (auth()->check() && ($this->conversation->created_by === auth()->id() || auth()->user()->is_admin))
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
            {{-- Guest preview label --}}
            @if (! auth()->check())
                <div class="flex items-center gap-2 mb-4 pb-2.5 border-b" style="border-color:#1A2028;">
                    <span class="text-xs italic" style="color:#3d4451;">A glimpse inside —</span>
                    <span class="text-xs" style="color:#252E3D;">last 10 messages</span>
                </div>
            @endif

            {{-- Empty state --}}
            @if ($this->chatMessages->isEmpty())
                @if (! auth()->check())
                    <div class="flex flex-col items-center justify-center text-center gap-2 py-12">
                        <p class="text-sm" style="color:#3d4451;">No one has said anything yet.</p>
                        <p class="text-xs" style="color:#21262D;">You could be the first.</p>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center gap-2 pb-8">
                        <p class="text-sm" style="color:#3d4451;">Quiet room right now.</p>
                        <p class="text-xs" style="color:#21262D;">No rush — messages can start whenever they're ready.</p>
                    </div>
                @endif
            @endif

            @foreach ($this->chatMessages as $message)
                @php
                    $isGuest        = ! auth()->check();
                    $isMine         = ! $isGuest && $message->user_id === auth()->id();
                    $reactionCounts = $message->reactions->groupBy('reaction')->map->count();
                    $myReactions    = $isGuest ? collect() : $message->reactions->where('user_id', auth()->id())->pluck('reaction');
                    $canPin         = ! $isGuest && ($this->conversation->created_by === auth()->id() || auth()->user()->is_admin);
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
                                @if ($isGuest)
                                    <p class="text-xs font-semibold" style="color:#8B949E;">Someone</p>
                                @elseif ($message->user)
                                    <x-user-name :user="$message->user" class="text-xs font-semibold" style="color:#8B949E;" />
                                @else
                                    <p class="text-xs font-semibold" style="color:#8B949E;">{{ $message->author_name }}</p>
                                @endif
                            @endif

                            {{-- Reply quote --}}
                            @if ($message->reply_to_message_id || $message->reply_to_prompt)
                                @php
                                    if ($message->reply_to_message_id) {
                                        $replyParent  = $message->replyToMessage;
                                        $quoteText    = $replyParent
                                            ? Str::limit($replyParent->content, 70)
                                            : null;
                                        $quoteSender  = $isGuest ? 'Someone' : ($replyParent?->user?->display_name ?? null);
                                    } else {
                                        $quoteText   = Str::limit($message->reply_to_prompt, 70);
                                        $quoteSender = null;
                                    }
                                @endphp
                                <div class="mb-1 rounded-md px-2.5 py-1.5 -mx-1"
                                    style="{{ $isMine
                                        ? 'background:rgba(0,0,0,0.18);border-left:2px solid rgba(255,255,255,0.25);'
                                        : 'background:rgba(0,0,0,0.18);border-left:2px solid #30363D;' }}"
                                >
                                    @if ($quoteText !== null)
                                        @if ($quoteSender)
                                            <p class="text-xs mb-0.5 font-medium" style="{{ $isMine ? 'color:rgba(255,255,255,0.55);' : 'color:#6B737C;' }}">↪ {{ $quoteSender }}</p>
                                        @else
                                            <p class="text-xs mb-0.5" style="{{ $isMine ? 'color:rgba(255,255,255,0.4);' : 'color:#3d4451;' }}">↪ prompt</p>
                                        @endif
                                        <p class="text-xs leading-snug truncate" style="{{ $isMine ? 'color:rgba(255,255,255,0.6);' : 'color:#8B949E;' }}">{{ $quoteText }}</p>
                                    @else
                                        <p class="text-xs italic" style="{{ $isMine ? 'color:rgba(255,255,255,0.35);' : 'color:#3d4451;' }}">↪ original message unavailable</p>
                                    @endif
                                </div>
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

                        @if (!$hideReactions && !$isGuest)
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
                                <span class="text-xs select-none" style="color:#2d3340;margin:0 2px;">│</span>
                                <button
                                    type="button"
                                    wire:click="setReply('{{ $message->id }}')"
                                    @click="reactOpen = false; pickerOpen = false"
                                    class="text-xs px-1.5 py-0.5 rounded-full transition"
                                    style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    aria-label="Reply to message"
                                    title="Reply"
                                >↩</button>
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
        @if (! auth()->check())
            {{-- Guest CTA — replaces the compose bar entirely --}}
            <div class="px-4 pb-5 pt-3 border-t flex-none" style="border-color:#30363D;">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 rounded-xl" style="background:#1C2333;border:1px solid #30363D;">
                    <p class="text-sm text-center sm:text-left" style="color:#8B949E;">Sign up to join the conversation</p>
                    <a
                        href="{{ route('register') }}"
                        class="flex-none px-5 py-2 text-sm font-semibold rounded-xl transition whitespace-nowrap"
                        style="background:#1D9E75;color:#fff;"
                        onmouseover="this.style.background='#1a9068'" onmouseout="this.style.background='#1D9E75'"
                    >Join the Conversation</a>
                </div>
            </div>
        @else
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

            {{-- Reply preview --}}
            @if ($replyingToMessageId || $replyingToPrompt)
                @php
                    if ($replyingToMessageId) {
                        $previewMsg    = $this->chatMessages->firstWhere('id', $replyingToMessageId);
                        $previewText   = $previewMsg ? Str::limit($previewMsg->content, 80) : '…';
                        $previewSender = $previewMsg?->user?->display_name ?? null;
                    } else {
                        $previewText   = Str::limit($replyingToPrompt, 80);
                        $previewSender = null;
                    }
                @endphp
                <div class="flex items-start gap-2 mb-2 px-3 py-2 rounded-lg" style="background:#1C2333;border-left:2px solid #30363D;">
                    <div class="flex-1 min-w-0">
                        @if ($previewSender)
                            <p class="text-xs mb-0.5" style="color:#8B949E;">↩ {{ $previewSender }}</p>
                        @else
                            <p class="text-xs mb-0.5" style="color:#3d4451;">↩ prompt</p>
                        @endif
                        <p class="text-xs truncate" style="color:#6B737C;">{{ $previewText }}</p>
                    </div>
                    <button type="button" wire:click="cancelReply"
                        class="w-5 h-5 flex items-center justify-center rounded transition text-base leading-none flex-none mt-0.5"
                        style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        aria-label="Cancel reply">×</button>
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
                    class="px-2 py-2 md:px-2.5 md:py-2.5 rounded-xl text-xs font-medium transition flex-none"
                    style="{{ $showCwInput ? 'background:rgba(210,153,34,0.25);color:#D29922;' : 'background:#21262D;color:#8B949E;' }}"
                >CW</button>
                <input
                    type="text"
                    wire:model="messageContent"
                    wire:keydown.debounce.500ms="broadcastTyping"
                    maxlength="2000"
                    placeholder="Message the room…"
                    class="flex-1 rounded-xl px-4 py-3 md:py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                    onfocus="this.style.borderColor='#1D9E75'" onblur="this.style.borderColor='#30363D'"
                    autocomplete="off"
                >
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-4 py-3 md:py-2.5 text-sm font-semibold rounded-xl transition disabled:opacity-50 flex-none"
                    style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                >Send</button>
            </form>
            @endif
        </div>
        @endif {{-- end auth()->check() compose block --}}

    </div>

    {{-- ── Mobile participants sheet ─────────────────────────────────────────── --}}
    <div
        x-show="showParticipants"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex md:hidden"
        style="display:none;"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0" @click="showParticipants = false" style="background:rgba(0,0,0,0.6);"></div>

        {{-- Sheet slides in from the right --}}
        <div class="absolute inset-y-0 right-0 w-72 flex flex-col" style="background:#161B22;border-left:1px solid #30363D;">

            {{-- Sheet header --}}
            <div class="flex items-center justify-between px-4 py-4 border-b flex-none" style="border-color:#21262D;">
                <p class="text-sm font-semibold" style="color:#E6EDF3;">In this room · {{ $activeParticipants->count() }}</p>
                <button
                    type="button"
                    @click="showParticipants = false"
                    class="w-8 h-8 flex items-center justify-center rounded-lg transition text-lg leading-none"
                    style="color:#8B949E;"
                    aria-label="Close"
                >×</button>
            </div>

            {{-- Participants list --}}
            <div class="flex-1 overflow-y-auto py-2 space-y-0.5">
                @foreach ($activeParticipants as $participant)
                    @if (auth()->check())
                        <a
                            href="{{ route('profile.show', $participant->gamertag) }}"
                            wire:navigate
                            @click="showParticipants = false"
                            class="flex items-center gap-3 px-4 py-2.5 transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.background='#21262D'" onmouseout="this.style.background=''"
                        >
                            <x-avatar :user="$participant" size="sm" />
                            <x-user-name :user="$participant" class="text-sm truncate" style="color:#E6EDF3;" />
                            @if ($participant->isOnline())
                                <span class="ml-auto w-1.5 h-1.5 rounded-full flex-none" style="background:#1D9E75;"></span>
                            @endif
                        </a>
                    @else
                        <div class="flex items-center gap-3 px-4 py-2.5">
                            <span class="w-8 h-8 rounded-full flex-none" style="background:#21262D;"></span>
                            <span class="text-sm truncate" style="color:#6B737C;">Someone</span>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Hangout timer --}}
            @if ($this->conversation->hangoutPost && !$this->conversation->hangoutPost->is_persistent)
                @if ($this->conversation->hangoutPost->isExpired())
                    <div class="px-4 py-3 border-t flex-none" style="border-color:#30363D;">
                        <p class="text-xs" style="color:#8B949E;">This hangout has ended.</p>
                    </div>
                @else
                    <div class="px-4 py-3 border-t flex-none" style="border-color:#30363D;">
                        <p class="text-xs" style="color:#D29922;">
                            Hangout closes in {{ $this->conversation->hangoutPost->expiresInFormatted() }}
                        </p>
                    </div>
                @endif
            @endif

            {{-- Collections (supporter only) --}}
            @if(auth()->check() && auth()->user()->isSupporter())
                <div class="px-4 py-3 border-t flex-none" style="border-color:#30363D;">
                    <livewire:rooms.add-to-collection :conversationId="$conversationId" :key="'atc-mobile-'.$conversationId" />
                </div>
            @endif

            {{-- Leave quietly (authenticated only) --}}
            @if (auth()->check())
                <div class="px-4 py-4 border-t flex-none" style="border-color:#30363D;">
                    <button
                        type="button"
                        wire:click="leaveQuietly"
                        wire:confirm="Leave this room quietly? No one will be notified."
                        @click="showParticipants = false"
                        class="w-full text-sm py-2.5 rounded-xl transition"
                        style="color:#8B949E;border:1px solid #30363D;"
                        onmouseover="this.style.color='#E24B4A';this.style.borderColor='rgba(226,75,74,0.3)'"
                        onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D'"
                    >Leave quietly</button>
                </div>
            @endif
        </div>
    </div>

</div>
