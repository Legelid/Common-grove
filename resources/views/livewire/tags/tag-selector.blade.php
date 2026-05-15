<div class="px-6 py-10 max-w-3xl mx-auto space-y-8">

    <div>
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Your Interests</h1>
        <p class="mt-1 text-sm" style="color:#8B949E;">Choose the things you're into — people with similar interests will find you.</p>
    </div>

    {{-- Popular Tags --}}
    <section>
        <h2 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#8B949E;">Popular Right Now</h2>
        <div class="flex flex-wrap gap-2">
            @foreach ($this->popularTags as $tag)
                <button type="button" wire:click="toggleTag('{{ $tag->id }}')" wire:key="popular-{{ $tag->id }}"
                    class="px-3 py-1.5 rounded-full text-sm font-medium transition"
                    style="{{ in_array($tag->id, $selectedTagIds) ? 'background:#1D9E75;color:#fff;outline:2px solid rgba(29,158,117,0.5);' : 'background:#1C2333;color:#8B949E;' }}"
                    onmouseover="this.style.color='#E6EDF3'" onmouseout=""
                >{{ $tag->name }}</button>
            @endforeach
        </div>
    </section>

    {{-- Counter --}}
    <div class="flex items-center gap-3">
        <span class="text-sm font-medium" style="color:#E6EDF3;">
            {{ count($selectedTagIds) }} selected
            @if (count($selectedTagIds) < 3)
                — <span style="color:#D29922;">pick at least {{ 3 - count($selectedTagIds) }} more</span>
            @else
                <span style="color:#1D9E75;">— good to go!</span>
            @endif
        </span>
        <span class="text-xs" style="color:#8B949E;">(max 30)</span>
    </div>

    @if ($maxTagsMessage)
        <p class="text-sm" style="color:#E24B4A;">{{ $maxTagsMessage }}</p>
    @endif

    {{-- Search --}}
    <div>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tags…"
            class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
            onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
        >
    </div>

    {{-- Tags by category --}}
    <div class="space-y-6">
        @forelse ($this->tagsByCategory as $category => $tags)
            <section wire:key="cat-{{ Str::slug($category) }}">
                <h2 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">{{ $category }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <button type="button" wire:click="toggleTag('{{ $tag->id }}')" wire:key="tag-{{ $tag->id }}"
                            class="px-3 py-1.5 rounded-full text-sm font-medium transition"
                            style="{{ in_array($tag->id, $selectedTagIds) ? 'background:#1D9E75;color:#fff;outline:2px solid rgba(29,158,117,0.5);' : 'background:#1C2333;color:#8B949E;' }}"
                            onmouseover="this.style.color='#E6EDF3'" onmouseout=""
                        >{{ $tag->name }}</button>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-sm" style="color:#8B949E;">No tags match your search.</p>
        @endforelse
    </div>

    {{-- Save --}}
    <div class="pt-2">
        <button type="button" wire:click="save" wire:loading.attr="disabled"
            class="px-6 py-2.5 rounded-lg font-semibold text-sm transition disabled:opacity-50"
            style="{{ count($selectedTagIds) >= 3 ? 'background:#1D9E75;color:#fff;' : 'background:#21262D;color:#8B949E;cursor:not-allowed;opacity:0.5;' }}"
            @if(count($selectedTagIds) >= 3) onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'" @endif
        >
            <span wire:loading.remove>Save interests</span>
            <span wire:loading>Saving…</span>
        </button>

        @if ($saveMessage)
            <p class="mt-2 text-sm" style="color:{{ str_starts_with($saveMessage, 'Your interests') ? '#1D9E75' : '#E24B4A' }};">
                {{ $saveMessage }}
            </p>
        @endif
    </div>

    <hr style="border-color:#30363D;">

    {{-- Submit custom tag --}}
    <section>
        <h2 class="text-sm font-semibold mb-1" style="color:#E6EDF3;">Don't see your interest?</h2>
        <p class="text-xs mb-3" style="color:#8B949E;">Submit a new tag for review. It will appear in the list once approved.</p>

        <div class="flex gap-3">
            <input type="text" wire:model="customTagName" wire:keydown.enter="submitCustomTag"
                placeholder="e.g. Urban Sketching" maxlength="50"
                class="flex-1 rounded-lg px-4 py-2 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            <button type="button" wire:click="submitCustomTag" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium rounded-lg transition disabled:opacity-50"
                style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
            >Submit</button>
        </div>

        @error('customTagName') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror

        @if ($customTagMessage)
            <p class="mt-2 text-sm" style="color:{{ str_starts_with($customTagMessage, 'Your tag') ? '#1D9E75' : '#D29922' }};">
                {{ $customTagMessage }}
            </p>
        @endif
    </section>

</div>
