<span
    class="inline-flex items-center"
    x-data="{
        show: false,
        top: 0,
        left: 0,
        open() {
            const r = $el.getBoundingClientRect();
            this.top = r.top;
            this.left = r.left + r.width / 2;
            this.show = true;
        },
    }"
    @mouseenter="open()" @mouseleave="show=false"
    @focusin="open()" @focusout="show=false"
    style="cursor:default;"
    aria-label="I believe in CommonGrove"
>
    <svg width="9" height="9" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="color:var(--accent);flex-shrink:0;">
        <path d="M8 14S2 9.35 2 5.5a3.5 3.5 0 0 1 6-2.45A3.5 3.5 0 0 1 14 5.5C14 9.35 8 14 8 14z" fill="currentColor" opacity="0.75"/>
    </svg>
    {{-- Teleported to <body> — see grovekeeper-badge.blade.php for why, and
         why :style must be an object rather than a string. --}}
    <template x-teleport="body">
        <span
            x-show="show"
            :style="{ top: top + 'px', left: left + 'px' }"
            style="display:none;position:fixed;transform:translate(-50%, calc(-100% - 5px));background:var(--surface);border:1px solid var(--border);color:var(--text-muted);padding:2px 7px;border-radius:5px;font-size:0.68rem;white-space:nowrap;z-index:9999;pointer-events:none;font-weight:400;"
        >I believe in CommonGrove</span>
    </template>
</span>
