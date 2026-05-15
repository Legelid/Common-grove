<div class="px-6 py-10 max-w-2xl mx-auto space-y-10">

    <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Profile Settings</h1>

    {{-- ── SECTION 1 — Identity ─────────────────────── --}}
    <section class="space-y-5">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Identity</h2>

        <div>
            <label for="identityMode" class="block text-sm font-medium mb-1" style="color:#8B949E;">How should others see you?</label>
            <select id="identityMode" wire:model.live="identityMode"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
                <option value="1">Gamertag only</option>
                <option value="2">Gamertag + friendly name</option>
                <option value="3">Display name forward</option>
            </select>
        </div>

        <div>
            <label for="displayNameInput" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                Friendly name <span class="font-normal opacity-70">(optional)</span>
            </label>
            <input id="displayNameInput" type="text" wire:model.live="displayNameInput" maxlength="50" placeholder="First name or nickname"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('displayNameInput') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            <p class="mt-1 text-xs" style="color:#8B949E;">What should friends call you?</p>
            @error('displayNameInput') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="text-sm font-medium" style="color:#E6EDF3;">Show friendly names</p>
                <p class="text-xs" style="color:#8B949E;">Show friendly names when others have set them</p>
            </div>
            <button type="button" wire:click="$toggle('showNamesPref')"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                style="background:{{ $showNamesPref ? '#1D9E75' : '#21262D' }};"
                role="switch" aria-checked="{{ $showNamesPref ? 'true' : 'false' }}"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showNamesPref ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>

        <div class="rounded-lg px-4 py-3 border" style="background:#161B22;border-color:#30363D;">
            <p class="text-xs mb-1" style="color:#8B949E;">How others will see you:</p>
            <p class="font-semibold text-sm" style="color:#E6EDF3;">{{ $this->previewName }}</p>
        </div>

        @if ($identityMessage)
            <p class="text-sm" style="color:#1D9E75;">{{ $identityMessage }}</p>
        @endif

        <button type="button" wire:click="saveIdentity" wire:loading.attr="disabled" wire:target="saveIdentity"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="saveIdentity">Save identity</span>
            <span wire:loading wire:target="saveIdentity">Saving…</span>
        </button>
    </section>

    {{-- ── SECTION 2 — Bio ─────────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Bio</h2>

        <div>
            <label for="bio" class="block text-sm font-medium mb-1" style="color:#8B949E;">
                About you <span class="font-normal opacity-70">(optional)</span>
            </label>
            <div class="relative">
                <textarea id="bio" wire:model.live="bio" maxlength="300" rows="4"
                    placeholder="A little about yourself — games you love, hobbies, whatever feels right."
                    class="w-full rounded-lg px-4 py-3 text-sm focus:outline-none resize-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('bio') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                ></textarea>
                <span class="absolute bottom-2 right-3 text-xs" style="color:{{ strlen($bio) >= 280 ? '#E24B4A' : '#8B949E' }};">{{ strlen($bio) }}/300</span>
            </div>
            @error('bio') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>

        @if ($bioMessage) <p class="text-sm" style="color:#1D9E75;">{{ $bioMessage }}</p> @endif

        <button type="button" wire:click="saveBio" wire:loading.attr="disabled" wire:target="saveBio"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="saveBio">Save bio</span>
            <span wire:loading wire:target="saveBio">Saving…</span>
        </button>
    </section>

    {{-- ── SECTION 3 — Avatar ───────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Avatar</h2>

        <div class="flex items-center gap-4">
            <img src="{{ auth()->user()->avatar_url }}" alt="Current avatar" class="w-16 h-16 rounded-full object-cover" style="background:#1C2333;">
            @if (auth()->user()->avatar_path)
                <button type="button" wire:click="removeAvatar" wire:confirm="Remove your avatar?"
                    class="text-sm underline transition" style="color:#E24B4A;">Remove avatar</button>
            @endif
        </div>

        <div>
            <label for="avatarUpload" class="block text-sm font-medium mb-1" style="color:#8B949E;">Upload new avatar</label>
            <input id="avatarUpload" type="file" wire:model="avatarUpload" accept="image/jpeg,image/png"
                class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold"
                style="color:#8B949E;"
            >
            <p class="mt-1 text-xs" style="color:#8B949E;">PNG or JPEG · max 2 MB · EXIF metadata is stripped automatically</p>
            @error('avatarUpload') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>

        @if ($avatarMessage)
            <p class="text-sm" style="color:{{ str_starts_with($avatarMessage, 'Avatar') ? '#1D9E75' : '#E24B4A' }};">{{ $avatarMessage }}</p>
        @endif

        <button type="button" wire:click="saveAvatar" wire:loading.attr="disabled" wire:target="saveAvatar"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="saveAvatar">Upload avatar</span>
            <span wire:loading wire:target="saveAvatar">Uploading…</span>
        </button>
    </section>

    {{-- ── SECTION 4 — Gamertag ────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Gamertag</h2>

        <p class="text-xs" style="color:#8B949E;">
            You can change your gamertag once every 30 days.
            @if (auth()->user()->last_gamertag_changed_at)
                Last changed {{ auth()->user()->last_gamertag_changed_at->diffForHumans() }}.
            @endif
        </p>

        <div>
            <div class="relative">
                <input id="gamertagInput" type="text" wire:model.live.debounce.400ms="gamertagInput" maxlength="20"
                    class="w-full rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('gamertagInput') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @if ($gamertagStatus === 'available')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:#1D9E75;">✓</span>
                @elseif ($gamertagStatus === 'taken')
                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:#E24B4A;">✗</span>
                @endif
            </div>

            @if ($gamertagStatus === 'taken' && count($gamertagSuggestions) > 0)
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($gamertagSuggestions as $suggestion)
                        <button type="button" wire:click="$set('gamertagInput', '{{ $suggestion }}')"
                            class="px-3 py-1 text-sm rounded-full transition"
                            style="background:#21262D;color:#1D9E75;">{{ $suggestion }}</button>
                    @endforeach
                </div>
            @endif
            @error('gamertagInput') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>

        @if ($gamertagMessage)
            <p class="text-sm" style="color:{{ str_starts_with($gamertagMessage, 'Gamertag updated') ? '#1D9E75' : (str_starts_with($gamertagMessage, 'You can change') ? '#D29922' : '#E24B4A') }};">
                {{ $gamertagMessage }}
            </p>
        @endif

        <button type="button" wire:click="changeGamertag" wire:loading.attr="disabled" wire:target="changeGamertag"
            @disabled($gamertagStatus === 'taken')
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="changeGamertag">Save gamertag</span>
            <span wire:loading wire:target="changeGamertag">Saving…</span>
        </button>
    </section>

    {{-- ── SECTION 5 — Status ──────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Status</h2>
        <p class="text-xs" style="color:#8B949E;">Let friends know what you're up to right now. Expires automatically after 24 hours.</p>
        <livewire:profile.status-update />
    </section>

    {{-- ── SECTION 6 — Currently Into ─────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Currently Into</h2>
        <p class="text-xs" style="color:#8B949E;">Share what you're enjoying right now. Each field is optional, max 60 characters.</p>

        <div class="space-y-3">
            @foreach (['currentlyPlaying' => 'Currently playing', 'currentlyReading' => 'Currently reading', 'currentlyWatching' => 'Currently watching'] as $field => $label)
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#8B949E;">{{ $label }}</label>
                    <input type="text" wire:model="{{ $field }}" maxlength="60"
                        placeholder="{{ $field === 'currentlyPlaying' ? 'e.g. Elden Ring, Minecraft…' : ($field === 'currentlyReading' ? 'e.g. Dune, a manga series…' : 'e.g. Arcane, old Doctor Who…') }}"
                        class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                        style="background:#1C2333;border:1px solid {{ $errors->has($field) ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                        onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                    >
                    @error($field) <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        @if ($currentlyMessage) <p class="text-sm" style="color:#1D9E75;">{{ $currentlyMessage }}</p> @endif

        <button type="button" wire:click="saveCurrently" wire:loading.attr="disabled" wire:target="saveCurrently"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="saveCurrently">Save currently into</span>
            <span wire:loading wire:target="saveCurrently">Saving…</span>
        </button>
    </section>

    {{-- ── SECTION 7 — Read Receipts ───────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Read Receipts</h2>

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="text-sm font-medium" style="color:#E6EDF3;">Show read receipts</p>
                <p class="text-xs max-w-sm" style="color:#8B949E;">
                    When both you and the other person have this on, you can both see when messages are read.
                </p>
            </div>
            <button type="button" wire:click="$toggle('showReadReceipts')"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                style="background:{{ $showReadReceipts ? '#1D9E75' : '#21262D' }};"
                role="switch" aria-checked="{{ $showReadReceipts ? 'true' : 'false' }}"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showReadReceipts ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>

        @if ($readReceiptsMessage) <p class="text-sm" style="color:#1D9E75;">{{ $readReceiptsMessage }}</p> @endif

        <button type="button" wire:click="saveReadReceiptPref" wire:loading.attr="disabled" wire:target="saveReadReceiptPref"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >Save</button>
    </section>

    {{-- ── SECTION 8 — Notification Preferences ────── --}}
    @if (auth()->user()->notificationPreferences)
        <section class="space-y-4">
            <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Notifications</h2>
            <livewire:profile.notification-preferences />
        </section>
    @endif

    {{-- ── SECTION 9 — Preferences ────────────────── --}}
    <section class="space-y-5">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Preferences</h2>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#8B949E;">Discovery</p>

            <div class="flex items-start justify-between gap-4 py-2">
                <div>
                    <p class="text-sm font-medium" style="color:#E6EDF3;">Show connection suggestions</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:#8B949E;">
                        We'll suggest rooms and people based on your interests.<br>
                        You can turn this off anytime.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('showConnectionSuggestions')"
                    class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                    style="background:{{ $showConnectionSuggestions ? '#1D9E75' : '#21262D' }};border:1px solid {{ $showConnectionSuggestions ? '#1D9E75' : '#30363D' }};"
                    role="switch"
                    aria-checked="{{ $showConnectionSuggestions ? 'true' : 'false' }}"
                    aria-label="Show connection suggestions"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showConnectionSuggestions ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if ($discoveryMessage)
                <p class="mt-1 text-sm" style="color:#8B949E;">{{ $discoveryMessage }}</p>
            @endif

            <button
                type="button"
                wire:click="saveDiscoveryPreferences"
                wire:loading.attr="disabled"
                wire:target="saveDiscoveryPreferences"
                class="mt-3 px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Save</button>

            {{-- Official rooms toggle --}}
            <div class="flex items-start justify-between gap-4 py-2 mt-4 pt-4 border-t" style="border-color:#21262D;">
                <div>
                    <p class="text-sm font-medium" style="color:#E6EDF3;">Show CommonGrove starter rooms</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:#8B949E;">
                        We'll show a few always-open rooms made by CommonGrove.<br>
                        You can hide them anytime.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('showOfficialRooms')"
                    class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                    style="background:{{ $showOfficialRooms ? '#1D9E75' : '#21262D' }};border:1px solid {{ $showOfficialRooms ? '#1D9E75' : '#30363D' }};"
                    role="switch"
                    aria-checked="{{ $showOfficialRooms ? 'true' : 'false' }}"
                    aria-label="Show CommonGrove starter rooms"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showOfficialRooms ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if ($officialRoomsMessage)
                <p class="mt-1 text-sm" style="color:#8B949E;">{{ $officialRoomsMessage }}</p>
            @endif

            <button
                type="button"
                wire:click="saveOfficialRoomsPref"
                wire:loading.attr="disabled"
                wire:target="saveOfficialRoomsPref"
                class="mt-3 px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Save</button>
        </div>

        <div class="mt-5 pt-5 border-t" style="border-color:#30363D;">
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#8B949E;">Comfort</p>

            <div class="flex items-start justify-between gap-4 py-2">
                <div>
                    <p class="text-sm font-medium" style="color:#E6EDF3;">Low-stimulation mode</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:#8B949E;">
                        Hides suggestion previews and subtle visual effects.<br>
                        Keeps all core features — rooms, messages, safety tools.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('lowStimulationMode')"
                    class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                    style="background:{{ $lowStimulationMode ? '#1D9E75' : '#21262D' }};border:1px solid {{ $lowStimulationMode ? '#1D9E75' : '#30363D' }};"
                    role="switch"
                    aria-checked="{{ $lowStimulationMode ? 'true' : 'false' }}"
                    aria-label="Low-stimulation mode"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $lowStimulationMode ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if ($comfortMessage)
                <p class="mt-1 text-sm" style="color:#8B949E;">{{ $comfortMessage }}</p>
            @endif

            <button
                type="button"
                wire:click="saveComfortPreferences"
                wire:loading.attr="disabled"
                wire:target="saveComfortPreferences"
                class="mt-3 px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Save</button>
        </div>
    </section>

</div>
