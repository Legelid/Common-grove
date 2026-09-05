@props([
    'label'    => '',
    'selected' => false,
    'disabled' => false,
])

@php
    $lowStim    = auth()->check() && auth()->user()->low_stimulation_mode;
    $baseClass  = 'px-3.5 py-1.5 rounded-full text-sm font-medium transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 select-none';

    if ($selected) {
        $glow  = $lowStim ? '' : 'box-shadow:0 0 0 3px rgba(210,153,34,0.1);';
        $style = 'background:rgba(210,153,34,0.18);color:#E6B84A;border:1px solid rgba(210,153,34,0.45);' . $glow . 'cursor:pointer;';
    } elseif ($disabled) {
        $style = 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);opacity:0.4;cursor:not-allowed;';
    } else {
        $style = 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);cursor:pointer;';
    }
@endphp

<button
    type="button"
    role="checkbox"
    aria-checked="{{ $selected ? 'true' : 'false' }}"
    @if($disabled) disabled aria-disabled="true" @endif
    {{ $attributes->merge(['class' => $baseClass]) }}
    style="{{ $style }}"
    @if(!$selected && !$disabled)
        onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)';this.style.borderColor='var(--text-faint)';"
        onmouseout="this.style.background='var(--surface)';this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';"
    @endif
>{{ $label }}</button>
