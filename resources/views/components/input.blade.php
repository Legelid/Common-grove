@props(['type' => 'text', 'error' => null])

@php
    $classes = 'w-full rounded-btn px-4 py-2.5 text-sm bg-surface text-text placeholder:text-text-faint border transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent '
        . ($error ? 'border-danger' : 'border-border focus:border-accent');
@endphp

<input type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>

@if ($error)
    <p
        role="alert"
        class="mt-1.5 text-xs font-semibold text-danger flex items-center gap-1.5"
        style="background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"
    ><span aria-hidden="true">⚠</span> {{ $error }}</p>
@endif
