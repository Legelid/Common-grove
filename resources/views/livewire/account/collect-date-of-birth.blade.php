<div class="rounded-2xl border p-8 space-y-6" style="background:#161B22;border-color:#30363D;">

    <div class="space-y-2">
        <h1 class="text-xl font-bold" style="color:#E6EDF3;">Before continuing, please add your date of birth.</h1>
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            We use your date of birth to keep CommonGrove appropriate for the community.
            Your date of birth is not shown publicly.
        </p>
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium" style="color:#C9D1D9;">Date of birth</label>

        <div class="grid grid-cols-3 gap-3">
            {{-- Month --}}
            <div>
                <label class="block text-xs mb-1" style="color:#8B949E;">Month</label>
                <select
                    wire:model="birthMonth"
                    class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none"
                    style="background:#0D1117;border:1px solid {{ $errors->has('birthMonth') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.borderColor='rgba(29,158,117,0.6)'" onblur="this.style.borderColor='{{ $errors->has('birthMonth') ? '#E24B4A' : '#30363D' }}'"
                >
                    <option value="">Month</option>
                    @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('birthMonth') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>

            {{-- Day --}}
            <div>
                <label class="block text-xs mb-1" style="color:#8B949E;">Day</label>
                <select
                    wire:model="birthDay"
                    class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none"
                    style="background:#0D1117;border:1px solid {{ $errors->has('birthDay') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.borderColor='rgba(29,158,117,0.6)'" onblur="this.style.borderColor='{{ $errors->has('birthDay') ? '#E24B4A' : '#30363D' }}'"
                >
                    <option value="">Day</option>
                    @for ($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endfor
                </select>
                @error('birthDay') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>

            {{-- Year --}}
            <div>
                <label class="block text-xs mb-1" style="color:#8B949E;">Year</label>
                <select
                    wire:model="birthYear"
                    class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none"
                    style="background:#0D1117;border:1px solid {{ $errors->has('birthYear') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.borderColor='rgba(29,158,117,0.6)'" onblur="this.style.borderColor='{{ $errors->has('birthYear') ? '#E24B4A' : '#30363D' }}'"
                >
                    <option value="">Year</option>
                    @for ($y = now()->year; $y >= now()->year - 120; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                @error('birthYear') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <button
        type="button"
        wire:click="save"
        wire:loading.attr="disabled"
        class="w-full py-2.5 rounded-xl text-sm font-semibold transition disabled:opacity-50"
        style="background:#1D9E75;color:#fff;"
        onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
    >
        <span wire:loading.remove>Continue</span>
        <span wire:loading>Saving…</span>
    </button>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="text-xs transition underline" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">
            Sign out
        </button>
    </form>

</div>
