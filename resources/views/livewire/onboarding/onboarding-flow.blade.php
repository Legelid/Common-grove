<div class="space-y-6">

    {{-- Progress indicator --}}
    <div class="flex items-center gap-1.5 justify-center mb-8">
        @for ($i = 1; $i <= 6; $i++)
            <div
                class="h-1 rounded-full transition-all duration-300"
                style="width:{{ $step >= $i ? '2rem' : '0.75rem' }};{{ $step >= $i ? 'background:#1D9E75;' : 'background:#21262D;' }}"
            ></div>
        @endfor
    </div>

    {{-- ── STEP 1: Welcome ───────────────────────────────────────────────── --}}
    @if ($step === 1)
        <div class="rounded-2xl border p-8 space-y-6 text-center" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-3">
                <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Welcome to CommonGround</h1>
                <p class="text-base leading-relaxed" style="color:#8B949E;">
                    This is a place to find real connection — at your own pace.
                </p>
                <p class="text-sm" style="color:#8B949E;">
                    You don't have to say anything right away.
                </p>
            </div>

            <button
                type="button"
                wire:click="next"
                class="w-full py-3 rounded-xl font-semibold text-sm transition-colors"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >
                Let's get you set up
            </button>
        </div>
    @endif

    {{-- ── STEP 2: Identity ──────────────────────────────────────────────── --}}
    @if ($step === 2)
        <div class="rounded-2xl border p-8 space-y-6" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">How do you want to show up here?</h1>
                <p class="text-sm leading-relaxed" style="color:#8B949E;">
                    You don't have to use your real name.
                </p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium" style="color:#C9D1D9;" for="display-name">
                    Nickname or display name <span class="font-normal" style="color:#8B949E;">(optional)</span>
                </label>
                <input
                    id="display-name"
                    type="text"
                    wire:model="displayName"
                    placeholder="e.g. Nox, Pixel, just your gamertag…"
                    maxlength="50"
                    autocomplete="off"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#0D1117;border:1px solid #30363D;color:#E6EDF3;"
                    onfocus="this.style.borderColor='rgba(29,158,117,0.6)'" onblur="this.style.borderColor='#30363D'"
                >
                <p class="text-xs" style="color:#8B949E;">You can change this later in your profile settings.</p>
            </div>

            @error('displayName')
                <p class="text-sm" style="color:#E24B4A;">{{ $message }}</p>
            @enderror

            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="back" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Back</button>
                <button type="button" wire:click="next" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">Continue</button>
            </div>
        </div>
    @endif

    {{-- ── STEP 3: Interests ─────────────────────────────────────────────── --}}
    @if ($step === 3)
        <div class="rounded-2xl border p-8 space-y-5" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">What kinds of things feel like you?</h1>
                <p class="text-sm leading-relaxed" style="color:#8B949E;">
                    Pick whatever feels right — this helps us find better spaces for you.
                    <br>You don't need to explain yourself.
                </p>
            </div>

            {{-- Counter --}}
            <div class="flex items-center gap-2">
                <span class="text-xs" style="color:#8B949E;">{{ count($selectedInterestIds) }} selected</span>
                @if (count($selectedInterestIds) < 3 && count($selectedInterestIds) > 0)
                    <span class="text-xs" style="color:#D29922;">— a few more helps us find better rooms</span>
                @elseif (count($selectedInterestIds) === 0)
                    <span class="text-xs" style="color:#8B949E;">— a few interests help us find better rooms for you</span>
                @endif
            </div>

            {{-- Tags by category --}}
            <div class="space-y-5 max-h-80 overflow-y-auto pr-1" style="scrollbar-width:thin;scrollbar-color:#30363D transparent;">
                @foreach ($this->interestTagsByCategory as $category => $tags)
                    <div wire:key="cat-{{ Str::slug($category) }}">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">{{ $category }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <x-tag-bubble
                                    :label="$tag->name"
                                    :selected="in_array($tag->id, $selectedInterestIds)"
                                    wire:click="toggleInterest('{{ $tag->id }}')"
                                    wire:key="int-{{ $tag->id }}"
                                />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="back" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Back</button>
                <button type="button" wire:click="next" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">Continue</button>
            </div>
        </div>
    @endif

    {{-- ── STEP 4: Shared Experiences ────────────────────────────────────── --}}
    @if ($step === 4)
        <div class="rounded-2xl border p-8 space-y-5" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">Anything you'd like us to consider?</h1>
                <p class="text-sm leading-relaxed" style="color:#8B949E;">
                    This is completely optional.
                </p>
                <p class="text-sm leading-relaxed" style="color:#8B949E;">
                    Some people like finding others with similar experiences. Others prefer not to share — both are okay.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach ($this->experienceTags as $tag)
                    <x-tag-bubble
                        :label="$tag->name"
                        :selected="in_array($tag->id, $selectedExperienceIds)"
                        wire:click="toggleExperience('{{ $tag->id }}')"
                        wire:key="exp-{{ $tag->id }}"
                    />
                @endforeach
            </div>

            <p class="text-xs" style="color:#8B949E;">
                These are used for room matching only. They won't appear publicly on your profile unless you choose to share them later.
            </p>

            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="back" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Back</button>
                <button type="button" wire:click="next" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">Continue</button>
                <button type="button" wire:click="skipToStep(5)" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:transparent;color:#8B949E;border:1px solid #30363D;" onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'">Skip this</button>
            </div>
        </div>
    @endif

    {{-- ── STEP 5: Comfort Level ─────────────────────────────────────────── --}}
    @if ($step === 5)
        <div class="rounded-2xl border p-8 space-y-5" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">What feels comfortable right now?</h1>
                <p class="text-sm leading-relaxed" style="color:#8B949E;">
                    This just helps us suggest spaces that match your pace.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach (\App\Livewire\Onboarding\OnboardingFlow::COMFORT_OPTIONS as $key => $label)
                    <x-tag-bubble
                        :label="$label"
                        :selected="in_array($key, $selectedComfortOptions)"
                        wire:click="toggleComfort('{{ $key }}')"
                        wire:key="comfort-{{ $key }}"
                    />
                @endforeach
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="back" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Back</button>
                <button type="button" wire:click="next" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition" style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'">Continue</button>
                <button type="button" wire:click="skipToStep(6)" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:transparent;color:#8B949E;border:1px solid #30363D;" onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'">Skip this</button>
            </div>
        </div>
    @endif

    {{-- ── STEP 6: Culture / Rules ───────────────────────────────────────── --}}
    @if ($step === 6)
        <div class="rounded-2xl border p-8 space-y-5" style="background:#161B22;border-color:#30363D;">
            <div class="space-y-2">
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">Before you jump in…</h1>
            </div>

            <div class="space-y-3 text-sm leading-relaxed" style="color:#8B949E;">
                <p>CommonGround is a place for real connection — not followers or attention.</p>
                <p>It's not a dating app, and it's not built for self-promotion.</p>
                <p style="color:#C9D1D9;">
                    Do not attack people over identity, beliefs, background, politics, religion, gender, sexuality, disability, neurodivergence, or personal circumstances.
                </p>
                <p>You can be yourself here. You cannot use who someone is as a weapon.</p>
                <p>Be kind. Be respectful. Let people exist at their own pace.</p>
                <hr style="border-color:#30363D;">
                <p class="text-xs">
                    This is a beta, and it's being built by a small team (mostly one person). Things won't be perfect yet.
                    If something breaks or doesn't feel right, feel free to reach out — I'm actively working on improving it.
                </p>
                <p class="text-xs">You don't have to say anything right away.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" wire:click="back" class="flex-none px-5 py-2.5 rounded-xl text-sm font-medium transition" style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">Back</button>
                <button
                    type="button"
                    wire:click="complete"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition disabled:opacity-50"
                    style="background:#1D9E75;color:#fff;"
                    onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                >
                    <span wire:loading.remove>I understand — take me in</span>
                    <span wire:loading>Setting things up…</span>
                </button>
            </div>
        </div>
    @endif

</div>
