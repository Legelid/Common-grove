<div>
    @if ($statusMessage)
        <p class="mb-3 text-sm" style="color:#1D9E75;">{{ $statusMessage }}</p>
    @endif

    {{-- Mood pills --}}
    <div class="flex flex-wrap gap-2 mb-3">
        @foreach ($this->moodOptions() as $mood)
            <button
                type="button"
                wire:click="$set('moodInput', '{{ $mood }}')"
                class="px-3 py-1.5 rounded-full text-xs font-medium capitalize transition"
                style="{{ $moodInput === $mood ? 'background:#1D9E75;color:#fff;' : 'background:#21262D;color:#8B949E;' }}"
                onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='{{ $moodInput === $mood ? '#fff' : '#8B949E' }}'"
            >{{ $mood }}</button>
        @endforeach
        @if ($moodInput)
            <button type="button" wire:click="$set('moodInput', '')"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition"
                style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
            >✕ Clear mood</button>
        @endif
    </div>

    {{-- Status text --}}
    <div class="flex gap-2">
        <input
            type="text"
            wire:model.live="statusTextInput"
            maxlength="60"
            placeholder="What are you up to? (optional)"
            class="flex-1 text-sm rounded-lg px-3 py-2 focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
            onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
        >
        <button type="button" wire:click="saveStatus"
            class="px-4 py-2 text-sm font-medium rounded-lg transition"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >Set</button>
        <button type="button" wire:click="clearStatus"
            class="px-4 py-2 text-sm font-medium rounded-lg transition"
            style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
        >Clear</button>
    </div>
    <p class="mt-1 text-xs" style="color:#8B949E;">{{ strlen($statusTextInput) }}/60 · Expires after 24 hours</p>
</div>
