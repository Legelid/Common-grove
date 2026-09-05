<div>
    <div class="rounded-card border border-border border-l-[3px] border-l-accent p-4 space-y-3"
        style="background:var(--surface);box-shadow:var(--card-shadow);">

        <div class="pb-2 border-b" style="border-color:var(--border);">
            <p class="font-display text-sm font-medium" style="color:var(--text);">Your Rooms</p>
            <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-muted);">Rooms you saved</p>
        </div>

        @if ($this->pinnedRooms->isEmpty())
            <p class="text-xs leading-snug" style="color:var(--text-faint);">Pin a room to come back later.</p>
        @else
            <div class="space-y-0.5">
                @foreach ($this->pinnedRooms->take(2) as $pin)
                    @php $isPersistent = $pin->conversation->hangoutPost?->is_persistent ?? false; @endphp
                    <div
                        x-data="{ hovered: false }"
                        @mouseenter="hovered = true"
                        @mouseleave="hovered = false"
                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl transition"
                        :style="hovered ? 'background:rgba(255,255,255,0.05);' : ''"
                    >
                        <div class="flex-1 min-w-0">
                            <a
                                href="{{ route('room.show', $pin->conversation_id) }}"
                                wire:navigate
                                class="block text-xs truncate leading-snug font-medium"
                                style="color:var(--text);"
                                title="{{ $pin->conversation->name ?? 'Room' }}"
                            >{{ Str::limit($pin->conversation->name ?? 'Room', 22) }}</a>
                            <span class="text-xs leading-none" style="color:{{ $isPersistent ? 'var(--accent)' : '#A07820' }};">
                                {{ $isPersistent ? 'Room' : 'Hangout' }}
                            </span>
                        </div>

                        <button
                            type="button"
                            x-show="hovered"
                            wire:click="unpin('{{ $pin->conversation_id }}')"
                            aria-label="Unpin room"
                            class="flex-none text-xs w-4 h-4 flex items-center justify-center rounded transition"
                            style="color:var(--text-muted);"
                            onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                        >×</button>
                    </div>
                @endforeach
            </div>

            @if ($this->pinnedRooms->count() > 2)
                <div x-data="{ showAll: false }">
                    <div x-show="showAll" class="space-y-0.5">
                        @foreach ($this->pinnedRooms->slice(2) as $pin)
                            @php $isPersistent = $pin->conversation->hangoutPost?->is_persistent ?? false; @endphp
                            <div
                                x-data="{ hovered: false }"
                                @mouseenter="hovered = true"
                                @mouseleave="hovered = false"
                                class="flex items-center gap-2 px-2.5 py-2 rounded-xl transition"
                                :style="hovered ? 'background:rgba(255,255,255,0.05);' : ''"
                            >
                                <div class="flex-1 min-w-0">
                                    <a
                                        href="{{ route('room.show', $pin->conversation_id) }}"
                                        wire:navigate
                                        class="block text-xs truncate leading-snug font-medium"
                                        style="color:var(--text);"
                                        title="{{ $pin->conversation->name ?? 'Room' }}"
                                    >{{ Str::limit($pin->conversation->name ?? 'Room', 22) }}</a>
                                    <span class="text-xs leading-none" style="color:{{ $isPersistent ? 'var(--accent)' : '#A07820' }};">
                                        {{ $isPersistent ? 'Room' : 'Hangout' }}
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    x-show="hovered"
                                    wire:click="unpin('{{ $pin->conversation_id }}')"
                                    aria-label="Unpin room"
                                    class="flex-none text-xs w-4 h-4 flex items-center justify-center rounded transition"
                                    style="color:var(--text-muted);"
                                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                                >×</button>
                            </div>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        x-show="!showAll"
                        @click="showAll = true"
                        class="mt-1"
                        aria-label="See all"
                    ><x-arrow-icon label="See all" /></button>
                </div>
            @endif
        @endif

    </div>
</div>
