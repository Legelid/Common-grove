<x-card padding="p-8" class="space-y-6">

    <div class="space-y-2">
        <h1 class="text-xl font-bold" style="color:var(--text);">Before continuing, please add your date of birth.</h1>
        <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
            We use your date of birth to keep CommonGrove appropriate for the community.
            Your date of birth is not shown publicly.
        </p>
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium" style="color:var(--text);">Date of birth</label>

        <div class="grid grid-cols-3 gap-3">
            {{-- Month --}}
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-muted);">Month</label>
                <select
                    wire:model="birthMonth"
                    class="w-full rounded-btn px-3 py-2.5 text-sm bg-bg text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('birthMonth') ? 'var(--danger)' : 'var(--border)' }};"
                >
                    <option value="">Month</option>
                    @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('birthMonth') <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p> @enderror
            </div>

            {{-- Day --}}
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-muted);">Day</label>
                <select
                    wire:model="birthDay"
                    class="w-full rounded-btn px-3 py-2.5 text-sm bg-bg text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('birthDay') ? 'var(--danger)' : 'var(--border)' }};"
                >
                    <option value="">Day</option>
                    @for ($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endfor
                </select>
                @error('birthDay') <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p> @enderror
            </div>

            {{-- Year --}}
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-muted);">Year</label>
                <select
                    wire:model="birthYear"
                    class="w-full rounded-btn px-3 py-2.5 text-sm bg-bg text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('birthYear') ? 'var(--danger)' : 'var(--border)' }};"
                >
                    <option value="">Year</option>
                    @for ($y = now()->year; $y >= now()->year - 120; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                @error('birthYear') <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <x-button type="button" wire:click="save" wire:loading.attr="disabled" variant="primary" class="w-full">
        <span wire:loading.remove>Continue</span>
        <span wire:loading>Saving…</span>
    </x-button>

    <form method="POST" action="/logout" class="text-center">
        @csrf
        <button type="submit" class="text-xs transition underline" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">
            Sign out
        </button>
    </form>

</x-card>
