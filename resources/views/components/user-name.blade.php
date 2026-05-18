@props(['user'])

@php
    $isAnon          = $user->identity_mode === 3;
    $primary         = $isAnon ? 'Anonymous' : $user->gamertag;
    $preferred       = $user->preferred_name; // null unless identity_mode === 2
    $showSupportIcon = $user->is_supporter && ($user->show_supporter_icon ?? true);
@endphp

@if ($preferred)
    {{-- Mode 2: gamertag + hover tooltip showing preferred name --}}
    <span {{ $attributes->class(['relative', 'cursor-default', 'inline-flex', 'items-center', 'gap-1']) }}
        x-data="{ t: false }"
        @mouseenter="t=true" @mouseleave="t=false"
        @focusin="t=true" @focusout="t=false"
        tabindex="0"
        aria-label="{{ $primary }}, prefers {{ $preferred }}"
    ><span class="relative">{{ $primary }}<span
        x-show="t"
        style="display:none;position:absolute;left:0;bottom:calc(100% + 4px);background:#1C2333;border:1px solid #30363D;color:#8B949E;padding:2px 7px;border-radius:5px;font-size:0.68rem;white-space:nowrap;z-index:50;pointer-events:none;font-weight:400;"
    >Prefers {{ $preferred }}</span></span>@if ($showSupportIcon)<x-supporter-icon />@endif</span>
@else
    @if ($showSupportIcon)
        <span {{ $attributes->class(['inline-flex', 'items-center', 'gap-1']) }}>{{ $primary }}<x-supporter-icon /></span>
    @else
        <span {{ $attributes }}>{{ $primary }}</span>
    @endif
@endif
