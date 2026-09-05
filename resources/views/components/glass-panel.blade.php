@props(['tier' => 'light'])

@php
    $tierClass = $tier === 'heavy' ? 'cg-glass-panel--heavy' : 'cg-glass-panel--light';
@endphp

<div {{ $attributes->merge(['class' => "cg-glass-panel {$tierClass}"]) }}>
    {{ $slot }}
</div>
