@foreach ($subcats as $subcat)
    @if ($subcat->tags->isNotEmpty())
        <div wire:key="subcat-block-{{ $subcat->id }}" style="{{ $loop->first ? '' : 'margin-top:1rem;' }}">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-faint);margin-bottom:0.5rem;">
                {{ $subcat->name }}
            </p>

            @if ($subcat->is_sensitive)
                <p style="font-size:0.75rem;color:var(--text-faint);font-style:italic;margin-bottom:0.375rem;">
                    These interests are private by default
                </p>
            @endif

            <div>
                @foreach ($subcat->tags as $tag)
                    @include('livewire.tags.partials.tag-row', ['tag' => $tag, 'rowKey' => 'subcat-tag-' . $subcat->id . '-' . $tag->id])
                @endforeach
            </div>
        </div>
    @endif
@endforeach
