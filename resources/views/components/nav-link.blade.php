@props(['active' => false, 'href' => '#'])

@php
    $classes = 'flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-colors duration-150 '
        . ($active
            ? 'bg-accent/10 text-text'
            : 'text-text-muted hover:text-text hover:bg-surface-raised');
@endphp

<a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => $classes]) }}>
    <span class="w-1 h-1 rounded-full flex-none {{ $active ? 'bg-accent' : 'bg-transparent' }}"></span>
    {{ $slot }}
</a>
