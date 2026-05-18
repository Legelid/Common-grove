<span
    class="inline-flex items-center"
    x-data="{ t: false }"
    @mouseenter="t=true" @mouseleave="t=false"
    @focusin="t=true" @focusout="t=false"
    style="position:relative;cursor:default;"
    aria-label="I believe in CommonGrove"
>
    <svg width="9" height="9" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="color:#1D9E75;flex-shrink:0;">
        <path d="M8 14S2 9.35 2 5.5a3.5 3.5 0 0 1 6-2.45A3.5 3.5 0 0 1 14 5.5C14 9.35 8 14 8 14z" fill="currentColor" opacity="0.75"/>
    </svg>
    <span
        x-show="t"
        style="display:none;position:absolute;left:50%;transform:translateX(-50%);bottom:calc(100% + 5px);background:#1C2333;border:1px solid #30363D;color:#8B949E;padding:2px 7px;border-radius:5px;font-size:0.68rem;white-space:nowrap;z-index:50;pointer-events:none;font-weight:400;"
    >I believe in CommonGrove</span>
</span>
