@if ($viewingSuggested)
    <p class="font-display" style="font-size:1.125rem;color:var(--text);margin-bottom:0.75rem;">Suggested for you</p>
    <div>
        @foreach ($this->suggestedTags as $tag)
            @include('livewire.tags.partials.tag-row', ['tag' => $tag, 'rowKey' => 'suggested-tag-' . $tag->id])
        @endforeach
    </div>
@elseif ($activeCategoryId !== null)
    @php $activeCat = $this->categories->firstWhere('id', $activeCategoryId); @endphp
    <p class="font-display" style="font-size:1.125rem;color:var(--text);margin-bottom:0.75rem;">{{ $activeCat->name ?? '' }}</p>

    {{-- Part E: broad-follow placeholder — UI only, no backend logic yet --}}
    <div
        x-data="{ tip: false }"
        @click="tip = ! tip"
        @click.outside="tip = false"
        class="relative"
        style="background:var(--surface-raised);border:1px dashed var(--border);border-radius:var(--radius-md);padding:0.5rem 0.625rem;margin-bottom:0.75rem;cursor:pointer;"
    >
        <p style="font-family:var(--font-body);font-size:0.8125rem;color:var(--text-muted);margin:0;">Follow {{ $activeCat->name ?? '' }} broadly</p>
        <p style="font-family:var(--font-body);font-size:0.8125rem;color:var(--text-muted);margin:0.125rem 0 0;">Get matched with any room or person in this category</p>
        <div
            x-show="tip"
            x-cloak
            style="display:none;position:absolute;top:calc(100% + 6px);left:0.625rem;background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-md);padding:0.5rem 0.75rem;font-size:0.75rem;color:var(--text);box-shadow:var(--shadow-md);z-index:10;white-space:nowrap;"
        >Coming soon — broad preferences are on the way.</div>
    </div>

    @if ($this->activeCategoryData->isEmpty() || $this->activeCategoryData->every(fn ($s) => $s->tags->isEmpty()))
        <p class="text-sm" style="color:var(--text-muted);">No interests found in this category.</p>
    @else
        @include('livewire.tags.partials.subcategory-tags', ['subcats' => $this->activeCategoryData])
    @endif
@else
    <div class="flex items-center justify-center" style="min-height:200px;">
        <p style="color:var(--text-faint);font-style:italic;font-family:var(--font-body);">Select a category to browse interests</p>
    </div>
@endif
