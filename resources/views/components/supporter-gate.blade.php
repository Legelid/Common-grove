@props([
    'feature' => null,   // optional human-readable feature name shown in the locked state
    'inline'  => false,  // true = compact inline lock; false = card-style lock
])

@if (auth()->check() && auth()->user()->isSupporter())
    {{ $slot }}
@else
    @if ($inline)
        <span class="inline-flex items-center gap-1.5 text-xs" style="color:#3d4451;">
            <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
            @if ($feature) {{ $feature }} — @endif Supporter feature
        </span>
    @else
        <div class="rounded-xl border px-4 py-3 flex items-start gap-3" style="background:#161B22;border-color:#21262D;">
            <svg class="flex-none mt-0.5" width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#3d4451" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
            <div>
                @if ($feature)
                    <p class="text-xs font-medium mb-0.5" style="color:#8B949E;">{{ $feature }}</p>
                @endif
                <p class="text-xs" style="color:#3d4451;">
                    Available to supporters.
                    <a href="{{ route('support') }}" class="underline" style="color:#8B949E;" wire:navigate>Learn more</a>
                </p>
            </div>
        </div>
    @endif
@endif
