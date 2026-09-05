{{--
    Connection requests (Section 4) need Accept + Decline together, so this
    accepts one optional second action beyond the single-action contract
    the rest of the page uses.
--}}
@props([
    'user',
    'action',
    'explanation'     => null,
    'href'            => null,
    'wireClick'       => null,
    'secondAction'    => null,
    'secondWireClick' => null,
])

@php
    $variants = [
        'Say hello again' => 'secondary',
        'Stay connected'  => 'primary',
        'Accept'          => 'primary',
        'Decline'         => 'secondary',
    ];
    $variant = $variants[$action] ?? 'secondary';
@endphp

{{--
    NEVER add follower/friend/post counts, exact join dates, or any other
    status-implying metric to this card — CommonGrove's "equal visibility,
    no status signaling" rule applies here specifically.
--}}
<div
    class="flex items-center gap-3 transition"
    style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem;"
    onmouseover="this.style.background='var(--surface-raised)'"
    onmouseout="this.style.background='var(--surface)'"
>
    <x-avatar :user="$user" size="md" />

    <div class="min-w-0 flex-1">
        <x-user-name :user="$user" class="text-sm font-medium block truncate" style="color:var(--text);" />
        @if ($explanation)
            <p style="font-size:0.8125rem;color:var(--text-faint);font-style:italic;margin-top:0.25rem;">{{ $explanation }}</p>
        @endif
    </div>

    <div class="flex-none flex items-center gap-2">
        @if ($href)
            <x-button :href="$href" variant="{{ $variant }}" class="!px-3 !py-1.5 !text-sm">{{ $action }}</x-button>
        @elseif ($wireClick)
            <x-button type="button" wire:click="{{ $wireClick }}" variant="{{ $variant }}" class="!px-3 !py-1.5 !text-sm">{{ $action }}</x-button>
        @endif

        @if ($secondAction && $secondWireClick)
            <x-button type="button" wire:click="{{ $secondWireClick }}" variant="{{ $variants[$secondAction] ?? 'secondary' }}" class="!px-3 !py-1.5 !text-sm">{{ $secondAction }}</x-button>
        @endif
    </div>
</div>
