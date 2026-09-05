@if ($this->isEnabled)
<div class="space-y-5 pb-2">

    {{-- Flash --}}
    @if ($flash)
        <p class="text-xs" style="color:var(--accent);">{{ $flash }}</p>
    @endif

    {{-- ── Meet people ─────────────────────────── --}}
    @if ($this->suggestedPeople->isNotEmpty())
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Meet people</p>
                <a href="{{ route('friends.index') }}" wire:navigate aria-label="See more"><x-arrow-icon label="See more" /></a>
            </div>

            @foreach ($this->suggestedPeople as $person)
                <div
                    class="flex items-center gap-3 rounded-xl px-4 py-3"
                    style="background:var(--surface);border:1px solid var(--border);"
                >
                    <x-avatar :user="$person" size="sm" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color:var(--text);">{{ $person->gamertag }}</p>
                        <p class="text-xs" style="color:var(--text-muted);">{{ $person->shared_tag_count }} {{ Str::plural('interest', $person->shared_tag_count) }} in common</p>
                    </div>
                    <x-button
                        wire:click="sendRequest('{{ $person->id }}')"
                        wire:loading.attr="disabled"
                        wire:target="sendRequest('{{ $person->id }}')"
                        variant="secondary"
                        class="flex-none !px-3 !py-1.5 !text-xs"
                    >
                        <span wire:loading.remove wire:target="sendRequest('{{ $person->id }}')">Add friend</span>
                        <span wire:loading wire:target="sendRequest('{{ $person->id }}')">Sending…</span>
                    </x-button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Find rooms ───────────────────────────── --}}
    @if ($this->suggestedRooms->isNotEmpty())
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Find rooms</p>
                <a href="{{ route('explore') }}" wire:navigate aria-label="See all"><x-arrow-icon label="See all" /></a>
            </div>

            @foreach ($this->suggestedRooms as $post)
                <div
                    class="rounded-xl px-4 py-3 space-y-1.5"
                    style="background:var(--surface);border:1px solid var(--border);"
                >
                    <p class="text-xs font-medium" style="color:var(--text-muted);">
                        @switch($post->user->identity_mode)
                            @case(3) Anonymous @break
                            @case(2) {{ $post->user->display_name ?? $post->user->gamertag }} @break
                            @default {{ $post->user->gamertag }}
                        @endswitch
                    </p>
                    <p class="text-sm leading-snug" style="color:var(--text);">{{ Str::limit($post->content, 100) }}</p>
                    @if ($post->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            @foreach ($post->tags->take(3) as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:var(--surface-raised);color:var(--text-muted);border:1px solid var(--border);">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($this->suggestedPeople->isNotEmpty() || $this->suggestedRooms->isNotEmpty())
        <hr style="border-color:var(--border);">
    @endif

</div>
@endif
