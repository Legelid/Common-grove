<div class="px-6 py-10 max-w-xl mx-auto space-y-6">

    <div>
        <a href="{{ route('feed') }}" wire:navigate class="text-sm underline transition" style="color:#1D9E75;">← Back to feed</a>
        <h1 class="mt-3 text-2xl font-bold" style="color:#E6EDF3;">Open a hangout or room</h1>
        <p class="mt-1 text-sm" style="color:#8B949E;">Choose what you want to create, then fill in the details.</p>
    </div>

    @error('postType')
        <p class="text-sm" style="color:#E24B4A;">{{ $message }}</p>
    @enderror

    {{-- ── Step 1: Type selection ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3">

        <button
            type="button"
            wire:click="selectType('hangout')"
            class="text-left rounded-xl border p-4 space-y-1.5 transition focus:outline-none focus-visible:ring-2"
            style="{{ $postType === 'hangout'
                ? 'background:rgba(210,153,34,0.12);border-color:rgba(210,153,34,0.6);'
                : 'background:#1C2333;border-color:#30363D;' }}"
            onmouseover="if('{{ $postType }}' !== 'hangout') { this.style.borderColor='rgba(210,153,34,0.35)'; }"
            onmouseout="if('{{ $postType }}' !== 'hangout') { this.style.borderColor='#30363D'; }"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:#D29922;">Temporary hangout</span>
                @if ($postType === 'hangout')
                    <span class="w-2 h-2 rounded-full" style="background:#D29922;"></span>
                @endif
            </div>
            <p class="text-xs leading-snug" style="color:#8B949E;">A short-lived space for people who want to talk right now.</p>
        </button>

        <button
            type="button"
            wire:click="selectType('room')"
            class="text-left rounded-xl border p-4 space-y-1.5 transition focus:outline-none focus-visible:ring-2"
            style="{{ $postType === 'room'
                ? 'background:rgba(29,158,117,0.12);border-color:rgba(29,158,117,0.6);'
                : 'background:#1C2333;border-color:#30363D;' }}"
            onmouseover="if('{{ $postType }}' !== 'room') { this.style.borderColor='rgba(29,158,117,0.35)'; }"
            onmouseout="if('{{ $postType }}' !== 'room') { this.style.borderColor='#30363D'; }"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:#1D9E75;">Always-open room</span>
                @if ($postType === 'room')
                    <span class="w-2 h-2 rounded-full" style="background:#1D9E75;"></span>
                @endif
            </div>
            <p class="text-xs leading-snug" style="color:#8B949E;">A persistent space people can return to anytime.</p>
        </button>

    </div>

    {{-- ── Persistent room limit message ────────────────────────────────── --}}
    @if ($roomLimitMessage === 'limit_reached')
        <div class="rounded-xl border px-5 py-5 space-y-3" style="background:rgba(29,158,117,0.04);border-color:rgba(29,158,117,0.15);">
            <p class="text-sm leading-relaxed" style="color:#C9D1D9;">
                You've created a few spaces already.
            </p>
            <p class="text-sm leading-relaxed" style="color:#8B949E;">
                If you'd like to create more, you can support CommonGrove.<br>
                No pressure — your current rooms will always stay available.
            </p>
            <a
                href="{{ route('feed') }}"
                wire:navigate
                class="inline-block text-xs underline transition"
                style="color:#1D9E75;"
            >Back to the feed →</a>
        </div>
    @endif

    {{-- ── Duration picker (hangout only) ────────────────────────────────── --}}
    @if ($postType === 'hangout')
        <div class="rounded-xl border p-4 space-y-3" style="background:#1C2333;border-color:#30363D;">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">How long should it stay open?</p>
            <div class="grid grid-cols-4 gap-2">
                @foreach (['1' => '1 hour', '4' => '4 hours', '12' => '12 hours', '24' => '24 hours'] as $val => $label)
                    <button
                        type="button"
                        wire:click="$set('duration', '{{ $val }}')"
                        class="py-2 rounded-lg text-xs font-medium transition focus:outline-none"
                        style="{{ $duration === $val
                            ? 'background:rgba(210,153,34,0.2);border:1px solid rgba(210,153,34,0.6);color:#D29922;'
                            : 'background:#21262D;border:1px solid #30363D;color:#8B949E;' }}"
                    >{{ $label }}</button>
                @endforeach
            </div>
            @error('duration')
                <p class="text-xs" style="color:#E24B4A;">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- ── Main form (shown once type is chosen, and limit not reached) ────── --}}
    @if ($postType !== '' && $roomLimitMessage !== 'limit_reached')

        <form wire:submit="submit" class="space-y-6">

            {{-- Room name (persistent rooms only) --}}
            @if ($postType === 'room')
                <div>
                    <label for="roomTitle" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                        Room name
                    </label>
                    <input
                        id="roomTitle"
                        type="text"
                        wire:model.live="roomTitle"
                        maxlength="60"
                        placeholder="e.g. Late-night cozy gaming"
                        autocomplete="off"
                        class="w-full rounded-lg px-4 py-3 text-sm focus:outline-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has('roomTitle') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    >
                    <div class="mt-1 flex items-start justify-between gap-2">
                        @error('roomTitle')
                            <p class="text-sm" style="color:#E24B4A;">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <span class="flex-none text-xs" style="color:{{ strlen($roomTitle) >= 54 ? '#E24B4A' : '#3d4451' }};">
                            {{ strlen($roomTitle) }}/60
                        </span>
                    </div>
                </div>
            @endif

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                    @if ($postType === 'hangout') What are you up to? @else Short description @endif
                </label>
                <div class="relative">
                    <textarea
                        id="content"
                        wire:model.live="content"
                        maxlength="280"
                        rows="4"
                        placeholder="{{ $postType === 'hangout'
                            ? 'Playing Stardew Valley tonight, looking for someone to chat with about the game…'
                            : 'A chill place to talk about books, low-pressure, drop in anytime…' }}"
                        class="w-full rounded-lg px-4 py-3 text-sm focus:outline-none resize-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has('content') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    ></textarea>
                    <span class="absolute bottom-2 right-3 text-xs" style="color:{{ strlen($content) >= 260 ? '#E24B4A' : '#8B949E' }};">
                        {{ strlen($content) }}/280
                    </span>
                </div>
                @if ($hasUrlWarning)
                    <p class="mt-1 text-sm" style="color:#D29922;">Posts cannot contain external links.</p>
                @endif
                @error('content')
                    <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tags --}}
            <div class="space-y-4 rounded-xl border p-5" style="background:#161B22;border-color:#30363D;">
                <p class="text-sm font-semibold" style="color:#E6EDF3;">Tags</p>
                <p class="text-xs" style="color:#8B949E;">These help the right people find the
                    @if ($postType === 'hangout') hangout @else room @endif.
                </p>

                {{-- Interests (required, pick 1–5) --}}
                <div class="space-y-3">
                    <div class="flex items-baseline justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">
                            Interests <span class="font-normal normal-case">— pick 1–5</span>
                        </p>
                        <span class="text-xs" style="color:{{ count($selectedTagIds) >= 5 ? '#D29922' : '#3d4451' }};">
                            {{ count($selectedTagIds) }}/5
                        </span>
                    </div>

                    {{-- Selected tag chips (amber, always visible, hover-to-remove) --}}
                    @if (count($selectedTagIds) > 0)
                        @php
                            $visibleIds   = $this->tagSelectionTags->pluck('id')->all();
                            $allChipIds   = array_unique(array_merge(
                                $this->tagSelectionTags->filter(fn($t) => in_array($t->id, $selectedTagIds))->pluck('id')->all(),
                                array_diff($selectedTagIds, $visibleIds),
                            ));
                            $chipTags = \App\Models\Tag::whereIn('id', $selectedTagIds)->get()->keyBy('id');
                        @endphp
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($selectedTagIds as $selId)
                                @if ($chipTags->has($selId))
                                    <button type="button"
                                        wire:click="toggleInterest('{{ $selId }}')"
                                        wire:key="chip-{{ $selId }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition"
                                        style="background:rgba(210,153,34,0.15);color:#E6B84A;border:1px solid rgba(210,153,34,0.4);"
                                        onmouseover="this.style.background='rgba(226,75,74,0.08)';this.style.color='#E24B4A';this.style.borderColor='rgba(226,75,74,0.25)';"
                                        onmouseout="this.style.background='rgba(210,153,34,0.15)';this.style.color='#E6B84A';this.style.borderColor='rgba(210,153,34,0.4)';"
                                    >{{ $chipTags[$selId]->name }} <span aria-hidden="true" style="opacity:0.7;">×</span></button>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Search --}}
                    <div class="relative">
                        <input
                            type="search"
                            wire:model.live.debounce.250ms="tagSearch"
                            placeholder="Search interests…"
                            aria-label="Search interests"
                            class="w-full rounded-lg px-3 py-2 text-xs focus:outline-none"
                            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                            onfocus="this.style.borderColor='rgba(29,158,117,0.4)'" onblur="this.style.borderColor='#30363D'"
                        >
                        @if ($tagSearch !== '')
                            <button type="button" wire:click="$set('tagSearch','')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs transition"
                                style="color:#3d4451;"
                                aria-label="Clear search"
                                onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                            >✕</button>
                        @endif
                    </div>

                    {{-- Browser area --}}
                    @if ($tagSearch !== '')

                        {{-- Search results: standard tag bubbles --}}
                        @if ($this->tagSelectionTags->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($this->tagSelectionTags as $tag)
                                    <x-tag-bubble
                                        :label="$tag->name"
                                        :selected="in_array($tag->id, $selectedTagIds, true)"
                                        :disabled="!in_array($tag->id, $selectedTagIds, true) && count($selectedTagIds) >= 5"
                                        wire:click="toggleInterest('{{ $tag->id }}')"
                                        wire:key="ts-{{ $tag->id }}"
                                    />
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs" style="color:#3d4451;">No matching interests.</p>
                        @endif

                    @elseif ($tagCategoryId !== null)

                        {{-- Category detail: back breadcrumb + subcategory pills + tag bubbles --}}
                        @php $activeCat = $this->tagCategories->firstWhere('id', $tagCategoryId); @endphp
                        <div class="space-y-3">

                            {{-- Breadcrumb --}}
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="clearTagNav"
                                    class="flex items-center gap-1 text-xs transition"
                                    style="color:#3d4451;"
                                    onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
                                ><span aria-hidden="true">←</span> All categories</button>
                                <span aria-hidden="true" style="color:#21262D;">·</span>
                                <span class="text-xs font-semibold" style="color:#C9D1D9;">{{ $activeCat?->name }}</span>
                            </div>

                            {{-- Subcategory pills (medium — between tile and bubble) --}}
                            @if ($this->tagSubcategories->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($this->tagSubcategories as $subcat)
                                        <button type="button"
                                            wire:click="setTagSubcategory({{ $subcat->id }})"
                                            wire:key="tsc-{{ $subcat->id }}"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                            style="{{ $tagSubcategoryId === $subcat->id
                                                ? 'background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);'
                                                : 'background:#21262D;color:#8B949E;border:1px solid #30363D;' }}"
                                            @if($tagSubcategoryId !== $subcat->id)
                                                onmouseover="this.style.color='#C9D1D9';this.style.borderColor='#3d4451';this.style.background='#1C2333';"
                                                onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D';this.style.background='#21262D';"
                                            @endif
                                        >{{ $subcat->name }}</button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Tag bubbles (standard — same style as Shared Experiences & Vibe) --}}
                            @if ($this->tagSelectionTags->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($this->tagSelectionTags as $tag)
                                        <x-tag-bubble
                                            :label="$tag->name"
                                            :selected="in_array($tag->id, $selectedTagIds, true)"
                                            :disabled="!in_array($tag->id, $selectedTagIds, true) && count($selectedTagIds) >= 5"
                                            wire:click="toggleInterest('{{ $tag->id }}')"
                                            wire:key="tt-{{ $tag->id }}"
                                        />
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs" style="color:#3d4451;">No tags in this area yet.</p>
                            @endif

                        </div>

                    @else

                        {{-- Category tiles (large — navigation) --}}
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($this->tagCategories as $cat)
                                <button type="button"
                                    wire:click="setTagCategory({{ $cat->id }})"
                                    wire:key="tc-{{ $cat->id }}"
                                    class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl text-sm font-medium transition text-left"
                                    style="background:#1C2333;color:#C9D1D9;border:1px solid #30363D;"
                                    onmouseover="this.style.background='#21262D';this.style.borderColor='#3d4451';this.style.color='#E6EDF3';"
                                    onmouseout="this.style.background='#1C2333';this.style.borderColor='#30363D';this.style.color='#C9D1D9';"
                                >
                                    <span>{{ $cat->name }}</span>
                                    <span aria-hidden="true" style="opacity:0.3;font-size:0.6rem;flex-shrink:0;">▸</span>
                                </button>
                            @endforeach
                        </div>

                    @endif

                    @error('selectedTagIds')
                        <p class="text-sm" style="color:#E24B4A;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shared experiences (optional, up to 3) --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">
                        Shared experiences <span class="font-normal normal-case" style="color:#8B949E;">— optional, up to 3</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->experienceTags as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedExperienceIds)"
                                :disabled="!in_array($tag->id, $selectedExperienceIds) && count($selectedExperienceIds) >= 3"
                                wire:click="toggleExperience('{{ $tag->id }}')"
                                wire:key="ce-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs" style="color:#8B949E;">Shared experience tags are optional and do not diagnose or label anyone.</p>
                </div>

                {{-- Room vibe (optional, up to 3) --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">
                        Room vibe <span class="font-normal normal-case" style="color:#8B949E;">— optional, up to 3</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->vibeTags as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedVibeIds)"
                                :disabled="!in_array($tag->id, $selectedVibeIds) && count($selectedVibeIds) >= 3"
                                wire:click="toggleVibe('{{ $tag->id }}')"
                                wire:key="cv-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- No self-promotion --}}
            <div class="flex items-start gap-3">
                <div class="flex items-center h-5 mt-0.5">
                    <input
                        id="confirmNoBranding"
                        type="checkbox"
                        wire:model="confirmNoBranding"
                        class="w-4 h-4 rounded"
                        style="accent-color:#1D9E75;background:#1C2333;border-color:#30363D;"
                    >
                </div>
                <label for="confirmNoBranding" class="text-sm leading-snug cursor-pointer" style="color:#8B949E;">
                    I am not promoting a stream, channel, or external link.
                </label>
            </div>
            @error('confirmNoBranding')
                <p class="-mt-3 text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror

            {{-- Submit --}}
            <button
                type="submit"
                wire:loading.attr="disabled"
                @disabled($hasUrlWarning)
                class="w-full py-2.5 px-4 text-sm font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                style="background:#1D9E75;color:#fff;"
                onmouseover="if(!this.disabled)this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >
                <span wire:loading.remove>
                    @if ($postType === 'hangout') Post hangout @else Create room @endif
                </span>
                <span wire:loading>
                    @if ($postType === 'hangout') Posting… @else Creating… @endif
                </span>
            </button>

        </form>

    @endif

</div>
