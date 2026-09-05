@php
    $selected = in_array($tag->id, $selectedTagIds);
    $rowStyle = 'padding:0.5rem 0.625rem;border-radius:var(--radius-md);background:transparent;border:none;cursor:pointer;';
    if ($selected) {
        $rowStyle .= 'border-left:2px solid var(--accent);padding-left:calc(0.625rem - 2px);';
    }
@endphp
<button
    type="button"
    wire:click="toggleTag('{{ $tag->id }}')"
    wire:key="{{ $rowKey }}"
    class="w-full flex items-center justify-between text-left transition"
    style="{{ $rowStyle }}"
    @if (! $selected)
        onmouseover="this.style.background='var(--surface-raised)'"
        onmouseout="this.style.background='transparent'"
    @endif
>
    <span style="font-family:var(--font-body);font-size:0.9375rem;color:{{ $selected ? 'var(--text-muted)' : 'var(--text)' }};">
        {{ $tag->name }}
        @isset($suffix)
            <span style="font-size:0.8125rem;color:var(--text-faint);margin-left:0.375rem;">· {{ $suffix }}</span>
        @endisset
    </span>
    <span style="color:{{ $selected ? 'var(--accent)' : 'var(--text-faint)' }};font-size:0.875rem;font-weight:{{ $selected ? '700' : '400' }};flex-none;">{{ $selected ? '✓' : '+' }}</span>
</button>
