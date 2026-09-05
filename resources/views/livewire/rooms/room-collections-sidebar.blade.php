<div>
    @if (auth()->user()->isSupporter())
        <div
            x-data="{ open: true }"
            class="rounded-card border border-border"
            style="background:var(--surface);box-shadow:var(--card-shadow);"
        >
            {{-- Header --}}
            <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-1.5 flex-1 text-left"
                >
                    <p class="font-display text-sm font-medium" style="color:var(--text);">Collections</p>
                    <svg
                        :class="open ? 'rotate-180' : ''"
                        class="transition-transform ml-auto"
                        width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--text-faint)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                    ><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                @if ($this->collections->count() < config('supporter.limits.room_collections.max_collections', 10))
                    <button
                        type="button"
                        wire:click="$set('showForm', true)"
                        title="New collection"
                        class="ml-2 flex-none w-5 h-5 flex items-center justify-center rounded transition text-xs"
                        style="color:var(--text-muted);"
                        onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                    >+</button>
                @endif
            </div>

            <div x-show="open" x-collapse>
                {{-- New collection form --}}
                @if ($showForm)
                    <div class="px-4 pb-3 flex gap-2">
                        <x-input
                            type="text"
                            wire:model="newName"
                            wire:keydown.enter="create"
                            wire:keydown.escape="$set('showForm', false)"
                            placeholder="Collection name"
                            maxlength="60"
                            class="flex-1 !text-xs !py-1.5"
                        />
                        <x-button type="button" wire:click="create" class="!text-xs !px-2.5 !py-1.5">Save</x-button>
                    </div>
                @endif

                @if ($message)
                    <p class="px-4 pb-2 text-xs" style="color:#D29922;">{{ $message }}</p>
                @endif

                {{-- Collections list --}}
                @if ($this->collections->isEmpty() && ! $showForm)
                    <p class="px-4 pb-4 text-xs leading-snug" style="color:var(--text-faint);">Create your first collection to group rooms you love.</p>
                @else
                    <div class="px-2 pb-3 space-y-1">
                        @foreach ($this->collections as $collection)
                            @php
                                // Filter out items whose room is gone or inactive
                                $liveItems = $collection->items->filter(
                                    fn ($item) => $item->conversation && $item->conversation->is_active
                                );
                            @endphp
                            <div
                                x-data="{ expanded: false, renaming: false, renamingVal: '{{ addslashes($collection->name) }}' }"
                                class="rounded-xl overflow-hidden"
                            >
                                {{-- Collection row --}}
                                <div
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl transition group"
                                    :style="expanded ? 'background:rgba(255,255,255,0.04);' : ''"
                                    onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="if(!this.querySelector('[x-cloak]')) this.style.background= expanded ? 'rgba(255,255,255,0.04)' : ''"
                                >
                                    <button
                                        type="button"
                                        @click="expanded = !expanded"
                                        class="flex-1 flex items-center gap-1.5 text-left min-w-0"
                                    >
                                        <svg
                                            :class="expanded ? 'rotate-90' : ''"
                                            class="flex-none transition-transform"
                                            width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="var(--text-faint)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                                        ><polyline points="9 18 15 12 9 6"/></svg>
                                        <span x-show="!renaming" class="text-xs truncate font-medium" style="color:var(--text);">{{ $collection->name }}</span>
                                        <span class="text-xs flex-none ml-auto" style="color:var(--text-faint);">{{ $liveItems->count() }}</span>
                                    </button>

                                    {{-- Actions (rename / delete) --}}
                                    <div class="flex items-center gap-1 flex-none opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            type="button"
                                            @click.stop="renaming = !renaming; if(renaming) $nextTick(() => $el.closest('[x-data]').querySelector('input.rename-input')?.focus())"
                                            title="Rename"
                                            class="w-4 h-4 flex items-center justify-center text-xs rounded transition"
                                            style="color:var(--text-muted);"
                                            onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                                        >
                                            <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.5 2.5a1.414 1.414 0 0 1 2 2L5 13H3v-2L11.5 2.5z"/></svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete('{{ $collection->id }}')"
                                            wire:confirm="Delete '{{ addslashes($collection->name) }}'? This can't be undone."
                                            title="Delete collection"
                                            class="w-4 h-4 flex items-center justify-center text-xs rounded transition"
                                            style="color:var(--text-muted);"
                                            onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                                        >×</button>
                                    </div>
                                </div>

                                {{-- Inline rename --}}
                                <div x-show="renaming" class="px-2.5 pb-2 flex gap-2" style="display:none;">
                                    <x-input
                                        x-model="renamingVal"
                                        @keydown.enter="$wire.rename('{{ $collection->id }}', renamingVal); renaming = false"
                                        @keydown.escape="renaming = false"
                                        class="rename-input flex-1 !text-xs !py-1"
                                        maxlength="60"
                                    />
                                    <x-button
                                        type="button"
                                        @click="$wire.rename('{{ $collection->id }}', renamingVal); renaming = false"
                                        class="!text-xs !px-2 !py-1"
                                    >Save</x-button>
                                </div>

                                {{-- Rooms in collection --}}
                                <div x-show="expanded" class="pl-5 pr-2 pb-1.5 space-y-0.5" style="display:none;">
                                    @if ($liveItems->isEmpty())
                                        <p class="text-xs py-1" style="color:var(--text-faint);">No rooms here yet.</p>
                                    @else
                                        @foreach ($liveItems as $item)
                                            @php
                                                $isPersistent = $item->conversation->hangoutPost?->is_persistent ?? true;
                                                $label = Str::limit($item->conversation->name ?? 'Room', 20);
                                            @endphp
                                            <div class="flex items-center gap-1.5 group/item py-0.5">
                                                <a
                                                    href="{{ route('room.show', $item->conversation_id) }}"
                                                    wire:navigate
                                                    class="flex-1 text-xs truncate"
                                                    style="color:var(--text-muted);"
                                                    onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                                                    title="{{ $item->conversation->name ?? 'Room' }}"
                                                >{{ $label }}</a>
                                                <button
                                                    type="button"
                                                    wire:click="removeRoom('{{ $collection->id }}', '{{ $item->conversation_id }}')"
                                                    title="Remove from collection"
                                                    class="flex-none opacity-0 group-hover/item:opacity-100 w-3 h-3 flex items-center justify-center text-xs transition"
                                                    style="color:var(--text-muted);"
                                                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                                                >×</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
