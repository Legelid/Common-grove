@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "rounded-card border border-border bg-surface shadow-card {$padding}"]) }}>
    {{ $slot }}
</div>
