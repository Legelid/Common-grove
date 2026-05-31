@if ($this->isEnabled)
<div class="space-y-5 pb-2">

    {{-- Flash --}}
    @if ($flash)
        <p class="text-xs" style="color:#1D9E75;">{{ $flash }}</p>
    @endif

    {{-- ── Meet people ─────────────────────────── --}}
    @if ($this->suggestedPeople->isNotEmpty())
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Meet people</p>
                <a href="{{ route('friends.index') }}" wire:navigate class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#8B949E'">See more →</a>
            </div>

            @foreach ($this->suggestedPeople as $person)
                <div
                    class="flex items-center gap-3 rounded-xl px-4 py-3"
                    style="background:#161B22;border:1px solid #30363D;"
                >
                    <x-avatar :user="$person" size="sm" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color:#E6EDF3;">{{ $person->gamertag }}</p>
                        <p class="text-xs" style="color:#8B949E;">{{ $person->shared_tag_count }} {{ Str::plural('interest', $person->shared_tag_count) }} in common</p>
                    </div>
                    <button
                        type="button"
                        wire:click="sendRequest('{{ $person->id }}')"
                        wire:loading.attr="disabled"
                        wire:target="sendRequest('{{ $person->id }}')"
                        class="flex-none text-xs px-3 py-1.5 rounded-lg transition font-medium disabled:opacity-50"
                        style="background:#1C2333;color:#8B949E;border:1px solid #30363D;"
                        onmouseover="this.style.background='#1D9E75';this.style.color='#fff';this.style.borderColor='#1D9E75';"
                        onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';this.style.borderColor='#30363D';"
                    >
                        <span wire:loading.remove wire:target="sendRequest('{{ $person->id }}')">Add friend</span>
                        <span wire:loading wire:target="sendRequest('{{ $person->id }}')">Sending…</span>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Find rooms ───────────────────────────── --}}
    @if ($this->suggestedRooms->isNotEmpty())
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Find rooms</p>
                <a href="{{ route('feed') }}" wire:navigate class="text-xs transition" style="color:#8B949E;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#8B949E'">See all →</a>
            </div>

            @foreach ($this->suggestedRooms as $post)
                <div
                    class="rounded-xl px-4 py-3 space-y-1.5"
                    style="background:#161B22;border:1px solid #30363D;"
                >
                    <p class="text-xs font-medium" style="color:#8B949E;">
                        @switch($post->user->identity_mode)
                            @case(3) Anonymous @break
                            @case(2) {{ $post->user->display_name ?? $post->user->gamertag }} @break
                            @default {{ $post->user->gamertag }}
                        @endswitch
                    </p>
                    <p class="text-sm leading-snug" style="color:#E6EDF3;">{{ Str::limit($post->content, 100) }}</p>
                    @if ($post->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            @foreach ($post->tags->take(3) as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#21262D;color:#8B949E;border:1px solid #30363D;">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($this->suggestedPeople->isNotEmpty() || $this->suggestedRooms->isNotEmpty())
        <hr style="border-color:#30363D;">
    @endif

</div>
@endif
