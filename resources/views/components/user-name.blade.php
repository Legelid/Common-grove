@props(['user'])

@php
    $isAnon           = $user->identity_mode === 3;
    $primary          = $isAnon ? 'Anonymous' : $user->gamertag;
    $preferred        = $user->preferred_name; // null unless identity_mode === 2
    $showSupportIcon  = $user->is_supporter && ($user->show_supporter_icon ?? true);
    $showGrovekeeper  = !$isAnon && ($user->is_admin ?? false);
@endphp

@if ($preferred)
    {{-- Mode 2: gamertag + hover tooltip showing preferred name. Teleported
         to <body> (see grovekeeper-badge.blade.php) — this span routinely
         carries a caller-supplied "truncate" class (overflow:hidden), which
         silently clipped a same-subtree tooltip no matter its z-index. --}}
    <span {{ $attributes->class(['cursor-default', 'inline-flex', 'items-center', 'gap-1']) }}
        x-data="{
            show: false,
            top: 0,
            left: 0,
            open() {
                const r = $el.getBoundingClientRect();
                this.top = r.top;
                this.left = r.left;
                this.show = true;
            },
        }"
        @mouseenter="open()" @mouseleave="show=false"
        @focusin="open()" @focusout="show=false"
        tabindex="0"
        aria-label="{{ $primary }}, prefers {{ $preferred }}"
    ><span>{{ $primary }}</span><template x-teleport="body"><span
        x-show="show"
        :style="{ top: top + 'px', left: left + 'px' }"
        style="display:none;position:fixed;transform:translateY(calc(-100% - 4px));background:var(--surface);border:1px solid var(--border);color:var(--text-muted);padding:2px 7px;border-radius:5px;font-size:0.68rem;white-space:nowrap;z-index:9999;pointer-events:none;font-weight:400;"
    >Prefers {{ $preferred }}</span></template>@if ($showSupportIcon)<x-supporter-icon />@endif@if ($showGrovekeeper)<x-grovekeeper-badge />@endif</span>
@else
    @if ($showSupportIcon || $showGrovekeeper)
        <span {{ $attributes->class(['inline-flex', 'items-center', 'gap-1']) }}>{{ $primary }}@if ($showSupportIcon)<x-supporter-icon />@endif@if ($showGrovekeeper)<x-grovekeeper-badge />@endif</span>
    @else
        <span {{ $attributes }}>{{ $primary }}</span>
    @endif
@endif
