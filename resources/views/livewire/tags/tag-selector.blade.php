<div class="px-6 py-10 max-w-3xl mx-auto space-y-8">

    <div>
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Your Interests</h1>
        <p class="mt-1 text-sm leading-relaxed" style="color:#8B949E;">Pick what feels right — people with similar interests will find you.</p>
    </div>

    {{-- ── Selected tags bar ────────────────────────────────────────────── --}}
    @if (count($selectedTagIds) > 0)
        <section>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">
                    Selected ({{ count($selectedTagIds) }}/30)
                    @if (count($selectedTagIds) >= 3)
                        &nbsp;<span style="color:#1D9E75;">✓</span>
                    @else
                        &nbsp;<span style="color:#D29922;">— pick {{ 3 - count($selectedTagIds) }} more</span>
                    @endif
                </span>
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="text-xs px-3 py-1 rounded-lg font-medium transition disabled:opacity-50"
                    style="{{ count($selectedTagIds) >= 3
                        ? 'background:#1D9E75;color:#fff;'
                        : 'background:#21262D;color:#8B949E;cursor:not-allowed;' }}"
                    @if (count($selectedTagIds) >= 3)
                        onmouseover="this.style.background='#1a9068'" onmouseout="this.style.background='#1D9E75'"
                    @endif
                >
                    <span wire:loading.remove>Save</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach ($this->selectedTags as $tag)
                    <button
                        type="button"
                        wire:click="toggleTag('{{ $tag->id }}')"
                        wire:key="sel-{{ $tag->id }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition"
                        style="background:#1D9E75;color:#fff;"
                        title="Remove"
                    >
                        {{ $tag->name }}
                        <span style="opacity:0.7;font-size:0.7rem;line-height:1;">✕</span>
                    </button>
                @endforeach
            </div>

            @if ($saveMessage)
                <p class="mt-2 text-sm" style="color:{{ str_starts_with($saveMessage, 'Your interests') ? '#1D9E75' : '#E24B4A' }};">
                    {{ $saveMessage }}
                </p>
            @endif

            @if ($maxTagsMessage)
                <p class="mt-2 text-sm" style="color:#E24B4A;">{{ $maxTagsMessage }}</p>
            @endif
        </section>
    @else
        <div class="flex items-center gap-2 text-sm" style="color:#8B949E;">
            <span>Pick at least 3 interests to get started.</span>
        </div>
    @endif

    {{-- ── Search ───────────────────────────────────────────────────────── --}}
    <div>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search all interests…"
            class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
            onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
        >
    </div>

    {{-- ── Category chips (hidden during search) ───────────────────────── --}}
    @if (trim($search) === '')
        <section>
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#8B949E;">Browse by category</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($this->categories as $cat)
                    <button
                        type="button"
                        wire:click="setCategory({{ $cat->id }})"
                        wire:key="cat-chip-{{ $cat->id }}"
                        class="px-3.5 py-1.5 rounded-full text-sm font-medium transition"
                        style="{{ $activeCategoryId === $cat->id
                            ? 'background:#1D9E75;color:#fff;'
                            : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                        onmouseover="{{ $activeCategoryId === $cat->id ? '' : "this.style.color='#E6EDF3'" }}"
                        onmouseout="{{ $activeCategoryId === $cat->id ? '' : "this.style.color='#8B949E'" }}"
                    >{{ $cat->name }}</button>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── Subcategories + tags ─────────────────────────────────────────── --}}
    @if ($this->subcategoriesWithTags->isNotEmpty())
        <div class="space-y-7">
            @if (trim($search) !== '')
                <p class="text-xs" style="color:#8B949E;">
                    Showing results for <span style="color:#E6EDF3;">"{{ $search }}"</span>
                </p>
            @endif

            @foreach ($this->subcategoriesWithTags as $subcat)
                @if ($subcat->tags->isNotEmpty())
                    {{--
                        Alpine x-data per subcategory handles "See more" client-side.
                        wire:key ensures Alpine state survives Livewire re-renders.
                    --}}
                    <section
                        wire:key="subcat-{{ $subcat->id }}"
                        x-data="{ showAll: false }"
                    >
                        <h3 class="text-xs font-semibold uppercase tracking-wider mb-2.5" style="color:#6B737C;">
                            {{ $subcat->name }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            {{-- First 8 always visible --}}
                            @foreach ($subcat->tags->take(8) as $tag)
                                @php $selected = in_array($tag->id, $selectedTagIds); @endphp
                                <button
                                    type="button"
                                    wire:click="toggleTag('{{ $tag->id }}')"
                                    wire:key="tag-{{ $tag->id }}"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition"
                                    style="{{ $selected
                                        ? 'background:#1D9E75;color:#fff;outline:2px solid rgba(29,158,117,0.4);'
                                        : 'background:#1C2333;color:#8B949E;' }}"
                                    onmouseover="{{ $selected ? '' : "this.style.color='#E6EDF3'" }}"
                                    onmouseout="{{ $selected ? '' : "this.style.color='#8B949E'" }}"
                                >{{ $tag->name }}</button>
                            @endforeach

                            {{-- Remaining, toggled by Alpine --}}
                            @foreach ($subcat->tags->skip(8) as $tag)
                                @php $selected = in_array($tag->id, $selectedTagIds); @endphp
                                <button
                                    type="button"
                                    x-show="showAll"
                                    wire:click="toggleTag('{{ $tag->id }}')"
                                    wire:key="tag-{{ $tag->id }}"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition"
                                    style="{{ $selected
                                        ? 'background:#1D9E75;color:#fff;outline:2px solid rgba(29,158,117,0.4);'
                                        : 'background:#1C2333;color:#8B949E;' }}"
                                    onmouseover="{{ $selected ? '' : "this.style.color='#E6EDF3'" }}"
                                    onmouseout="{{ $selected ? '' : "this.style.color='#8B949E'" }}"
                                >{{ $tag->name }}</button>
                            @endforeach
                        </div>

                        @if ($subcat->tags->count() > 8)
                            <button
                                type="button"
                                @click="showAll = !showAll"
                                class="mt-2 text-xs transition"
                                style="color:#3d4451;"
                                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                                x-text="showAll ? 'Show less' : 'See {{ $subcat->tags->count() - 8 }} more…'"
                            ></button>
                        @endif
                    </section>
                @endif
            @endforeach
        </div>
    @elseif ($activeCategoryId !== null || trim($search) !== '')
        <p class="text-sm" style="color:#8B949E;">No interests found{{ trim($search) !== '' ? ' for that search' : '' }}.</p>
    @endif

    {{-- ── Save (bottom, for when user scrolls down) ───────────────────── --}}
    @if (count($selectedTagIds) >= 3)
        <div class="pt-2">
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-lg font-semibold text-sm transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#1a9068'" onmouseout="this.style.background='#1D9E75'"
            >
                <span wire:loading.remove>Save interests</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>
    @endif

    <hr style="border-color:#30363D;">

    {{-- ── Submit custom tag ────────────────────────────────────────────── --}}
    <section>
        <h2 class="text-sm font-semibold mb-1" style="color:#E6EDF3;">Don't see your interest?</h2>
        <p class="text-xs mb-3" style="color:#8B949E;">Submit a tag for review. It will appear once approved.</p>

        <div class="flex gap-3">
            <input
                type="text"
                wire:model="customTagName"
                wire:keydown.enter="submitCustomTag"
                placeholder="e.g. Urban Sketching"
                maxlength="50"
                class="flex-1 rounded-lg px-4 py-2 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            <button
                type="button"
                wire:click="submitCustomTag"
                wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium rounded-lg transition disabled:opacity-50"
                style="background:#21262D;color:#8B949E;"
                onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
            >Submit</button>
        </div>

        @error('customTagName')
            <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
        @enderror

        @if ($customTagMessage)
            <p class="mt-2 text-sm" style="color:{{ str_starts_with($customTagMessage, 'Your tag') ? '#1D9E75' : '#D29922' }};">
                {{ $customTagMessage }}
            </p>
        @endif
    </section>

</div>
