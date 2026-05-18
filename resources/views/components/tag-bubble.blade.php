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
        $style = 'background:#1C2333;color:#8B949E;border:1px solid #30363D;opacity:0.4;cursor:not-allowed;';
    } else {
        $style = 'background:#1C2333;color:#8B949E;border:1px solid #30363D;cursor:pointer;';
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
        onmouseover="this.style.background='#21262D';this.style.color='#C9D1D9';this.style.borderColor='#3d4451';"
        onmouseout="this.style.background='#1C2333';this.style.color='#8B949E';this.style.borderColor='#30363D';"
    @endif
>{{ $label }}</button>
