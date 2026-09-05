<div class="space-y-3">
    @if ($this->isEnabled)

        {{-- Meet People --}}
        @if ($this->suggestedPeople->isNotEmpty())
            <div class="rounded-card border border-border border-l-[3px] border-l-accent p-4 space-y-3"
                style="background:var(--surface);box-shadow:var(--card-shadow);">

                <div class="pb-2 border-b" style="border-color:var(--border);">
                    <p class="font-display text-sm font-medium" style="color:var(--text);">Meet People</p>
                    <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-muted);">People you might get along with</p>
                </div>

                @if ($flash)
                    <p class="text-xs" style="color:var(--accent);">{{ $flash }}</p>
                @endif

                <div class="space-y-2.5">
                    @foreach ($this->suggestedPeople as $person)
                        <div class="flex items-center gap-2.5">
                            <x-avatar :user="$person" size="sm" />
                            <div class="flex-1 min-w-0">
                                <a
                                    href="{{ route('profile.show', $person->gamertag) }}"
                                    wire:navigate
                                    class="block text-xs font-medium truncate leading-snug transition"
                                    style="color:var(--text);"
                                    onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'"
                                >{{ $person->gamertag }}</a>
                                <p class="text-xs leading-snug" style="color:var(--text-muted);">{{ $person->shared_tag_count }} in common</p>
                            </div>
                            <button
                                type="button"
                                wire:click="sendRequest('{{ $person->id }}')"
                                class="flex-none w-6 h-6 text-xs flex items-center justify-center rounded-lg transition"
                                style="color:var(--text-muted);border:1px solid var(--border);"
                                onmouseover="this.style.borderColor='rgba(var(--accent-rgb),0.5)';this.style.color='var(--accent)';"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)';"
                                title="Add {{ $person->gamertag }}"
                            >+</button>
                        </div>
                    @endforeach
                </div>

                <a
                    href="{{ route('friends.index') }}"
                    wire:navigate
                    class="block"
                    aria-label="See more"
                >
                    <x-arrow-icon label="See more" />
                </a>

            </div>
        @endif

        {{-- Find Rooms --}}
        @if ($this->suggestedRooms->isNotEmpty() || $flash)
            <div class="rounded-card border border-border border-l-[3px] border-l-accent p-4 space-y-3"
                style="background:var(--surface);box-shadow:var(--card-shadow);">

                <div class="pb-2 border-b" style="border-color:var(--border);">
                    <p class="font-display text-sm font-medium" style="color:var(--text);">Find Rooms</p>
                    <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-muted);">Rooms that fit your interests</p>
                </div>

                @if ($flash && $this->suggestedRooms->isEmpty())
                    <p class="text-xs" style="color:var(--text-muted);">{{ $flash }}</p>
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
                                        style="background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.25);">Room</span>
                                    <span class="text-xs" style="color:var(--text-muted);">Always open</span>
                                @else
                                    <span class="text-xs px-1.5 py-px rounded-full font-medium flex-none"
                                        style="background:rgba(210,153,34,0.12);color:#D29922;border:1px solid rgba(210,153,34,0.25);">Hangout</span>
                                    <span class="text-xs" style="color:var(--text-muted);">
                                        @if ($post->expires_at && $post->expires_at->diffInMinutes(now()) <= 60)
                                            Closes soon
                                        @else
                                            Closes in {{ $post->expiresInFormatted() }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs leading-snug" style="color:var(--text);">{{ Str::limit($post->content, 55) }}</p>
                            @if ($post->tags->isNotEmpty())
                                <p class="text-xs truncate mt-0.5" style="color:var(--text-muted);">
                                    {{ $post->tags->pluck('name')->take(2)->join(' · ') }}
                                </p>
                            @endif
                        </button>
                    @endforeach
                </div>

                <a
                    href="{{ route('explore') }}"
                    wire:navigate
                    class="block"
                    aria-label="See more"
                >
                    <x-arrow-icon label="See more" />
                </a>

            </div>
        @endif

    @endif
</div>
