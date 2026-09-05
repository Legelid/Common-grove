@props(['type' => 'text', 'error' => null])

@php
    $classes = 'w-full rounded-btn px-4 py-2.5 text-sm bg-surface text-text placeholder:text-text-faint border transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent '
        . ($error ? 'border-danger' : 'border-border focus:border-accent');
@endphp

<input type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>

@if ($error)
    <p class="mt-1.5 text-xs text-danger">{{ $error }}</p>
@endif
