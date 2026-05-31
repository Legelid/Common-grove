<div
    class="px-6 py-10 max-w-2xl mx-auto space-y-10"
    x-data="{ dirty: false, saved: false }"
    @input.capture="dirty = true"
    @change.capture="dirty = true"
    @click.capture="
        const btn = $event.target.closest('button[wire\\:click]');
        if (btn && !btn.closest('[data-no-dirty]')) dirty = true;
    "
    @settings-saved.window="dirty = false; saved = true; setTimeout(() => saved = false, 3000)"
>

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

    </section>

    {{-- ── SECTION 3 — Avatar ───────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Avatar</h2>
        <div class="flex items-center gap-4">
            <x-avatar :user="auth()->user()" size="lg" />
            <a href="{{ route('profile.edit') }}#avatar" wire:navigate
               class="text-sm transition"
               style="color:#8B949E;"
               onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
            >Manage photo</a>
        </div>
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
            data-no-dirty
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

    {{-- ── SECTION 6 — Comfort lately ────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Comfort lately</h2>
        <p class="text-xs" style="color:#8B949E;">Small things you've been spending time with lately. Each field is optional, max 60 characters.</p>

        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#8B949E;">Something you keep returning to</label>
                <input type="text" wire:model="currentlyPlaying" maxlength="60"
                    placeholder="e.g. a game, book, show, hobby…"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('currentlyPlaying') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @error('currentlyPlaying') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#8B949E;">Something comforting lately</label>
                <input type="text" wire:model="currentlyReading" maxlength="60"
                    placeholder="e.g. rainy walks, coffee, Minecraft…"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('currentlyReading') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @error('currentlyReading') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#8B949E;">Something living in your head</label>
                <input type="text" wire:model="currentlyWatching" maxlength="60"
                    placeholder="e.g. a song, story, idea, character…"
                    class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid {{ $errors->has('currentlyWatching') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
                @error('currentlyWatching') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    {{-- ── SECTION 7 — Your Space ────────────────── --}}
    <section class="space-y-6">
        @php
            $statusSuggestions = [
                'Taking things slow lately', 'Mostly here to listen', 'Usually around late at night',
                'Trying to talk more', 'Just looking for good company', 'Quiet rooms > busy rooms',
                'One conversation at a time', 'Existing counts too', 'Still figuring things out',
                'Books, tea, and quiet spaces', 'More comfortable in smaller groups',
                'Low-pressure conversations preferred', 'Probably overthinking something',
                'Happy to just sit quietly', 'I reply slowly sometimes', 'Cozy games and calm chats',
                'Here for real people', 'Deep talks > small talk', 'Usually somewhere in the background',
                'Taking a break from noisy apps', 'Just trying to feel less alone',
                'More comfortable typing than talking', 'Looking for a calm corner online',
                'Soft conversations appreciated', 'Often awake when I shouldn\'t be',
                'Here for shared interests and good vibes', 'Socially anxious but trying',
                'I like people who ramble', 'Existing quietly online',
                'Better in one-on-one conversations', 'Late-night overthinker', 'Introvert energy',
                'Usually multitasking badly', 'Here for slower conversations',
                'I like people who feel familiar', 'Sometimes I disappear for a bit',
                'More listener than speaker', 'Looking for genuine connection',
                'Small groups feel safer', 'Just trying to make friends', 'Quiet but friendly',
                'Probably listening to music right now', 'Low energy but trying my best',
                'Still learning how to socialize again', 'Kindness matters here',
                'Usually in cozy rooms', 'Here for thoughtful conversations',
                'Sometimes I just need company', 'No pressure conversations only',
                'Trying to find my grove',
            ];
            $bannerOptions = [
                'night_rain'    => ['label' => 'Night Rain',    'g' => 'linear-gradient(160deg, #163055 0%, #0d1f3a 60%, #0a1828 100%)'],
                'forest'        => ['label' => 'Forest',         'g' => 'linear-gradient(160deg, #0d3318 0%, #062210 60%, #041a0c 100%)'],
                'cozy_room'     => ['label' => 'Cozy Room',      'g' => 'radial-gradient(ellipse at bottom right, #5c2a00 0%, #3c1c00 60%, #2a1200 100%)'],
                'pixel_sky'     => ['label' => 'Pixel Sky',      'g' => 'linear-gradient(180deg, #1a2070 0%, #0e1452 60%, #0a1040 100%)'],
                'aquarium'      => ['label' => 'Aquarium',        'g' => 'radial-gradient(ellipse at center, #003d5c 0%, #002840 60%, #001e2e 100%)'],
                'snowfall'      => ['label' => 'Snowfall',        'g' => 'linear-gradient(160deg, #2a3a60 0%, #1e2a48 60%, #141e38 100%)'],
                'sunset_fog'    => ['label' => 'Sunset Fog',     'g' => 'linear-gradient(160deg, #4a1a44 0%, #300e2c 60%, #200820 100%)'],
                'moonlight'     => ['label' => 'Moonlight',       'g' => 'radial-gradient(ellipse at top right, #1a1a40 0%, #10102a 60%, #080814 100%)'],
                'coffee_shop'   => ['label' => 'Coffee Shop',    'g' => 'radial-gradient(ellipse at bottom, #3c2a10 0%, #281a08 60%, #1c1004 100%)'],
                'soft_abstract' => ['label' => 'Soft Abstract',  'g' => 'linear-gradient(135deg, #24183c 0%, #1a2030 60%, #101820 100%)'],
                'deep_blue'     => ['label' => 'Deep Blue',       'g' => 'linear-gradient(180deg, #042060 0%, #031840 60%, #021028 100%)'],
                'warm_lamp'     => ['label' => 'Warm Lamp',       'g' => 'radial-gradient(ellipse at top left, #3c2800 0%, #281a00 60%, #1a1000 100%)'],
            ];
            $socialStyleMap = [
                'quiet_chatter'        => 'Quiet chatter',
                'mostly_listening'     => 'Mostly listening',
                'slow_replies'         => 'Slow replies OK',
                'deep_talks'           => 'Deep talks',
                'late_night'           => 'Late-night person',
                'introvert_friendly'   => 'Introvert-friendly',
                'listener_first'       => 'Listener first',
                'casual_conversations' => 'Casual conversations',
                'low_pressure'         => 'Low-pressure vibes',
                'group_chats_okay'     => 'Group chats are OK',
                'one_on_one_preferred' => 'One-on-one preferred',
                'small_groups'         => 'Small groups feel safer',
                'usually_multitasking' => 'Usually multitasking',
                'social_battery'       => 'Social battery varies',
                'thoughtful_replies'   => 'Thoughtful replies',
                'cozy_energy'          => 'Cozy energy',
                'random_conversations' => 'Likes random chats',
                'comfortable_online'   => 'More comfortable online',
                'sometimes_awkward'    => 'Sometimes awkward at first',
                'better_warmed_up'     => 'Better once warmed up',
                'open_to_friends'      => 'Open to new friends',
                'quiet_but_friendly'   => 'Quiet but friendly',
                'easygoing'            => 'Easygoing',
                'rambles_sometimes'    => 'Rambles sometimes',
                'comfortable_silence'  => 'Comfortable with silence',
            ];
            $openToMap = [
                'new_friends'              => 'New friends',
                'quiet_conversations'      => 'Quiet conversations',
                'group_chats'              => 'Group chats',
                'one_on_one_chats'         => 'One-on-one chats',
                'shared_hobbies'           => 'Shared hobbies',
                'deep_talks'               => 'Deep talks',
                'casual_conversation'      => 'Casual conversation',
                'listening_more'           => 'Listening more than talking',
                'gaming_together'          => 'Gaming together',
                'book_discussions'         => 'Book discussions',
                'slow_conversations'       => 'Slow conversations',
                'creative_discussions'     => 'Creative discussions',
                'nighttime_chats'          => 'Night-time chats',
                'similar_experiences'      => 'Similar experiences',
                'just_existing'            => 'Just existing together',
                'advice_support'           => 'Advice and support',
                'meeting_slowly'           => 'Meeting people slowly',
                'cozy_conversation'        => 'Cozy conversation',
                'joining_quietly'          => 'Joining rooms quietly',
                'talking_when_comfortable' => 'Talking when comfortable',
            ];
            $comfortPrompts = [
                'Comfort game', 'Comfort movie', 'Comfort show', 'Favorite rainy-day activity',
                'Favorite late-night snack', 'Favorite music genre', 'Favorite comfort food',
                'Favorite fictional world', 'Favorite hobby', 'Favorite place to relax',
                'Favorite kind of weather', 'Favorite book', 'Favorite animal',
                'Favorite way to unwind', 'Favorite calming activity', 'Favorite conversation topic',
                'Favorite cozy game', 'Favorite tea or coffee', 'Favorite comfort song',
                'Favorite season', 'Favorite comfort app or site', 'Favorite kind of room here',
                'Favorite thing to ramble about', 'Favorite quiet activity', 'Favorite nostalgic thing',
            ];
        @endphp

        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Your Space</h2>
        <p class="text-xs -mt-4" style="color:#8B949E;">Optional details that help others get a feel for you. Nothing here is required.</p>

        {{-- Profile status / tagline --}}
        <div x-data="{ showSuggestions: false }">
            <div class="flex items-center justify-between mb-1">
                <label for="profileStatus" class="text-sm font-medium" style="color:#8B949E;">
                    Short tagline <span class="font-normal opacity-70">(optional)</span>
                </label>
                <button type="button" @click="showSuggestions = !showSuggestions"
                    class="text-xs transition"
                    style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                    x-text="showSuggestions ? 'Hide suggestions' : 'Browse suggestions'"
                ></button>
            </div>
            <input id="profileStatus" type="text" wire:model.live="profileStatus" maxlength="120"
                placeholder="e.g. mostly lurking, occasionally brave"
                class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                style="background:#1C2333;border:1px solid {{ $errors->has('profileStatus') ? '#E24B4A' : '#30363D' }};color:#E6EDF3;"
                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
            >
            <p class="mt-1 text-xs" style="color:#8B949E;">Shows softly under your name. Max 120 characters.</p>
            @error('profileStatus') <p class="mt-1 text-sm" style="color:#E24B4A;">{{ $message }}</p> @enderror

            <div x-show="showSuggestions" x-transition.opacity class="mt-2 rounded-lg border p-3" style="background:#161B22;border-color:#30363D;">
                <p class="text-xs mb-2" style="color:#8B949E;">Click one to fill in the field:</p>
                <div class="flex flex-wrap gap-1.5 max-h-44 overflow-y-auto">
                    @foreach ($statusSuggestions as $s)
                        <button type="button"
                            @click="$wire.set('profileStatus', @js($s)); showSuggestions = false"
                            class="px-2.5 py-1 rounded-full text-xs transition"
                            style="background:#1C2333;color:#8B949E;border:1px solid #30363D;"
                            onmouseover="this.style.color='#E6EDF3';this.style.borderColor='#4B5563'" onmouseout="this.style.color='#8B949E';this.style.borderColor='#30363D'"
                        >{{ $s }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Accent color --}}
        <div>
            <p class="text-sm font-medium mb-2" style="color:#8B949E;">Profile accent <span class="font-normal opacity-70">(optional)</span></p>
            <div class="flex items-center gap-3 flex-wrap">
                @foreach (['green' => '#1D9E75', 'blue' => '#4A8BB5', 'amber' => '#D29922', 'purple' => '#8B5CF6', 'slate' => '#8B949E'] as $key => $color)
                    <button type="button" wire:click="$set('accentColor', '{{ $accentColor === $key ? '' : $key }}')"
                        class="w-8 h-8 rounded-full transition"
                        style="background:{{ $color }};outline:{{ $accentColor === $key ? '2px solid #E6EDF3' : '2px solid transparent' }};outline-offset:2px;"
                        title="{{ ucfirst($key) }}"
                    ></button>
                @endforeach
            </div>
            <p class="mt-1.5 text-xs" style="color:#8B949E;">Subtle colour used on your profile. Click again to clear.</p>
        </div>

        {{-- Atmosphere background --}}
        <div>
            <p class="text-sm font-medium mb-2" style="color:#8B949E;">Atmosphere <span class="font-normal opacity-70">(optional)</span></p>
            <div class="grid grid-cols-4 gap-2">
                @foreach ($bannerOptions as $key => $option)
                    <button type="button" wire:click="$set('bannerStyle', '{{ $bannerStyle === $key ? '' : $key }}')"
                        class="flex flex-col items-center gap-1 pb-1 rounded-lg transition"
                        title="{{ $option['label'] }}"
                    >
                        <span class="block w-full rounded-lg" style="height:28px;background:{{ $option['g'] }};outline:{{ $bannerStyle === $key ? '2px solid #E6EDF3' : '2px solid #30363D' }};outline-offset:-1px;"></span>
                        <span class="text-xs leading-tight text-center" style="color:{{ $bannerStyle === $key ? '#E6EDF3' : '#8B949E' }};font-size:0.65rem;">{{ $option['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <p class="mt-1.5 text-xs" style="color:#8B949E;">A soft atmospheric tint shown behind your profile. Click again to remove.</p>
        </div>

        {{-- Comfort things (up to 5 slots) --}}
        <div>
            <p class="text-sm font-medium mb-1" style="color:#8B949E;">Comfort things <span class="font-normal opacity-70">(optional · up to 5)</span></p>
            <p class="text-xs mb-3" style="color:#8B949E;">Small conversation hooks — what you love, what you reach for, what you could talk about forever.</p>

            <datalist id="comfort-prompts">
                @foreach ($comfortPrompts as $p)
                    <option value="{{ $p }}">
                @endforeach
            </datalist>

            <div class="space-y-2">
                @foreach ($comfortThings as $i => $thing)
                    <div class="flex gap-2 items-start">
                        <div class="flex-1 flex gap-2">
                            <input type="text" wire:model="comfortThings.{{ $i }}.label"
                                list="comfort-prompts" maxlength="60" placeholder="What kind of thing…"
                                class="w-2/5 rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                            >
                            <input type="text" wire:model="comfortThings.{{ $i }}.value"
                                maxlength="120" placeholder="Your answer…"
                                class="flex-1 rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                            >
                        </div>
                        <button type="button" wire:click="removeComfortThing({{ $i }})"
                            class="text-xs mt-2.5 transition flex-none"
                            style="color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                        >Remove</button>
                    </div>
                @endforeach
            </div>

            @if (count($comfortThings) < 5)
                <button type="button" wire:click="addComfortThing"
                    class="mt-2 text-xs transition"
                    style="color:#8B949E;" onmouseover="this.style.color='#1D9E75'" onmouseout="this.style.color='#8B949E'"
                >+ Add a comfort thing</button>
            @endif
        </div>

        {{-- Social style --}}
        <div>
            <p class="text-sm font-medium mb-1" style="color:#8B949E;">Social style <span class="font-normal opacity-70">(up to 5)</span></p>
            <p class="text-xs mb-2" style="color:#8B949E;">Helps people understand how you like to connect.</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($socialStyleMap as $key => $label)
                    <button type="button" wire:click="toggleSocialStyle('{{ $key }}')"
                        class="px-3 py-1.5 rounded-full text-xs transition"
                        style="{{ in_array($key, $socialStyles) ? 'background:rgba(29,158,117,0.12);color:#1D9E75;border:1px solid rgba(29,158,117,0.35);' : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                    >{{ $label }}</button>
                @endforeach
            </div>
            @if (count($socialStyles) >= 5)
                <p class="mt-1.5 text-xs" style="color:#D29922;">5 selected — deselect one to choose another.</p>
            @endif
        </div>

        {{-- Open To --}}
        <div>
            <p class="text-sm font-medium mb-1" style="color:#8B949E;">Open to… <span class="font-normal opacity-70">(optional)</span></p>
            <p class="text-xs mb-2" style="color:#8B949E;">Helps people know how approachable you feel.</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($openToMap as $key => $label)
                    <button type="button" wire:click="toggleOpenTo('{{ $key }}')"
                        class="px-3 py-1.5 rounded-full text-xs transition"
                        style="{{ in_array($key, $openTo) ? 'background:rgba(74,139,181,0.12);color:#4A8BB5;border:1px solid rgba(74,139,181,0.35);' : 'background:#1C2333;color:#8B949E;border:1px solid #30363D;' }}"
                    >{{ $label }}</button>
                @endforeach
            </div>
        </div>

    </section>

    {{-- ── SECTION 8 — Read Receipts ────────────────── --}}
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

            {{-- Hide reactions --}}
            <div class="flex items-start justify-between gap-4 py-3 mt-2 border-t" style="border-color:#30363D;">
                <div>
                    <p class="text-sm font-medium" style="color:#E6EDF3;">Hide message reactions</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:#8B949E;">
                        Hides emoji reactions on messages — yours and everyone else's.<br>
                        You can still send messages normally.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('hideReactions')"
                    class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                    style="background:{{ $hideReactions ? '#1D9E75' : '#21262D' }};border:1px solid {{ $hideReactions ? '#1D9E75' : '#30363D' }};"
                    role="switch"
                    aria-checked="{{ $hideReactions ? 'true' : 'false' }}"
                    aria-label="Hide message reactions"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $hideReactions ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>
        </div>

        {{-- Advanced Comfort — Supporter only --}}
        <div class="mt-6 pt-5 border-t" style="border-color:#30363D;">
            <div class="flex items-center gap-3 mb-1">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Advanced Comfort</p>
                @if (! $this->userIsSupporter)
                    <a href="{{ route('support') }}" wire:navigate class="text-xs transition" style="color:#1D9E75;" onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'">Supporter feature</a>
                @endif
            </div>
            <p class="text-xs mb-4 leading-relaxed max-w-prose" style="color:#8B949E;">Extra control over how calm and minimal the interface feels. None of these affect other people.</p>

            @php
                $advancedOptions = [
                    'ultra_minimal'    => ['label' => 'Ultra-minimal mode',       'desc' => 'Hides the tagline, suggestion card, and room type labels.'],
                    'hide_phrases'     => ['label' => 'Hide comfort phrases',      'desc' => 'Removes the rotating phrase from the sidebar.'],
                    'hide_suggestions' => ['label' => 'Hide suggestion previews',  'desc' => 'Hides the weekly friend suggestion card.'],
                    'hide_gradients'   => ['label' => 'Hide decorative gradients', 'desc' => 'Shows a plain dark background instead of gradient tints.'],
                    'extra_spacing'    => ['label' => 'Extra spacing',             'desc' => 'More breathing room between chat messages.'],
                    'compact_chat'     => ['label' => 'Compact chat',              'desc' => 'Tighter message spacing inside rooms.'],
                    'reduce_sidebar'   => ['label' => 'Reduce sidebar density',    'desc' => 'Compacts the links in the left sidebar.'],
                    'simple_room_cards'=> ['label' => 'Simplified room cards',     'desc' => 'Hides type badges and interest tags on room cards.'],
                    'larger_text'      => ['label' => 'Larger text',               'desc' => 'Slightly larger text across the interface.'],
                ];
            @endphp

            <div class="space-y-0.5 {{ $this->userIsSupporter ? '' : 'opacity-50 pointer-events-none select-none' }}" aria-disabled="{{ $this->userIsSupporter ? 'false' : 'true' }}">
                @foreach ($advancedOptions as $settingKey => $option)
                    @php $isOn = in_array($settingKey, $advancedComfortSettings, true); @endphp
                    <div class="flex items-start justify-between gap-4 py-2.5 border-b" style="border-color:#161B22;">
                        <div>
                            <p class="text-sm font-medium" style="color:#E6EDF3;">{{ $option['label'] }}</p>
                            <p class="text-xs mt-0.5" style="color:#8B949E;">{{ $option['desc'] }}</p>
                        </div>
                        <button
                            type="button"
                            @if ($this->userIsSupporter) wire:click="toggleAdvancedComfort('{{ $settingKey }}')" @endif
                            class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                            style="background:{{ $isOn ? '#1D9E75' : '#21262D' }};border:1px solid {{ $isOn ? '#1D9E75' : '#30363D' }};"
                            role="switch"
                            aria-checked="{{ $isOn ? 'true' : 'false' }}"
                            aria-label="{{ $option['label'] }}"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $isOn ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ── SECTION — Appearance ─────────────────────── --}}
    <section class="space-y-5">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Appearance</h2>

        <div>
            <p class="text-sm font-medium mb-1" style="color:#E6EDF3;">App gradient</p>
            <p class="text-xs mb-4" style="color:#8B949E;">
                A subtle colour tint applied to your app background — only you see it.
                @if ($lowStimulationMode)
                    <span style="color:#D29922;">Low-stimulation mode is on — gradient is hidden.</span>
                @endif
            </p>

            @php
                $allGradients       = config('gradients');
                $supporterKeys      = config('supporter.gradient_packs', []);
                $freeGradients      = array_filter($allGradients, fn ($k) => ! in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                $supporterGradients = array_filter($allGradients, fn ($k) => in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                $isLocked           = count($this->lockedGradientKeys) > 0;
            @endphp

            {{-- Free gradients (None + 5) --}}
            <div class="grid grid-cols-5 gap-2">
                <button
                    type="button"
                    wire:click="$set('personalGradientTheme', '')"
                    class="flex flex-col items-center gap-1.5"
                >
                    <div class="w-full h-10 rounded-lg border-2 transition" style="background:#0D1117;{{ $personalGradientTheme === '' ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                    <span class="text-xs" style="color:{{ $personalGradientTheme === '' ? '#E6EDF3' : '#8B949E' }};">None</span>
                </button>

                @foreach ($freeGradients as $key => $gradient)
                    <button
                        type="button"
                        wire:click="$set('personalGradientTheme', '{{ $key }}')"
                        class="flex flex-col items-center gap-1.5"
                    >
                        <div class="w-full h-10 rounded-lg border-2 transition" style="background:{{ $gradient['css'] }};{{ $personalGradientTheme === $key ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                        <span class="text-xs text-center leading-tight" style="color:{{ $personalGradientTheme === $key ? '#E6EDF3' : '#8B949E' }};">{{ $gradient['label'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Supporter gradients --}}
            <div class="mt-4">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Supporter</p>
                    @if ($isLocked)
                        <a href="{{ route('support') }}" wire:navigate class="text-xs transition" style="color:#1D9E75;" onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'">Learn more</a>
                    @endif
                </div>
                <div class="grid grid-cols-5 gap-2">
                    @foreach ($supporterGradients as $key => $gradient)
                        @if (! $isLocked)
                            <button
                                type="button"
                                wire:click="$set('personalGradientTheme', '{{ $key }}')"
                                class="flex flex-col items-center gap-1.5"
                            >
                                <div class="w-full h-10 rounded-lg border-2 transition" style="background:{{ $gradient['css'] }};{{ $personalGradientTheme === $key ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"></div>
                                <span class="text-xs text-center leading-tight" style="color:{{ $personalGradientTheme === $key ? '#E6EDF3' : '#8B949E' }};">{{ $gradient['label'] }}</span>
                            </button>
                        @else
                            <div class="flex flex-col items-center gap-1.5 cursor-default">
                                <div class="w-full h-10 rounded-lg border-2 relative overflow-hidden" style="background:{{ $gradient['css'] }};border-color:#21262D;opacity:0.45;">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="#8B949E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="1"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
                                    </div>
                                </div>
                                <span class="text-xs text-center leading-tight" style="color:#3d4451;">{{ $gradient['label'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Birthday theme toggle --}}
        <div class="flex items-start justify-between gap-4 pt-2">
            <div>
                <p class="text-sm font-medium" style="color:#E6EDF3;">Show birthday theme</p>
                <p class="text-xs mt-0.5" style="color:#8B949E;">We'll add a small private birthday touch on your birthday. Only you see it.</p>
            </div>
            <button
                type="button"
                wire:click="$toggle('birthdayThemeEnabled')"
                class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                style="background:{{ $birthdayThemeEnabled ? '#1D9E75' : '#21262D' }};border:1px solid {{ $birthdayThemeEnabled ? '#1D9E75' : '#30363D' }};"
                role="switch"
                aria-checked="{{ $birthdayThemeEnabled ? 'true' : 'false' }}"
                aria-label="Show birthday theme"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $birthdayThemeEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>

        {{-- Holiday atmospheres toggle --}}
        <div class="flex items-start justify-between gap-4 pt-2 border-t" style="border-color:#21262D;">
            <div>
                <p class="text-sm font-medium" style="color:#E6EDF3;">Show holiday atmospheres</p>
                <p class="text-xs mt-0.5 leading-relaxed max-w-sm" style="color:#8B949E;">We'll add small seasonal touches on certain days. You can turn this off anytime.</p>
            </div>
            <button
                type="button"
                wire:click="$toggle('holidayThemesEnabled')"
                class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                style="background:{{ $holidayThemesEnabled ? '#1D9E75' : '#21262D' }};border:1px solid {{ $holidayThemesEnabled ? '#1D9E75' : '#30363D' }};"
                role="switch"
                aria-checked="{{ $holidayThemesEnabled ? 'true' : 'false' }}"
                aria-label="Show holiday atmospheres"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $holidayThemesEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>

    </section>

    {{-- ── SECTION — Tone Pack ─────────────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Tone Pack</h2>

        <p class="text-xs leading-relaxed max-w-prose" style="color:#8B949E;">
            Personalise the small phrases and comfort copy you see around the site.
            Other people never see your tone pack — it's just for you.
        </p>

        <div class="grid grid-cols-2 gap-2">
            @foreach ($this->tonePacks as $packKey => $pack)
                @php $isPackLocked = $pack['locked'] ?? false; @endphp

                @if ($isPackLocked)
                    <div
                        class="p-3 rounded-xl border-2 text-left relative overflow-hidden"
                        style="background:#161B22;border-color:#21262D;opacity:0.55;cursor:default;"
                    >
                        <p class="text-xs font-medium" style="color:#3d4451;">{{ $pack['label'] }}</p>
                        <p class="text-xs mt-0.5 leading-snug" style="color:#3d4451;">{{ $pack['phrases'][0] ?? '' }}</p>
                        <a href="{{ route('support') }}" wire:navigate class="text-xs mt-1 inline-block" style="color:#1D9E75;opacity:1;">Supporter</a>
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="$set('tonePackKey', '{{ $packKey }}')"
                        class="p-3 rounded-xl border-2 text-left transition"
                        style="background:#161B22;{{ $tonePackKey === $packKey ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"
                        onmouseover="if('{{ $tonePackKey }}' !== '{{ $packKey }}') this.style.borderColor='#3d4451';" onmouseout="if('{{ $tonePackKey }}' !== '{{ $packKey }}') this.style.borderColor='#30363D';"
                    >
                        <p class="text-xs font-medium" style="color:{{ $tonePackKey === $packKey ? '#E6EDF3' : '#C9D1D9' }};">{{ $pack['label'] }}</p>
                        <p class="text-xs mt-0.5 leading-snug" style="color:#3d4451;">{{ $pack['phrases'][0] ?? '' }}</p>
                    </button>
                @endif
            @endforeach
        </div>

    </section>

    {{-- ── SECTION — Comfort Prompts ─────────────── --}}
    <section class="space-y-4">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:#8B949E;border-color:#30363D;">Comfort Prompts</h2>

        <p class="text-xs leading-relaxed max-w-prose" style="color:#8B949E;">
            Optional conversation starters that appear gently inside rooms. Only you control which packs are shown.
        </p>

        {{-- Master toggle --}}
        <div class="flex items-start justify-between gap-4 py-2">
            <div>
                <p class="text-sm font-medium" style="color:#E6EDF3;">Show conversation prompts</p>
                <p class="text-xs mt-0.5 max-w-sm" style="color:#8B949E;">A gentle prompt card appears at the top of rooms. Dismiss it any time.</p>
            </div>
            <button type="button" wire:click="$toggle('showConversationPrompts')"
                class="flex-none relative inline-flex h-6 w-11 items-center rounded-full transition"
                style="background:{{ $showConversationPrompts ? '#1D9E75' : '#21262D' }};border:1px solid {{ $showConversationPrompts ? '#1D9E75' : '#30363D' }};"
                role="switch" aria-checked="{{ $showConversationPrompts ? 'true' : 'false' }}" aria-label="Show conversation prompts"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $showConversationPrompts ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>

        {{-- Pack grid --}}
        <div class="grid grid-cols-2 gap-2">
            @foreach ($this->promptPacks as $packKey => $pack)
                @php $isPackLocked = $pack['locked'] ?? false; $isPackEnabled = in_array($packKey, $enabledPromptPacks, true); @endphp

                @if ($isPackLocked)
                    <div class="p-3 rounded-xl border-2 text-left relative"
                        style="background:#161B22;border-color:#21262D;opacity:0.55;cursor:default;">
                        <p class="text-xs font-medium" style="color:#3d4451;">{{ $pack['label'] }}</p>
                        <p class="text-xs mt-0.5 leading-snug" style="color:#3d4451;">{{ $pack['description'] ?? '' }}</p>
                        <a href="{{ route('support') }}" wire:navigate class="text-xs mt-1 inline-block" style="color:#1D9E75;opacity:1;">Supporter</a>
                    </div>
                @else
                    <button type="button" wire:click="togglePromptPack('{{ $packKey }}')"
                        class="p-3 rounded-xl border-2 text-left transition"
                        style="background:#161B22;{{ $isPackEnabled ? 'border-color:#1D9E75;' : 'border-color:#30363D;' }}"
                        onmouseover="this.style.borderColor='{{ $isPackEnabled ? '#1D9E75' : '#3d4451' }}';" onmouseout="this.style.borderColor='{{ $isPackEnabled ? '#1D9E75' : '#30363D' }}';"
                    >
                        <p class="text-xs font-medium" style="color:{{ $isPackEnabled ? '#E6EDF3' : '#C9D1D9' }};">{{ $pack['label'] }}</p>
                        <p class="text-xs mt-0.5 leading-snug" style="color:#3d4451;">{{ $pack['description'] ?? '' }}</p>
                    </button>
                @endif
            @endforeach
        </div>

    </section>

    {{-- ── Bottom save button ──────────────────────────────────────────────── --}}
    <div class="pt-2" data-no-dirty>
        <button
            type="button"
            wire:click="saveAll"
            wire:loading.attr="disabled"
            wire:target="saveAll"
            data-no-dirty
            class="w-full py-3 text-sm font-semibold rounded-xl transition disabled:opacity-50"
            style="background:#1D9E75;color:#fff;"
            onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
        >
            <span wire:loading.remove wire:target="saveAll">Save settings</span>
            <span wire:loading wire:target="saveAll">Saving…</span>
        </button>
    </div>

    <div class="pt-2 text-center" style="border-color:#21262D;">
        <a
            href="{{ route('report') }}"
            wire:navigate
            class="text-xs transition"
            style="color:#3d4451;"
            onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'"
        >Report a problem</a>
    </div>

    {{-- ── Sticky save bar ─────────────────────────────────────────────────── --}}
    <div
        x-show="dirty || saved"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="sticky bottom-0 left-0 right-0 z-30 -mx-6 flex items-center justify-between gap-4 px-6 py-3 border-t"
        style="display:none;background:#161B22;border-color:#30363D;box-shadow:0 -4px 20px rgba(0,0,0,0.45);"
        data-no-dirty
    >
        <p class="text-sm flex-1">
            <span x-show="saved && !dirty" style="color:#1D9E75;">Changes saved.</span>
            <span x-show="dirty" style="color:#8B949E;">You have unsaved changes.</span>
        </p>
        <div class="flex items-center gap-3 flex-none" x-show="dirty">
            <button
                type="button"
                @click="window.location.reload()"
                class="text-sm transition"
                style="color:#8B949E;"
                onmouseover="this.style.color='#C9D1D9'" onmouseout="this.style.color='#8B949E'"
            >Discard</button>
            <button
                type="button"
                wire:click="saveAll"
                wire:loading.attr="disabled"
                wire:target="saveAll"
                data-no-dirty
                class="px-4 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
                onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >
                <span wire:loading.remove wire:target="saveAll">Save changes</span>
                <span wire:loading wire:target="saveAll">Saving…</span>
            </button>
        </div>
    </div>

</div>
