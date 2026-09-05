{{-- Desktop: two-pane browser --}}
<div class="hidden md:flex" style="align-items:stretch;">
    {{-- Left pane: category navigation --}}
    @php
        $mainCategories = $this->categories->reject(fn ($c) => $c->name === 'Conversation Preferences');
        $prefsCategory  = $this->categories->firstWhere('name', 'Conversation Preferences');
    @endphp
    <div style="width:200px;flex-shrink:0;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg) 0 0 var(--radius-lg);overflow-y:auto;padding:0.5rem;max-height:480px;display:flex;flex-direction:column;">
        <div style="flex:1;">
            @if ($this->suggestedTags->isNotEmpty())
                <button
                    type="button"
                    wire:click="selectSuggested"
                    wire:key="nav-suggested"
                    class="transition"
                    style="display:flex;align-items:center;justify-content:space-between;padding:0.625rem 0.875rem;border-radius:var(--radius-md);font-size:0.9375rem;font-family:var(--font-body);width:100%;text-align:left;cursor:pointer;border:none;margin-bottom:0.25rem;background:{{ $viewingSuggested ? 'var(--accent)' : 'transparent' }};color:{{ $viewingSuggested ? 'var(--bg)' : 'var(--text-muted)' }};font-weight:{{ $viewingSuggested ? '600' : '400' }};"
                    @if (! $viewingSuggested)
                        onmouseover="this.style.background='var(--surface-raised)';this.style.color='var(--text)'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'"
                    @endif
                >Suggested</button>
            @endif

            @foreach ($mainCategories as $cat)
                @include('livewire.tags.partials.category-nav-item', ['cat' => $cat, 'rowKey' => 'nav-cat-' . $cat->id])
            @endforeach
        </div>

        @if ($prefsCategory)
            <div style="border-top:1px solid var(--border);margin-top:0.5rem;padding-top:0.5rem;">
                @include('livewire.tags.partials.category-nav-item', ['cat' => $prefsCategory, 'rowKey' => 'nav-cat-' . $prefsCategory->id])
            </div>
        @endif
    </div>

    {{-- Right pane: interests for selected category --}}
    <div style="flex:1;background:var(--surface);border:1px solid var(--border);border-left:none;border-radius:0 var(--radius-lg) var(--radius-lg) 0;overflow-y:auto;padding:1rem 1.25rem;max-height:480px;">
        @include('livewire.tags.partials.browse-panel')
    </div>
</div>

{{-- Mobile: horizontal category pill row + single-column list --}}
<div class="md:hidden">
    <div style="display:flex;overflow-x:auto;gap:0.5rem;padding-bottom:0.5rem;scrollbar-width:none;-webkit-overflow-scrolling:touch;" class="cg-hide-scrollbar">
        @if ($this->suggestedTags->isNotEmpty())
            <button
                type="button"
                wire:click="selectSuggested"
                wire:key="mnav-suggested"
                style="flex:none;background:{{ $viewingSuggested ? 'var(--accent)' : 'var(--surface)' }};border:1px solid {{ $viewingSuggested ? 'transparent' : 'var(--border)' }};border-radius:var(--radius-pill);padding:0.5rem 1rem;font-size:0.875rem;font-family:var(--font-body);color:{{ $viewingSuggested ? 'var(--bg)' : 'var(--text-muted)' }};font-weight:{{ $viewingSuggested ? '600' : '400' }};cursor:pointer;white-space:nowrap;"
            >Suggested</button>
        @endif
        @foreach ($this->categories as $cat)
            @php $catActive = ! $viewingSuggested && $activeCategoryId === $cat->id; @endphp
            <button
                type="button"
                wire:click="setCategory({{ $cat->id }})"
                wire:key="mnav-cat-{{ $cat->id }}"
                style="flex:none;background:{{ $catActive ? 'var(--accent)' : 'var(--surface)' }};border:1px solid {{ $catActive ? 'transparent' : 'var(--border)' }};border-radius:var(--radius-pill);padding:0.5rem 1rem;font-size:0.875rem;font-family:var(--font-body);color:{{ $catActive ? 'var(--bg)' : 'var(--text-muted)' }};font-weight:{{ $catActive ? '600' : '400' }};cursor:pointer;white-space:nowrap;"
            >{{ $cat->name }}</button>
        @endforeach
    </div>

    <div style="margin-top:0.75rem;">
        @include('livewire.tags.partials.browse-panel')
    </div>
</div>
