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
            <div class="w-full max-w-md rounded-2xl border shadow-2xl" style="background:#161B22;border-color:#30363D;">

                <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:#30363D;">
                    <h2 id="report-modal-title" class="font-semibold text-base" style="color:#E6EDF3;">Report a User</h2>
                    <button wire:click="closeModal" class="text-xl leading-none transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">&times;</button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    @if ($successMessage)
                        <p class="text-sm" style="color:#1D9E75;">{{ $successMessage }}</p>
                        <div class="flex justify-end">
                            <button wire:click="closeModal"
                                class="px-4 py-2 text-sm font-semibold rounded-xl transition"
                                style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                            >Done</button>
                        </div>
                    @else

                        @if ($errorMessage)
                            <p class="text-sm" style="color:#E24B4A;">{{ $errorMessage }}</p>
                        @endif

                        <div>
                            <label for="report-reason" class="block text-xs mb-1.5" style="color:#8B949E;">Reason</label>
                            <select id="report-reason" wire:model="reason"
                                class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                            >
                                <option value="">— Select a reason —</option>
                                @foreach ($reasonLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('reason') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="report-detail" class="block text-xs mb-1.5" style="color:#8B949E;">
                                Details <span style="opacity:0.6;">(20–500 characters)</span>
                            </label>
                            <textarea id="report-detail" wire:model="detail" rows="4" maxlength="500" placeholder="Describe what happened…"
                                class="w-full rounded-xl px-3 py-2.5 text-sm resize-none focus:outline-none"
                                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                            ></textarea>
                            <p class="text-right text-xs mt-0.5" style="color:#8B949E;">{{ strlen($detail) }}/500</p>
                            @error('detail') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button wire:click="closeModal"
                                class="px-4 py-2 rounded-xl text-sm transition"
                                style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                            >Cancel</button>
                            <button wire:click="submit" wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-semibold rounded-xl transition disabled:opacity-50"
                                style="background:#E24B4A;color:#fff;" onmouseover="this.style.background='#f05252'" onmouseout="this.style.background='#E24B4A'"
                            >Submit Report</button>
                        </div>

                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
