@props([
    'room',
    'variant'   => 'list',
    'stateLabel' => 'Open',
    'isPinned'  => false,
    'isGuest'   => false,
])

@php
    $vibeWords = [
        'quiet' => 'Quiet', 'low-key' => 'Low-key', 'chill' => 'Chill', 'casual' => 'Casual',
        'listener-friendly' => 'Listener-friendly', 'advice-welcome' => 'Advice welcome',
        'low-stimulation-spaces' => 'Low-stimulation', 'deep-talks' => 'Deep talks welcome',
        'open-to-strangers' => 'Open to strangers',
    ];
    $vibeParts = $room->tags->pluck('slug')->map(fn ($slug) => $vibeWords[$slug] ?? null)->filter()->values();

    $metaParts = [$stateLabel];
    if ($room->is_persistent) {
        $metaParts[] = 'Always-open';
    }
    foreach ($vibeParts as $vp) {
        if (count($metaParts) >= 3) break;
        $metaParts[] = $vp;
    }
    $metaLine = implode(' · ', $metaParts);

    $tagLimit = $variant === 'list' ? 2 : 3;
@endphp

@if ($variant === 'compact')

    {{-- ── Compact tile — CommonGrove always-open starter rooms only ────────── --}}
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:0.75rem 1rem;">
        @if ($room->icon)
            <span class="flex-none" style="color:var(--text-faint);" aria-hidden="true">
                @switch($room->icon)
                    @case('moon') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg> @break
                    @case('brain') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.16Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.16Z"/></svg> @break
                    @case('leaf') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8C8 10 5.9 16.17 3.82 20.6c-.26.57.56 1.04.97.55C7 18 10 16 15 16c4.58 0 7-3.5 7-3.5C24 9.5 17 8 17 8z"/></svg> @break
                    @case('book') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg> @break
                    @case('gamepad') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="15" y1="13" x2="15.01" y2="13"/><line x1="18" y1="11" x2="18.01" y2="11"/><rect x="2" y="6" width="20" height="12" rx="2"/></svg> @break
                    @case('chat') <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> @break
                    @default <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                @endswitch
            </span>
        @endif
        <div class="flex-1 min-w-0">
            @if ($room->title)
                <p class="font-display text-sm font-semibold truncate" style="color:var(--text);">{{ $room->title }}</p>
            @endif
            <p class="text-xs truncate" style="color:var(--text-muted);">{{ \Illuminate\Support\Str::words($room->content, 8) }}</p>
        </div>
        @if ($isGuest)
            <a href="{{ route('register') }}" class="flex-none" aria-label="Step in"><x-arrow-icon label="Step in" /></a>
        @else
            <button type="button" wire:click="joinHangout('{{ $room->id }}')" class="flex-none" style="background:transparent;border:none;cursor:pointer;" aria-label="Step in"><x-arrow-icon label="Step in" /></button>
        @endif
    </div>

