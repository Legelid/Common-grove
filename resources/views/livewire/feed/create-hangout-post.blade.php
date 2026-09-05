<div class="px-6 py-10 max-w-xl mx-auto space-y-6">

    <div>
        <a href="{{ route('feed') }}" wire:navigate class="text-sm underline transition" style="color:var(--accent);">← Back to feed</a>
        <h1 class="mt-3 text-2xl font-bold" style="color:var(--text);">Open a hangout or room</h1>
        <p class="mt-1 text-sm" style="color:var(--text-muted);">Choose what you want to create, then fill in the details.</p>
    </div>

    @error('postType')
        <p class="text-sm" style="color:var(--danger);">{{ $message }}</p>
    @enderror

    {{-- ── Step 1: Type selection ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3">

        <button
            type="button"
            wire:click="selectType('hangout')"
            class="text-left rounded-xl border p-4 space-y-1.5 transition focus:outline-none focus-visible:ring-2"
            style="{{ $postType === 'hangout'
                ? 'background:rgba(210,153,34,0.12);border-color:rgba(210,153,34,0.6);'
                : 'background:var(--surface);border-color:var(--border);' }}"
            onmouseover="if('{{ $postType }}' !== 'hangout') { this.style.borderColor='rgba(210,153,34,0.35)'; }"
            onmouseout="if('{{ $postType }}' !== 'hangout') { this.style.borderColor='var(--border)'; }"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:#D29922;">Temporary hangout</span>
                @if ($postType === 'hangout')
                    <span class="w-2 h-2 rounded-full" style="background:#D29922;"></span>
                @endif
            </div>
            <p class="text-xs leading-snug" style="color:var(--text-muted);">A short-lived space for people who want to talk right now.</p>
        </button>

        <button
            type="button"
            wire:click="selectType('room')"
            class="text-left rounded-xl border p-4 space-y-1.5 transition focus:outline-none focus-visible:ring-2"
            style="{{ $postType === 'room'
                ? 'background:rgba(var(--accent-rgb),0.12);border-color:rgba(var(--accent-rgb),0.6);'
                : 'background:var(--surface);border-color:var(--border);' }}"
            onmouseover="if('{{ $postType }}' !== 'room') { this.style.borderColor='rgba(var(--accent-rgb),0.35)'; }"
            onmouseout="if('{{ $postType }}' !== 'room') { this.style.borderColor='var(--border)'; }"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--accent);">Always-open room</span>
                @if ($postType === 'room')
                    <span class="w-2 h-2 rounded-full" style="background:var(--accent);"></span>
                @endif
            </div>
            <p class="text-xs leading-snug" style="color:var(--text-muted);">A persistent space people can return to anytime.</p>
        </button>

    </div>

    {{-- ── Persistent room limit message ────────────────────────────────── --}}
    @if ($roomLimitMessage === 'limit_reached')
        <div class="rounded-xl border px-5 py-5 space-y-3" style="background:rgba(var(--accent-rgb),0.04);border-color:rgba(var(--accent-rgb),0.15);">
            <p class="text-sm leading-relaxed" style="color:var(--text);">
                You've created a few spaces already.
            </p>
            <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                If you'd like to create more, you can support CommonGrove.<br>
                No pressure — your current rooms will always stay available.
            </p>
            <a
                href="{{ route('feed') }}"
                wire:navigate
                class="inline-block"
                aria-label="Back to the feed"
            ><x-arrow-icon label="Back to the feed" /></a>
        </div>
    @endif

    {{-- ── Duration picker (hangout only) ────────────────────────────────── --}}
    @if ($postType === 'hangout')
        <div class="rounded-xl border p-4 space-y-3" style="background:var(--surface);border-color:var(--border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">How long should it stay open?</p>
            <div class="grid grid-cols-4 gap-2">
                @foreach (['1' => '1 hour', '4' => '4 hours', '12' => '12 hours', '24' => '24 hours'] as $val => $label)
                    <button
                        type="button"
                        wire:click="$set('duration', '{{ $val }}')"
                        class="py-2 rounded-lg text-xs font-medium transition"
                        style="{{ $duration === $val
                            ? 'background:rgba(210,153,34,0.2);border:1px solid rgba(210,153,34,0.6);color:#D29922;'
                            : 'background:var(--surface-raised);border:1px solid var(--border);color:var(--text-muted);' }}outline:none;"
                        onfocus="this.style.outline='2px solid var(--accent)';this.style.outlineOffset='2px'"
                        onblur="this.style.outline='none'"
                    >{{ $label }}</button>
                @endforeach
            </div>
            @error('duration')
                <p class="text-xs" style="color:var(--danger);">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- ── Main form (shown once type is chosen, and limit not reached) ────── --}}
    @if ($postType !== '' && $roomLimitMessage !== 'limit_reached')

        <form wire:submit="submit" class="space-y-6">

            {{-- Room name (persistent rooms only) --}}
            @if ($postType === 'room')
                <div>
                    <label for="roomTitle" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                        Room name
                    </label>
                    <x-input
                        id="roomTitle"
                        type="text"
                        wire:model.live="roomTitle"
                        maxlength="60"
                        placeholder="e.g. Late-night cozy gaming"
                        autocomplete="off"
                        :error="$errors->first('roomTitle')"
                    />
                    <div class="mt-1 flex items-start justify-end gap-2">
                        <span class="flex-none text-xs" style="color:{{ strlen($roomTitle) >= 54 ? 'var(--danger)' : 'var(--text-faint)' }};">
                            {{ strlen($roomTitle) }}/60
                        </span>
                    </div>
                </div>
            @endif

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                    @if ($postType === 'hangout') What are you up to? @else Short description @endif
                </label>
                <div class="relative">
                    <x-textarea
                        id="content"
                        wire:model.live="content"
                        maxlength="280"
                        rows="4"
                        placeholder="{{ $postType === 'hangout'
                            ? 'Playing Stardew Valley tonight, looking for someone to chat with about the game…'
                            : 'A chill place to talk about books, low-pressure, drop in anytime…' }}"
                        :error="$errors->first('content')"
                    >{{ $content }}</x-textarea>
                    <span class="absolute bottom-2 right-3 text-xs" style="color:{{ strlen($content) >= 260 ? 'var(--danger)' : 'var(--text-muted)' }};">
                        {{ strlen($content) }}/280
                    </span>
                </div>
                @if ($hasUrlWarning)
                    <p class="mt-1 text-sm" style="color:#D29922;">Posts cannot contain external links.</p>
                @endif
            </div>

            {{-- Tags --}}
            <div class="space-y-4 rounded-xl border p-5" style="background:var(--surface);border-color:var(--border);">
                <p class="text-sm font-semibold" style="color:var(--text);">Tags</p>
                <p class="text-xs" style="color:var(--text-muted);">These help the right people find the
                    @if ($postType === 'hangout') hangout @else room @endif.
                </p>

                {{-- Interests (required, pick 1–5) --}}
                <div class="space-y-3">
                    <div class="flex items-baseline justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">
                            Interests <span class="font-normal normal-case">· pick 1–5</span>
                        </p>
                        <span class="text-xs" style="color:{{ count($selectedTagIds) >= 5 ? '#D29922' : 'var(--text-faint)' }};">
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
                                        onmouseover="this.style.background='rgba(var(--danger-rgb),0.08)';this.style.color='var(--danger)';this.style.borderColor='rgba(var(--danger-rgb),0.25)';"
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
                            style="background:var(--surface);border:1px solid var(--border);color:var(--text);"
                            onfocus="this.style.borderColor='rgba(var(--accent-rgb),0.4)'" onblur="this.style.borderColor='var(--border)'"
                        >
                        @if ($tagSearch !== '')
                            <button type="button" wire:click="$set('tagSearch','')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs transition"
                                style="color:var(--text-faint);"
                                aria-label="Clear search"
                                onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
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
                            <p class="text-xs" style="color:var(--text-faint);">No matching interests.</p>
                        @endif

                    @elseif ($tagCategoryId !== null)

                        {{-- Category detail: back breadcrumb + subcategory pills + tag bubbles --}}
                        @php $activeCat = $this->tagCategories->firstWhere('id', $tagCategoryId); @endphp
                        <div class="space-y-3">

                            {{-- Breadcrumb --}}
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="clearTagNav"
                                    class="flex items-center gap-1 text-xs transition"
                                    style="color:var(--text-faint);"
                                    onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                                ><span aria-hidden="true">←</span> All categories</button>
                                <span aria-hidden="true" style="color:var(--surface-raised);">·</span>
                                <span class="text-xs font-semibold" style="color:var(--text);">{{ $activeCat?->name }}</span>
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
                                                ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);'
                                                : 'background:var(--surface-raised);color:var(--text-muted);border:1px solid var(--border);' }}"
                                            @if($tagSubcategoryId !== $subcat->id)
                                                onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--text-faint)';this.style.background='var(--surface)';"
                                                onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';this.style.background='var(--surface-raised)';"
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
                                <p class="text-xs" style="color:var(--text-faint);">No tags in this area yet.</p>
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
                                    style="background:var(--surface);color:var(--text);border:1px solid var(--border);"
                                    onmouseover="this.style.background='var(--surface-raised)';this.style.borderColor='var(--text-faint)';this.style.color='var(--text)';"
                                    onmouseout="this.style.background='var(--surface)';this.style.borderColor='var(--border)';this.style.color='var(--text)';"
                                >
                                    <span>{{ $cat->name }}</span>
                                    <span aria-hidden="true" style="opacity:0.3;font-size:0.6rem;flex-shrink:0;">▸</span>
                                </button>
                            @endforeach
                        </div>

                    @endif

                    @error('selectedTagIds')
                        <p class="text-sm" style="color:var(--danger);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shared experiences (optional, up to 3) --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted);">
                        Shared experiences <span class="font-normal normal-case" style="color:var(--text-muted);">· optional, up to 3</span>
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
                    <p class="mt-1 text-xs" style="color:var(--text-muted);">Shared experience tags are optional and do not diagnose or label anyone.</p>
                </div>

                {{-- Room vibe (optional, up to 3) --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted);">
                        Room vibe <span class="font-normal normal-case" style="color:var(--text-muted);">· optional, up to 3</span>
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
                        style="accent-color:var(--accent);background:var(--surface);border-color:var(--border);"
                    >
                </div>
                <label for="confirmNoBranding" class="text-sm leading-snug cursor-pointer" style="color:var(--text-muted);">
                    I am not promoting a stream, channel, or external link.
                </label>
            </div>
            @error('confirmNoBranding')
                <p class="-mt-3 text-sm" style="color:var(--danger);">{{ $message }}</p>
            @enderror

            {{-- Submit --}}
            <x-button
                type="submit"
                wire:loading.attr="disabled"
                :disabled="$hasUrlWarning"
                class="w-full !py-2.5"
            >
                <span wire:loading.remove>
                    @if ($postType === 'hangout') Post hangout @else Create room @endif
                </span>
                <span wire:loading>
                    @if ($postType === 'hangout') Posting… @else Creating… @endif
                </span>
            </x-button>

        </form>

    @endif

</div>
