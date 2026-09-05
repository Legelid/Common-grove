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
    @click.stop="show ? show=false : open()"
    @focusin="open()" @focusout="show=false"
    style="cursor:default;"
    aria-label="Grovekeeper · founder of CommonGrove"
    tabindex="0"
>
    <span style="font-size:0.6rem;color:var(--accent);letter-spacing:0.025em;font-weight:500;line-height:1;opacity:0.85;">🌿</span>
    {{-- Teleported to <body>: badges render inside all sorts of truncated /
         scrollable containers (room dropdowns, message bubbles, participant
         lists), and any of those ancestors clips a same-subtree tooltip via
         overflow:hidden/auto regardless of z-index. Teleporting escapes that
         entirely; position is computed from the trigger's own rect on open.
         :style is bound as an OBJECT (not a string) — Alpine sets object
         entries per-property, so it merges with the static style attribute
         instead of replacing its whole cssText (which would also wipe out
         x-show's own display toggling). --}}
    <template x-teleport="body">
        <span
            x-show="show"
            :style="{ top: top + 'px', left: left + 'px' }"
            style="display:none;position:fixed;transform:translate(-50%, calc(-100% - 5px));background:var(--surface);border:1px solid rgba(var(--accent-rgb),0.25);color:var(--text-muted);padding:2px 8px;border-radius:5px;font-size:0.65rem;white-space:nowrap;z-index:9999;pointer-events:none;font-weight:400;"
        >GroveKeeper - Founder of CommonGrove</span>
    </template>
</span>
