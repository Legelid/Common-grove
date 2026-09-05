<div class="px-6 py-10 max-w-3xl mx-auto space-y-8">
    <style>
        .cg-hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>

    @if ($showResetNotice)
        <div style="background:var(--surface);border:1px solid var(--accent);border-radius:var(--radius-lg);padding:1.25rem 1.5rem;margin-bottom:1.5rem;box-shadow:0 0 0 3px rgba(var(--accent-rgb), 0.1);">
            <p style="font-family:var(--font-body);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">A note from CommonGrove</p>
            <p class="font-display" style="font-size:1.125rem;color:var(--text);margin-bottom:0.5rem;">We updated how interests work</p>
            <p style="font-family:var(--font-body);font-size:0.9375rem;color:var(--text-muted);line-height:1.6;margin-bottom:0.75rem;">
                We reorganized CommonGrove's interests system to make it easier to find what feels right. Your previous selections were reset as part of this update.
            </p>
            <p style="font-family:var(--font-body);font-size:0.9375rem;color:var(--text-muted);line-height:1.6;margin-bottom:0.75rem;">
                We're sorry for the inconvenience.
            </p>
            <p style="font-family:var(--font-body);font-size:0.9375rem;color:var(--text-muted);line-height:1.6;margin-bottom:1rem;">
                Take a moment to browse and pick what fits you now.
            </p>
            <div class="flex items-center" style="gap:1rem;">
                <x-button
                    type="button"
                    wire:click="dismissResetNotice"
                    x-on:click="document.getElementById('browse-interests')?.scrollIntoView({ behavior: 'smooth' })"
                    variant="primary"
                >Browse interests</x-button>
                <button
                    type="button"
                    wire:click="dismissResetNotice"
                    style="background:transparent;border:none;cursor:pointer;padding:0;color:var(--text-faint);font-size:0.875rem;"
                    onmouseover="this.style.color='var(--text-muted)'"
                    onmouseout="this.style.color='var(--text-faint)'"
                >Dismiss</button>
            </div>
        </div>
    @endif

    {{-- ── Header ───────────────────────────────────────────────────────── --}}
    {{-- Glass UI: light tier — hero heading over the forest photo, matching Home/Explore. --}}
    <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;">
        <h1 class="font-display" style="font-size:clamp(1.75rem,4vw,2.5rem);color:var(--text);font-weight:700;line-height:1.2;">Your Interests</h1>
        <p style="font-family:var(--font-body);font-size:1rem;color:var(--text-muted);margin-top:0.5rem;">These help CommonGrove suggest rooms and people that may feel comfortable.</p>
    </x-glass-panel>

    {{-- ── Selected count ───────────────────────────────────────────────── --}}
    @php $selectedCount = count($selectedTagIds); @endphp
    <p style="font-size:0.875rem;color:var(--text-muted);font-family:var(--font-body);">
        {{ $selectedCount }} {{ Str::plural('interest', $selectedCount) }} selected
        @if ($selectedCount >= 90)
            <span>· almost at the limit</span>
        @endif
    </p>

    {{-- ── Selected interests — grouped by category, floating card ────────── --}}
    {{-- Glass UI: heavy tier — the page's primary dense content block, matching Explore's "officialAll" section. --}}
    <x-glass-panel tier="heavy" style="border-radius:var(--radius-lg);padding:1.25rem 1.5rem;box-shadow:var(--shadow-sm);margin-bottom:2rem;">

        {{-- Core interests --}}
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent-amber);margin-bottom:0.25rem;">
                Core interests
            </p>
            <p style="font-size:0.75rem;color:var(--text-faint);margin-bottom:0.5rem;">
                Up to 5 — shown first when others see what you're into.
            </p>

            @if ($this->coreInterestTags->isEmpty())
                <p style="color:var(--text-faint);font-size:0.875rem;font-style:italic;">
                    Mark up to 5 interests as core to feature them prominently.
                </p>
            @else
                <div>
                    @foreach ($this->coreInterestTags as $tag)
                        @include('livewire.tags.partials.selected-interest-row', ['tag' => $tag, 'rowKey' => 'core-row-' . $tag->id, 'context' => 'core'])
                    @endforeach
                </div>
            @endif

            @if (count($coreInterests) >= 5)
                <p style="color:var(--text-faint);font-size:0.75rem;margin-top:0.375rem;">
                    Core interests full — remove one to add another.
                </p>
            @endif
        </div>

        @if ($this->selectedTagsByCategory->isEmpty())
            <div class="flex flex-col items-center justify-center text-center gap-2" style="padding:2.5rem 1rem;">
                <p class="font-display" style="font-size:1.1rem;color:var(--text-muted);">You haven't picked anything yet.</p>
                <p class="text-sm" style="color:var(--text-muted);">Browse below and choose what feels right.</p>
            </div>
        @else
            @foreach ($this->selectedTagsByCategory as $categoryName => $tagsInCategory)
                <div style="{{ $loop->first ? '' : 'margin-top:1.25rem;' }}">
                    <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">
                        {{ $categoryName }}
                    </p>

                    @if ($tagsInCategory->contains(fn ($t) => $t->subcategory?->is_sensitive))
                        <p style="font-size:0.75rem;color:var(--text-faint);font-style:italic;margin-bottom:0.375rem;">
                            Interests in this category are sensitive. They're shared only when you choose.
                        </p>
                    @endif

                    <div>
                        @foreach ($tagsInCategory as $tag)
                            @include('livewire.tags.partials.selected-interest-row', ['tag' => $tag, 'rowKey' => 'sel-row-' . $tag->id, 'context' => 'category'])
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </x-glass-panel>

    {{-- ── Save — single action, right-aligned, directly below the card ───── --}}
    <div class="flex flex-col items-end gap-2" style="margin-top:-1rem;margin-bottom:2rem;">
        <x-button
            type="button"
            wire:click="save"
            wire:loading.attr="disabled"
            variant="primary"
            :disabled="$selectedCount < 3"
            :style="$isDirty ? 'box-shadow:0 0 0 3px rgba(var(--accent-rgb), 0.3);' : ''"
        >
            <span wire:loading.remove>Save interests</span>
            <span wire:loading>Saving…</span>
        </x-button>

        @if ($isDirty)
            <span style="font-size:0.75rem;color:var(--accent-amber);">Unsaved changes</span>
        @endif

        @if ($saveMessage)
            <p class="text-sm" style="color:{{ str_starts_with($saveMessage, 'Your interests') ? 'var(--accent)' : 'var(--danger)' }};">
                {{ $saveMessage }}
            </p>
        @endif
        @if ($maxTagsMessage)
            <p class="text-sm" style="color:var(--danger);">{{ $maxTagsMessage }}</p>
        @endif
    </div>

    {{-- ── Search — restyled pill input ────────────────────────────────── --}}
    <div>
        <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:0.5rem;">Add more interests</p>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:var(--text-faint);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search all interests…"
                aria-label="Search all interests"
                class="placeholder:text-text-faint"
                style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.625rem 1rem 0.625rem 2.5rem;font-family:var(--font-body);color:var(--text);width:100%;outline:none;"
                onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgb(var(--accent-rgb) / 0.15)'"
                onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"
            >
        </div>
    </div>

    {{-- ── Browse / search results — floating card ─────────────────────── --}}
    <div id="browse-interests" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem 1.5rem;box-shadow:var(--shadow-sm);">

        @if (trim($search) === '')
            {{-- Part A/B: two-pane browser (desktop) + pill row & list (mobile) --}}
            @include('livewire.tags.partials.category-browser')
        @else
            {{-- Part D: search results, grouped by category --}}
            <p class="text-xs" style="color:var(--text-muted);margin-bottom:1rem;">
                Showing results for <span style="color:var(--text);">"{{ $search }}"</span>
            </p>

            @if ($this->searchResultsByCategory->isNotEmpty())
                <div class="space-y-6">
                    @foreach ($this->searchResultsByCategory as $categoryName => $tagsInCategory)
                        <section wire:key="search-cat-{{ Str::slug($categoryName) }}">
                            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">
                                {{ $categoryName }}
                            </p>
                            <div>
                                @foreach ($tagsInCategory as $tag)
                                    @include('livewire.tags.partials.tag-row', [
                                        'tag' => $tag,
                                        'rowKey' => 'search-tag-' . $tag->id,
                                        'suffix' => $tag->_subcategoryName ?? null,
                                    ])
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <div class="text-center" style="padding:2rem 1rem;">
                    <p style="font-family:var(--font-body);color:var(--text-muted);">Nothing found for "{{ $search }}"</p>
                    <p class="text-sm" style="color:var(--text-muted);margin-top:0.25rem;">Try different words, or browse by category below.</p>
                </div>

                <div style="margin-top:1.5rem;">
                    @include('livewire.tags.partials.category-browser')
                </div>
            @endif
        @endif

        {{-- User's own custom interests matching search --}}
        @if (trim($search) !== '' && $this->myCustomTagResults->isNotEmpty())
            <section style="margin-top:1.5rem;">
                <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:0.375rem;">
                    My personal interests
                </p>
                <div>
                    @foreach ($this->myCustomTagResults as $tag)
                        @php $selected = in_array($tag->id, $selectedTagIds); @endphp
                        <button
                            type="button"
                            wire:click="toggleTag('{{ $tag->id }}')"
                            wire:key="custom-srch-{{ $tag->id }}"
                            class="w-full flex items-center justify-between text-left transition"
                            style="padding:0.375rem 0.5rem;border-radius:var(--radius-sm);background:transparent;border:none;cursor:pointer;"
                            @if (! $selected)
                                onmouseover="this.style.background='var(--surface-raised)';this.querySelector('[data-indicator]').style.color='var(--text)'"
                                onmouseout="this.style.background='transparent';this.querySelector('[data-indicator]').style.color='var(--text-faint)'"
                            @endif
                        >
                            <span style="font-family:var(--font-body);font-size:0.9375rem;color:{{ $selected ? 'var(--text-muted)' : 'var(--text)' }};">{{ $tag->name }}</span>
                            <span data-indicator style="color:{{ $selected ? 'var(--accent)' : 'var(--text-faint)' }};font-size:0.875rem;flex-none;">{{ $selected ? '✓' : '+' }}</span>
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Add as personal interest --}}
        @if (trim($search) !== '' && mb_strlen(trim($search)) >= 2 && mb_strlen(trim($search)) <= 40)
            <div style="margin-top:1.5rem;">
                <button
                    type="button"
                    wire:click="addCustomTag"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-4 py-2 text-sm rounded-btn transition disabled:opacity-50"
                    style="background:var(--surface-raised);border:1px dashed var(--border);color:var(--text-faint);"
                    onmouseover="this.style.color='var(--text-muted)';this.style.borderColor='var(--text-muted)'"
                    onmouseout="this.style.color='var(--text-faint)';this.style.borderColor='var(--border)'"
                >
                    <span style="font-size:1.1em;line-height:1;">+</span>
                    <span>Add <span style="color:var(--text);">"{{ trim($search) }}"</span> as a personal interest</span>
                </button>

                @if ($customTagMessage)
                    <p class="mt-2 text-sm" style="color:{{ str_starts_with($customTagMessage, '"') ? 'var(--accent)' : '#D29922' }};">
                        {{ $customTagMessage }}
                    </p>
                @endif
            </div>
        @endif

    </div>

    {{-- ── Review & organize — collapsible read-only summary ───────────────── --}}
    <div style="margin-top:1.5rem;" x-data="{ open: false }">
        <button
            type="button"
            @click="open = ! open"
            style="font-size:0.875rem;color:var(--text-muted);cursor:pointer;background:transparent;border:none;padding:0;"
        ><span x-text="open ? 'Review & organize ▴' : 'Review & organize ▾'"></span></button>

        <div x-show="open" x-cloak style="display:none;margin-top:0.75rem;">
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem 1.5rem;box-shadow:var(--shadow-sm);">
                @if ($this->selectedTagsByCategory->isEmpty())
                    <p style="font-size:0.875rem;color:var(--text-muted);">Nothing selected yet.</p>
                @else
                    @foreach ($this->selectedTagsByCategory as $categoryName => $tagsInCategory)
                        <div style="{{ $loop->first ? '' : 'margin-top:1.25rem;' }}">
                            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">
                                {{ $categoryName }}
                                <span style="color:var(--text-faint);font-weight:400;text-transform:none;letter-spacing:normal;">— {{ $tagsInCategory->count() }}</span>
                            </p>
                            <div>
                                @foreach ($tagsInCategory as $tag)
                                    @php
                                        $isCore   = in_array($tag->id, $coreInterests, true);
                                        $isHidden = in_array($tag->id, $hiddenInterests, true);
                                    @endphp
                                    <div
                                        class="transition"
                                        style="font-family:var(--font-body);font-size:0.9375rem;color:var(--text);padding:0.375rem 0.5rem;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between;"
                                        onmouseover="this.style.background='var(--surface-raised)'"
                                        onmouseout="this.style.background='transparent'"
                                        wire:key="review-row-{{ $tag->id }}"
                                    >
                                        <span>
                                            @if ($isCore)
                                                <span style="color:var(--accent-amber);margin-right:0.25rem;" title="Core interest">★</span>
                                            @endif
                                            @if ($isHidden)
                                                <span style="color:var(--text-faint);margin-right:0.25rem;" title="Hidden from others">🚫</span>
                                            @endif
                                            {{ $tag->name }}
                                        </span>
                                        <button
                                            type="button"
                                            wire:click="toggleTag('{{ $tag->id }}')"
                                            class="transition flex-none"
                                            style="color:var(--text-faint);font-size:0.75rem;padding:0.25rem 0.5rem;min-width:44px;min-height:44px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;cursor:pointer;"
                                            onmouseover="this.style.color='var(--danger)'"
                                            onmouseout="this.style.color='var(--text-faint)'"
                                            aria-label="Remove {{ $tag->name }}"
                                        >×</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>
