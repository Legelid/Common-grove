<span
    class="inline-flex items-center"
    x-data="{ gt: false }"
    @mouseenter="gt=true" @mouseleave="gt=false"
    @click.stop="gt=!gt"
    @focusin="gt=true" @focusout="gt=false"
    style="position:relative;cursor:default;"
    aria-label="Grovekeeper — founder of CommonGrove"
    tabindex="0"
>
    <span style="font-size:0.6rem;color:#4A9E6A;letter-spacing:0.025em;font-weight:500;line-height:1;opacity:0.85;">🌿 Grovekeeper</span>
    <span
        x-show="gt"
        style="display:none;position:absolute;left:50%;transform:translateX(-50%);bottom:calc(100% + 5px);background:#1C2333;border:1px solid rgba(74,158,106,0.25);color:#8B949E;padding:2px 8px;border-radius:5px;font-size:0.65rem;white-space:nowrap;z-index:50;pointer-events:none;font-weight:400;"
    >Founder of CommonGrove</span>
</span>
