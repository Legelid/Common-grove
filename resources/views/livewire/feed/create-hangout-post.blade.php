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

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                    @if ($postType === 'hangout') What are you up to? @else What is this room about? @endif
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

                {{-- Interests (required, up to 5) --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">
                        Interests <span class="font-normal normal-case" style="color:#8B949E;">— pick 1–5</span>
                    </p>
                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:#30363D transparent;">
                        @foreach ($this->interestTags as $tag)
                            <x-tag-bubble
                                :label="$tag->name"
                                :selected="in_array($tag->id, $selectedTagIds)"
                                :disabled="!in_array($tag->id, $selectedTagIds) && count($selectedTagIds) >= 5"
                                wire:click="toggleInterest('{{ $tag->id }}')"
                                wire:key="ci-{{ $tag->id }}"
                            />
                        @endforeach
                    </div>
                    <p class="mt-1.5 text-xs" style="color:#8B949E;">{{ count($selectedTagIds) }} of 5 selected</p>
                    @error('selectedTagIds')
                        <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p>
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
