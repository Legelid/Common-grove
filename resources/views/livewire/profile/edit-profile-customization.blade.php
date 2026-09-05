<div class="px-6 py-10 max-w-2xl mx-auto space-y-10">
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
        $promptGroups = [
            'Comfort'      => ['Comfort game', 'Comfort movie', 'Comfort show', 'Comfort song', 'Comfort food', 'Favorite book', 'Favorite cozy game'],
            'Lifestyle'    => ['Favorite hobby', 'Favorite way to unwind', 'Favorite quiet activity', 'Favorite calming activity', 'Favorite tea or coffee', 'Favorite rainy-day activity', 'Favorite place to relax', 'Favorite kind of weather', 'Favorite season', 'Favorite animal'],
            'Conversation' => ['Favorite thing to ramble about', 'Favorite conversation topic', 'Favorite fictional world', 'Favorite nostalgic thing', 'Favorite late-night snack', 'Favorite music genre'],
            'Here'         => ['Favorite comfort app or site', 'Favorite kind of room here'],
        ];
    @endphp

    {{-- Header --}}
    {{-- Glass UI: light tier — hero heading over the forest photo, matching Home/Explore. --}}
    <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;">
        <h1 class="font-display" style="font-size:clamp(1.75rem, 4vw, 2.5rem);color:var(--text);font-weight:700;line-height:1.2;">Edit your profile</h1>
        <p class="mt-2" style="font-family:var(--font-body);font-size:1rem;color:var(--text-muted);">Make your profile feel a little more like you.</p>
        <p class="mt-0.5 text-xs" style="color:var(--text-muted);">Everything here is optional.</p>
    </x-glass-panel>

    {{-- ── 0. Avatar ───────────────────────────────────────────── --}}
    @php
        $lowStim       = auth()->user()->low_stimulation_mode;
        $hasAvatar     = auth()->user()->avatar_id !== null || auth()->user()->avatar_path !== null;
        $firstCatKey   = $this->activeAvatarsByCategory->keys()->first() ?? '';
    @endphp
    <section id="avatar" class="space-y-4"
             x-data="{
                 tab: @js($firstCatKey),
                 search: '',
                 get isSearching() { return this.search.length >= 1 }
             }">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Profile photo</h2>

        {{-- Current avatar + reset --}}
        <div class="flex items-center gap-4">
            <x-avatar :user="auth()->user()" size="lg" />
            @if ($hasAvatar)
                <button type="button" wire:click="resetAvatar"
                        class="text-xs transition"
                        style="color:var(--text-muted);"
                        @if (!$lowStim) onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'" @endif
                >Reset to default</button>
            @endif
        </div>

        {{-- Choose an avatar --}}
        <div>
            <p class="text-xs mb-2" style="color:var(--text-muted);">Choose an avatar</p>

            {{-- Search --}}
            <div class="relative mb-3">
                <x-input type="text" x-model="search" placeholder="Search avatars…" class="!pr-8" />
                <button type="button" x-show="isSearching" @click="search = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs"
                        style="color:var(--text-muted);"
                >✕</button>
            </div>

            {{-- Category tabs — hidden while searching --}}
            <div x-show="!isSearching" class="flex flex-wrap gap-1.5 mb-3">
                @foreach ($this->activeAvatarsByCategory as $catKey => $catAvatars)
                    <button type="button"
                            @click="tab = @js($catKey)"
                            class="px-3 py-1.5 rounded-full text-xs transition"
                            :style="tab === @js($catKey)
                                ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);'
                                : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);'"
                    >{{ $catAvatars->first()->category_label }}</button>
                @endforeach
            </div>

            {{-- Search results — flat grid across all active avatars --}}
            <div x-show="isSearching"
                 x-transition:enter="transition-opacity duration-100"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="grid grid-cols-5 gap-2 sm:grid-cols-6">
                @foreach ($this->activeAvatarsByCategory as $catAvatars)
                    @foreach ($catAvatars as $avatar)
                        <button type="button"
                                wire:click="selectAvatar({{ $avatar->id }})"
                                x-show="@js(strtolower($avatar->name)).includes(search.toLowerCase())"
                                title="{{ $avatar->name }}"
                                class="flex flex-col items-center gap-1 p-1 rounded-lg"
                                @if (!$lowStim) onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background=''" @endif
                        >
                            <img src="{{ asset($avatar->image_path) }}"
                                 alt="{{ $avatar->name }}"
                                 class="w-14 h-14 rounded-full object-cover"
                                 style="background:var(--surface);outline:{{ $selectedAvatarId === $avatar->id ? '2px solid var(--accent)' : '2px solid transparent' }};outline-offset:2px;">
                            <span class="leading-tight text-center" style="color:{{ $selectedAvatarId === $avatar->id ? 'var(--accent)' : 'var(--text-muted)' }};font-size:0.6rem;">{{ $avatar->name }}</span>
                        </button>
                    @endforeach
                @endforeach
            </div>

            {{-- Category grids — one per tab --}}
            @foreach ($this->activeAvatarsByCategory as $catKey => $catAvatars)
                <div x-show="!isSearching && tab === @js($catKey)"
                     x-transition:enter="transition-opacity duration-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="grid grid-cols-5 gap-2 sm:grid-cols-6"
                >
                    @foreach ($catAvatars as $avatar)
                        <button type="button"
                                wire:click="selectAvatar({{ $avatar->id }})"
                                title="{{ $avatar->name }}"
                                class="flex flex-col items-center gap-1 p-1 rounded-lg"
                                @if (!$lowStim) onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background=''" @endif
                        >
                            <img src="{{ asset($avatar->image_path) }}"
                                 alt="{{ $avatar->name }}"
                                 class="w-14 h-14 rounded-full object-cover"
                                 style="background:var(--surface);outline:{{ $selectedAvatarId === $avatar->id ? '2px solid var(--accent)' : '2px solid transparent' }};outline-offset:2px;">
                            <span class="leading-tight text-center" style="color:{{ $selectedAvatarId === $avatar->id ? 'var(--accent)' : 'var(--text-muted)' }};font-size:0.6rem;">{{ $avatar->name }}</span>
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    {{-- ── 1. Tagline / status ─────────────────────────────────── --}}
    <section class="space-y-3" x-data="{ showSuggestions: false }">
        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b mb-3" style="color:var(--text-muted);border-color:var(--border);">Your tagline</h2>
            <p class="text-xs mb-3" style="color:var(--text-muted);">A short line that appears under your name — quiet, personal, no pressure.</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="profileStatus" class="text-sm" style="color:var(--text-muted);">Tagline</label>
                <div class="flex items-center gap-3">
                    <span class="text-xs" style="color:{{ strlen($profileStatus) >= 100 ? '#D29922' : 'var(--text-muted)' }};">{{ strlen($profileStatus) }}/120</span>
                    <button type="button" @click="showSuggestions = !showSuggestions"
                        class="text-xs transition"
                        style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                        x-text="showSuggestions ? 'Hide suggestions' : 'Browse suggestions'"
                    ></button>
                </div>
            </div>
            <x-input id="profileStatus" type="text" wire:model.live="profileStatus" maxlength="120"
                placeholder="e.g. mostly lurking, occasionally brave"
                :error="$errors->first('profileStatus')"
            />

            <div x-show="showSuggestions" x-transition.opacity class="mt-2 rounded-lg border p-3" style="background:var(--surface);border-color:var(--border);">
                <p class="text-xs mb-2" style="color:var(--text-muted);">Click to use:</p>
                <div class="flex flex-wrap gap-1.5 max-h-44 overflow-y-auto pr-1">
                    @foreach ($statusSuggestions as $s)
                        <button type="button"
                            @click="$wire.set('profileStatus', @js($s)); showSuggestions = false"
                            class="px-2.5 py-1 rounded-full text-xs transition"
                            style="background:var(--surface);color:var(--text-muted);border:1px solid var(--border);"
                            onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--text-faint)'" onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)'"
                        >{{ $s }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── 2. Atmosphere ──────────────────────────────────────── --}}
    <section class="space-y-3">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Atmosphere</h2>
        <p class="text-xs" style="color:var(--text-muted);">A soft tint that appears behind your profile — subtle and calm.</p>

        <div class="grid grid-cols-4 gap-2">
            @foreach ($bannerOptions as $key => $option)
                <button type="button" wire:click="$set('bannerStyle', '{{ $bannerStyle === $key ? '' : $key }}')"
                    class="flex flex-col items-center gap-1 pb-1 rounded-lg transition"
                    title="{{ $option['label'] }}"
                >
                    <span class="block w-full rounded-lg" style="height:32px;background:{{ $option['g'] }};outline:{{ $bannerStyle === $key ? '2px solid var(--text)' : '2px solid var(--border)' }};outline-offset:-1px;"></span>
                    <span class="text-center leading-tight" style="color:{{ $bannerStyle === $key ? 'var(--text)' : 'var(--text-muted)' }};font-size:0.65rem;">{{ $option['label'] }}</span>
                </button>
            @endforeach
        </div>

        @if ($bannerStyle)
            <button type="button" wire:click="$set('bannerStyle', '')"
                class="text-xs transition"
                style="color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
            >Remove atmosphere</button>
        @endif
    </section>

    {{-- ── 3. Interests ───────────────────────────────────────── --}}
    <section class="space-y-3">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Interests</h2>
                <p class="text-xs mt-1" style="color:var(--text-muted);">
                    Your top interests — up to 8 are shown on your profile.
                    @if ($this->selectedTags->count() > 0)
                        <span style="color:var(--text-muted);">({{ $this->selectedTags->count() }} selected)</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Current tags --}}
        @if ($this->selectedTags->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($this->selectedTags as $tag)
                    <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs" style="background:rgba(var(--accent-rgb),0.1);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.3);">
                        {{ $tag->name }}
                        <button type="button" wire:click="removeTag('{{ $tag->id }}')"
                            class="leading-none transition"
                            style="color:rgba(var(--accent-rgb),0.6);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='rgba(var(--accent-rgb),0.6)'"
                            aria-label="Remove {{ $tag->name }}"
                        >×</button>
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Search to add --}}
        <div>
            <x-input type="text" wire:model.live.debounce.300ms="tagSearch" placeholder="Search for an interest to add…" />
        </div>

        @if ($this->tagSearchResults->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($this->tagSearchResults as $tag)
                    <button type="button" wire:click="addTag('{{ $tag->id }}')"
                        class="px-3 py-1 rounded-full text-xs transition"
                        style="background:var(--surface);color:var(--text-muted);border:1px solid var(--border);"
                        onmouseover="this.style.color='var(--accent)';this.style.borderColor='rgba(var(--accent-rgb),0.4)'" onmouseout="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)'"
                    >+ {{ $tag->name }}</button>
                @endforeach
            </div>
        @elseif (strlen($tagSearch) >= 2)
            <p class="text-xs" style="color:var(--text-muted);">No matching interests found.</p>
        @endif
    </section>

    {{-- ── 4. Comfort things ──────────────────────────────────── --}}
    <section class="space-y-3">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Comfort things</h2>
        <p class="text-xs" style="color:var(--text-muted);">Small conversation starters — what you love, what you reach for, what you could talk about forever. Up to 5.</p>

        <div class="space-y-2">
            @foreach ($comfortThings as $i => $thing)
                <div class="flex gap-2 items-start">
                    <div class="flex-1 flex gap-2">

                        {{-- Label picker — click to open grouped prompt popover --}}
                        <div class="w-2/5 relative"
                             x-data="{ open: false, search: '' }"
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                        >
                            <x-input type="text" wire:model="comfortThings.{{ $i }}.label"
                                maxlength="60" placeholder="What kind of thing…"
                                @focus="open = true"
                            />

                            {{-- Prompt picker popover --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-0.5"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-0.5"
                                 class="absolute z-20 left-0 top-full mt-1 w-72 rounded-lg border shadow-xl overflow-hidden"
                                 style="background:var(--surface);border-color:var(--border);"
                            >
                                {{-- Search --}}
                                <div class="p-2 border-b" style="border-color:var(--border);">
                                    <x-input type="text" x-model="search" x-ref="searchInput"
                                        x-effect="if(open) $nextTick(() => $refs.searchInput && $refs.searchInput.focus())"
                                        placeholder="Search prompts…"
                                        class="!rounded !px-2.5 !py-1.5 !text-xs"
                                        @keydown.escape="open = false"
                                    />
                                </div>

                                {{-- Grouped prompts --}}
                                <div class="max-h-52 overflow-y-auto p-2 space-y-3">
                                    @foreach ($promptGroups as $group => $prompts)
                                        <div x-show="@js(array_map('strtolower', $prompts)).some(p => p.includes(search.toLowerCase()))">
                                            <p class="text-xs font-semibold uppercase tracking-wider px-1.5 mb-1" style="color:var(--text-faint);">{{ $group }}</p>
                                            @foreach ($prompts as $prompt)
                                                <button type="button"
                                                    x-show="@js(strtolower($prompt)).includes(search.toLowerCase())"
                                                    @click="$wire.set('comfortThings.{{ $i }}.label', @js($prompt)); open = false; search = ''"
                                                    class="w-full text-left px-2 py-1.5 rounded text-xs transition"
                                                    style="color:var(--text);"
                                                    onmouseover="this.style.background='var(--surface)';this.style.color='var(--text)'" onmouseout="this.style.background='transparent';this.style.color='var(--text)'"
                                                >{{ $prompt }}</button>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Footer --}}
                                <div class="px-3 py-2 border-t" style="border-color:var(--border);">
                                    <p class="text-xs" style="color:var(--text-muted);">Or just type your own label above</p>
                                </div>
                            </div>
                        </div>

                        {{-- Value input --}}
                        <x-input type="text" wire:model="comfortThings.{{ $i }}.value"
                            maxlength="120" placeholder="Your answer…"
                            class="flex-1"
                        />
                    </div>
                    <button type="button" wire:click="removeComfortThing({{ $i }})"
                        class="text-xs mt-2.5 transition flex-none"
                        style="color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                    >Remove</button>
                </div>
            @endforeach
        </div>

        @if (count($comfortThings) < 5)
            <button type="button" wire:click="addComfortThing"
                class="text-xs transition"
                style="color:var(--text-muted);" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'"
            >+ Add a comfort thing</button>
        @endif
    </section>

    {{-- ── 5. Social style ─────────────────────────────────────── --}}
    <section class="space-y-3">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Social style</h2>
        <p class="text-xs" style="color:var(--text-muted);">How you like to connect — choose up to 5.</p>

        <div class="flex flex-wrap gap-1.5">
            @foreach ($socialStyleMap as $key => $label)
                <button type="button" wire:click="toggleSocialStyle('{{ $key }}')"
                    class="px-3 py-1.5 rounded-full text-xs transition"
                    style="{{ in_array($key, $socialStyles) ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);' : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                >{{ $label }}</button>
            @endforeach
        </div>

        @if (count($socialStyles) >= 5)
            <p class="text-xs" style="color:#D29922;">5 selected — deselect one to choose another.</p>
        @endif
    </section>

    {{-- ── 6. Open to ──────────────────────────────────────────── --}}
    <section class="space-y-3">
        <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Open to…</h2>
        <p class="text-xs" style="color:var(--text-muted);">Helps people know how approachable you feel.</p>

        <div class="flex flex-wrap gap-1.5">
            @foreach ($openToMap as $key => $label)
                <button type="button" wire:click="toggleOpenTo('{{ $key }}')"
                    class="px-3 py-1.5 rounded-full text-xs transition"
                    style="{{ in_array($key, $openTo) ? 'background:rgba(74,139,181,0.12);color:#4A8BB5;border:1px solid rgba(74,139,181,0.35);' : 'background:var(--surface);color:var(--text-muted);border:1px solid var(--border);' }}"
                >{{ $label }}</button>
            @endforeach
        </div>
    </section>

    {{-- ── 7. Usually found in (read-only) ────────────────────── --}}
    @if ($this->usualRooms->isNotEmpty())
        <section class="space-y-3">
            <h2 class="text-xs font-semibold uppercase tracking-wider pb-2 border-b" style="color:var(--text-muted);border-color:var(--border);">Usually found in</h2>
            <p class="text-xs" style="color:var(--text-muted);">Rooms you've created — shown automatically on your profile.</p>
            <ul class="space-y-1.5">
                @foreach ($this->usualRooms as $room)
                    <li class="flex items-center gap-2 text-sm" style="color:var(--text);">
                        <span style="color:var(--text-muted);">·</span>
                        {{ $room->title }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ── Save ────────────────────────────────────────────────── --}}
    <div class="pt-2 space-y-3">
        <div class="flex items-center gap-4">
            <x-button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save" variant="primary" class="!px-6">
                <span wire:loading.remove wire:target="save">Save profile</span>
                <span wire:loading wire:target="save">Saving…</span>
            </x-button>

            <a href="{{ route('profile.show', auth()->user()->gamertag) }}" wire:navigate
                class="text-sm transition"
                style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
            >View your profile</a>
        </div>
    </div>

</div>
