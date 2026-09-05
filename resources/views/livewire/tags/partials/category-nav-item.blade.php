@php
    $catActive = ! $viewingSuggested && $activeCategoryId === $cat->id;
    $catCount  = $this->selectedCountsByCategory->get($cat->id, 0);
@endphp
<button
    type="button"
    wire:click="setCategory({{ $cat->id }})"
    wire:key="{{ $rowKey }}"
    class="transition"
    style="display:flex;align-items:center;justify-content:space-between;padding:0.625rem 0.875rem;border-radius:var(--radius-md);font-size:0.9375rem;font-family:var(--font-body);width:100%;text-align:left;cursor:pointer;border:none;margin-bottom:0.25rem;background:{{ $catActive ? 'var(--accent)' : 'transparent' }};color:{{ $catActive ? 'var(--bg)' : 'var(--text-muted)' }};font-weight:{{ $catActive ? '600' : '400' }};"
    @if (! $catActive)
        onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)'"
        onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'"
    @endif
>
    <span>{{ $cat->name }}</span>
    @if ($catCount > 0)
        <span style="background:rgba(255,255,255,0.2);color:inherit;border-radius:var(--radius-pill);padding:0.1rem 0.4rem;font-size:0.6875rem;font-weight:600;">{{ $catCount }}</span>
    @endif
</button>
