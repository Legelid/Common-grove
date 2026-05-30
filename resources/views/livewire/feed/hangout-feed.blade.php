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
        style="display:none;border-color:#30363D;background:#161B22;"
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
                <p class="text-sm font-semibold" style="color:#E6EDF3;">Find your kind of room</p>
                <p class="text-xs mt-0.5 leading-relaxed" style="color:#8B949E;">Choose what feels right.</p>
            </div>
            <button
                type="button"
                @click="toggle()"
                :aria-expanded="filterOpen.toString()"
                aria-controls="filter-panel"
                aria-label="Close filter panel"
                class="flex-none w-5 h-5 flex items-center justify-center rounded text-xs transition mt-0.5"
                style="color:#3d4451;"
                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
            >✕</button>
        </div>

        {{-- Active filter chips --}}
        @if ($this->filtersActive)
            <div class="flex-none space-y-2 pb-3 border-b" style="border-color:#21262D;">
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($this->activeFilterTags as $tag)
                        <button
                            type="button"
                            wire:click="toggleFilter('{{ $tag->id }}')"
                            wire:key="af-{{ $tag->id }}"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition"
                            style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                            onmouseover="this.style.background='rgba(226,75,74,0.1)';this.style.color='#E24B4A';this.style.borderColor='rgba(226,75,74,0.3)';"
                            onmouseout="this.style.background='rgba(29,158,117,0.15)';this.style.color='#1D9E75';this.style.borderColor='rgba(29,158,117,0.3)';"
                        >{{ $tag->name }} <span aria-hidden="true" style="opacity:0.7;">×</span></button>
                    @endforeach
                </div>
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-xs transition"
                    style="color:#3d4451;"
                    onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                >Clear all</button>
            </div>
        @endif

        {{-- Search --}}
        <div class="flex-none relative">
            <input
                type="search"
                wire:model.live.debounce.300ms="filterSearch"
                placeholder="Search interests…"
                aria-label="Search interests"
                class="w-full rounded-lg px-3 py-2 text-xs focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.borderColor='rgba(29,158,117,0.4)'" onblur="this.style.borderColor='#30363D'"
            >
            @if ($filterSearch !== '')
                <button
                    type="button"
                    wire:click="$set('filterSearch', '')"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs leading-none transition"
                    style="color:#3d4451;"
                    aria-label="Clear search"
                    onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
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
                                ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                            @if(!$isSel)
                                onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
                            @endif
                        >{{ $tag->name }}</button>
                    @endforeach
                </div>
            @else
                <p class="text-xs" style="color:#3d4451;">No matching interests yet.</p>
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
                    style="color:#3d4451;"
                    onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                >
                    <span aria-hidden="true">←</span> All categories
                </button>

                <p class="text-xs font-semibold" style="color:#C9D1D9;">{{ $activeCat?->name }}</p>

                @if ($this->filterSubcategories->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->filterSubcategories as $subcat)
                            <button
                                type="button"
                                wire:click="setFilterSubcategory({{ $subcat->id }})"
                                wire:key="sc-{{ $subcat->id }}"
                                class="px-2 py-0.5 rounded text-xs transition"
                                style="{{ $filterSubcategoryId === $subcat->id
                                    ? 'background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);'
                                    : 'background:#21262D;color:#8B949E;border:1px solid #30363D;' }}"
                                @if($filterSubcategoryId !== $subcat->id)
                                    onmouseover="this.style.color='#C9D1D9';this.style.borderColor='#3d4451';"
                                    onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D';"
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
                                    ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                    : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                @if(!$isSel)
                                    onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                    onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
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
                                        ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                        : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                    @if(!$isSel)
                                        onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                        onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
                                    @endif
                                >{{ $tag->name }}</button>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="text-xs transition"
                            style="color:#3d4451;"
                            onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                        >
                            <span x-show="!showAll">See {{ $tagHidden }} more</span>
                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            @else
                <p class="text-xs" style="color:#3d4451;">No tags in this area yet.</p>
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
                        style="color:#8B949E;"
                        onmouseover="this.style.color='#C9D1D9';this.style.background='rgba(255,255,255,0.04)';"
                        onmouseout="this.style.color='#8B949E';this.style.background='';"
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

            {{-- Header row --}}
            <div class="flex items-end justify-between">
                <div>
                    <h1 class="text-xl font-semibold" style="color:#E6EDF3;">Open rooms</h1>
                    <p class="text-xs mt-1" style="color:#8B949E;">Places you might like right now</p>
                </div>
                @if (auth()->check())
                    <a
                        href="{{ route('feed.post') }}"
                        wire:navigate
                        class="px-4 py-2 text-sm font-medium rounded-lg transition"
                        style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                        onmouseover="this.style.background='rgba(29,158,117,0.25)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                    >+ Open a room</a>
                @endif
            </div>

            {{-- Filter trigger: desktop (authenticated only, hidden on mobile) --}}
            @auth
            <div class="hidden md:block" x-show="!filterOpen" style="display:none;">
                <p class="text-xs mb-2" style="color:#3d4451;">Not seeing something that fits?</p>
                <button
                    type="button"
                    @click="toggle()"
                    :aria-expanded="filterOpen.toString()"
                    aria-controls="filter-panel"
                    aria-label="Open room filters"
                    class="flex items-center justify-between gap-3 w-full px-4 py-3 rounded-xl text-sm transition"
                    style="background:#161B22;border:1px solid #30363D;color:#8B949E;"
                    onmouseover="this.style.background='#1C2333';this.style.borderColor='#3d4451';this.style.color='#C9D1D9';"
                    onmouseout="this.style.background='#161B22';this.style.borderColor='#30363D';this.style.color='#8B949E';"
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
            </div>

            @endauth

            {{-- Filter trigger + inline panel: mobile (authenticated only) --}}
            @auth
            <div class="md:hidden">
                <button
                    type="button"
                    @click="toggle()"
                    :aria-expanded="filterOpen.toString()"
                    aria-controls="mobile-filter-panel"
                    aria-label="Open room filters"
                    class="flex items-center justify-between gap-3 w-full px-4 py-3 rounded-xl text-sm transition"
                    :style="filterOpen
                        ? 'background:rgba(29,158,117,0.08);border:1px solid rgba(29,158,117,0.25);color:#1D9E75;'
                        : 'background:#161B22;border:1px solid #30363D;color:#8B949E;'"
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
            </div>

            {{-- Mobile inline filter panel (hidden on desktop via wrapper; Alpine toggles visibility on mobile) --}}
            <div class="md:hidden">
            <div
                id="mobile-filter-panel"
                class="rounded-xl border overflow-hidden"
                style="display:none;border-color:#30363D;background:#161B22;"
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
                        <div class="space-y-2 pb-3 border-b" style="border-color:#21262D;">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($this->activeFilterTags as $tag)
                                    <button
                                        type="button"
                                        wire:click="toggleFilter('{{ $tag->id }}')"
                                        wire:key="mob-af-{{ $tag->id }}"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition"
                                        style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                                        onmouseover="this.style.background='rgba(226,75,74,0.1)';this.style.color='#E24B4A';this.style.borderColor='rgba(226,75,74,0.3)';"
                                        onmouseout="this.style.background='rgba(29,158,117,0.15)';this.style.color='#1D9E75';this.style.borderColor='rgba(29,158,117,0.3)';"
                                    >{{ $tag->name }} <span aria-hidden="true" style="opacity:0.7;">×</span></button>
                                @endforeach
                            </div>
                            <button
                                type="button"
                                wire:click="clearFilters"
                                class="text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                            >Clear all</button>
                        </div>
                    @endif

                    {{-- Search --}}
                    <div class="relative">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="filterSearch"
                            placeholder="Search interests…"
                            aria-label="Search interests"
                            class="w-full rounded-lg px-3 py-2 text-xs focus:outline-none"
                            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                            onfocus="this.style.borderColor='rgba(29,158,117,0.4)'" onblur="this.style.borderColor='#30363D'"
                        >
                        @if ($filterSearch !== '')
                            <button
                                type="button"
                                wire:click="$set('filterSearch', '')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs leading-none transition"
                                style="color:#3d4451;"
                                aria-label="Clear search"
                                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
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
                                            ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                            : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                        @if(!$isSel)
                                            onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                            onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
                                        @endif
                                    >{{ $tag->name }}</button>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs" style="color:#3d4451;">No matching interests yet.</p>
                        @endif

                    @elseif ($filterCategoryId !== null)

                        {{-- CATEGORY DETAIL: back + subcategory chips + tags --}}
                        @php $activeCat = $this->filterCategories->firstWhere('id', $filterCategoryId); @endphp

                        <div class="space-y-3">
                            <button
                                type="button"
                                wire:click="clearFilterNav"
                                class="flex items-center gap-1 text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                            ><span aria-hidden="true">←</span> All categories</button>

                            <p class="text-xs font-semibold" style="color:#C9D1D9;">{{ $activeCat?->name }}</p>

                            @if ($this->filterSubcategories->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($this->filterSubcategories as $subcat)
                                        <button
                                            type="button"
                                            wire:click="setFilterSubcategory({{ $subcat->id }})"
                                            wire:key="mob-sc-{{ $subcat->id }}"
                                            class="px-2 py-0.5 rounded text-xs transition"
                                            style="{{ $filterSubcategoryId === $subcat->id
                                                ? 'background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);'
                                                : 'background:#21262D;color:#8B949E;border:1px solid #30363D;' }}"
                                            @if($filterSubcategoryId !== $subcat->id)
                                                onmouseover="this.style.color='#C9D1D9';this.style.borderColor='#3d4451';"
                                                onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D';"
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
                                                ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                                : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                            @if(!$isSel)
                                                onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                                onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
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
                                                    ? 'background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);'
                                                    : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                                                @if(!$isSel)
                                                    onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';"
                                                    onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';"
                                                @endif
                                            >{{ $tag->name }}</button>
                                        @endforeach
                                    </div>
                                    <button
                                        type="button"
                                        @click="mobShowAll = !mobShowAll"
                                        class="text-xs transition"
                                        style="color:#3d4451;"
                                        onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                                    >
                                        <span x-show="!mobShowAll">See {{ $mobTagHidden }} more</span>
                                        <span x-show="mobShowAll" style="display:none;">Show less</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <p class="text-xs" style="color:#3d4451;">No tags in this area yet.</p>
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
                                    style="color:#8B949E;"
                                    onmouseover="this.style.color='#C9D1D9';this.style.background='rgba(255,255,255,0.04)';"
                                    onmouseout="this.style.color='#8B949E';this.style.background='';"
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

            {{-- ── Starter rooms ─────────────────────────────────────────────── --}}
            @if ($this->officialRooms->isNotEmpty())
                @php
                    $lowStim = auth()->check() && auth()->user()->low_stimulation_mode;
                    $vibeMap = [
                        'Just Existing' => 'Quiet room',
                        'Casual Chat'   => 'Easy conversation',
                        'Brain Dump'    => 'Say what\'s on your mind',
                        'Starting Slow' => 'Gentle pace',
                    ];
                    $accentMap = [
                        'Just Existing' => [
                            'color'          => '#7A9FBF',
                            'badge_bg'       => 'rgba(100,130,160,0.08)',
                            'badge_border'   => 'rgba(100,130,160,0.18)',
                            'btn_bg'         => 'rgba(100,130,160,0.07)',
                            'btn_border'     => 'rgba(100,130,160,0.16)',
                            'btn_hover_bg'   => 'rgba(100,130,160,0.16)',
                            'btn_hover_brd'  => 'rgba(100,130,160,0.3)',
                            'glow_base'      => 'rgba(100,130,160,0.07)',
                            'glow_hover'     => 'rgba(100,130,160,0.13)',
                        ],
                        'Casual Chat' => [
                            'color'          => '#1D9E75',
                            'badge_bg'       => 'rgba(29,158,117,0.08)',
                            'badge_border'   => 'rgba(29,158,117,0.15)',
                            'btn_bg'         => 'rgba(29,158,117,0.08)',
                            'btn_border'     => 'rgba(29,158,117,0.18)',
                            'btn_hover_bg'   => 'rgba(29,158,117,0.18)',
                            'btn_hover_brd'  => 'rgba(29,158,117,0.3)',
                            'glow_base'      => 'rgba(29,158,117,0.09)',
                            'glow_hover'     => 'rgba(29,158,117,0.16)',
                        ],
                        'Brain Dump' => [
                            'color'          => '#9B8DD9',
                            'badge_bg'       => 'rgba(148,120,210,0.08)',
                            'badge_border'   => 'rgba(148,120,210,0.18)',
                            'btn_bg'         => 'rgba(148,120,210,0.07)',
                            'btn_border'     => 'rgba(148,120,210,0.16)',
                            'btn_hover_bg'   => 'rgba(148,120,210,0.18)',
                            'btn_hover_brd'  => 'rgba(148,120,210,0.32)',
                            'glow_base'      => 'rgba(148,120,210,0.10)',
                            'glow_hover'     => 'rgba(148,120,210,0.18)',
                        ],
                        'Starting Slow' => [
                            'color'          => '#C8A055',
                            'badge_bg'       => 'rgba(180,148,80,0.08)',
                            'badge_border'   => 'rgba(180,148,80,0.18)',
                            'btn_bg'         => 'rgba(180,148,80,0.07)',
                            'btn_border'     => 'rgba(180,148,80,0.16)',
                            'btn_hover_bg'   => 'rgba(180,148,80,0.16)',
                            'btn_hover_brd'  => 'rgba(180,148,80,0.3)',
                            'glow_base'      => 'rgba(180,148,80,0.07)',
                            'glow_hover'     => 'rgba(180,148,80,0.13)',
                        ],
                    ];
                @endphp
                {{-- Alpine scope shared by the mobile scroll row and the bottom sheet --}}
                <div
                    x-data="{ sheetOpen: false, room: {} }"
                    class="space-y-3"
                >
                    <div>
                        <p class="text-sm font-medium" style="color:#8B949E;">Starter rooms from CommonGrove</p>
                        <p class="text-xs mt-0.5" style="color:#3d4451;">Always-open spaces you can step into anytime.</p>
                    </div>

                    {{-- ── Mobile: 4-column grid ───────────────────────────────────── --}}
                    <div class="md:hidden">
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($this->officialRooms as $official)
                                @php
                                    $accent = $accentMap[$official->title] ?? $accentMap['Casual Chat'];
                                    $vibe   = $vibeMap[$official->title] ?? '';
                                    $icon   = $official->icon ?? 'room';
                                    $roomJs = \Illuminate\Support\Js::from([
                                        'id'      => $official->id,
                                        'title'   => $official->title ?? '',
                                        'content' => $official->content,
                                        'icon'    => $icon,
                                        'vibe'    => $vibe,
                                        'accent'  => $accent,
                                    ]);
                                @endphp
                                <button
                                    type="button"
                                    wire:key="mob-official-{{ $official->id }}"
                                    @click="room = {{ $roomJs }}; sheetOpen = true"
                                    class="rounded-xl border flex flex-col items-center text-center gap-1.5 p-2.5 active:opacity-70 transition-opacity"
                                    style="background:#121820;border-color:#253040;box-shadow:inset 0 0 0 1px {{ $accent['glow_base'] }};"
                                    aria-label="Open {{ $official->title }} room"
                                >
                                    <span style="color:{{ $accent['color'] }};" aria-hidden="true">
                                        @switch($icon)
                                            @case('moon') <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg> @break
                                            @case('brain') <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.16Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.16Z"/></svg> @break
                                            @case('leaf') <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8C8 10 5.9 16.17 3.82 20.6c-.26.57.56 1.04.97.55C7 18 10 16 15 16c4.58 0 7-3.5 7-3.5C24 9.5 17 8 17 8z"/></svg> @break
                                            @case('chat') <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> @break
                                            @default <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                        @endswitch
                                    </span>
                                    <p class="text-xs font-semibold leading-tight line-clamp-2" style="color:#E6EDF3;">{{ $official->title }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Desktop: 4-card grid ────────────────────────────────────── --}}
                    <div class="hidden md:block">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ($this->officialRooms as $official)
                            @php
                                $officialConvId   = $official->conversation?->id;
                                $officialIsPinned = $officialConvId && in_array($officialConvId, $this->pinnedConversationIds);
                                $icon             = $official->icon ?? 'room';
                                $accent     = $accentMap[$official->title] ?? $accentMap['Casual Chat'];
                                $cardBg     = '#121820';
                                $cardBorder = '#253040';
                                $cardShadow = $lowStim
                                    ? '0 1px 3px rgba(0,0,0,0.2)'
                                    : "inset 0 0 0 1px {$accent['glow_base']},0 1px 4px rgba(0,0,0,0.3)";
                                $hoverShadow = $lowStim
                                    ? '0 1px 3px rgba(0,0,0,0.2)'
                                    : "inset 0 0 0 1px {$accent['glow_hover']},0 2px 8px rgba(0,0,0,0.35)";
                            @endphp
                            <div
                                wire:key="official-{{ $official->id }}"
                                x-data="{ isTouch: ('ontouchstart' in window || window.matchMedia('(pointer:coarse)').matches) }"
                                @click="if (isTouch && !$event.target.closest('[data-no-card-tap]')) $wire.joinHangout('{{ $official->id }}')"
                                :class="isTouch ? 'cursor-pointer' : ''"
                                class="cg-room-card rounded-xl border flex flex-col"
                                style="background:{{ $cardBg }};border-color:{{ $cardBorder }};box-shadow:{{ $cardShadow }};"
                            >
                                {{-- Top: icon + badge + "Always open" + pin --}}
                                <div class="flex items-start justify-between gap-1.5 px-3 pt-3 pb-2">
                                    <div class="flex flex-wrap items-center gap-1 min-w-0">
                                        <span class="flex-none" style="color:#3d4451;" aria-hidden="true">
                                            @switch($icon)
                                                @case('moon')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                                                    @break
                                                @case('brain')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.16Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.16Z"/></svg>
                                                    @break
                                                @case('leaf')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8C8 10 5.9 16.17 3.82 20.6c-.26.57.56 1.04.97.55C7 18 10 16 15 16c4.58 0 7-3.5 7-3.5C24 9.5 17 8 17 8z"/></svg>
                                                    @break
                                                @case('book')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                                    @break
                                                @case('gamepad')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="15" y1="13" x2="15.01" y2="13"/><line x1="18" y1="11" x2="18.01" y2="11"/><rect x="2" y="6" width="20" height="12" rx="2"/></svg>
                                                    @break
                                                @case('chat')
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                                    @break
                                                @default
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                            @endswitch
                                        </span>
                                        <span class="text-xs px-1.5 py-px rounded font-medium leading-none"
                                            style="background:{{ $accent['badge_bg'] }};color:{{ $accent['color'] }};border:1px solid {{ $accent['badge_border'] }};">Open Grove</span>
                                        <span class="text-xs leading-none" style="color:#3d4451;">Always open</span>
                                    </div>
                                    @if (auth()->check())
                                        <button
                                            data-no-card-tap
                                            type="button"
                                            wire:click="toggleCardPin('{{ $official->id }}')"
                                            title="{{ $officialIsPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                                            aria-label="{{ $officialIsPinned ? 'Unpin room' : 'Pin room' }}"
                                            class="flex-none p-1 rounded"
                                            style="{{ $officialIsPinned ? 'color:#1D9E75;' : 'color:#3d4451;' }}"
                                            onmouseover="this.style.color='{{ $officialIsPinned ? '#E24B4A' : '#8B949E' }}'"
                                            onmouseout="this.style.color='{{ $officialIsPinned ? '#1D9E75' : '#3d4451' }}'"
                                        >
                                            @if ($officialIsPinned)
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                            @else
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                            @endif
                                        </button>
                                    @endif
                                </div>

                                {{-- Middle: title + description (grows to fill available space) --}}
                                <div class="flex-1 px-3 pb-2 space-y-1">
                                    @if ($official->title)
                                        <p class="text-xs font-semibold leading-snug" style="color:#E6EDF3;">{{ $official->title }}</p>
                                    @endif
                                    <p class="text-xs leading-relaxed line-clamp-2" style="color:#8B949E;">{{ $official->content }}</p>
                                </div>

                                {{-- Bottom: Step in button, always flush to the card bottom --}}
                                <div class="px-3 pb-3">
                                    @if (auth()->check())
                                        <button
                                            data-no-card-tap
                                            type="button"
                                            wire:click="joinHangout('{{ $official->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="joinHangout('{{ $official->id }}')"
                                            class="w-full py-1.5 text-xs font-medium rounded-lg disabled:opacity-50"
                                            style="background:{{ $accent['btn_bg'] }};color:{{ $accent['color'] }};border:1px solid {{ $accent['btn_border'] }};"
                                            onmouseover="this.style.background='{{ $accent['btn_hover_bg'] }}';this.style.borderColor='{{ $accent['btn_hover_brd'] }}';"
                                            onmouseout="this.style.background='{{ $accent['btn_bg'] }}';this.style.borderColor='{{ $accent['btn_border'] }}';"
                                        >
                                            <span wire:loading.remove wire:target="joinHangout('{{ $official->id }}')">Step in</span>
                                            <span wire:loading wire:target="joinHangout('{{ $official->id }}')">Entering…</span>
                                        </button>
                                    @else
                                        @php $previewConvId = $official->conversation?->id; @endphp
                                        <a
                                            href="{{ $previewConvId ? route('room.show', $previewConvId) : route('register') }}"
                                            wire:navigate
                                            data-no-card-tap
                                            class="block w-full py-1.5 text-xs font-medium rounded-lg text-center transition"
                                            style="background:{{ $accent['btn_bg'] }};color:{{ $accent['color'] }};border:1px solid {{ $accent['btn_border'] }};"
                                            onmouseover="this.style.background='{{ $accent['btn_hover_bg'] }}'"
                                            onmouseout="this.style.background='{{ $accent['btn_bg'] }}'"
                                        >Take a peek</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>{{-- end hidden md:block desktop grid wrapper --}}

                    {{-- ── Mobile bottom sheet ───────────────────────────────────── --}}
                    {{--
                        Outer: fixed overlay fades in (covers the backdrop).
                        Inner: sheet panel sits at the bottom of the flex column.
                        md:hidden keeps it invisible on desktop even if sheetOpen fires.
                    --}}
                    <div
                        x-show="sheetOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex flex-col justify-end md:hidden"
                        style="display:none;"
                    >
                        {{-- Backdrop --}}
                        <div
                            class="absolute inset-0"
                            @click="sheetOpen = false"
                            style="background:rgba(0,0,0,0.65);"
                        ></div>

                        {{-- Sheet panel --}}
                        <div class="relative rounded-t-2xl overflow-hidden" style="background:#161B22;border-top:1px solid #30363D;">

                            {{-- Drag handle --}}
                            <div class="flex justify-center pt-3 pb-0.5" aria-hidden="true">
                                <div class="w-10 h-1 rounded-full" style="background:#30363D;"></div>
                            </div>

                            <div class="px-6 pt-5 pb-8 space-y-5">

                                {{-- Header: icon + title/vibe + close --}}
                                <div class="flex items-start gap-4">
                                    {{-- Icon — one SVG per possible value, Alpine toggles which is visible --}}
                                    <span class="flex-none mt-0.5" :style="{ color: room.accent?.color ?? '#8B949E' }" aria-hidden="true">
                                        <svg x-show="room.icon === 'moon'" style="display:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                                        <svg x-show="room.icon === 'chat'" style="display:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        <svg x-show="room.icon === 'brain'" style="display:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.16Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.16Z"/></svg>
                                        <svg x-show="room.icon === 'leaf'" style="display:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8C8 10 5.9 16.17 3.82 20.6c-.26.57.56 1.04.97.55C7 18 10 16 15 16c4.58 0 7-3.5 7-3.5C24 9.5 17 8 17 8z"/></svg>
                                        <svg x-show="!['moon','chat','brain','leaf'].includes(room.icon)" style="display:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                    </span>

                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-base font-semibold leading-snug" style="color:#E6EDF3;" x-text="room.title"></h2>
                                        <p class="text-xs mt-0.5" style="color:#6B737C;" x-text="room.vibe"></p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="sheetOpen = false"
                                        class="flex-none w-8 h-8 flex items-center justify-center rounded-lg text-lg leading-none transition"
                                        style="color:#8B949E;"
                                        onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                        aria-label="Close"
                                    >×</button>
                                </div>

                                {{-- Description --}}
                                <p class="text-sm leading-relaxed" style="color:#8B949E;" x-text="room.content"></p>

                                {{-- Badges --}}
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="text-xs px-2 py-0.5 rounded font-medium"
                                        :style="{ background: room.accent?.badge_bg, color: room.accent?.color, border: '1px solid ' + (room.accent?.badge_border ?? 'transparent') }"
                                    >Open Grove</span>
                                    <span class="text-xs" style="color:#3d4451;">Always open</span>
                                </div>

                                {{-- Step in --}}
                                <button
                                    type="button"
                                    @click="sheetOpen = false; $wire.joinHangout(room.id)"
                                    class="w-full py-4 text-sm font-semibold rounded-xl transition"
                                    :style="{
                                        background: room.accent?.btn_hover_bg,
                                        color:      room.accent?.color,
                                        border:     '1px solid ' + (room.accent?.btn_border ?? 'transparent')
                                    }"
                                >Step in</button>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t" style="border-color:#21262D;"></div>
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
                    @foreach ($this->activeFilterTags as $ft)
                        <span class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
                            style="background:rgba(210,153,34,0.15);color:#E6B84A;border:1px solid rgba(210,153,34,0.35);">
                            {{ $ft->name }}
                            <button wire:click="toggleFilter('{{ $ft->id }}')" class="ml-0.5 leading-none" style="color:#D29922;">&times;</button>
                        </span>
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

            {{-- Feed content (user-created rooms — authenticated only) --}}
            @auth
            @if ($this->showingFallback)
                <div class="rounded-2xl border px-6 py-8 text-sm space-y-3 text-center" style="background:#161B22;border-color:#30363D;color:#8B949E;box-shadow:0 1px 4px rgba(0,0,0,0.25);">
                    <p>@tone('empty_feed', "It's quiet right now — no rooms match those filters.")</p>
                    @if (auth()->check())
                        <a href="{{ route('feed.post') }}" wire:navigate class="inline-block text-sm underline transition" style="color:#1D9E75;">Open one →</a>
                    @endif
                </div>
            @elseif ($this->posts->isEmpty())
                <div class="text-center py-24 space-y-3">
                    <p class="text-sm" style="color:#8B949E;">@tone('empty_feed', 'No active rooms right now.')</p>
                    @if (auth()->check())
                        <a href="{{ route('feed.post') }}" wire:navigate class="inline-block text-sm underline transition" style="color:#1D9E75;">Open a room →</a>
                    @endif
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
                            x-data="{ isTouch: ('ontouchstart' in window || window.matchMedia('(pointer:coarse)').matches) }"
                            @click="if (isTouch && !$event.target.closest('[data-no-card-tap]')) $wire.joinHangout('{{ $post->id }}')"
                            :class="isTouch ? 'cursor-pointer' : ''"
                            class="cg-room-card rounded-2xl border p-5 md:p-7 space-y-4 md:space-y-5"
                            style="background:#161B22;border-color:#30363D;box-shadow:0 1px 3px rgba(0,0,0,0.25),0 0 0 1px rgba(255,255,255,0.03) inset;"
                        >
                            {{-- Card header: badge + author (hangouts/untitled) or badge alone (titled rooms) --}}
                            <div class="flex items-start justify-between gap-3">
                                @if ($isPersistent && $post->title)
                                    <div></div>
                                @else
                                    <div class="min-w-0">
                                        <div class="flex flex-col md:flex-row md:items-center md:gap-0">
                                            <x-user-name :user="$post->user" class="font-medium text-sm" style="color:#E6EDF3;" />
                                            <span class="hidden md:inline text-xs mx-1.5" style="color:#3d4451;">·</span>
                                            <span class="text-xs whitespace-nowrap mt-0.5 md:mt-0" style="color:#8B949E;">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if ($isPersistent)
                                    <span class="cg-room-badge flex-none text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap"
                                        style="background:rgba(29,158,117,0.1);color:#1D9E75;border:1px solid rgba(29,158,117,0.25);">
                                        Always-open room
                                    </span>
                                @else
                                    <span class="cg-room-badge flex-none text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap"
                                        style="background:rgba(210,153,34,0.1);color:#D29922;border:1px solid rgba(210,153,34,0.25);">
                                        Temporary hangout
                                    </span>
                                @endif
                            </div>

                            {{-- Room identity: title + creator metadata (titled persistent rooms only) --}}
                            @if ($isPersistent && $post->title)
                                <div class="space-y-0.5">
                                    <h2 class="text-base font-semibold leading-snug" style="color:#E6EDF3;">{{ $post->title }}</h2>
                                    <div class="flex flex-wrap items-baseline gap-x-1 text-xs" style="color:#8B949E;">
                                        <span>by <x-user-name :user="$post->user" style="color:#C9D1D9;" /></span>
                                        <span class="whitespace-nowrap">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Content --}}
                            <p class="text-sm leading-relaxed" style="color:#E6EDF3;">{{ $post->content }}</p>

                            {{-- Tags --}}
                            @if ($post->tags->isNotEmpty())
                                <div class="cg-room-card-tags flex flex-wrap gap-2">
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
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-2 md:pt-1 border-t" style="border-color:#21262D;">

                                {{-- Metadata: stacked on mobile, inline on desktop --}}
                                <div class="flex flex-col md:flex-row md:items-center text-xs gap-y-0.5" style="color:#8B949E;">
                                    @if ($isPersistent)
                                        <span>by <x-user-name :user="$post->user" style="color:#C9D1D9;" /></span>
                                        <span class="hidden md:inline mx-1.5" style="color:#30363D;">·</span>
                                    @else
                                        <span class="whitespace-nowrap">Closes in {{ $post->expiresInFormatted() }}</span>
                                        <span class="hidden md:inline mx-1.5" style="color:#30363D;">·</span>
                                    @endif
                                    <span>{{ $post->joined_count }} {{ Str::plural('person', $post->joined_count) }} joined</span>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    {{-- Pin button (authenticated only) --}}
                                    @if (auth()->check())
                                        <button
                                            data-no-card-tap
                                            type="button"
                                            wire:click="toggleCardPin('{{ $post->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleCardPin('{{ $post->id }}')"
                                            title="{{ $isCardPinned ? 'Remove from Your Rooms' : 'Save to Your Rooms' }}"
                                            aria-label="{{ $isCardPinned ? 'Unpin room' : 'Pin room' }}"
                                            class="flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs transition disabled:opacity-40 flex-none"
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
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                                </svg>
                                                <span>Pinned</span>
                                            @else
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                                </svg>
                                                <span>Pin</span>
                                            @endif
                                        </button>
                                    @endif

                                    {{-- Join / enter button: authenticated users get the wire action, guests get a signup link --}}
                                    @if (auth()->check())
                                        <button
                                            data-no-card-tap
                                            type="button"
                                            wire:click="joinHangout('{{ $post->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="joinHangout('{{ $post->id }}')"
                                            class="flex-1 md:flex-none px-5 py-1.5 text-sm font-medium rounded-lg transition disabled:opacity-50 whitespace-nowrap text-center"
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
                                    @else
                                        <a
                                            href="{{ route('register') }}"
                                            class="flex-1 md:flex-none px-5 py-1.5 text-sm font-medium rounded-lg whitespace-nowrap text-center transition"
                                            style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                                            onmouseover="this.style.background='rgba(29,158,117,0.28)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                                        >Join the Conversation</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
            @endauth

        </div>
    </div>

</div>
