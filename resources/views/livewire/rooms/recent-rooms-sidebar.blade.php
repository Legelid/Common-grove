<div>
    @if ($this->recentRooms->isNotEmpty())
        <div class="rounded-card border border-border p-4 space-y-3"
            style="background:var(--surface);box-shadow:var(--card-shadow);">

            <div class="pb-2 border-b" style="border-color:var(--surface-raised);">
                <p class="font-display text-sm font-medium" style="color:var(--text-muted);">Recent</p>
                <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-faint);">Rooms you visited</p>
            </div>

            <div class="space-y-0.5">
                @foreach ($this->recentRooms as $recent)
                    @php $isPersistent = $recent->conversation->hangoutPost?->is_persistent ?? false; @endphp
                    <a
                        href="{{ route('room.show', $recent->conversation_id) }}"
                        wire:navigate
                        class="flex items-center gap-2 px-2.5 py-2 rounded-xl transition"
                        onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background=''"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="text-xs truncate leading-snug"
                                style="color:var(--text-muted);">{{ Str::limit($recent->conversation->name ?? 'Room', 22) }}</p>
                            <span class="text-xs leading-none" style="color:var(--text-faint);">
                                {{ $isPersistent ? 'Room' : 'Hangout' }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    @endif
</div>
