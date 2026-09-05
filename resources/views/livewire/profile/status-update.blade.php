<div>
    @if ($statusMessage)
        <p class="mb-3 text-sm text-accent">{{ $statusMessage }}</p>
    @endif

    {{-- Mood pills --}}
    <div class="flex flex-wrap gap-2 mb-3">
        @foreach ($this->moodOptions() as $mood)
            <button
                type="button"
                wire:click="$set('moodInput', '{{ $mood }}')"
                class="px-3 py-1.5 rounded-full text-xs font-medium capitalize transition"
                style="{{ $moodInput === $mood ? 'background:var(--accent);color:var(--on-accent);' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                onmouseover="this.style.color='{{ $moodInput === $mood ? 'var(--on-accent)' : 'var(--text)' }}'" onmouseout="this.style.color='{{ $moodInput === $mood ? 'var(--on-accent)' : 'var(--text-muted)' }}'"
            >{{ $mood }}</button>
        @endforeach
        @if ($moodInput)
            <button type="button" wire:click="$set('moodInput', '')"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition"
                style="color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
            >✕ Clear mood</button>
        @endif
    </div>

    {{-- Status text --}}
    <div class="flex gap-2">
        <x-input
            type="text"
            wire:model.live="statusTextInput"
            maxlength="60"
            placeholder="What are you up to? (optional)"
            class="flex-1"
        />
        <x-button type="button" wire:click="saveStatus" variant="primary">Set</x-button>
        <x-button type="button" wire:click="clearStatus" variant="secondary">Clear</x-button>
    </div>
    <p class="mt-1 text-xs" style="color:var(--text-muted);">{{ strlen($statusTextInput) }}/60 · Expires after 24 hours</p>
</div>
