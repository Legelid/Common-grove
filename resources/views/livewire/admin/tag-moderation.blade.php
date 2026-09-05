<div class="p-8">
    <h1 class="text-2xl font-bold mb-6 font-display" style="color:var(--text);">Tag Moderation</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b" style="border-color:var(--border);">
        @foreach (['pending' => 'Pending Submissions', 'approved' => 'Approved Tags'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg transition -mb-px border-b-2"
                style="{{ $tab === $key ? 'color:var(--text);border-color:var(--accent);' : 'color:var(--text-muted);border-color:transparent;' }}"
            >
                {{ $label }}
                <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full"
                    style="{{ $key === 'pending' && $this->tabCounts[$key] > 0 ? 'background:rgba(210,153,34,0.2);color:#D29922;' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                >{{ $this->tabCounts[$key] }}</span>
            </button>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <label class="flex items-center gap-2 text-sm" style="color:var(--text-muted);">
            Category
            <select wire:model.live="filterCategoryId" class="text-sm rounded-lg border" style="background:var(--surface);border-color:var(--border);color:var(--text);padding:0.375rem 0.5rem;">
                <option value="">All categories</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:var(--text-muted);">
            <input type="checkbox" wire:model.live="filterSensitiveOnly">
            Sensitive subcategories only
        </label>
    </div>

    @if ($tab === 'pending' && $this->tags->isNotEmpty())
        <div class="flex flex-wrap items-center gap-3 mb-4 text-sm">
            <button type="button" wire:click="selectAllVisible" class="underline" style="color:var(--text-muted);">Select all on page</button>
            @if (! empty($selectedForBulk))
                <button type="button" wire:click="clearBulkSelection" class="underline" style="color:var(--text-muted);">Clear selection</button>
                <button
                    type="button"
                    wire:click="bulkApprove"
                    wire:confirm="Approve {{ count($selectedForBulk) }} selected {{ Str::plural('tag', count($selectedForBulk)) }}?"
                    class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                    style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);"
                    onmouseover="this.style.background='rgb(var(--accent-rgb) / 0.3)'" onmouseout="this.style.background='rgb(var(--accent-rgb) / 0.15)'"
                >Approve {{ count($selectedForBulk) }} selected</button>
            @endif
        </div>
    @endif

    @if ($this->tags->isEmpty())
        <p class="text-sm" style="color:var(--text-muted);">
            {{ $tab === 'pending' ? 'No tags awaiting moderation.' : 'No approved tags found.' }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wider border-b" style="color:var(--text-muted);border-color:var(--border);">
                    <tr>
                        @if ($tab === 'pending')
                            <th class="pb-3 pr-3"></th>
                        @endif
                        <th class="pb-3 pr-6">Tag Name</th>
                        <th class="pb-3 pr-6">Category</th>
                        <th class="pb-3 pr-6">Usage</th>
                        <th class="pb-3 pr-6">Added</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:var(--border);">
                    @foreach ($this->tags as $tag)
                        <tr wire:key="tag-{{ $tag->id }}" style="border-color:var(--border);">
                            @if ($tab === 'pending')
                                <td class="py-3 pr-3">
                                    <input type="checkbox" wire:model.live="selectedForBulk" value="{{ $tag->id }}">
                                </td>
                            @endif
                            <td class="py-3 pr-6">
                                <span class="font-medium" style="color:var(--text);">{{ $tag->name }}</span>
                                @if ($tag->is_curated)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);">Curated</span>
                                @elseif ($tag->source === 'custom')
                                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(210,153,34,0.15);color:#D29922;">Custom</span>
                                @endif
                                @if ($tag->subcategory?->is_sensitive)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(var(--danger-rgb),0.15);color:var(--danger);">Sensitive</span>
                                @endif
                                @if ($tab === 'pending' && $tag->createdBy)
                                    <div class="mt-0.5 text-xs" style="color:var(--text-faint);">by {{ $tag->createdBy->gamertag }}</div>
                                @endif
                            </td>
                            @php $tagCategory = $tag->getRelation('category'); @endphp
                            <td class="py-3 pr-6 text-xs" style="color:var(--text-muted);">
                                @if ($tagCategory)
                                    {{ $tagCategory->name }}
                                    @if ($tag->subcategory)
                                        <span style="color:var(--text-faint);">&gt;</span> {{ $tag->subcategory->name }}
                                    @endif
                                    <span style="color:var(--text-faint);">&gt;</span> {{ $tag->name }}
                                @else
                                    {{ $tag->category ?? '—' }}
                                @endif
                            </td>
                            <td class="py-3 pr-6 text-xs" style="color:var(--text-muted);">{{ number_format($tag->usage_count ?? 0) }}</td>
                            <td class="py-3 pr-6 text-xs" style="color:var(--text-muted);">
                                {{ $tag->created_at->format('Y-m-d') }}
                                @if ($tab === 'approved' && $tag->approved_at)
                                    <div style="color:var(--text-faint);">approved {{ $tag->approved_at->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($tab === 'pending')
                                        <button
                                            wire:click="approve('{{ $tag->id }}')"
                                            wire:confirm="Approve the tag '{{ $tag->name }}'?"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);"
                                            onmouseover="this.style.background='rgb(var(--accent-rgb) / 0.3)'" onmouseout="this.style.background='rgb(var(--accent-rgb) / 0.15)'"
                                        >Approve</button>
                                        <button
                                            wire:click="reject('{{ $tag->id }}')"
                                            wire:confirm="Reject and delete '{{ $tag->name }}'? This will detach it from all users."
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(var(--danger-rgb),0.15);color:var(--danger);"
                                            onmouseover="this.style.background='rgba(var(--danger-rgb),0.3)'" onmouseout="this.style.background='rgba(var(--danger-rgb),0.15)'"
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
