<div>
    @if (auth()->user()->isSupporter())
        <div
            x-data="{ open: false }"
            @click.outside="open = false"
            class="relative"
        >
            <button
                type="button"
                @click="open = !open"
                class="w-full text-xs py-1 transition text-left flex items-center gap-1.5"
                style="color:#8B949E;"
                onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
            >
                <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v12M2 8h12"/></svg>
                Add to collection
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                style="display:none;position:absolute;bottom:calc(100% + 6px);left:0;right:0;background:#1C2333;border:1px solid #30363D;border-radius:10px;z-index:50;box-shadow:0 8px 24px rgba(0,0,0,0.5);"
            >
                {{-- Collections list --}}
                @if ($this->collections->isNotEmpty())
                    <div class="py-1.5 max-h-44 overflow-y-auto">
                        @foreach ($this->collections as $collection)
                            <button
                                type="button"
                                wire:click="toggle('{{ $collection->id }}')"
                                class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs text-left transition"
                                style="color:#C9D1D9;"
                                onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background=''"
                            >
                                <span class="flex-none w-3 h-3 rounded-sm border flex items-center justify-center"
                                    style="border-color:{{ $collection->in_collection ? '#1D9E75' : '#30363D' }};background:{{ $collection->in_collection ? '#1D9E75' : 'transparent' }};"
                                >
                                    @if ($collection->in_collection)
                                        <svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                                    @endif
                                </span>
                                <span class="truncate">{{ $collection->name }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="border-t" style="border-color:#30363D;"></div>
                @endif

                {{-- New collection inline form --}}
                @if ($showForm)
                    <div class="px-3 py-2 flex gap-2">
                        <input
                            type="text"
                            wire:model="newName"
                            wire:keydown.enter="create"
                            wire:keydown.escape="$set('showForm', false)"
                            placeholder="Collection name"
                            maxlength="60"
                            autofocus
                            class="flex-1 rounded-lg px-2 py-1 text-xs focus:outline-none"
                            style="background:#0D1117;border:1px solid #30363D;color:#E6EDF3;"
                        >
                        <button
                            type="button"
                            wire:click="create"
                            class="text-xs px-2 py-1 rounded-lg transition"
                            style="background:#1D9E75;color:#fff;"
                        >Add</button>
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="$set('showForm', true)"
                        class="w-full flex items-center gap-2 px-3 py-2 text-xs transition"
                        style="color:#8B949E;"
                        onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background=''"
                    >
                        <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M8 2v12M2 8h12"/></svg>
                        New collection
                    </button>
                @endif

                @if ($message)
                    <p class="px-3 pb-2 text-xs" style="color:#D29922;">{{ $message }}</p>
                @endif
            </div>
        </div>
    @endif
</div>
