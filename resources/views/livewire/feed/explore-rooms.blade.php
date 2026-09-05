<div class="flex h-full"
    x-data="{
        filterOpen: sessionStorage.getItem('cgFilterOpen') === 'true',
        toggle() {
            this.filterOpen = !this.filterOpen;
            sessionStorage.setItem('cgFilterOpen', String(this.filterOpen));
        }
    }"
>

    {{-- ── Filter panel (desktop, collapsible) — authenticated only ─────── --}}
    @auth
    <aside
        id="filter-panel"
        x-show="filterOpen"
        style="display:none;border-color:var(--border);background:var(--surface);"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="hidden md:flex flex-col w-56 flex-none border-r overflow-y-auto py-5 px-4 space-y-4"
        :aria-hidden="(!filterOpen).toString()"
        aria-label="Room filters"
    >

        {{-- Panel header --}}
        <div class="flex items-start justify-between gap-2 flex-none">
            <div>
                <p class="text-sm font-semibold" style="color:var(--text);">Find your kind of room</p>
                <p class="text-xs mt-0.5 leading-relaxed" style="color:var(--text-muted);">Choose what feels right.</p>
            </div>
            <button
                type="button"
                @click="toggle()"
                :aria-expanded="filterOpen.toString()"
                aria-controls="filter-panel"
                aria-label="Close filter panel"
                class="flex-none w-5 h-5 flex items-center justify-center rounded text-xs transition mt-0.5"
                style="color:var(--text-faint);"
                onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
            >✕</button>
        </div>

        {{-- Active filter chips --}}
        @if ($this->filtersActive)
            <div class="flex-none space-y-2 pb-3 border-b" style="border-color:var(--surface-raised);">
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($this->activeFilterTags as $tag)
                        <button
                            type="button"
                            wire:click="toggleFilter('{{ $tag->id }}')"
                            wire:key="af-{{ $tag->id }}"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition"
                            style="background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);"
                            onmouseover="this.style.background='rgba(var(--danger-rgb),0.1)';this.style.color='var(--danger)';this.style.borderColor='rgba(var(--danger-rgb),0.3)';"
                            onmouseout="this.style.background='rgba(var(--accent-rgb),0.15)';this.style.color='var(--accent)';this.style.borderColor='rgba(var(--accent-rgb),0.3)';"
                        >{{ $tag->name }} <span aria-hidden="true" style="opacity:0.7;">×</span></button>
                    @endforeach
                </div>
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-xs transition"
                    style="color:var(--text-faint);"
                    onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                >Clear all</button>
            </div>
        @endif

        {{-- Search --}}
        <div class="flex-none relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:var(--text-faint);" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input
                type="search"
                wire:model.live.debounce.300ms="filterSearch"
                placeholder="Find your kind of room"
                aria-label="Find your kind of room"
                class="w-full text-sm focus:outline-none"
                style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.625rem 1rem 0.625rem 2.5rem;font-family:var(--font-body);color:var(--text);max-width:480px;"
                onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgb(var(--accent-rgb) / 0.15)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"
            >
            @if ($filterSearch !== '')
                <button
                    type="button"
                    wire:click="$set('filterSearch', '')"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs leading-none transition"
                    style="color:var(--text-faint);"
                    aria-label="Clear search"
                    onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                >✕</button>
            @endif
        </div>

        {{-- ── Content area ───────────────────────────────────────────────── --}}

        @if ($filterSearch !== '')

            {{-- SEARCH RESULTS --}}
            @if ($this->filterTags->isNotEmpty())
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($this->filterTags as $tag)
                        @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                        <button
                            type="button"
                            wire:click="toggleFilter('{{ $tag->id }}')"
                            wire:key="fs-{{ $tag->id }}"
                            role="checkbox"
                            aria-checked="{{ $isSel ? 'true' : 'false' }}"
                            class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                            style="{{ $isSel
                                ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                            @if(!$isSel)
                                onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                            @endif
                        >{{ $tag->name }}</button>
                    @endforeach
                </div>
            @else
                <p class="text-xs" style="color:var(--text-faint);">No matching interests yet.</p>
            @endif

        @elseif ($filterCategoryId !== null)

            {{-- CATEGORY DETAIL: back + subcategory chips + tags --}}
            @php
                $activeCat = $this->filterCategories->firstWhere('id', $filterCategoryId);
            @endphp

            <div class="flex-none space-y-3">
                <button
                    type="button"
                    wire:click="clearFilterNav"
                    class="flex items-center gap-1 text-xs transition"
                    style="color:var(--text-faint);"
                    onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                >
                    <span aria-hidden="true">←</span> All categories
                </button>

                <p class="text-xs font-semibold" style="color:var(--text);">{{ $activeCat?->name }}</p>

                @if ($this->filterSubcategories->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterSubcategories as $subcat)
                            <button
                                type="button"
                                wire:click="setFilterSubcategory({{ $subcat->id }})"
                                wire:key="sc-{{ $subcat->id }}"
                                class="px-2 py-0.5 rounded text-xs transition"
                                style="{{ $filterSubcategoryId === $subcat->id
                                    ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);'
                                    : 'background:var(--surface-raised);color:var(--text-muted);border:1px solid var(--border);' }}"
                                @if($filterSubcategoryId !== $subcat->id)
                                    onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--text-faint)';"
                                    onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';"
                                @endif
                            >{{ $subcat->name }}</button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tags --}}
            @if ($this->filterTags->isNotEmpty())
                @php $tagHidden = max(0, $this->filterTags->count() - 8); @endphp
                <div x-data="{ showAll: false }" class="space-y-2">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterTags->take(8) as $tag)
                            @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                            <button
                                type="button"
                                wire:click="toggleFilter('{{ $tag->id }}')"
                                wire:key="ft-{{ $tag->id }}"
                                role="checkbox"
                                aria-checked="{{ $isSel ? 'true' : 'false' }}"
                                class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                                style="{{ $isSel
                                    ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                    : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                                @if(!$isSel)
                                    onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                    onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                                @endif
                            >{{ $tag->name }}</button>
                        @endforeach
                    </div>
                    @if ($tagHidden > 0)
                        <div x-show="showAll" style="display:none;" class="flex flex-wrap gap-1.5">
                            @foreach ($this->filterTags->skip(8) as $tag)
                                @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                                <button
                                    type="button"
                                    wire:click="toggleFilter('{{ $tag->id }}')"
                                    wire:key="ft-x-{{ $tag->id }}"
                                    role="checkbox"
                                    aria-checked="{{ $isSel ? 'true' : 'false' }}"
                                    class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                                    style="{{ $isSel
                                        ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                        : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                                    @if(!$isSel)
                                        onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                        onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                                    @endif
                                >{{ $tag->name }}</button>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="text-xs transition"
                            style="color:var(--text-faint);"
                            onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                        >
                            <span x-show="!showAll">See {{ $tagHidden }} more</span>
                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            @else
                <p class="text-xs" style="color:var(--text-faint);">No tags in this area yet.</p>
            @endif

        @else

            {{-- CATEGORY LIST --}}
            <nav aria-label="Filter by category" class="space-y-0.5">
                @foreach ($this->filterCategories as $cat)
                    <button
                        type="button"
                        wire:click="setFilterCategory({{ $cat->id }})"
                        wire:key="cat-{{ $cat->id }}"
                        class="w-full text-left flex items-center justify-between px-2 py-2 rounded-lg text-xs transition"
                        style="color:var(--text-muted);"
                        onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)';"
                        onmouseout="this.style.color='var(--text-muted)';this.style.background='';"
                    >
                        <span>{{ $cat->name }}</span>
                        <span aria-hidden="true" style="opacity:0.35;font-size:0.6rem;">▸</span>
                    </button>
                @endforeach
            </nav>

        @endif

    </aside>
    @endauth

    {{-- ── Main feed ────────────────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0 overflow-y-auto">
        <div class="px-6 py-8 max-w-5xl mx-auto space-y-8">

            {{-- ── Page header ───────────────────────────────────────────────── --}}
            {{-- Glass UI (Phase 4): light tier — hero heading over the photo. --}}
            <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;">
                <h1 class="font-display" style="font-size:clamp(1.75rem, 4vw, 2.5rem);color:var(--text);font-weight:700;line-height:1.2;">Find a comfortable place</h1>
                <p class="mt-2" style="font-family:var(--font-body);font-size:1rem;color:var(--text-muted);">Browse at your own pace. You can preview a room before stepping in.</p>
            </x-glass-panel>

            {{-- Filter trigger: desktop (authenticated only, hidden on mobile) --}}
            {{-- Glass UI (Phase 4): light tier — the search bar over the photo. --}}
            @auth
            <div class="hidden md:block" x-show="!filterOpen" style="display:none;">
                <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1rem 1.25rem;">
                    <p class="text-xs mb-2" style="color:var(--text-faint);">Not seeing something that fits?</p>
                    <button
                        type="button"
                        @click="toggle()"
                        :aria-expanded="filterOpen.toString()"
                        aria-controls="filter-panel"
                        aria-label="Open room filters"
                        class="flex items-center justify-between gap-3 w-full px-4 py-3 rounded-xl text-sm transition"
                        style="background:var(--surface);border:1px solid var(--border);color:var(--text-muted);"
                        onmouseover="this.style.background='var(--surface)';this.style.borderColor='var(--text-faint)';this.style.color='var(--text)';"
                        onmouseout="this.style.background='var(--surface)';this.style.borderColor='var(--border)';this.style.color='var(--text-muted)';"
                    >
                        <span class="flex items-center gap-2.5">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            Find your kind of room
                        </span>
                        <span aria-hidden="true" style="opacity:0.35;font-size:0.65rem;">▸</span>
                    </button>
                </x-glass-panel>
            </div>

            @endauth

            {{-- Filter trigger + inline panel: mobile (authenticated only) --}}
            {{-- Glass UI (Phase 4): light tier — the search bar over the photo. --}}
            @auth
            <div class="md:hidden">
                <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:0.5rem;">
                <button
                    type="button"
                    @click="toggle()"
                    :aria-expanded="filterOpen.toString()"
                    aria-controls="mobile-filter-panel"
                    aria-label="Open room filters"
                    class="flex items-center justify-between gap-3 w-full px-4 py-3 rounded-xl text-sm transition"
                    :style="filterOpen
                        ? 'background:rgba(var(--accent-rgb),0.08);border:1px solid rgba(var(--accent-rgb),0.25);color:var(--accent);'
                        : 'background:var(--surface);border:1px solid var(--border);color:var(--text-muted);'"
                >
                    <span class="flex items-center gap-2.5">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span x-show="!filterOpen">Find your kind of room</span>
                        <span x-show="filterOpen" style="display:none;">Hide filters</span>
                    </span>
                    <span x-show="!filterOpen" aria-hidden="true" style="opacity:0.35;font-size:0.65rem;">▸</span>
                    <span x-show="filterOpen" style="display:none;opacity:0.6;" aria-hidden="true">✕</span>
                </button>
                </x-glass-panel>
            </div>

            {{-- Mobile inline filter panel (hidden on desktop via wrapper; Alpine toggles visibility on mobile) --}}
            <div class="md:hidden">
            <div
                id="mobile-filter-panel"
                class="rounded-xl border overflow-hidden"
                style="display:none;border-color:var(--border);background:var(--surface);"
                x-show="filterOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :aria-hidden="(!filterOpen).toString()"
                aria-label="Room filters"
            >
                <div class="py-4 px-4 space-y-4">

                    {{-- Active filter chips --}}
                    @if ($this->filtersActive)
                        <div class="space-y-2 pb-3 border-b" style="border-color:var(--surface-raised);">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($this->activeFilterTags as $tag)
                                    <button
                                        type="button"
                                        wire:click="toggleFilter('{{ $tag->id }}')"
                                        wire:key="mob-af-{{ $tag->id }}"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition"
                                        style="background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);"
                                        onmouseover="this.style.background='rgba(var(--danger-rgb),0.1)';this.style.color='var(--danger)';this.style.borderColor='rgba(var(--danger-rgb),0.3)';"
                                        onmouseout="this.style.background='rgba(var(--accent-rgb),0.15)';this.style.color='var(--accent)';this.style.borderColor='rgba(var(--accent-rgb),0.3)';"
                                    >{{ $tag->name }} <span aria-hidden="true" style="opacity:0.7;">×</span></button>
                                @endforeach
                            </div>
                            <button
                                type="button"
                                wire:click="clearFilters"
                                class="text-xs transition"
                                style="color:var(--text-faint);"
                                onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                            >Clear all</button>
                        </div>
                    @endif

                    {{-- Search --}}
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:var(--text-faint);" aria-hidden="true">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="filterSearch"
                            placeholder="Find your kind of room"
                            aria-label="Find your kind of room"
                            class="w-full text-sm focus:outline-none"
                            style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.625rem 1rem 0.625rem 2.5rem;font-family:var(--font-body);color:var(--text);max-width:480px;"
                            onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgb(var(--accent-rgb) / 0.15)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"
                        >
                        @if ($filterSearch !== '')
                            <button
                                type="button"
                                wire:click="$set('filterSearch', '')"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs leading-none transition"
                                style="color:var(--text-faint);"
                                aria-label="Clear search"
                                onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                            >✕</button>
                        @endif
                    </div>

                    {{-- ── Content area ───────────────────────────────────── --}}

                    @if ($filterSearch !== '')

                        {{-- SEARCH RESULTS --}}
                        @if ($this->filterTags->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($this->filterTags as $tag)
                                    @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                                    <button
                                        type="button"
                                        wire:click="toggleFilter('{{ $tag->id }}')"
                                        wire:key="mob-fs-{{ $tag->id }}"
                                        role="checkbox"
                                        aria-checked="{{ $isSel ? 'true' : 'false' }}"
                                        class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                                        style="{{ $isSel
                                            ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                            : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                                        @if(!$isSel)
                                            onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                            onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                                        @endif
                                    >{{ $tag->name }}</button>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs" style="color:var(--text-faint);">No matching interests yet.</p>
                        @endif

                    @elseif ($filterCategoryId !== null)

                        {{-- CATEGORY DETAIL: back + subcategory chips + tags --}}
                        @php $activeCat = $this->filterCategories->firstWhere('id', $filterCategoryId); @endphp

                        <div class="space-y-3">
                            <button
                                type="button"
                                wire:click="clearFilterNav"
                                class="flex items-center gap-1 text-xs transition"
                                style="color:var(--text-faint);"
                                onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                            ><span aria-hidden="true">←</span> All categories</button>

                            <p class="text-xs font-semibold" style="color:var(--text);">{{ $activeCat?->name }}</p>

                            @if ($this->filterSubcategories->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($this->filterSubcategories as $subcat)
                                        <button
                                            type="button"
                                            wire:click="setFilterSubcategory({{ $subcat->id }})"
                                            wire:key="mob-sc-{{ $subcat->id }}"
                                            class="px-2 py-0.5 rounded text-xs transition"
                                            style="{{ $filterSubcategoryId === $subcat->id
                                                ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);'
                                                : 'background:var(--surface-raised);color:var(--text-muted);border:1px solid var(--border);' }}"
                                            @if($filterSubcategoryId !== $subcat->id)
                                                onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--text-faint)';"
                                                onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';"
                                            @endif
                                        >{{ $subcat->name }}</button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Tags --}}
                        @if ($this->filterTags->isNotEmpty())
                            @php $mobTagHidden = max(0, $this->filterTags->count() - 8); @endphp
                            <div x-data="{ mobShowAll: false }" class="space-y-2">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($this->filterTags->take(8) as $tag)
                                        @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                                        <button
                                            type="button"
                                            wire:click="toggleFilter('{{ $tag->id }}')"
                                            wire:key="mob-ft-{{ $tag->id }}"
                                            role="checkbox"
                                            aria-checked="{{ $isSel ? 'true' : 'false' }}"
                                            class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                                            style="{{ $isSel
                                                ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                                : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                                            @if(!$isSel)
                                                onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                                onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                                            @endif
                                        >{{ $tag->name }}</button>
                                    @endforeach
                                </div>
                                @if ($mobTagHidden > 0)
                                    <div x-show="mobShowAll" style="display:none;" class="flex flex-wrap gap-1.5">
                                        @foreach ($this->filterTags->skip(8) as $tag)
                                            @php $isSel = in_array($tag->id, $selectedFilterTagIds, true); @endphp
                                            <button
                                                type="button"
                                                wire:click="toggleFilter('{{ $tag->id }}')"
                                                wire:key="mob-ft-x-{{ $tag->id }}"
                                                role="checkbox"
                                                aria-checked="{{ $isSel ? 'true' : 'false' }}"
                                                class="px-2.5 py-1 rounded-full text-xs font-medium transition"
                                                style="{{ $isSel
                                                    ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                                    : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                                                @if(!$isSel)
                                                    onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';"
                                                    onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';"
                                                @endif
                                            >{{ $tag->name }}</button>
                                        @endforeach
                                    </div>
                                    <button
                                        type="button"
                                        @click="mobShowAll = !mobShowAll"
                                        class="text-xs transition"
                                        style="color:var(--text-faint);"
                                        onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                                    >
                                        <span x-show="!mobShowAll">See {{ $mobTagHidden }} more</span>
                                        <span x-show="mobShowAll" style="display:none;">Show less</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <p class="text-xs" style="color:var(--text-faint);">No tags in this area yet.</p>
                        @endif

                    @else

                        {{-- CATEGORY LIST --}}
                        <nav aria-label="Filter by category" class="space-y-0.5">
                            @foreach ($this->filterCategories as $cat)
                                <button
                                    type="button"
                                    wire:click="setFilterCategory({{ $cat->id }})"
                                    wire:key="mob-cat-{{ $cat->id }}"
                                    class="w-full text-left flex items-center justify-between px-2 py-2 rounded-lg text-xs transition"
                                    style="color:var(--text-muted);"
                                    onmouseover="this.style.color='var(--text)';this.style.background='rgba(255,255,255,0.04)';"
                                    onmouseout="this.style.color='var(--text-muted)';this.style.background='';"
                                >
                                    <span>{{ $cat->name }}</span>
                                    <span aria-hidden="true" style="opacity:0.35;font-size:0.6rem;">▸</span>
                                </button>
                            @endforeach
                        </nav>

                    @endif

                </div>
            </div>
            </div>{{-- end md:hidden mobile filter panel wrapper --}}
            @endauth

            @php
                $isPinnedRoom = fn ($room) => $room->conversation && in_array($room->conversation->id, $this->pinnedConversationIds);
                $isGuestView  = ! auth()->check();
                $browseMode   = $this->intent === null && empty($selectedFilterTagIds);
            @endphp

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
                    style="background:rgba(var(--accent-rgb),0.08);border-color:rgba(var(--accent-rgb),0.2);color:var(--accent);"
                >
                    {{ $pinToast }}
                </div>
            @endif

            {{-- Active filter chips (mobile only) --}}
            @if ($this->filtersActive)
                <div class="flex flex-wrap gap-2 items-center md:hidden">
                    <span class="text-xs" style="color:var(--text-muted);">Filtered by:</span>
                    @foreach ($this->activeFilterTags as $ft)
                        <span class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
                            style="background:rgba(210,153,34,0.15);color:#E6B84A;border:1px solid rgba(210,153,34,0.35);">
                            {{ $ft->name }}
                            <button wire:click="toggleFilter('{{ $ft->id }}')" class="ml-0.5 leading-none" style="color:#D29922;">&times;</button>
                        </span>
                    @endforeach
                    <button wire:click="clearFilters" class="text-xs underline" style="color:var(--text-muted);">Clear all</button>
                </div>
            @endif

            {{-- Join message --}}
            @if ($joinMessage)
                <div class="rounded-xl border px-4 py-3 text-sm" style="background:rgba(210,153,34,0.06);border-color:rgba(210,153,34,0.25);color:#D29922;">
                    {{ $joinMessage }}
                </div>
            @endif

            {{-- Loading — only while a filter/intent change is actually in flight, never during the background poll or join/pin actions --}}
            <div wire:loading wire:target="setIntent,toggleFilter,setFilterCategory,setFilterSubcategory,clearFilters,filterSearch" class="text-center py-16">
                <span class="cg-pulse-dot" aria-hidden="true"></span>
                <p class="mt-3 text-sm" style="color:var(--text-muted);">Finding comfortable places for you...</p>
            </div>

            <div wire:loading.remove wire:target="setIntent,toggleFilter,setFilterCategory,setFilterSubcategory,clearFilters,filterSearch">
            @if ($this->hasError)

                <div class="rounded-2xl border px-6 py-8 text-sm space-y-2 text-center" style="background:var(--surface);border-color:var(--border);color:var(--text-muted);">
                    <p style="color:var(--text);">Something went a bit wrong.</p>
                    <p>Try refreshing — we'll be here when you're back.</p>
                </div>

            @elseif ($browseMode)

                {{-- ═══ Discovery sections (browse mode) ═══════════════════════ --}}
                @php
                    $sections = $this->discoverySections;
                    $allEmpty = $sections['officialAll']->isEmpty()
                        && $sections['interestMatches']->isEmpty()
                        && $sections['secondaryMatches']->isEmpty()
                        && $sections['newRooms']->isEmpty()
                        && $sections['quietRooms']->isEmpty();
                @endphp

                @if ($allEmpty)
                    <div class="text-center py-24 space-y-3">
                        <p style="color:var(--text);">It's quiet everywhere right now.</p>
                        <p class="text-sm" style="color:var(--text-muted);">You could open a room and see who shows up.</p>
                        @auth
                            <a
                                href="{{ route('feed.post') }}"
                                wire:navigate
                                class="inline-block mt-2 px-4 py-2 text-sm font-medium rounded-lg transition"
                                style="background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);"
                                onmouseover="this.style.background='rgba(var(--accent-rgb),0.25)'" onmouseout="this.style.background='rgba(var(--accent-rgb),0.15)'"
                            >+ Open a room</a>
                        @endauth
                    </div>
                @else

                    {{-- SECTION 1 — A comfortable place to begin --}}
                    {{-- Glass UI (Phase 4): heavy tier — dense label/subtext/room entries. --}}
                    @if ($sections['officialAll']->isNotEmpty())
                        <x-glass-panel tier="heavy" x-data="{ expanded: false }" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-card-section);padding:1.75rem 2rem;margin-bottom:2.5rem;">
                            <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.25rem;">A comfortable place to begin</h2>
                            <p style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);margin-bottom:1.25rem;">Community spaces that stay open and welcome new arrivals.</p>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($sections['officialShown'] as $official)
                                    <x-room-card
                                        :room="$official"
                                        variant="compact"
                                        :state-label="$this->roomStateLabel($official)"
                                        :is-pinned="$isPinnedRoom($official)"
                                        :is-guest="$isGuestView"
                                        wire:key="disc-official-{{ $official->id }}"
                                    />
                                @endforeach
                            </div>
                            @if ($sections['officialMoreCount'] > 0)
                                <div x-show="expanded" style="display:none;" class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($sections['officialAll']->skip(2) as $official)
                                        <x-room-card
                                            :room="$official"
                                            variant="compact"
                                            :state-label="$this->roomStateLabel($official)"
                                            :is-pinned="$isPinnedRoom($official)"
                                            :is-guest="$isGuestView"
                                            wire:key="disc-official-more-{{ $official->id }}"
                                        />
                                    @endforeach
                                </div>
                                <button type="button" @click="expanded = !expanded" class="mt-2 text-xs transition" style="color:var(--accent);background:transparent;border:none;cursor:pointer;">
                                    <span x-show="!expanded" class="inline-flex items-center gap-1.5"><x-arrow-icon label="View all" /> ({{ $sections['officialMoreCount'] }} more)</span>
                                    <span x-show="expanded" style="display:none;">Show fewer</span>
                                </button>
                            @endif
                        </x-glass-panel>
                    @endif

                    @guest
                        {{-- Sections 2–5 require an account: user-created room content stays hidden from guests everywhere in this app. --}}
                        <div class="rounded-2xl border px-6 py-6 text-center text-sm" style="background:var(--surface);border-color:var(--border);color:var(--text-muted);">
                            <a href="{{ route('register') }}" wire:navigate style="color:var(--accent);text-decoration:underline;">Create a free account</a> to see rooms matched to your interests.
                        </div>
                    @endguest

                    @auth
                        {{-- SECTION 2 — You may feel at home here --}}
                        @if ($sections['interestMatches']->isNotEmpty())
                            <section style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card-section);padding:1.75rem 2rem;margin-bottom:2.5rem;">
                                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.25rem;">You may feel at home here</h2>
                                <p style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);margin-bottom:1.25rem;">Based on what you enjoy.</p>
                                <div class="mt-4">
                                    @foreach ($sections['interestMatches'] as $i => $room)
                                        <x-room-card
                                            :room="$room"
                                            :variant="$i === 0 ? 'featured' : 'list'"
                                            :state-label="$this->roomStateLabel($room)"
                                            :is-pinned="$isPinnedRoom($room)"
                                            :is-guest="false"
                                            wire:key="disc-interest-{{ $room->id }}"
                                            class="{{ $i === 0 ? 'mb-3' : '' }}"
                                        />
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- SECTION 3 — Based on your interests --}}
                        @if ($sections['secondaryMatches']->isNotEmpty())
                            <section style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card-section);padding:1.75rem 2rem;margin-bottom:2.5rem;">
                                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.25rem;">Based on your interests</h2>
                                <div class="mt-4">
                                    @foreach ($sections['secondaryMatches'] as $room)
                                        <x-room-card
                                            :room="$room"
                                            variant="list"
                                            :state-label="$this->roomStateLabel($room)"
                                            :is-pinned="$isPinnedRoom($room)"
                                            :is-guest="false"
                                            wire:key="disc-secondary-{{ $room->id }}"
                                        />
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- SECTION 4 — New conversations --}}
                        @if ($sections['newRooms']->isNotEmpty())
                            <section style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card-section);padding:1.75rem 2rem;margin-bottom:2.5rem;">
                                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.25rem;">New conversations</h2>
                                <p style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);margin-bottom:1.25rem;">Rooms opened recently.</p>
                                <div class="mt-4">
                                    @foreach ($sections['newRooms'] as $room)
                                        <x-room-card
                                            :room="$room"
                                            variant="list"
                                            :state-label="$this->roomStateLabel($room)"
                                            :is-pinned="$isPinnedRoom($room)"
                                            :is-guest="false"
                                            wire:key="disc-new-{{ $room->id }}"
                                        />
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- SECTION 5 — Quiet right now --}}
                        @if ($sections['quietRooms']->isNotEmpty())
                            <section style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card-section);padding:1.75rem 2rem;margin-bottom:2.5rem;">
                                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.25rem;">Quiet right now</h2>
                                <p style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);margin-bottom:1.25rem;">Worth a look when you want somewhere calm.</p>
                                <div class="mt-4">
                                    @foreach ($sections['quietRooms'] as $room)
                                        <x-room-card
                                            :room="$room"
                                            variant="list"
                                            :state-label="$this->roomStateLabel($room)"
                                            :is-pinned="$isPinnedRoom($room)"
                                            :is-guest="false"
                                            wire:key="disc-quiet-{{ $room->id }}"
                                        />
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endauth

                @endif

            @elseif (! auth()->check())

                {{-- Filtered/intent mode is a signed-in-only concept today (guests never had a personal filtered list). --}}

            @elseif ($this->showingFallback)

                <div class="rounded-2xl border px-6 py-8 text-sm space-y-3 text-center" style="background:var(--surface);border-color:var(--border);color:var(--text-muted);box-shadow:0 1px 4px rgba(0,0,0,0.25);">
                    <p style="color:var(--text);">Nothing quite like that right now.</p>
                    <p>Try a different search, or open a room yourself.</p>
                    <a href="{{ route('feed.post') }}" wire:navigate class="inline-block" aria-label="Open one"><x-arrow-icon label="Open one" /></a>
                </div>

            @elseif ($this->posts->isEmpty())

                <div class="text-center py-24 space-y-3">
                    <p style="color:var(--text);">It's quiet everywhere right now.</p>
                    <p class="text-sm" style="color:var(--text-muted);">You could open a room and see who shows up.</p>
                    <a
                        href="{{ route('feed.post') }}"
                        wire:navigate
                        class="inline-block mt-2 px-4 py-2 text-sm font-medium rounded-lg transition"
                        style="background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);"
                        onmouseover="this.style.background='rgba(var(--accent-rgb),0.25)'" onmouseout="this.style.background='rgba(var(--accent-rgb),0.15)'"
                    >+ Open a room</a>
                </div>

            @else

                @if ($this->filtersActive)
                    <p class="text-xs mb-3" style="color:var(--text-muted);">Showing closest matches first.</p>
                @endif

                <div>
                    @foreach ($this->posts as $post)
                        <x-room-card
                            :room="$post"
                            variant="list"
                            :state-label="$this->roomStateLabel($post)"
                            :is-pinned="$isPinnedRoom($post)"
                            :is-guest="false"
                            wire:key="post-{{ $post->id }}"
                        />
                    @endforeach
                </div>

            @endif
            </div>

            {{-- ── Not seeing the right place? — footer CTA ────────────────────── --}}
            {{-- Glass UI (Phase 4): light tier. --}}
            <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.75rem 2rem;text-align:center;margin-top:1rem;">
                <p class="font-display" style="font-size:1.1rem;color:var(--text);">Not seeing the right place?</p>
                <p class="mt-1" style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);">Open a room and describe the kind of conversation you want.</p>
                @auth
                    <a
                        href="{{ route('feed.post') }}"
                        wire:navigate
                        class="inline-block mt-4 px-4 py-2 text-sm font-medium rounded-lg transition"
                        style="background:rgba(var(--accent-rgb),0.15);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);"
                        onmouseover="this.style.background='rgba(var(--accent-rgb),0.25)'" onmouseout="this.style.background='rgba(var(--accent-rgb),0.15)'"
                    >+ Open a room</a>
                @endauth
            </x-glass-panel>

        </div>
    </div>

</div>
