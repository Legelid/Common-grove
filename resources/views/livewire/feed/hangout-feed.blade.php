<div class="flex h-full">

    {{-- ── Filter panel ────────────────────────────────────────────────────── --}}
    <aside class="hidden md:flex flex-col w-56 flex-none border-r overflow-y-auto py-6 px-4 space-y-4" style="border-color:#30363D;background:#161B22;">

        <div>
            <p class="text-sm font-semibold" style="color:#E6EDF3;">Find your kind of room</p>
            <p class="text-xs mt-1 leading-relaxed" style="color:#8B949E;">Choose what feels right.</p>
        </div>

        {{-- ── Interests ─────────────────────────────────────────────────── --}}
        @if ($this->filterInterestTags->isNotEmpty())
            @php $interestHidden = max(0, $this->filterInterestTags->count() - 10); @endphp
            <div x-data="{ open: true, showAll: false }">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between mb-2 text-xs font-semibold uppercase tracking-wider transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                >
                    <span>Interests</span>
                    <span x-text="open ? '▴' : '▾'" class="opacity-60 text-xs"></span>
                </button>
                <div x-show="open" style="display:block;">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterInterestTags->take(10) as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedFilterTagIds)"
                                wire:click="toggleFilter('{{ $tag->id }}')"
                                wire:key="f-int-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                    @if ($interestHidden > 0)
                        <div x-show="showAll" class="flex flex-wrap gap-1.5 mt-1.5">
                            @foreach ($this->filterInterestTags->skip(10) as $tag)
                                <x-tag-bubble
                                    :label="$tag->name"
                                    :selected="in_array($tag->id, $selectedFilterTagIds)"
                                    wire:click="toggleFilter('{{ $tag->id }}')"
                                    wire:key="f-int-x-{{ $tag->id }}"
                                />
                            @endforeach
                        </div>
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="mt-2 text-xs transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                        >
                            <span x-show="!showAll">See {{ $interestHidden }} more</span>
                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── Shared experiences ─────────────────────────────────────────── --}}
        @if ($this->filterExperienceTags->isNotEmpty())
            @php $expHidden = max(0, $this->filterExperienceTags->count() - 6); @endphp
            <div x-data="{ open: false, showAll: false }">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between mb-2 text-xs font-semibold uppercase tracking-wider transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                >
                    <span>Experiences</span>
                    <span x-text="open ? '▴' : '▾'" class="opacity-60 text-xs"></span>
                </button>
                <div x-show="open" style="display:none;">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterExperienceTags->take(6) as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedFilterTagIds)"
                                wire:click="toggleFilter('{{ $tag->id }}')"
                                wire:key="f-exp-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                    @if ($expHidden > 0)
                        <div x-show="showAll" class="flex flex-wrap gap-1.5 mt-1.5">
                            @foreach ($this->filterExperienceTags->skip(6) as $tag)
                                <x-tag-bubble
                                    :label="$tag->name"
                                    :selected="in_array($tag->id, $selectedFilterTagIds)"
                                    wire:click="toggleFilter('{{ $tag->id }}')"
                                    wire:key="f-exp-x-{{ $tag->id }}"
                                />
                            @endforeach
                        </div>
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="mt-2 text-xs transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                        >
                            <span x-show="!showAll">See {{ $expHidden }} more</span>
                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── Room vibe ───────────────────────────────────────────────────── --}}
        @if ($this->filterVibeTags->isNotEmpty())
            @php $vibeHidden = max(0, $this->filterVibeTags->count() - 6); @endphp
            <div x-data="{ open: false, showAll: false }">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between mb-2 text-xs font-semibold uppercase tracking-wider transition"
                    style="color:#8B949E;"
                    onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                >
                    <span>Room vibe</span>
                    <span x-text="open ? '▴' : '▾'" class="opacity-60 text-xs"></span>
                </button>
                <div x-show="open" style="display:none;">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterVibeTags->take(6) as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedFilterTagIds)"
                                wire:click="toggleFilter('{{ $tag->id }}')"
                                wire:key="f-vibe-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                    @if ($vibeHidden > 0)
                        <div x-show="showAll" class="flex flex-wrap gap-1.5 mt-1.5">
                            @foreach ($this->filterVibeTags->skip(6) as $tag)
                                <x-tag-bubble
                                    :label="$tag->name"
                                    :selected="in_array($tag->id, $selectedFilterTagIds)"
                                    wire:click="toggleFilter('{{ $tag->id }}')"
                                    wire:key="f-vibe-x-{{ $tag->id }}"
                                />
                            @endforeach
                        </div>
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="mt-2 text-xs transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                        >
                            <span x-show="!showAll">See {{ $vibeHidden }} more</span>
                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if ($this->filtersActive)
            <button
                type="button"
                wire:click="clearFilters"
                class="text-xs underline text-left transition"
                style="color:#8B949E;"
                onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
            >Clear filters</button>
        @endif

    </aside>

    {{-- ── Main feed ────────────────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto">
        <div class="px-8 py-10 max-w-2xl mx-auto space-y-8">

            {{-- Header row --}}
            <div class="flex items-end justify-between">
                <div>
                    <h1 class="text-xl font-semibold" style="color:#E6EDF3;">Open rooms</h1>
                    <p class="text-xs mt-1" style="color:#8B949E;">Places you might like right now</p>
                </div>
                <a
                    href="{{ route('feed.post') }}"
                    wire:navigate
                    class="px-4 py-2 text-sm font-medium rounded-lg transition"
                    style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                    onmouseover="this.style.background='rgba(29,158,117,0.25)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                >+ Open a room</a>
            </div>

            {{-- ── Official starter rooms section ──────────────────────────── --}}
            @if ($this->officialRooms->isNotEmpty())
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium" style="color:#8B949E;">Starter rooms from CommonGrove</p>
                        <p class="text-xs mt-0.5" style="color:#3d4451;">Always-open spaces you can step into anytime.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($this->officialRooms as $official)
                            @php
                                $officialConvId   = $official->conversation?->id;
                                $officialIsPinned = $officialConvId && in_array($officialConvId, $this->pinnedConversationIds);
                            @endphp
                            <div
                                wire:key="official-{{ $official->id }}"
                                class="rounded-xl border p-4 space-y-3 transition"
                                style="background:#161B22;border-color:#2D333B;box-shadow:0 1px 3px rgba(0,0,0,0.2);"
                                onmouseover="this.style.background='#1C2333';this.style.borderColor='#3d4451';"
                                onmouseout="this.style.background='#161B22';this.style.borderColor='#2D333B';"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                                            style="background:rgba(29,158,117,0.08);color:#1D9E75;border:1px solid rgba(29,158,117,0.15);">CommonGrove room</span>
                                        <span class="ml-2 text-xs" style="color:#3d4451;">Always open</span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="toggleCardPin('{{ $official->id }}')"
                                        title="{{ $officialIsPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                                        aria-label="{{ $officialIsPinned ? 'Unpin room' : 'Pin room' }}"
                                        class="flex-none flex items-center gap-1 px-1.5 py-1 rounded text-xs transition"
                                        style="{{ $officialIsPinned ? 'color:#1D9E75;' : 'color:#8B949E;' }}"
                                        onmouseover="this.style.color='{{ $officialIsPinned ? '#E24B4A' : '#C9D1D9' }}'"
                                        onmouseout="this.style.color='{{ $officialIsPinned ? '#1D9E75' : '#8B949E' }}'"
                                    >
                                        @if ($officialIsPinned)
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                        @else
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                        @endif
                                    </button>
                                </div>

                                <p class="text-sm leading-snug" style="color:#C9D1D9;">{{ Str::limit($official->content, 90) }}</p>

                                @if ($official->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($official->tags->take(3) as $tag)
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#1C2333;color:#8B949E;border:1px solid #30363D;">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <button
                                    type="button"
                                    wire:click="joinHangout('{{ $official->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="joinHangout('{{ $official->id }}')"
                                    class="w-full py-1.5 text-xs font-medium rounded-lg transition disabled:opacity-50"
                                    style="background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.2);"
                                    onmouseover="this.style.background='rgba(29,158,117,0.2)'" onmouseout="this.style.background='rgba(29,158,117,0.1)'"
                                >
                                    <span wire:loading.remove wire:target="joinHangout('{{ $official->id }}')">Step in</span>
                                    <span wire:loading wire:target="joinHangout('{{ $official->id }}')">Entering…</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr style="border-color:#21262D;">
            @endif

            {{-- Pin toast --}}
            @if ($pinToast)
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => { show = false; $wire.set('pinToast', null) }, 2200)"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="rounded-xl border px-4 py-2.5 text-xs"
                    style="background:rgba(29,158,117,0.08);border-color:rgba(29,158,117,0.2);color:#1D9E75;"
                >
                    {{ $pinToast }}
                </div>
            @endif

            {{-- Active filter chips (mobile only) --}}
            @if ($this->filtersActive)
                <div class="flex flex-wrap gap-2 items-center md:hidden">
                    <span class="text-xs" style="color:#8B949E;">Filtered by:</span>
                    @foreach ($selectedFilterTagIds as $fid)
                        @php
                            $ft = $this->filterInterestTags->firstWhere('id', $fid)
                                ?? $this->filterExperienceTags->firstWhere('id', $fid)
                                ?? $this->filterVibeTags->firstWhere('id', $fid);
                        @endphp
                        @if ($ft)
                            <span class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
                                style="background:rgba(210,153,34,0.15);color:#E6B84A;border:1px solid rgba(210,153,34,0.35);">
                                {{ $ft->name }}
                                <button wire:click="toggleFilter('{{ $fid }}')" class="ml-0.5 leading-none" style="color:#D29922;">&times;</button>
                            </span>
                        @endif
                    @endforeach
                    <button wire:click="clearFilters" class="text-xs underline" style="color:#8B949E;">Clear all</button>
                </div>
            @endif

            {{-- Join message --}}
            @if ($joinMessage)
                <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(210,153,34,0.06);border-color:rgba(210,153,34,0.25);color:#D29922;">
                    {{ $joinMessage }}
                </div>
            @endif

            {{-- Feed content --}}
            @if ($this->showingFallback)
                <div class="rounded-2xl border px-6 py-8 text-sm space-y-3 text-center" style="background:#161B22;border-color:#30363D;color:#8B949E;box-shadow:0 1px 4px rgba(0,0,0,0.25);">
                    <p>It's quiet right now — no rooms match those filters.</p>
                    <a href="{{ route('feed.post') }}" wire:navigate class="inline-block text-sm underline transition" style="color:#1D9E75;">Open one →</a>
                </div>
            @elseif ($this->posts->isEmpty())
                <div class="text-center py-24 space-y-3">
                    <p class="text-sm" style="color:#8B949E;">No active rooms right now.</p>
                    <p class="text-xs leading-relaxed" style="color:#8B949E;">It's quiet. You could open a room, or come back a little later.</p>
                    <a href="{{ route('feed.post') }}" wire:navigate class="inline-block text-sm underline transition" style="color:#1D9E75;">Open a room →</a>
                </div>
            @else
                @if ($this->filtersActive)
                    <p class="text-xs" style="color:#8B949E;">Showing closest matches first.</p>
                @endif

                <div class="space-y-5">
                    @foreach ($this->posts as $post)
                        @php
                            $isPersistent  = $post->is_persistent;
                            $convId        = $post->conversation?->id;
                            $isCardPinned  = $convId && in_array($convId, $this->pinnedConversationIds);
                        @endphp
                        <article
                            wire:key="post-{{ $post->id }}"
                            class="rounded-2xl border p-7 space-y-5 transition-all duration-150"
                            style="background:#161B22;border-color:#30363D;box-shadow:0 1px 3px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.03) inset;"
                            onmouseover="this.style.background='#1C2333';this.style.borderColor='#3d4451';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.3),0 0 0 1px rgba(255,255,255,0.04) inset';"
                            onmouseout="this.style.background='#161B22';this.style.borderColor='#30363D';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.03) inset';"
                        >
                            {{-- Card header: author + type badge --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm" style="color:#E6EDF3;">
                                            @switch($post->user->identity_mode)
                                                @case(3) Anonymous @break
                                                @case(2) {{ $post->user->display_name ?? $post->user->gamertag }} @break
                                                @default {{ $post->user->gamertag }}
                                            @endswitch
                                        </span>
                                        <span class="text-xs" style="color:#3d4451;">·</span>
                                        <span class="text-xs" style="color:#8B949E;">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                {{-- Type badge --}}
                                @if ($isPersistent)
                                    <span class="flex-none text-xs px-2.5 py-1 rounded-full font-medium"
                                        style="background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.25);">
                                        Always-open room
                                    </span>
                                @else
                                    <span class="flex-none text-xs px-2.5 py-1 rounded-full font-medium"
                                        style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.25);">
                                        Temporary hangout
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <p class="text-sm leading-relaxed" style="color:#E6EDF3;">{{ $post->content }}</p>

                            {{-- Tags --}}
                            @if ($post->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($post->tags as $tag)
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs"
                                            style="{{ in_array($tag->id, $selectedFilterTagIds)
                                                ? 'background:rgba(210,153,34,0.12);color:#E6B84A;border:1px solid rgba(210,153,34,0.3);'
                                                : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                        >{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Footer: meta + pin + CTA --}}
                            <div class="flex items-center justify-between pt-1 border-t" style="border-color:#21262D;">
                                <span class="text-xs" style="color:#8B949E;">
                                    @if ($isPersistent)
                                        Come back anytime
                                    @else
                                        Closes in {{ $post->expiresInFormatted() }}
                                    @endif
                                    <span class="mx-1.5" style="color:#30363D;">·</span>
                                    {{ $post->joined_count }} {{ Str::plural('person', $post->joined_count) }} joined
                                </span>

                                <div class="flex items-center gap-2">
                                    {{-- Pin button --}}
                                    <button
                                        type="button"
                                        wire:click="toggleCardPin('{{ $post->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleCardPin('{{ $post->id }}')"
                                        title="{{ $isCardPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                                        aria-label="{{ $isCardPinned ? 'Unpin room' : 'Pin room' }}"
                                        class="flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs transition disabled:opacity-40"
                                        style="{{ $isCardPinned
                                            ? 'color:#1D9E75;background:rgba(29,158,117,0.08);border:1px solid rgba(29,158,117,0.2);'
                                            : 'color:#8B949E;border:1px solid transparent;' }}"
                                        onmouseover="{{ $isCardPinned
                                            ? "this.style.color='#E24B4A';this.style.background='rgba(226,75,74,0.06)';this.style.borderColor='rgba(226,75,74,0.2)';"
                                            : "this.style.color='#C9D1D9';this.style.borderColor='#30363D';" }}"
                                        onmouseout="{{ $isCardPinned
                                            ? "this.style.color='#1D9E75';this.style.background='rgba(29,158,117,0.08)';this.style.borderColor='rgba(29,158,117,0.2)';"
                                            : "this.style.color='#8B949E';this.style.borderColor='transparent';" }}"
                                    >
                                        @if ($isCardPinned)
                                            {{-- Bookmark filled --}}
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                            </svg>
                                            <span>Pinned</span>
                                        @else
                                            {{-- Bookmark outline --}}
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                            </svg>
                                            <span>Pin</span>
                                        @endif
                                    </button>

                                    {{-- Join / enter button --}}
                                    <button
                                        type="button"
                                        wire:click="joinHangout('{{ $post->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="joinHangout('{{ $post->id }}')"
                                        class="px-5 py-1.5 text-sm font-medium rounded-lg transition disabled:opacity-50"
                                        style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                                        onmouseover="this.style.background='rgba(29,158,117,0.28)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                                    >
                                        <span wire:loading.remove wire:target="joinHangout('{{ $post->id }}')">
                                            @if ($isPersistent) Enter room @else Step in @endif
                                        </span>
                                        <span wire:loading wire:target="joinHangout('{{ $post->id }}')">
                                            @if ($isPersistent) Entering… @else Stepping in… @endif
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</div>
