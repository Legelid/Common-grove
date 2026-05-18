<div class="px-5 py-8 max-w-lg mx-auto space-y-5">

    <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Messages</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 rounded-lg p-1" style="background:#161B22;">
        <button
            type="button"
            wire:click="$set('activeTab', 'messages')"
            class="flex-1 py-2 text-sm font-semibold rounded-md transition"
            style="{{ $activeTab === 'messages' ? 'background:#21262D;color:#E6EDF3;' : 'color:#8B949E;' }}"
        >Messages</button>
        <button
            type="button"
            wire:click="$set('activeTab', 'requests')"
            class="flex-1 py-2 text-sm font-semibold rounded-md transition flex items-center justify-center gap-2"
            style="{{ $activeTab === 'requests' ? 'background:#21262D;color:#E6EDF3;' : 'color:#8B949E;' }}"
        >
            Requests
            @if ($this->requests->isNotEmpty())
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold" style="background:#1D9E75;color:#fff;">
                    {{ $this->requests->count() }}
                </span>
            @endif
        </button>
    </div>

    {{-- Messages tab --}}
    @if ($activeTab === 'messages')
        @forelse ($this->conversations as $convo)
            @php
                $other    = $convo->participants->firstWhere('id', '!=', auth()->id());
                $label    = $convo->isRoom() ? ($convo->name ?? 'Room') : ($other?->display_name ?? 'Unknown');
                $lastMsg  = $convo->latestMessage;
                $myPivot  = $convo->participants->find(auth()->id())?->pivot;
                $isUnread = $lastMsg && ($myPivot?->last_read_at === null || $lastMsg->created_at->isAfter($myPivot->last_read_at));
            @endphp
            <a
                href="{{ $convo->isRoom() ? route('room.show', $convo->id) : route('messages.show', $convo->id) }}"
                wire:navigate
                wire:key="convo-{{ $convo->id }}"
                class="flex items-center gap-4 p-4 rounded-xl border transition"
                style="background:#161B22;border-color:#30363D;"
                onmouseover="this.style.background='#21262D'" onmouseout="this.style.background='#161B22'"
            >
                <div class="flex-none w-2.5 h-2.5 rounded-full" style="background:{{ $isUnread ? '#1D9E75' : 'transparent' }};"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold truncate" style="color:{{ $isUnread ? '#E6EDF3' : '#8B949E' }};">
                            {{ $label }}
                        </span>
                        @if ($lastMsg)
                            <span class="text-xs flex-none ml-2" style="color:#8B949E;">{{ $lastMsg->created_at->diffForHumans(short: true) }}</span>
                        @endif
                    </div>
                    @if ($lastMsg)
                        <p class="text-xs truncate mt-0.5" style="color:#8B949E;">{{ Str::limit($lastMsg->content, 60) }}</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-center text-sm py-10" style="color:#8B949E;">@tone('empty_messages', 'No conversations yet.')</p>
        @endforelse
    @endif

    {{-- Requests tab --}}
    @if ($activeTab === 'requests')
        @forelse ($this->requests as $convo)
            @php
                $requester = $convo->participants->firstWhere('id', '!=', auth()->id());
                $lastMsg   = $convo->latestMessage;
            @endphp
            <a
                href="{{ route('messages.show', $convo->id) }}"
                wire:navigate
                wire:key="req-{{ $convo->id }}"
                class="flex items-center gap-4 p-4 rounded-xl border transition"
                style="background:#161B22;border-color:rgba(210,153,34,0.3);"
                onmouseover="this.style.background='#21262D'" onmouseout="this.style.background='#161B22'"
            >
                <div class="flex-none w-2.5 h-2.5 rounded-full" style="background:#D29922;"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:#E6EDF3;">{{ $requester?->display_name ?? 'Unknown' }}</p>
                    @if ($lastMsg)
                        <p class="text-xs truncate mt-0.5" style="color:#8B949E;">{{ Str::limit($lastMsg->content, 60) }}</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-center text-sm py-10" style="color:#8B949E;">No pending requests.</p>
        @endforelse
    @endif

</div>
