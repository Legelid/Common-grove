@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-btn text-sm font-medium transition-all duration-150 ease-out px-4 py-2.5 hover:scale-[1.02] active:scale-100 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100';

    $variants = [
        'primary'     => 'bg-accent text-on-accent shadow-sm hover:bg-accent-hover',
        'secondary'   => 'bg-transparent text-accent border border-accent hover:bg-accent hover:text-on-accent',
        'destructive' => 'bg-danger text-on-danger shadow-sm hover:brightness-110',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
