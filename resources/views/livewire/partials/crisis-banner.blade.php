@if ($showCrisisBanner)
    <div class="mx-4 mb-2 rounded-xl border px-4 py-3 text-sm space-y-2 flex-none" style="background:rgba(210,153,34,0.08);border-color:rgba(210,153,34,0.4);">
        <div class="flex items-start justify-between gap-3">
            <p class="font-semibold" style="color:#D29922;">If you're going through a difficult time, support is available:</p>
            <button wire:click="dismissCrisisBanner" class="flex-none text-lg leading-none mt-0.5 transition" style="color:#D29922;" aria-label="Dismiss"
                onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='#D29922'">&times;</button>
        </div>
        <ul class="space-y-1 text-sm" style="color:rgba(210,153,34,0.85);">
            @foreach (app(\App\Services\CrisisDetectionService::class)->getResources() as $resource)
                <li>
                    <span class="font-medium">{{ $resource['name'] }}:</span>
                    {{ $resource['contact'] }} — {{ $resource['description'] }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
