<div class="px-6 py-8 max-w-2xl mx-auto">

    {{-- Flash --}}
    @if ($flash)
        <div class="mb-4 px-3 py-2.5 rounded-lg text-sm border" style="background:rgba(29,158,117,0.1);border-color:rgba(29,158,117,0.4);color:#1D9E75;">
            {{ $flash }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b" style="border-color:#30363D;">
        <button wire:click="switchTab('friends')"
            class="pb-2 text-sm font-medium -mb-px border-b-2 transition"
            style="{{ $activeTab === 'friends' ? 'border-color:#1D9E75;color:#1D9E75;' : 'border-color:transparent;color:#8B949E;' }}"
        >
            Friends
            @if ($this->onlineFriends->count() + $this->offlineFriends->count() > 0)
                <span class="ml-1 text-xs" style="color:#8B949E;">({{ $this->onlineFriends->count() + $this->offlineFriends->count() }})</span>
            @endif
        </button>
        <button wire:click="switchTab('requests')"
            class="pb-2 text-sm font-medium -mb-px border-b-2 transition"
            style="{{ $activeTab === 'requests' ? 'border-color:#1D9E75;color:#1D9E75;' : 'border-color:transparent;color:#8B949E;' }}"
        >
            Requests
            @if ($this->pendingRequests->count() > 0)
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full font-semibold" style="background:rgba(29,158,117,0.15);color:#1D9E75;">{{ $this->pendingRequests->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Friends tab --}}
    @if ($activeTab === 'friends')

        {{-- Weekly match suggestion (Group 11) --}}
        @if ($this->weeklyMatch)
            @php
                $match = $this->weeklyMatch;
                $sharedCount = $match->matchedUser->tags->pluck('id')->intersect(auth()->user()->tags()->pluck('tags.id'))->count();
            @endphp
            <div class="cg-suggestion-preview mb-6 p-4 rounded-xl border" style="background:rgba(29,158,117,0.08);border-color:rgba(29,158,117,0.3);">
                <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#1D9E75;">Suggested this week</p>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm" style="color:#E6EDF3;">{{ $match->matchedUser->gamertag }}</p>
                        @if ($sharedCount > 0)
                            <p class="text-xs" style="color:#8B949E;">{{ $sharedCount }} {{ Str::plural('interest', $sharedCount) }} in common</p>
                        @endif
                    </div>
                    <button wire:click="sendFriendRequestToMatch"
                        class="text-sm font-semibold px-3 py-1.5 rounded-lg transition"
                        style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                    >Send friend request</button>
                </div>
            </div>
        @endif

        @if ($this->onlineFriends->isEmpty() && $this->offlineFriends->isEmpty())
            <div class="space-y-1 py-2">
                <p class="text-sm" style="color:#8B949E;">@tone('empty_friends', 'No friends yet.')</p>
                <p class="text-sm" style="color:#3d4451;">That's okay — these things take time.</p>
            </div>
        @else
            {{-- Online friends --}}
            @foreach ($this->onlineFriends as $friend)
                <div class="flex items-center justify-between py-3 border-b" style="border-color:#30363D;">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ $friend->avatar_url }}" alt="" class="w-10 h-10 rounded-full" style="background:#1C2333;">
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2" style="background:#1D9E75;border-color:#0D1117;" title="Online"></span>
                        </div>
                        <div>
                            <p class="font-medium text-sm" style="color:#E6EDF3;">{{ $this->visibleName($friend) }}</p>
                            @if ($friend->hasActiveStatus())
                                <p class="text-xs" style="color:#8B949E;">
                                    @if ($friend->status_mood)<span class="capitalize">{{ $friend->status_mood }}</span>@if ($friend->status_text) · @endif@endif
                                    {{ $friend->status_text }}
                                </p>
                            @else
                                <p class="text-xs" style="color:#1D9E75;">Online now</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show', $friend->gamertag) }}" class="text-xs underline transition" style="color:#1D9E75;">View profile</a>
                        <button wire:click="unfriend('{{ $friend->id }}')" wire:confirm="Remove {{ $friend->gamertag }} from your friends?"
                            class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                        >Unfriend</button>
                    </div>
                </div>
            @endforeach

            {{-- Offline friends --}}
            @foreach ($this->offlineFriends as $friend)
                <div class="flex items-center justify-between py-3 border-b" style="border-color:#30363D;">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ $friend->avatar_url }}" alt="" class="w-10 h-10 rounded-full" style="background:#1C2333;">
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2" style="background:#30363D;border-color:#0D1117;" title="Offline"></span>
                        </div>
                        <div>
                            <p class="font-medium text-sm inline-flex items-center gap-1" style="color:#E6EDF3;">
                                <span>{{ $this->visibleName($friend) }}</span>
                                @if ($friend->is_supporter && ($friend->show_supporter_icon ?? true))<x-supporter-icon />@endif
                            </p>
                            @if ($friend->hasActiveStatus())
                                <p class="text-xs" style="color:#8B949E;">
                                    @if ($friend->status_mood)<span class="capitalize">{{ $friend->status_mood }}</span>@if ($friend->status_text) · @endif@endif
                                    {{ $friend->status_text }}
                                </p>
                            @elseif ($friend->last_seen_at)
                                <p class="text-xs" style="color:#8B949E;">Last seen {{ $friend->last_seen_at->diffForHumans() }}</p>
                            @else
                                <p class="text-xs" style="color:#8B949E;">Offline</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show', $friend->gamertag) }}" class="text-xs underline transition" style="color:#1D9E75;">View profile</a>
                        <button wire:click="unfriend('{{ $friend->id }}')" wire:confirm="Remove {{ $friend->gamertag }} from your friends?"
                            class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                        >Unfriend</button>
                    </div>
                </div>
            @endforeach
        @endif
    @endif

    {{-- Requests tab --}}
    @if ($activeTab === 'requests')
        @if ($this->pendingRequests->isEmpty())
            <div class="space-y-1 py-2">
                <p class="text-sm" style="color:#8B949E;">No requests right now.</p>
                <p class="text-sm" style="color:#3d4451;">Nothing needs your attention.</p>
            </div>
        @else
            @foreach ($this->pendingRequests as $friendship)
                <div class="flex items-center justify-between py-3 border-b" style="border-color:#30363D;">
                    <div class="flex items-center gap-3">
                        <img src="{{ $friendship->requester->avatar_url }}" alt="" class="w-10 h-10 rounded-full" style="background:#1C2333;">
                        <div>
                            <p class="font-medium text-sm" style="color:#E6EDF3;">{{ $friendship->requester->gamertag }}</p>
                            <p class="text-xs" style="color:#8B949E;">Sent {{ $friendship->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="accept('{{ $friendship->id }}')"
                            class="text-sm font-semibold px-3 py-1.5 rounded-lg transition"
                            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                        >Accept</button>
                        <button wire:click="decline('{{ $friendship->id }}')"
                            class="text-sm font-semibold px-3 py-1.5 rounded-lg border transition"
                            style="border-color:#30363D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                        >Decline</button>
                    </div>
                </div>
            @endforeach
        @endif
    @endif

</div>
