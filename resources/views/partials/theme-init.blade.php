<script>
(function () {
    // Light/dark toggle is hidden pending a different feature (see
    // partials.theme-toggle) — force dark and ignore any stored preference
    // so an old "light" value can't take effect. Flip this to false and the
    // original stored-preference logic below resumes as before.
    var FORCE_DARK = true;

    function applyStoredTheme() {
        if (FORCE_DARK) {
            document.documentElement.setAttribute('data-theme', 'dark');
            return;
        }
        try {
            var stored = localStorage.getItem('cg-theme');
            if (stored === 'light' || stored === 'dark') {
                document.documentElement.setAttribute('data-theme', stored);
            }
        } catch (e) {}
    }

    applyStoredTheme();

    // wire:navigate swaps pages via AJAX and reconciles <html>'s attributes
    // against the freshly-fetched page — since data-theme is only ever set
    // client-side (never rendered server-side), that reconciliation strips
    // it on every navigation. Re-apply it once the swap completes.
    document.addEventListener('livewire:navigated', applyStoredTheme);
})();
</script>
