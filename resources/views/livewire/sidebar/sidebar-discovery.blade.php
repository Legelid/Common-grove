<div class="space-y-3">
    @if ($this->isEnabled)

        {{-- Meet People --}}
        @if ($this->suggestedPeople->isNotEmpty())
            <div class="rounded-2xl border p-4 space-y-3"
                style="background:#1C2333;border-color:#2D333B;box-shadow:0 2px 8px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.04) inset;">

                <div class="pb-2 border-b" style="border-color:#30363D;">
                    <p class="text-sm font-medium" style="color:#C9D1D9;">Meet People</p>
                    <p class="text-xs mt-0.5 leading-snug" style="color:#8B949E;">People you might get along with</p>
                </div>

                @if ($flash)
                    <p class="text-xs" style="color:#1D9E75;">{{ $flash }}</p>
                @endif

                <div class="space-y-2.5">
                    @foreach ($this->suggestedPeople as $person)
                        <div class="flex items-center gap-2.5">
                            <img
                                src="{{ $person->avatar_url }}"
                                alt=""
                                class="w-7 h-7 rounded-full object-cover flex-none"
                                style="background:#21262D;"
                            >
                            <div class="flex-1 min-w-0">
                                <a
                                    href="{{ route('profile.show', $person->gamertag) }}"
                                    wire:navigate
                                    class="block text-xs font-medium truncate leading-snug transition"
                                    style="color:#C9D1D9;"
                                    onmouseover="this.style.color='#1D9E75'" onmouseout="this.style.color='#C9D1D9'"
                                >{{ $person->gamertag }}</a>
                                <p class="text-xs leading-snug" style="color:#8B949E;">{{ $person->shared_tag_count }} in common</p>
                            </div>
                            <button
                                type="button"
                                wire:click="sendRequest('{{ $person->id }}')"
                                class="flex-none w-6 h-6 text-xs flex items-center justify-center rounded-lg transition"
                                style="color:#8B949E;border:1px solid #30363D;"
                                onmouseover="this.style.borderColor='rgba(29,158,117,0.5)';this.style.color='#1D9E75';"
                                onmouseout="this.style.borderColor='#30363D';this.style.color='#8B949E';"
                                title="Add {{ $person->gamertag }}"
                            >+</button>
                        </div>
                    @endforeach
                </div>

                <a
                    href="{{ route('friends.index') }}"
                    wire:navigate
                    class="block text-xs transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                >See more →</a>

            </div>
        @endif

        {{-- Find Rooms --}}
        @if ($this->suggestedRooms->isNotEmpty() || $flash)
            <div class="rounded-2xl border p-4 space-y-3"
                style="background:#1C2333;border-color:#2D333B;box-shadow:0 2px 8px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.04) inset;">

                <div class="pb-2 border-b" style="border-color:#30363D;">
                    <p class="text-sm font-medium" style="color:#C9D1D9;">Find Rooms</p>
                    <p class="text-xs mt-0.5 leading-snug" style="color:#8B949E;">Rooms that fit your interests</p>
                </div>

                @if ($flash && $this->suggestedRooms->isEmpty())
                    <p class="text-xs" style="color:#8B949E;">{{ $flash }}</p>
                @endif

                <div class="space-y-1">
                    @foreach ($this->suggestedRooms as $post)
                        <button
                            type="button"
                            wire:click="joinRoom('{{ $post->id }}')"
                            class="w-full text-left px-2.5 py-2 rounded-xl transition focus:outline-none focus-visible:ring-2"
                            onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background=''"
                        >
                            <div class="flex items-center gap-1.5 mb-0.5">
                                @if ($post->is_persistent)
                                    <span class="text-xs px-1.5 py-px rounded-full font-medium flex-none"
                                        style="background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.25);">Room</span>
                                    <span class="text-xs" style="color:#8B949E;">Always open</span>
                                @else
                                    <span class="text-xs px-1.5 py-px rounded-full font-medium flex-none"
                                        style="background:rgba(210,153,34,0.12);color:#D29922;border:1px solid rgba(210,153,34,0.25);">Hangout</span>
                                    <span class="text-xs" style="color:#8B949E;">
                                        @if ($post->expires_at && $post->expires_at->diffInMinutes(now()) <= 60)
                                            Closes soon
                                        @else
                                            Closes in {{ $post->expiresInFormatted() }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs leading-snug" style="color:#C9D1D9;">{{ Str::limit($post->content, 55) }}</p>
                            @if ($post->tags->isNotEmpty())
                                <p class="text-xs truncate mt-0.5" style="color:#8B949E;">
                                    {{ $post->tags->pluck('name')->take(2)->join(' · ') }}
                                </p>
                            @endif
                        </button>
                    @endforeach
                </div>

                <a
                    href="{{ route('feed') }}"
                    wire:navigate
                    class="block text-xs transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                >See more →</a>

            </div>
        @endif

    @endif
</div>