@elseif ($variant === 'featured')

    {{-- ── Featured card — one per section maximum ───────────────────────── --}}
    <div {{ $attributes->merge(['class' => 'w-full']) }} style="background:var(--surface);border:1px solid var(--border-strong);border-left:4px solid var(--accent);border-radius:var(--radius-lg);padding:1.5rem;box-shadow:var(--shadow-md);">
        @if ($room->title)
            <h3 class="font-display font-semibold" style="font-size:1.25rem;color:var(--text);">{{ $room->title }}</h3>
        @endif
        <p class="mt-2 text-sm leading-relaxed line-clamp-2" style="color:var(--text-muted);">{{ $room->content }}</p>
        <p class="mt-3" style="color:var(--text-faint);font-size:0.8125rem;">{{ $metaLine }}</p>

        @if ($room->tags->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($room->tags->take($tagLimit) as $tag)
                    <x-tag-bubble :label="$tag->name" :selected="false" tabindex="-1" />
                @endforeach
            </div>
        @endif

        <div class="mt-5 flex flex-wrap gap-2">
            <x-button variant="secondary" type="button" disabled title="Coming soon">Preview</x-button>

            @if ($isGuest)
                <x-button variant="secondary" type="button" disabled title="Sign in to save">Save</x-button>
            @else
                <x-button variant="secondary" type="button" wire:click="toggleCardPin('{{ $room->id }}')" wire:loading.attr="disabled" wire:target="toggleCardPin('{{ $room->id }}')">
                    {{ $isPinned ? 'Saved ✓' : 'Save' }}
                </x-button>
            @endif

            @if ($isGuest)
                <x-button variant="primary" href="{{ route('register') }}">Step in</x-button>
            @else
                <x-button variant="primary" type="button" wire:click="joinHangout('{{ $room->id }}')" wire:loading.attr="disabled" wire:target="joinHangout('{{ $room->id }}')">
                    <span wire:loading.remove wire:target="joinHangout('{{ $room->id }}')">Step in</span>
                    <span wire:loading wire:target="joinHangout('{{ $room->id }}')">Stepping in…</span>
                </x-button>
            @endif
        </div>
    </div>

@else

    {{-- ── List row — default variant, used for most rooms ───────────────── --}}
    <div
        {{ $attributes->merge(['class' => 'flex items-center gap-4 transition']) }}
        style="background:transparent;border-bottom:1px solid var(--border);border-radius:0;padding:1rem 0.5rem;"
        onmouseover="this.style.background='var(--surface)';this.style.borderRadius='var(--radius-md)';this.style.borderBottomColor='transparent';"
        onmouseout="this.style.background='transparent';this.style.borderRadius='0';this.style.borderBottomColor='var(--border)';"
    >
        <div class="flex-1 min-w-0">
            @if ($room->title)
                <p class="font-display font-semibold truncate" style="font-size:1rem;color:var(--text);">{{ $room->title }}</p>
            @endif
            <p class="text-sm truncate" style="color:var(--text-muted);">{{ $room->content }}</p>
            <p class="mt-1" style="color:var(--text-faint);font-size:0.8125rem;">{{ $metaLine }}</p>

            @if ($room->tags->isNotEmpty())
                <div class="mt-1.5 flex flex-wrap gap-1.5">
                    @foreach ($room->tags->take($tagLimit) as $tag)
                        <x-tag-bubble :label="$tag->name" :selected="false" tabindex="-1" class="!text-xs !px-2.5 !py-0.5" />
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 flex-none">
            <span title="Coming soon" class="hidden sm:inline" style="color:var(--text-faint);cursor:not-allowed;font-size:0.8125rem;">Preview</span>

            @if ($isGuest)
                <button type="button" disabled title="Sign in to save" class="flex-none" style="color:var(--text-faint);opacity:0.5;cursor:not-allowed;background:transparent;border:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                </button>
            @else
                <button type="button" wire:click="toggleCardPin('{{ $room->id }}')" title="{{ $isPinned ? 'Saved' : 'Save' }}" class="flex-none" style="background:transparent;border:none;cursor:pointer;color:{{ $isPinned ? 'var(--accent)' : 'var(--text-faint)' }};">
                    @if ($isPinned)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    @else
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    @endif
                </button>
            @endif

            @if ($isGuest)
                <x-button variant="primary" href="{{ route('register') }}" class="!px-4 !py-1.5 !text-xs">Step in</x-button>
            @else
                <x-button variant="primary" type="button" wire:click="joinHangout('{{ $room->id }}')" wire:loading.attr="disabled" wire:target="joinHangout('{{ $room->id }}')" class="!px-4 !py-1.5 !text-xs">
                    <span wire:loading.remove wire:target="joinHangout('{{ $room->id }}')">Step in</span>
                    <span wire:loading wire:target="joinHangout('{{ $room->id }}')">…</span>
                </x-button>
            @endif
        </div>
    </div>

@endif
