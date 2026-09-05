@php
    $isCore      = in_array($tag->id, $coreInterests, true);
    $isHidden    = in_array($tag->id, $hiddenInterests, true);
    $isSensitive = $tag->subcategory?->is_sensitive ?? false;
@endphp
<div
    class="transition"
    style="font-family:var(--font-body);font-size:0.9375rem;color:var(--text);padding:0.375rem 0.5rem;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between;gap:0.5rem;"
    onmouseover="this.style.background='var(--surface-raised)'"
    onmouseout="this.style.background='transparent'"
    wire:key="{{ $rowKey }}"
>
    <span style="display:flex;align-items:center;gap:0.375rem;min-width:0;">
        <button
            type="button"
            wire:click="toggleCore('{{ $tag->id }}')"
            class="flex-none"
            style="background:transparent;border:none;cursor:pointer;padding:0.25rem;min-width:32px;min-height:32px;display:flex;align-items:center;justify-content:center;color:{{ $isCore ? 'var(--accent-amber)' : 'var(--text-faint)' }};font-size:0.875rem;"
            aria-label="{{ $isCore ? 'Remove ' . $tag->name . ' from core interests' : 'Mark ' . $tag->name . ' as a core interest' }}"
        >{{ $isCore ? '★' : '☆' }}</button>

        <span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            @if ($isHidden)
                <span style="font-size:0.75rem;color:var(--text-faint);margin-right:0.25rem;">[Hidden]</span>
            @endif
            {{ $tag->name }}
            @if ($tag->source === 'custom')
                <span style="opacity:0.6;font-size:0.65rem;margin-left:0.25rem;" title="Personal interest">★</span>
            @endif
        </span>
    </span>

    <span style="display:flex;align-items:center;gap:0.125rem;flex-none;">
        @if ($isSensitive)
            <button
                type="button"
                wire:click="toggleHidden('{{ $tag->id }}')"
                style="background:transparent;border:none;cursor:pointer;padding:0.25rem;min-width:32px;min-height:32px;display:flex;align-items:center;justify-content:center;color:var(--text-faint);font-size:0.9375rem;"
                onmouseover="this.style.color='var(--text)'"
                onmouseout="this.style.color='var(--text-faint)'"
                aria-label="{{ $isHidden ? 'Make ' . $tag->name . ' visible to others' : 'Hide ' . $tag->name . ' from others' }}"
            >{{ $isHidden ? '🚫' : '👁' }}</button>
        @endif

        <button
            type="button"
            wire:click="{{ $context === 'core' ? 'toggleCore' : 'toggleTag' }}('{{ $tag->id }}')"
            class="transition flex-none"
            style="color:var(--text-faint);font-size:0.75rem;padding:0.25rem 0.5rem;min-width:44px;min-height:44px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;cursor:pointer;"
            onmouseover="this.style.color='var(--danger)'"
            onmouseout="this.style.color='var(--text-faint)'"
            aria-label="{{ $context === 'core' ? 'Remove ' . $tag->name . ' from core interests' : 'Remove ' . $tag->name }}"
        >×</button>
    </span>
</div>
