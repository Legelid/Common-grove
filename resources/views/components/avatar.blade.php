@props([
    'user',
    'size' => 'md',
])

@php
    $dimensions = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-16 h-16',
        'xl' => 'w-20 h-20',
    ];
    $dim = $dimensions[$size] ?? $dimensions['md'];
    $isFirstRoots = $user->is_first_roots ?? false;
@endphp

@once
<style>
@keyframes fr-ring-pulse {
    0%, 100% { box-shadow: 0 0 0 2px rgba(29,158,117,0.65), 0 0 10px rgba(29,158,117,0.22); }
    50%       { box-shadow: 0 0 0 2px rgba(29,158,117,0.28), 0 0 16px rgba(29,158,117,0.10); }
}
.fr-avatar-ring { animation: fr-ring-pulse 3s ease-in-out infinite; }
</style>
@endonce

<span
    class="relative inline-block flex-none"
    @if($isFirstRoots) x-data="{ frTip: false }" @mouseenter="frTip = true" @mouseleave="frTip = false" @endif
>
    <img
        src="{{ $user->avatar_url }}"
        alt=""
        class="{{ $dim }} rounded-full object-cover"
        style="background:#21262D;"
    >

    @if($isFirstRoots)
        <span
            class="fr-avatar-ring absolute inset-0 rounded-full pointer-events-none"
            aria-hidden="true"
        ></span>

        <span
            x-show="frTip"
            x-cloak
            class="absolute z-50 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-xs pointer-events-none"
            style="bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#161B22;border:1px solid rgba(29,158,117,0.4);color:#1D9E75;box-shadow:0 4px 12px rgba(0,0,0,0.5);"
        >FirstRoots — Founding Member of CommonGrove</span>
    @endif
</span>
