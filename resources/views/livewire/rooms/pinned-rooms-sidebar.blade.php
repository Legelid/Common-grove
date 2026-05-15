<div>
    <div class="rounded-2xl border p-4 space-y-3"
        style="background:#1C2333;border-color:#2D333B;box-shadow:0 2px 8px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.04) inset;">

        <div class="pb-2 border-b" style="border-color:#30363D;">
            <p class="text-sm font-medium" style="color:#C9D1D9;">Your Rooms</p>
            <p class="text-xs mt-0.5 leading-snug" style="color:#8B949E;">Rooms you saved</p>
        </div>

        @if ($this->pinnedRooms->isEmpty())
            <p class="text-xs leading-snug" style="color:#3d4451;">Pin a room to come back later.</p>
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
                                style="color:#C9D1D9;"
                                title="{{ $pin->conversation->name ?? 'Room' }}"
                            >{{ Str::limit($pin->conversation->name ?? 'Room', 22) }}</a>
                            <span class="text-xs leading-none" style="color:{{ $isPersistent ? '#1D9E75' : '#A07820' }};">
                                {{ $isPersistent ? 'Room' : 'Hangout' }}
                            </span>
                        </div>

                        <button
                            type="button"
                            x-show="hovered"
                            wire:click="unpin('{{ $pin->conversation_id }}')"
                            aria-label="Unpin room"
                            class="flex-none text-xs w-4 h-4 flex items-center justify-center rounded transition"
                            style="color:#8B949E;"
                            onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
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
                                        style="color:#C9D1D9;"
                                        title="{{ $pin->conversation->name ?? 'Room' }}"
                                    >{{ Str::limit($pin->conversation->name ?? 'Room', 22) }}</a>
                                    <span class="text-xs leading-none" style="color:{{ $isPersistent ? '#1D9E75' : '#A07820' }};">
                                        {{ $isPersistent ? 'Room' : 'Hangout' }}
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    x-show="hovered"
                                    wire:click="unpin('{{ $pin->conversation_id }}')"
                                    aria-label="Unpin room"
                                    class="flex-none text-xs w-4 h-4 flex items-center justify-center rounded transition"
                                    style="color:#8B949E;"
                                    onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                                >×</button>
                            </div>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        x-show="!showAll"
                        @click="showAll = true"
                        class="text-xs transition mt-1 px-2.5"
                        style="color:#8B949E;"
                        onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
                    >See all →</button>
                </div>
            @endif
        @endif

    </div>
</div>
