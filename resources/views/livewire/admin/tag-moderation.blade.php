<div class="p-8">
    <h1 class="text-2xl font-bold mb-6" style="color:#E6EDF3;">Tag Moderation</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b" style="border-color:#30363D;">
        @foreach (['pending' => 'Pending Submissions', 'approved' => 'Approved Tags'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg transition -mb-px border-b-2"
                style="{{ $tab === $key ? 'color:#E6EDF3;border-color:#1D9E75;' : 'color:#8B949E;border-color:transparent;' }}"
            >
                {{ $label }}
                <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full"
                    style="{{ $key === 'pending' && $this->tabCounts[$key] > 0 ? 'background:rgba(210,153,34,0.2);color:#D29922;' : 'background:#21262D;color:#8B949E;' }}"
                >{{ $this->tabCounts[$key] }}</span>
            </button>
        @endforeach
    </div>

    @if ($this->tags->isEmpty())
        <p class="text-sm" style="color:#8B949E;">
            {{ $tab === 'pending' ? 'No tags awaiting moderation.' : 'No approved tags found.' }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wider border-b" style="color:#8B949E;border-color:#30363D;">
                    <tr>
                        <th class="pb-3 pr-6">Tag Name</th>
                        <th class="pb-3 pr-6">Category</th>
                        <th class="pb-3 pr-6">Usage</th>
                        <th class="pb-3 pr-6">Added</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:#30363D;">
                    @foreach ($this->tags as $tag)
                        <tr wire:key="tag-{{ $tag->id }}" style="border-color:#30363D;">
                            <td class="py-3 pr-6">
                                <span class="font-medium" style="color:#E6EDF3;">{{ $tag->name }}</span>
                                @if ($tag->is_curated)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(29,158,117,0.15);color:#1D9E75;">Curated</span>
                                @elseif ($tag->source === 'custom')
                                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(210,153,34,0.15);color:#D29922;">Custom</span>
                                @endif
                                @if ($tab === 'pending' && $tag->createdBy)
                                    <div class="mt-0.5 text-xs" style="color:#6B737C;">by {{ $tag->createdBy->gamertag }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-6 text-xs" style="color:#8B949E;">{{ $tag->category ?? '—' }}</td>
                            <td class="py-3 pr-6 text-xs" style="color:#8B949E;">{{ number_format($tag->usage_count ?? 0) }}</td>
                            <td class="py-3 pr-6 text-xs" style="color:#8B949E;">
                                {{ $tag->created_at->format('Y-m-d') }}
                                @if ($tab === 'approved' && $tag->approved_at)
                                    <div style="color:#6B737C;">approved {{ $tag->approved_at->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($tab === 'pending')
                                        <button
                                            wire:click="approve('{{ $tag->id }}')"
                                            wire:confirm="Approve the tag '{{ $tag->name }}'?"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(29,158,117,0.15);color:#1D9E75;"
                                            onmouseover="this.style.background='rgba(29,158,117,0.3)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                                        >Approve</button>
                                        <button
                                            wire:click="reject('{{ $tag->id }}')"
                                            wire:confirm="Reject and delete '{{ $tag->name }}'? This will detach it from all users."
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(226,75,74,0.15);color:#E24B4A;"
                                            onmouseover="this.style.background='rgba(226,75,74,0.3)'" onmouseout="this.style.background='rgba(226,75,74,0.15)'"
                                        >Reject</button>
                                    @else
                                        <button
                                            wire:click="deprecate('{{ $tag->id }}')"
                                            wire:confirm="Deprecate '{{ $tag->name }}'? It will be hidden from new selections but remain on existing profiles."
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(210,153,34,0.15);color:#D29922;"
                                            onmouseover="this.style.background='rgba(210,153,34,0.3)'" onmouseout="this.style.background='rgba(210,153,34,0.15)'"
                                        >Deprecate</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $this->tags->links() }}</div>
    @endif
</div>
