@props(['size' => '4.8em', 'bubble' => '2.75em', 'label' => null])

<span
    @if ($label)
        x-data="{ tipShow: false }"
        @mouseenter="tipShow = true"
        @mouseleave="tipShow = false"
    @endif
    style="position:relative;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;vertical-align:middle;width:{{ $bubble }};height:{{ $bubble }};border-radius:50%;background:var(--surface-raised);border:1px solid var(--border);transition:all 150ms ease;"
    onmouseover="this.style.background='var(--surface)';this.style.borderColor='var(--accent)';"
    onmouseout="this.style.background='var(--surface-raised)';this.style.borderColor='var(--border)';"
>
    <span
        aria-hidden="true"
        {{ $attributes->merge(['style' => "display:inline-block;flex-shrink:0;width:{$size};height:{$size};background-color:var(--accent);-webkit-mask-image:url('" . asset('images/leaf-arrow-icon.png') . "');mask-image:url('" . asset('images/leaf-arrow-icon.png') . "');-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;"]) }}
    ></span>

    @if ($label)
        <span
            x-show="tipShow"
            style="display:none;position:absolute;bottom:calc(100% + 8px);left:50%;transform:translateX(-50%);background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.25rem 0.75rem;font-size:0.75rem;font-weight:400;color:var(--text);white-space:nowrap;pointer-events:none;z-index:30;"
        >{{ $label }}</span>
    @endif
</span>
