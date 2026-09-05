<div>
    @if ($open)
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.7);"
            wire:click="closeModal"
            x-data
            x-on:keydown.escape.window="$wire.closeModal()"
        ></div>

        {{-- Modal --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="report-modal-title">
            <div class="w-full max-w-md rounded-2xl border shadow-2xl" style="background:var(--surface);border-color:var(--border);">

                <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--border);">
                    <h2 id="report-modal-title" class="font-semibold text-base" style="color:var(--text);">Report a User</h2>
                    <button wire:click="closeModal" class="text-xl leading-none transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">&times;</button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    @if ($successMessage)
                        <p class="text-sm" style="color:var(--accent);">{{ $successMessage }}</p>
                        <div class="flex justify-end">
                            <x-button wire:click="closeModal" variant="primary">Done</x-button>
                        </div>
                    @else

                        @if ($errorMessage)
                            <p class="text-sm" style="color:var(--danger);">{{ $errorMessage }}</p>
                        @endif

                        <div>
                            <label for="report-reason" class="block text-xs mb-1.5" style="color:var(--text-muted);">Reason</label>
                            <select id="report-reason" wire:model="reason"
                                class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                                style="background:var(--surface);border:1px solid var(--border);color:var(--text);"
                                onfocus="this.style.boxShadow='0 0 0 2px var(--accent)'" onblur="this.style.boxShadow=''"
                            >
                                <option value="">Select a reason</option>
                                @foreach ($reasonLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('reason') <p class="mt-1 text-xs" style="color:var(--danger);">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="report-detail" class="block text-xs mb-1.5" style="color:var(--text-muted);">
                                Details <span style="opacity:0.6;">(20–500 characters)</span>
                            </label>
                            <x-textarea id="report-detail" wire:model="detail" rows="4" maxlength="500" placeholder="Describe what happened…" :error="$errors->first('detail')" />
                            <p class="text-right text-xs mt-0.5" style="color:var(--text-muted);">{{ strlen($detail) }}/500</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button wire:click="closeModal"
                                class="px-4 py-2 rounded-xl text-sm transition"
                                style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                            >Cancel</button>
                            <x-button wire:click="submit" wire:loading.attr="disabled" variant="destructive">Submit Report</x-button>
                        </div>

                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
