<div class="space-y-6">

    {{-- Progress indicator --}}
    <div class="flex items-center gap-1.5 justify-center mb-8">
        @for ($i = 1; $i <= 6; $i++)
            <div
                class="h-1 rounded-full transition-all duration-300"
                style="width:{{ $step >= $i ? '2rem' : '0.75rem' }};background:{{ $step >= $i ? 'var(--accent)' : 'var(--surface-raised)' }};"
            ></div>
        @endfor
    </div>

    {{-- ── STEP 1: Welcome ───────────────────────────────────────────────── --}}
    @if ($step === 1)
        <x-card padding="p-8" class="space-y-6 text-center">
            <div class="space-y-3">
                <h1 class="text-2xl font-bold" style="color:var(--text);">Welcome to CommonGrove</h1>
                <p class="text-base leading-relaxed" style="color:var(--text-muted);">
                    This is a place to find real connection — at your own pace.
                </p>
                <p class="text-sm" style="color:var(--text-muted);">
                    You don't have to say anything right away.
                </p>
            </div>

            <x-button type="button" wire:click="next" variant="primary" class="w-full py-3">
                Let's get you set up
            </x-button>
        </x-card>
    @endif

    {{-- ── STEP 2: Identity ──────────────────────────────────────────────── --}}
    @if ($step === 2)
        <x-card padding="p-8" class="space-y-6">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:var(--text);">How do you want to show up here?</h1>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    You don't have to use your real name.
                </p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium" style="color:var(--text);" for="display-name">
                    Nickname or display name <span class="font-normal" style="color:var(--text-muted);">(optional)</span>
                </label>
                <x-input
                    id="display-name"
                    type="text"
                    wire:model="displayName"
                    placeholder="e.g. Nox, Pixel, just your gamertag…"
                    maxlength="50"
                    autocomplete="off"
                />
                <p class="text-xs" style="color:var(--text-muted);">You can change this later in your profile settings.</p>
            </div>

            @error('displayName')
                <p class="text-sm" style="color:var(--danger);">{{ $message }}</p>
            @enderror

            <div class="flex gap-3 pt-2">
                <x-button type="button" wire:click="back" variant="secondary" class="flex-none">Back</x-button>
                <x-button type="button" wire:click="next" variant="primary" class="flex-1">Continue</x-button>
            </div>
        </x-card>
    @endif

    {{-- ── STEP 3: Interests ─────────────────────────────────────────────── --}}
    @if ($step === 3)
        <x-card padding="p-8" class="space-y-5">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:var(--text);">What kinds of things feel like you?</h1>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    Pick a category, then choose whatever feels right.
                    You don't need to explain yourself.
                </p>
            </div>

            {{-- Counter --}}
            <div class="flex items-center gap-2">
                <span class="text-xs" style="color:var(--text-muted);">{{ count($selectedInterestIds) }} selected</span>
                @if (count($selectedInterestIds) > 0 && count($selectedInterestIds) < 3)
                    <span class="text-xs" style="color:#D29922;">· a few more helps us find better rooms</span>
                @endif
            </div>

            {{-- Search --}}
            <div class="relative">
                <x-input
                    type="text"
                    wire:model.live.debounce.300ms="onboardingSearch"
                    placeholder="Search interests…"
                    class="pr-9"
                />
                @if ($onboardingSearch !== '')
                    <button type="button" wire:click="$set('onboardingSearch','')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs transition"
                        style="color:var(--text-faint);"
                        onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                        aria-label="Clear search"
                    >✕</button>
                @endif
            </div>

            {{-- Search results --}}
            @if ($onboardingSearch !== '')
                @if ($this->onboardingSearchResults->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->onboardingSearchResults as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedInterestIds)"
                                wire:click="toggleInterest('{{ $tag->id }}')"
                                wire:key="srch-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                @endif

                {{-- Add as personal interest (curated results shown first, custom add last) --}}
                @if (mb_strlen(trim($onboardingSearch)) >= 2 && mb_strlen(trim($onboardingSearch)) <= 40)
                    <div class="{{ $this->onboardingSearchResults->isEmpty() ? '' : 'pt-1' }}">
                        <button
                            type="button"
                            wire:click="addCustomInterest"
                            wire:loading.attr="disabled"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg transition disabled:opacity-50"
                            style="background:var(--bg);border:1px dashed var(--border);color:var(--text-faint);"
                            onmouseover="this.style.color='var(--text-muted)';this.style.borderColor='var(--text-muted)'"
                            onmouseout="this.style.color='var(--text-faint)';this.style.borderColor='var(--border)'"
                        >
                            <span>+</span>
                            <span>Add <span style="color:var(--text);">"{{ trim($onboardingSearch) }}"</span> as my own interest</span>
                        </button>

                        @if ($onboardingCustomMessage)
                            <p class="mt-1.5 text-xs" style="color:{{ str_starts_with($onboardingCustomMessage, '"') ? 'var(--accent)' : '#D29922' }};">
                                {{ $onboardingCustomMessage }}
                            </p>
                        @endif
                    </div>
                @endif
            @else
                {{-- Category chips --}}
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->interestCategories as $cat)
                        <button
                            type="button"
                            wire:click="setOnboardingCategory({{ $cat->id }})"
                            wire:key="onb-cat-{{ $cat->id }}"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium transition"
                            style="{{ $onboardingCategoryId === $cat->id
                                ? 'background:var(--accent);color:var(--on-accent);'
                                : 'background:var(--surface-raised);color:var(--text-muted);border:1px solid var(--border);' }}"
                            onmouseover="{{ $onboardingCategoryId === $cat->id ? '' : "this.style.color='var(--text)'" }}"
                            onmouseout="{{ $onboardingCategoryId === $cat->id ? '' : "this.style.color='var(--text-muted)'" }}"
                        >{{ $cat->name }}</button>
                    @endforeach
                </div>

                {{-- Subcategory tags for the selected category --}}
                @if ($this->onboardingSubcats->isNotEmpty())
                    <div class="space-y-4 max-h-72 overflow-y-auto pr-1" style="scrollbar-width:thin;scrollbar-color:var(--border) transparent;">
                        @foreach ($this->onboardingSubcats as $subcat)
                            @if ($subcat->tags->isNotEmpty())
                                <div wire:key="onb-sub-{{ $subcat->id }}" x-data="{ showAll: false }">
                                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-faint);">{{ $subcat->name }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($subcat->tags->take(8) as $tag)
                                            <x-tag-bubble
                                                :label="$tag->name"
                                                :selected="in_array($tag->id, $selectedInterestIds)"
                                                wire:click="toggleInterest('{{ $tag->id }}')"
                                                wire:key="int-{{ $tag->id }}"
                                            />
                                        @endforeach
                                        @foreach ($subcat->tags->skip(8) as $tag)
                                            <span x-show="showAll">
                                                <x-tag-bubble
                                                    :label="$tag->name"
                                                    :selected="in_array($tag->id, $selectedInterestIds)"
                                                    wire:click="toggleInterest('{{ $tag->id }}')"
                                                    wire:key="int-{{ $tag->id }}"
                                                />
                                            </span>
                                        @endforeach
                                    </div>
                                    @if ($subcat->tags->count() > 8)
                                        <button type="button" @click="showAll = !showAll"
                                            class="mt-1.5 text-xs transition" style="color:var(--text-faint);"
                                            onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                                            x-text="showAll ? 'Show less' : 'See {{ $subcat->tags->count() - 8 }} more…'"
                                        ></button>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif ($onboardingCategoryId === null)
                    <p class="text-sm" style="color:var(--text-faint);">Pick a category above or search to find interests.</p>
                @endif
            @endif

            <div class="flex gap-3 pt-2">
                <x-button type="button" wire:click="back" variant="secondary" class="flex-none">Back</x-button>
                <x-button type="button" wire:click="next" variant="primary" class="flex-1">Continue</x-button>
            </div>
        </x-card>
    @endif

    {{-- ── STEP 4: Shared Experiences ────────────────────────────────────── --}}
    @if ($step === 4)
        <x-card padding="p-8" class="space-y-5">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:var(--text);">Anything you'd like us to consider?</h1>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    This is completely optional.
                </p>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    Some people like finding others with similar experiences. Others prefer not to share — both are okay.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach ($this->experienceTags as $tag)
                    <x-tag-bubble
                        :label="$tag->name"
                        :selected="in_array($tag->id, $selectedExperienceIds)"
                        wire:click="toggleExperience('{{ $tag->id }}')"
                        wire:key="exp-{{ $tag->id }}"
                    />
                @endforeach
            </div>

            <p class="text-xs" style="color:var(--text-muted);">
                These are used for room matching only. They won't appear publicly on your profile unless you choose to share them later.
            </p>

            <div class="flex gap-3 pt-2">
                <x-button type="button" wire:click="back" variant="secondary" class="flex-none">Back</x-button>
                <x-button type="button" wire:click="next" variant="primary" class="flex-1">Continue</x-button>
                <x-button type="button" wire:click="skipToStep(5)" variant="secondary" class="flex-none">Skip this</x-button>
            </div>
        </x-card>
    @endif

    {{-- ── STEP 5: Comfort Level ─────────────────────────────────────────── --}}
    @if ($step === 5)
        <x-card padding="p-8" class="space-y-5">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:var(--text);">What feels comfortable right now?</h1>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    This just helps us suggest spaces that match your pace.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach (\App\Livewire\Onboarding\OnboardingFlow::COMFORT_OPTIONS as $key => $label)
                    <x-tag-bubble
                        :label="$label"
                        :selected="in_array($key, $selectedComfortOptions)"
                        wire:click="toggleComfort('{{ $key }}')"
                        wire:key="comfort-{{ $key }}"
                    />
                @endforeach
            </div>

            <div class="flex gap-3 pt-2">
                <x-button type="button" wire:click="back" variant="secondary" class="flex-none">Back</x-button>
                <x-button type="button" wire:click="next" variant="primary" class="flex-1">Continue</x-button>
                <x-button type="button" wire:click="skipToStep(6)" variant="secondary" class="flex-none">Skip this</x-button>
            </div>
        </x-card>
    @endif

    {{-- ── STEP 6: Culture / Rules ───────────────────────────────────────── --}}
    @if ($step === 6)
        <x-card padding="p-8" class="space-y-5">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:var(--text);">Before you jump in…</h1>
            </div>

            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text-muted);">
                <p>CommonGrove is a place for real connection — not followers or attention.</p>
                <p>It's not a dating app, and it's not built for self-promotion.</p>
                <p style="color:var(--text);">
                    Do not attack people over identity, beliefs, background, politics, religion, gender, sexuality, disability, neurodivergence, or personal circumstances.
                </p>
                <p>You can be yourself here. You cannot use who someone is as a weapon.</p>
                <p>Be kind. Be respectful. Let people exist at their own pace.</p>
                <hr style="border-color:var(--border);">
                <p class="text-xs">
                    This is a beta, and it's being built by a small team (mostly one person). Things won't be perfect yet.
                    If something breaks or doesn't feel right, feel free to reach out — I'm actively working on improving it.
                </p>
                <p class="text-xs">You don't have to say anything right away.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <x-button type="button" wire:click="back" variant="secondary" class="flex-none">Back</x-button>
                <x-button type="button" wire:click="complete" wire:loading.attr="disabled" variant="primary" class="flex-1">
                    <span wire:loading.remove>I understand — take me in</span>
                    <span wire:loading>Setting things up…</span>
                </x-button>
            </div>
        </x-card>
    @endif

</div>
