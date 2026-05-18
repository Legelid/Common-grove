<div class="px-6 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color:#E6EDF3;">Problem Reports</h1>
            <p class="text-xs mt-1" style="color:#8B949E;">
                Reports submitted via the "Report a problem" page.
                Open: {{ $this->statusCounts['open'] ?? 0 }} &middot;
                Reviewing: {{ $this->statusCounts['reviewing'] ?? 0 }} &middot;
                Resolved: {{ $this->statusCounts['resolved'] ?? 0 }} &middot;
                Dismissed: {{ $this->statusCounts['dismissed'] ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <select wire:model.live="filterType"
            class="rounded-lg px-3 py-1.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#8B949E;">
            <option value="">All types</option>
            @foreach (\App\Models\ProblemReport::TYPES as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterStatus"
            class="rounded-lg px-3 py-1.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#8B949E;">
            <option value="">All statuses</option>
            @foreach (\App\Models\ProblemReport::STATUSES as $s)
                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterPriority"
            class="rounded-lg px-3 py-1.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#8B949E;">
            <option value="">All priorities</option>
            @foreach (\App\Models\ProblemReport::PRIORITIES as $p)
                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Report list --}}
    <div class="space-y-3">
        @forelse ($this->reports as $report)

            @php
                $priorityColour = match($report->priority) {
                    'urgent' => '#E24B4A',
                    'high'   => '#D29922',
                    'low'    => '#3d4451',
                    default  => '#8B949E',
                };
                $statusColour = match($report->status) {
                    'resolved'  => '#1D9E75',
                    'dismissed' => '#3d4451',
                    'reviewing' => '#D29922',
                    default     => '#8B949E',
                };
                $isViewing = $viewingId === $report->id;
            @endphp

            <div
                class="rounded-xl border overflow-hidden"
                style="background:#161B22;border-color:#30363D;"
                wire:key="pr-{{ $report->id }}"
            >
                {{-- Summary row --}}
                <div
                    class="flex items-start gap-4 px-5 py-4 cursor-pointer"
                    wire:click="toggleView('{{ $report->id }}')"
                >
                    {{-- Priority indicator --}}
                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-none" style="background:{{ $priorityColour }};"></span>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded font-medium" style="background:#21262D;color:#8B949E;">
                                {{ $report->typeLabel() }}
                            </span>
                            <span class="text-xs font-medium" style="color:{{ $statusColour }};">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="text-xs" style="color:#3d4451;">
                                {{ $report->created_at->diffForHumans() }}
                                @if ($report->user)
                                    &middot; {{ $report->user->gamertag }}
                                @elseif ($report->contact_email)
                                    &middot; {{ $report->contact_email }}
                                @else
                                    &middot; Guest
                                @endif
                            </span>
                        </div>
                        <p class="mt-1 text-sm font-medium truncate" style="color:#C9D1D9;">{{ $report->subject }}</p>
                    </div>

                    <span class="text-xs flex-none mt-1" style="color:#3d4451;">{{ $isViewing ? '▴' : '▾' }}</span>
                </div>

                {{-- Expanded detail --}}
                @if ($isViewing)
                    <div class="border-t px-5 py-5 space-y-5" style="border-color:#21262D;">

                        {{-- Description --}}
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:#3d4451;">Description</p>
                            <p class="text-sm whitespace-pre-wrap leading-relaxed" style="color:#C9D1D9;">{{ $report->description }}</p>
                        </div>

                        {{-- Context fields --}}
                        @if ($report->page_url || $report->related_user || $report->related_room || $report->contact_email)
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                @if ($report->page_url)
                                    <div>
                                        <span style="color:#3d4451;">Page URL</span><br>
                                        <a href="{{ $report->page_url }}" target="_blank" rel="noopener" class="underline break-all" style="color:#1D9E75;">{{ $report->page_url }}</a>
                                    </div>
                                @endif
                                @if ($report->related_user)
                                    <div>
                                        <span style="color:#3d4451;">Related user</span><br>
                                        <span style="color:#C9D1D9;">{{ $report->related_user }}</span>
                                    </div>
                                @endif
                                @if ($report->related_room)
                                    <div>
                                        <span style="color:#3d4451;">Related room</span><br>
                                        <span style="color:#C9D1D9;">{{ $report->related_room }}</span>
                                    </div>
                                @endif
                                @if ($report->contact_email)
                                    <div>
                                        <span style="color:#3d4451;">Contact email</span><br>
                                        <span style="color:#C9D1D9;">{{ $report->contact_email }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Screenshot --}}
                        @if ($report->screenshot_path)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:#3d4451;">Screenshot</p>
                                <a
                                    href="{{ route('admin.problem-reports.screenshot', $report->id) }}"
                                    target="_blank"
                                    class="text-sm underline" style="color:#1D9E75;"
                                >View screenshot</a>
                            </div>
                        @endif

                        {{-- Admin controls --}}
                        <div class="flex flex-wrap gap-4 items-end pt-1">
                            <div>
                                <label class="block text-xs mb-1" style="color:#3d4451;">Status</label>
                                <select
                                    wire:change="updateStatus('{{ $report->id }}', $event.target.value)"
                                    class="rounded-lg px-3 py-1.5 text-sm focus:outline-none"
                                    style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                >
                                    @foreach (\App\Models\ProblemReport::STATUSES as $s)
                                        <option value="{{ $s }}" {{ $report->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs mb-1" style="color:#3d4451;">Priority</label>
                                <select
                                    wire:change="updatePriority('{{ $report->id }}', $event.target.value)"
                                    class="rounded-lg px-3 py-1.5 text-sm focus:outline-none"
                                    style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                >
                                    @foreach (\App\Models\ProblemReport::PRIORITIES as $p)
                                        <option value="{{ $p }}" {{ $report->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Admin note --}}
                            <div class="flex-1 min-w-48" x-data="{ note: @js($report->admin_note ?? ''), saved: false }">
                                <label class="block text-xs mb-1" style="color:#3d4451;">Internal note</label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="note"
                                        placeholder="Add a note…"
                                        maxlength="500"
                                        class="flex-1 rounded-lg px-3 py-1.5 text-sm focus:outline-none"
                                        style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                                    >
                                    <button
                                        type="button"
                                        @click="$wire.saveNote('{{ $report->id }}', note); saved = true; setTimeout(() => saved = false, 2000)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                        style="background:#21262D;color:#8B949E;"
                                        onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    >
                                        <span x-show="!saved">Save</span>
                                        <span x-show="saved" style="color:#1D9E75;">Saved</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif
            </div>

        @empty
            <p class="text-sm" style="color:#8B949E;">No reports match those filters.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $this->reports->links() }}
    </div>

</div>
