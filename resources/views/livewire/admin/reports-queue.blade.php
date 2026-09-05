<div class="p-8">
    <h1 class="text-2xl font-bold mb-6 font-display" style="color:var(--text);">Reports Queue</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b" style="border-color:var(--border);">
        @foreach (['pending' => 'Pending', 'actioned' => 'Actioned', 'dismissed' => 'Dismissed'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg transition -mb-px border-b-2"
                style="{{ $tab === $key ? 'color:var(--text);border-color:var(--accent);' : 'color:var(--text-muted);border-color:transparent;' }}"
            >
                {{ $label }}
                <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full"
                    style="{{ $key === 'pending' && $this->tabCounts[$key] > 0 ? 'background:rgba(var(--danger-rgb),0.2);color:var(--danger);' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                >{{ $this->tabCounts[$key] }}</span>
            </button>
        @endforeach
    </div>

    @if ($this->reports->isEmpty())
        <p class="text-sm" style="color:var(--text-muted);">No reports in this category.</p>
    @else
        <div class="space-y-4">
            @foreach ($this->reports as $report)
                <x-card padding="p-5" wire:key="report-{{ $report->id }}" class="space-y-3">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-sm" style="color:var(--text);">
                                <span class="font-semibold" style="color:var(--danger);">{{ $report->reportedUser?->gamertag ?? '—' }}</span>
                                reported by
                                <span class="font-semibold" style="color:var(--text);">{{ $report->reporter?->gamertag ?? '—' }}</span>
                            </p>
                            <p class="text-xs" style="color:var(--text-muted);">
                                Type: <span style="color:var(--text);">{{ class_basename($report->reportable_type) }}</span>
                                · Reason: <span style="color:var(--text);">{{ $report->reason }}</span>
                                · {{ $report->created_at->diffForHumans() }}
                            </p>
                            @if ($report->detail)
                                <p class="text-xs max-w-xl" style="color:var(--text-muted);">{{ $report->detail }}</p>
                            @endif
                            @if ($report->reviewer)
                                <p class="text-xs" style="color:var(--text-muted);">Reviewed by {{ $report->reviewer->gamertag }} · {{ $report->updated_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        @if ($tab === 'pending')
                            @php $reportedIsSelf = $report->reportedUser?->is(auth()->user()); @endphp
                            @if ($confirmingId === $report->id)
                                <div class="rounded-lg px-4 py-3 space-y-2 min-w-60" style="background:var(--surface-raised);">
                                    @php
                                    $strikeLevel = match($confirmingAction) { 'strike1' => 1, 'strike2' => 2, 'strike3' => 3, default => null };
                                    @endphp
                                    <p class="text-sm" style="color:#D29922;">
                                        @if ($confirmingAction === 'dismiss')
                                            Dismiss this report? This will increment the reporter's dismiss count.
                                        @else
                                            Issue Strike {{ $strikeLevel }} to <strong>{{ $report->reportedUser?->gamertag }}</strong>?
                                            @if ($strikeLevel === 3) This will suspend their account. @endif
                                        @endif
                                    </p>
                                    <div class="flex gap-2">
                                        <x-button wire:click="executeAction" variant="destructive" class="!px-3 !py-1.5 !text-xs">Confirm</x-button>
                                        <x-button wire:click="cancelConfirm" variant="secondary" class="!px-3 !py-1.5 !text-xs">Cancel</x-button>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <x-button wire:click="toggleContext('{{ $report->id }}')" variant="secondary" class="!px-3 !py-1.5 !text-xs">{{ $viewingContextId === $report->id ? 'Hide' : 'View' }} context</x-button>
                                    <x-button wire:click="confirmAction('{{ $report->id }}', 'dismiss')" variant="secondary" class="!px-3 !py-1.5 !text-xs">Dismiss</x-button>
                                    @if ($reportedIsSelf)
                                        <span class="px-3 py-1.5 text-xs rounded-lg"
                                            style="background:var(--surface);color:var(--text-muted);border:1px solid var(--border);"
                                            title="You cannot perform this action on yourself">
                                            Cannot action own account
                                        </span>
                                    @else
                                        <button wire:click="confirmAction('{{ $report->id }}', 'strike1')"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(210,153,34,0.2);color:#D29922;" onmouseover="this.style.background='rgba(210,153,34,0.35)'" onmouseout="this.style.background='rgba(210,153,34,0.2)'"
                                        >Warn · S1</button>
                                        <button wire:click="confirmAction('{{ $report->id }}', 'strike2')"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(249,115,22,0.2);color:#f97316;" onmouseover="this.style.background='rgba(249,115,22,0.35)'" onmouseout="this.style.background='rgba(249,115,22,0.2)'"
                                        >Restrict · S2</button>
                                        <button wire:click="confirmAction('{{ $report->id }}', 'strike3')"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(var(--danger-rgb),0.2);color:var(--danger);" onmouseover="this.style.background='rgba(var(--danger-rgb),0.35)'" onmouseout="this.style.background='rgba(var(--danger-rgb),0.2)'"
                                        >Suspend · S3</button>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Inline context --}}
                    @if ($viewingContextId === $report->id && $report->reportable)
                        <div class="mt-2 p-3 rounded-lg border" style="background:var(--surface);border-color:var(--border);">
                            <p class="text-xs uppercase tracking-wider mb-1" style="color:var(--text-muted);">Reported content ({{ class_basename($report->reportable_type) }})</p>
                            @if (isset($report->reportable->content))
                                <p class="text-sm" style="color:var(--text);">{{ $report->reportable->content }}</p>
                            @else
                                <p class="text-sm italic" style="color:var(--text-muted);">No displayable content available for this type.</p>
                            @endif
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">{{ $this->reports->links() }}</div>
    @endif
</div>
