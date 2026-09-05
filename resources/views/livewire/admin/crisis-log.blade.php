<div class="p-8">
    <h1 class="text-2xl font-bold mb-4 font-display" style="color:var(--text);">Crisis Log</h1>

    {{-- Welfare notice --}}
    <div class="mb-6 rounded-xl border px-5 py-4" style="background:rgba(210,153,34,0.06);border-color:rgba(210,153,34,0.35);">
        <p class="text-sm font-semibold mb-1" style="color:#D29922;">Welfare monitoring only</p>
        <p class="text-sm" style="color:rgba(210,153,34,0.8);">
            This log shows crisis keyword detections only. Message content is never stored or displayed here.
            Use this to identify users who may need additional support — reach out via their profile if concerned.
        </p>
    </div>

    @if ($this->detections->isEmpty())
        <p class="text-sm" style="color:var(--text-muted);">No crisis keyword detections on record.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wider border-b" style="color:var(--text-muted);border-color:var(--border);">
                    <tr>
                        <th class="pb-3 pr-6">Detected at</th>
                        <th class="pb-3 pr-6">User</th>
                        <th class="pb-3 pr-6">Keyword triggered</th>
                        <th class="pb-3">Banner dismissed?</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:var(--border);">
                    @foreach ($this->detections as $detection)
                        <tr wire:key="detection-{{ $detection->id }}" style="border-color:var(--border);">
                            <td class="py-3 pr-6 text-xs whitespace-nowrap" style="color:var(--text-muted);">
                                {{ $detection->detected_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-3 pr-6">
                                @if ($detection->user)
                                    <a href="{{ route('profile.show', $detection->user->gamertag) }}" target="_blank"
                                        class="text-sm font-medium transition" style="color:var(--accent);"
                                        onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'"
                                    >{{ $detection->user->gamertag }}</a>
                                @else
                                    <span class="text-sm" style="color:var(--border);">Deleted user</span>
                                @endif
                            </td>
                            <td class="py-3 pr-6 text-xs font-mono" style="color:var(--text);">
                                {{ $detection->triggered_keyword }}
                            </td>
                            <td class="py-3">
                                @if ($detection->was_dismissed)
                                    <span class="px-2 py-0.5 text-xs rounded-full" style="background:var(--surface-raised);color:var(--text-muted);">Dismissed</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(210,153,34,0.15);color:#D29922;">Not dismissed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $this->detections->links() }}</div>
    @endif
</div>
