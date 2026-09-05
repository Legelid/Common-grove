@props(['error' => null])

@php
    $classes = 'w-full rounded-btn px-4 py-2.5 text-sm bg-surface text-text placeholder:text-text-faint border transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent resize-none '
        . ($error ? 'border-danger' : 'border-border focus:border-accent');
@endphp

<textarea {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</textarea>

@if ($error)
    <p class="mt-1.5 text-xs text-danger">{{ $error }}</p>
@endif
