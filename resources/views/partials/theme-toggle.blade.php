{{-- Sun/moon theme toggle. Include inside a header/nav; sizes via the wrapping context.
     Icon visibility is pure CSS (see .cg-theme-icon-* in app.css) — driven directly by
     html[data-theme], not a one-time Alpine snapshot, so it can never go stale across
     wire:navigate page swaps. Alpine here only handles the click behavior. --}}
<button
    type="button"
    x-data="{
        toggle() {
            var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('cg-theme', next); } catch (e) {}
        },
    }"
    @click="toggle()"
    class="flex items-center justify-center w-8 h-8 rounded-full transition"
    style="color:var(--text-muted);"
    onmouseover="this.style.color='var(--text)';this.style.background='var(--surface-raised)'"
    onmouseout="this.style.color='var(--text-muted)';this.style.background='transparent'"
    aria-label="Toggle theme"
>
    {{-- Moon (shown in dark mode) --}}
    <svg class="cg-theme-icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
    {{-- Sun (shown in light mode) --}}
    <svg class="cg-theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
    </svg>
</button>
