<div>
    <h1 class="text-2xl font-bold text-center mb-8" style="color:var(--text);">Create your account</h1>

    <a
        href="{{ route('auth.google.redirect') }}"
        class="w-full inline-flex items-center justify-center gap-2 rounded-btn text-sm font-medium transition-all duration-150 ease-out px-4 py-2.5 hover:scale-[1.02] active:scale-100 mb-5"
        style="background:var(--surface);border:1px solid var(--border);color:var(--text);"
    ><x-google-icon /> Continue with Google</a>

    <div class="flex items-center gap-3 mb-5">
        <div class="flex-1 h-px" style="background:var(--border);"></div>
        <span class="text-xs" style="color:var(--text-faint);">or create with email</span>
        <div class="flex-1 h-px" style="background:var(--border);"></div>
    </div>

    <form wire:submit="register" class="space-y-5">

        {{-- Gamertag --}}
        <div>
            <label for="gamertag" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Gamertag</label>
            <div class="relative">
                <x-input
                    id="gamertag"
                    type="text"
                    wire:model.live.debounce.400ms="gamertag"
                    autocomplete="username"
                    maxlength="20"
                    placeholder="YourGamertag"
                    class="pr-10"
                    :error="$errors->first('gamertag')"
                />
                @if ($gamertagStatus === 'available')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold text-accent">✓</span>
                @elseif ($gamertagStatus === 'taken')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:var(--danger);">✗</span>
                @endif
            </div>
            <p class="mt-1 text-xs" style="color:var(--text-muted);">3–20 characters · starts with a letter · letters, numbers, _ and - only</p>

            @if ($gamertagStatus === 'taken')
                <div class="mt-2">
                    <p class="text-sm mb-1" style="color:var(--danger);">That gamertag is taken. Try one of these:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($suggestions as $suggestion)
                            <button
                                type="button"
                                wire:click="useSuggestion('{{ $suggestion }}')"
                                class="px-3 py-1 text-sm rounded-full transition bg-surface-raised text-accent hover:bg-accent hover:text-on-accent"
                            >{{ $suggestion }}</button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                Email address
                <span class="font-normal" style="color:var(--text-muted);opacity:0.7;">(for account recovery only — never shown)</span>
            </label>
            <x-input
                id="email"
                type="email"
                wire:model="email"
                autocomplete="email"
                :error="$errors->first('email')"
            />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="new-password"
                    class="w-full rounded-btn px-4 py-2.5 pr-10 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                    style="border-color:{{ $errors->has('password') ? 'var(--danger)' : 'var(--border)' }};"
                >
                <button type="button" @click="show = !show" tabindex="-1" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-3 flex items-center transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password')
                <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs" style="color:var(--text-muted);">Minimum 10 characters. Checked against known breaches.</p>
        </div>

        {{-- Confirm password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Confirm password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    wire:model="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-btn px-4 py-2.5 pr-10 text-sm bg-surface text-text border border-border focus:outline-none focus:ring-2 focus:ring-accent"
                >
                <button type="button" @click="show = !show" tabindex="-1" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-3 flex items-center transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
        </div>

        {{-- Date of birth --}}
        <div>
            <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Date of birth</label>
            <p class="text-xs mb-2" style="color:var(--text-faint);">
                We use your date of birth for age eligibility. You must be 18 or older to use CommonGrove.
            </p>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <select wire:model="birthMonth"
                        class="w-full rounded-btn px-3 py-2.5 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                        style="border-color:{{ $errors->has('birthMonth') ? 'var(--danger)' : 'var(--border)' }};"
                    >
                        <option value="">Month</option>
                        @foreach ([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $num => $abbr)
                            <option value="{{ $num }}">{{ $abbr }}</option>
                        @endforeach
                    </select>
                    @error('birthMonth') <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p> @enderror
                </div>
                <div>
                    <select wire:model="birthDay"
                        class="w-full rounded-btn px-3 py-2.5 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
                        style="border-color:{{ $errors->has('birthDay') ? 'var(--danger)' : 'var(--border)' }};"
                    >
                        <option value="">Day</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endfor
                    </select>
                    @error('birthDay') <p role="alert" class="mt-1.5 text-xs font-semibold flex items-center gap-1.5" style="color:var(--danger);background:rgb(var(--danger-rgb) / 0.12);border:1px solid var(--danger);border-radius:var(--radius-sm);padding:0.375rem 0.625rem;"><span aria-hidden="true">⚠</span> {{ $message }}</p> @enderror
                </div>
                <div>
                    <select wire:model="birthYear"
                        class="w-full rounded-btn px-3 py-2.5 text-sm bg-surface text-text border focus:outline-none focus:ring-2 focus:ring-accent"
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

        {{-- Submit --}}
        <x-button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove>Create account</span>
            <span wire:loading>Creating account…</span>
        </x-button>

    </form>

    <p class="mt-6 text-center text-sm" style="color:var(--text-muted);">
        Already have an account?
        <a href="{{ route('login') }}" class="underline transition text-accent" wire:navigate>Sign in</a>
    </p>
</div>
