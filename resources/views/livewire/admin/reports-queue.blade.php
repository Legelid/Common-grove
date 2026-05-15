<div class="p-8">
    <h1 class="text-2xl font-bold mb-6" style="color:#E6EDF3;">Reports Queue</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b" style="border-color:#30363D;">
        @foreach (['pending' => 'Pending', 'actioned' => 'Actioned', 'dismissed' => 'Dismissed'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg transition -mb-px border-b-2"
                style="{{ $tab === $key ? 'color:#E6EDF3;border-color:#1D9E75;' : 'color:#8B949E;border-color:transparent;' }}"
            >
                {{ $label }}
                <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full"
                    style="{{ $key === 'pending' && $this->tabCounts[$key] > 0 ? 'background:rgba(226,75,74,0.2);color:#E24B4A;' : 'background:#21262D;color:#8B949E;' }}"
                >{{ $this->tabCounts[$key] }}</span>
            </button>
        @endforeach
    </div>

    @if ($this->reports->isEmpty())
        <p class="text-sm" style="color:#8B949E;">No reports in this category.</p>
    @else
        <div class="space-y-4">
            @foreach ($this->reports as $report)
                <div wire:key="report-{{ $report->id }}" class="rounded-xl border p-5 space-y-3" style="background:#161B22;border-color:#30363D;">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-sm" style="color:#E6EDF3;">
                                <span class="font-semibold" style="color:#E24B4A;">{{ $report->reportedUser?->gamertag ?? '—' }}</span>
                                reported by
                                <span class="font-semibold" style="color:#E6EDF3;">{{ $report->reporter?->gamertag ?? '—' }}</span>
                            </p>
                            <p class="text-xs" style="color:#8B949E;">
                                Type: <span style="color:#E6EDF3;">{{ class_basename($report->reportable_type) }}</span>
                                · Reason: <span style="color:#E6EDF3;">{{ $report->reason }}</span>
                                · {{ $report->created_at->diffForHumans() }}
                            </p>
                            @if ($report->detail)
                                <p class="text-xs max-w-xl" style="color:#8B949E;">{{ $report->detail }}</p>
                            @endif
                            @if ($report->reviewer)
                                <p class="text-xs" style="color:#8B949E;">Reviewed by {{ $report->reviewer->gamertag }} · {{ $report->updated_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        @if ($tab === 'pending')
                            @php $reportedIsSelf = $report->reportedUser?->is(auth()->user()); @endphp
                            @if ($confirmingId === $report->id)
                                <div class="rounded-lg px-4 py-3 space-y-2 min-w-60" style="background:#21262D;">
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
                                        <button wire:click="executeAction"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                            style="background:#E24B4A;color:#fff;" onmouseover="this.style.background='#f05252'" onmouseout="this.style.background='#E24B4A'"
                                        >Confirm</button>
                                        <button wire:click="cancelConfirm"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                            style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                        >Cancel</button>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="toggleContext('{{ $report->id }}')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                        style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    >{{ $viewingContextId === $report->id ? 'Hide' : 'View' }} context</button>
                                    <button wire:click="confirmAction('{{ $report->id }}', 'dismiss')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition"
                                        style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    >Dismiss</button>
                                    @if ($reportedIsSelf)
                                        <span class="px-3 py-1.5 text-xs rounded-lg"
                                            style="background:#1C2333;color:#8B949E;border:1px solid #30363D;"
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
                                            style="background:rgba(226,75,74,0.2);color:#E24B4A;" onmouseover="this.style.background='rgba(226,75,74,0.35)'" onmouseout="this.style.background='rgba(226,75,74,0.2)'"
                                        >Suspend · S3</button>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Inline context --}}
                    @if ($viewingContextId === $report->id && $report->reportable)
                        <div class="mt-2 p-3 rounded-lg border" style="background:#1C2333;border-color:#30363D;">
                            <p class="text-xs uppercase tracking-wider mb-1" style="color:#8B949E;">Reported content ({{ class_basename($report->reportable_type) }})</p>
                            @if (isset($report->reportable->content))
                                <p class="text-sm" style="color:#E6EDF3;">{{ $report->reportable->content }}</p>
                            @else
                                <p class="text-sm italic" style="color:#8B949E;">No displayable content available for this type.</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $this->reports->links() }}</div>
    @endif
</div>
