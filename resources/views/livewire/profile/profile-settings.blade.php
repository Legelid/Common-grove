<div
    class="px-6 py-10 max-w-2xl mx-auto"
    style="padding-bottom:6rem;"
    x-data="{
        tab: (localStorage.getItem('cg-settings-tab') || 'profile'),
        dirty: false,
        saved: false,
        setTab(t) { this.tab = t; try { localStorage.setItem('cg-settings-tab', t); } catch (e) {} },
    }"
    @input.capture="dirty = true"
    @change.capture="dirty = true"
    @click.capture="
        const btn = $event.target.closest('button[wire\\:click]');
        if (btn && !btn.closest('[data-no-dirty]')) dirty = true;
    "
    @settings-saved.window="dirty = false; saved = true; setTimeout(() => saved = false, 3000)"
>
    <style>
        /* x-show removes the whole inline `display` value (not just toggles it)
           when showing an element — so display:flex must live in this class,
           never in the element's own inline style, or it gets wiped the first
           time the bar appears. */
        .cg-settings-savebar { display:flex; position:fixed; bottom:0; left:0; right:0; background:var(--surface); border-top:1px solid var(--border); padding:0.875rem 1.5rem; align-items:center; justify-content:space-between; z-index:30; box-shadow:0 -2px 8px rgba(0,0,0,0.15); }
    </style>

    {{-- Glass UI: light tier — hero heading + tab pills over the forest photo, matching Home/Explore. --}}
    <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;margin-bottom:2rem;">
    <h1 class="font-display" style="font-size:clamp(1.75rem, 4vw, 2.5rem);color:var(--text);font-weight:700;line-height:1.2;margin-bottom:1.25rem;">Settings</h1>

    {{-- ── Tab pills ────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach (['profile' => 'Profile', 'vibe' => 'Vibe', 'comfort' => 'Comfort', 'notifications' => 'Notifications', 'account' => 'Account'] as $tabKey => $tabLabel)
            <button
                type="button"
                @click="setTab('{{ $tabKey }}')"
                :style="(tab === '{{ $tabKey }}' ? 'background:var(--accent);color:var(--bg);border-color:transparent;font-weight:600;' : 'background:var(--surface);color:var(--text-muted);') + 'border-radius:var(--radius-pill);padding:0.5rem 1.25rem;font-size:0.875rem;font-family:var(--font-body);cursor:pointer;transition:all 150ms ease;border:1px solid var(--border);'"
            >{{ $tabLabel }}</button>
        @endforeach
    </div>
    </x-glass-panel>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 1 — Profile
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'profile'" x-cloak>
        <h2 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:1.25rem;">Your Profile</h2>

        {{-- IDENTITY --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Identity</p>

            <div class="space-y-4">
                <div>
                    <label for="identityMode" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">How should others see you?</label>
                    <select id="identityMode" wire:model.live="identityMode"
                        class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none"
                        style="background:var(--surface);border:1px solid var(--border);color:var(--text);"
                        onfocus="this.style.boxShadow='0 0 0 2px var(--accent)'" onblur="this.style.boxShadow=''"
                    >
                        <option value="1">Gamertag only</option>
                        <option value="2">Gamertag + friendly name</option>
                        <option value="3">Display name forward</option>
                    </select>
                </div>

                <div>
                    <label for="displayNameInput" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                        Friendly name <span class="font-normal opacity-70">(optional)</span>
                    </label>
                    <x-input id="displayNameInput" type="text" wire:model.live="displayNameInput" maxlength="50" placeholder="First name or nickname"
                        :error="$errors->first('displayNameInput')"
                    />
                    <p class="mt-1 text-xs" style="color:var(--text-muted);">What should friends call you?</p>
                </div>

                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-medium" style="color:var(--text);">Show friendly names</p>
                        <p class="text-xs" style="color:var(--text-muted);">Show friendly names when others have set them</p>
                    </div>
                    <x-toggle wire:click="$toggle('showNamesPref')" :checked="$showNamesPref" role="switch" aria-label="Show friendly names" />
                </div>

                <div class="rounded-lg px-4 py-3 border" style="background:var(--bg);border-color:var(--border);">
                    <p class="text-xs mb-1" style="color:var(--text-muted);">How others will see you:</p>
                    <p class="font-semibold text-sm" style="color:var(--text);">{{ $this->previewName }}</p>
                </div>

                @if ($identityMessage)
                    <p class="text-sm" style="color:var(--accent);">{{ $identityMessage }}</p>
                @endif
            </div>
        </div>

        {{-- BIO --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Bio</p>

            <label for="bio" class="block text-sm font-medium mb-1" style="color:var(--text-muted);">
                About you <span class="font-normal opacity-70">(optional)</span>
            </label>
            <div class="relative">
                <x-textarea id="bio" wire:model.live="bio" maxlength="300" rows="4"
                    placeholder="A little about yourself — games you love, hobbies, whatever feels right."
                    class="!py-3"
                    :error="$errors->first('bio')"
                />
                <span class="absolute bottom-2 right-3 text-xs" style="color:{{ strlen($bio) >= 280 ? 'var(--danger)' : 'var(--text-muted)' }};">{{ strlen($bio) }}/300</span>
            </div>
            @if ($bioMessage)
                <p class="text-sm mt-2" style="color:var(--accent);">{{ $bioMessage }}</p>
            @endif
        </div>

        {{-- AVATAR --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Avatar</p>
            <div class="flex items-center gap-4">
                <x-avatar :user="auth()->user()" size="lg" />
                <a href="{{ route('profile.edit') }}#avatar" wire:navigate
                   class="text-sm transition"
                   style="color:var(--text-muted);"
                   onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                >Manage photo</a>
            </div>
        </div>

        {{-- GAMERTAG --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Gamertag</p>

            <p class="text-xs mb-3" style="color:var(--text-muted);">
                You can change your gamertag once every 30 days.
                @if (auth()->user()->last_gamertag_changed_at)
                    Last changed {{ auth()->user()->last_gamertag_changed_at->diffForHumans() }}.
                @endif
            </p>

            <div>
                <div class="relative">
                    <x-input id="gamertagInput" type="text" wire:model.live.debounce.400ms="gamertagInput" maxlength="20"
                        class="!pr-10"
                        :error="$errors->first('gamertagInput')"
                    />
                    @if ($gamertagStatus === 'available')
                        <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:var(--accent);">✓</span>
                    @elseif ($gamertagStatus === 'taken')
                        <span class="absolute inset-y-0 right-3 flex items-center text-sm font-bold" style="color:var(--danger);">✗</span>
                    @endif
                </div>

                @if ($gamertagStatus === 'taken' && count($gamertagSuggestions) > 0)
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($gamertagSuggestions as $suggestion)
                            <button type="button" wire:click="$set('gamertagInput', '{{ $suggestion }}')"
                                class="px-3 py-1 text-sm rounded-full transition"
                                style="background:var(--surface-raised);color:var(--accent);">{{ $suggestion }}</button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($gamertagMessage)
                <p class="text-sm mt-3" style="color:{{ str_starts_with($gamertagMessage, 'Gamertag updated') ? 'var(--accent)' : (str_starts_with($gamertagMessage, 'You can change') ? '#D29922' : 'var(--danger)') }};">
                    {{ $gamertagMessage }}
                </p>
            @endif

            <x-button type="button" wire:click="changeGamertag" wire:loading.attr="disabled" wire:target="changeGamertag"
                :disabled="$gamertagStatus === 'taken'"
                data-no-dirty
                variant="primary" class="!px-5 !py-2 mt-3"
            >
                <span wire:loading.remove wire:target="changeGamertag">Save gamertag</span>
                <span wire:loading wire:target="changeGamertag">Saving…</span>
            </x-button>
        </div>

        {{-- STATUS --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Status</p>
            <p class="text-xs mb-3" style="color:var(--text-muted);">Let friends know what you're up to right now. Expires automatically after 24 hours.</p>
            <livewire:profile.status-update />
        </div>

        {{-- YOUR SPACE --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
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

            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Your Space</p>
            <p class="text-xs mb-4" style="color:var(--text-muted);">Optional details that help others get a feel for you. Nothing here is required.</p>

            <div class="space-y-6">
                {{-- Profile status / tagline --}}
                <div x-data="{ showSuggestions: false }">
                    <div class="flex items-center justify-between mb-1">
                        <label for="profileStatus" class="text-sm font-medium" style="color:var(--text-muted);">
                            Short tagline <span class="font-normal opacity-70">(optional)</span>
                        </label>
                        <button type="button" @click="showSuggestions = !showSuggestions"
                            class="text-xs transition"
                            style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                            x-text="showSuggestions ? 'Hide suggestions' : 'Browse suggestions'"
                        ></button>
                    </div>
                    <x-input id="profileStatus" type="text" wire:model.live="profileStatus" maxlength="120"
                        placeholder="e.g. mostly lurking, occasionally brave"
                        :error="$errors->first('profileStatus')"
                    />
                    <p class="mt-1 text-xs" style="color:var(--text-muted);">Shows softly under your name. Max 120 characters.</p>

                    <div x-show="showSuggestions" x-transition.opacity class="mt-2 rounded-lg border p-3" style="background:var(--bg);border-color:var(--border);">
                        <p class="text-xs mb-2" style="color:var(--text-muted);">Click one to fill in the field:</p>
                        <div class="flex flex-wrap gap-1.5 max-h-44 overflow-y-auto">
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

                {{-- Comfort things (up to 5 slots) --}}
                <div>
                    <p class="text-sm font-medium mb-1" style="color:var(--text-muted);">Comfort things <span class="font-normal opacity-70">(optional · up to 5)</span></p>
                    <p class="text-xs mb-3" style="color:var(--text-muted);">Small conversation hooks — what you love, what you reach for, what you could talk about forever.</p>

                    <datalist id="comfort-prompts">
                        @foreach ($comfortPrompts as $p)
                            <option value="{{ $p }}">
                        @endforeach
                    </datalist>

                    <div class="space-y-2">
                        @foreach ($comfortThings as $i => $thing)
                            <div class="flex gap-2 items-start">
                                <div class="flex-1 flex gap-2">
                                    <x-input type="text" wire:model="comfortThings.{{ $i }}.label"
                                        list="comfort-prompts" maxlength="60" placeholder="What kind of thing…"
                                        class="w-2/5"
                                    />
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
                            class="mt-2 text-xs transition"
                            style="color:var(--text-muted);" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'"
                        >+ Add a comfort thing</button>
                    @endif
                </div>

                {{-- Social style --}}
                <div>
                    <p class="text-sm font-medium mb-1" style="color:var(--text-muted);">Social style <span class="font-normal opacity-70">(up to 5)</span></p>
                    <p class="text-xs mb-2" style="color:var(--text-muted);">Helps people understand how you like to connect.</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($socialStyleMap as $key => $label)
                            <button type="button" wire:click="toggleSocialStyle('{{ $key }}')"
                                class="px-3 py-1.5 rounded-full text-xs transition"
                                style="{{ in_array($key, $socialStyles) ? 'background:rgba(var(--accent-rgb),0.12);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.35);' : 'background:var(--bg);color:var(--text-muted);border:1px solid var(--border);' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                    @if (count($socialStyles) >= 5)
                        <p class="mt-1.5 text-xs" style="color:#D29922;">5 selected — deselect one to choose another.</p>
                    @endif
                </div>

                {{-- Open To --}}
                <div>
                    <p class="text-sm font-medium mb-1" style="color:var(--text-muted);">Open to… <span class="font-normal opacity-70">(optional)</span></p>
                    <p class="text-xs mb-2" style="color:var(--text-muted);">Helps people know how approachable you feel.</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($openToMap as $key => $label)
                            <button type="button" wire:click="toggleOpenTo('{{ $key }}')"
                                class="px-3 py-1.5 rounded-full text-xs transition"
                                style="{{ in_array($key, $openTo) ? 'background:rgba(74,139,181,0.12);color:#4A8BB5;border:1px solid rgba(74,139,181,0.35);' : 'background:var(--bg);color:var(--text-muted);border:1px solid var(--border);' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                @if ($expressionMessage)
                    <p class="text-sm" style="color:var(--accent);">{{ $expressionMessage }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 2 — Vibe
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'vibe'" x-cloak x-data="{ lockedPackMessage: null }">
        <h2 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:0.5rem;">Your Vibe</h2>
        <p style="font-size:0.9375rem;color:var(--text-muted);margin-bottom:1.5rem;">Personalise how CommonGrove feels for you. Only you see these settings.</p>

        {{-- THEME --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;" x-data="{ lockedThemeMessage: null }">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Theme</p>
            <p class="text-xs mb-4" style="color:var(--text-muted);">
                The background photo and glass tint you see behind panels. More themes are on the way for Supporters.
            </p>

            <div class="grid grid-cols-2 gap-2">
                @foreach ($this->siteThemes as $themeKey => $theme)
                    @php $isThemeLocked = $theme['locked'] ?? false; @endphp

                    @if ($isThemeLocked)
                        <button
                            type="button"
                            @click="lockedThemeMessage = 'theme-{{ $themeKey }}'"
                            class="rounded-xl border-2 text-left relative overflow-hidden"
                            style="border-color:var(--surface-raised);opacity:0.5;cursor:pointer;"
                        >
                            <span style="position:absolute;top:0.5rem;right:0.5rem;z-index:1;background:var(--accent-amber);color:var(--bg);border-radius:var(--radius-pill);padding:0.125rem 0.5rem;font-size:0.6875rem;font-weight:600;">Supporter</span>
                            <span class="block w-full" style="height:72px;background-image:url('{{ asset($theme['image']) }}');background-size:cover;background-position:center;filter:grayscale(0.6);"></span>
                            <p class="text-xs font-medium p-2" style="color:var(--text-faint);">{{ $theme['label'] }}</p>
                            <div x-show="lockedThemeMessage === 'theme-{{ $themeKey }}'" x-cloak class="px-2 pb-2" style="border-top:1px solid var(--border);padding-top:0.5rem;">
                                <p class="text-xs" style="color:var(--text-muted);">This theme is available to CommonGrove Supporters. <a href="{{ route('support') }}" wire:navigate style="color:var(--accent);" @click.stop>Upgrade to unlock all themes.</a></p>
                            </div>
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="$set('siteTheme', '{{ $themeKey }}')"
                            class="rounded-xl border-2 text-left transition overflow-hidden"
                            style="{{ $siteTheme === $themeKey ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"
                            onmouseover="if('{{ $siteTheme }}' !== '{{ $themeKey }}') this.style.borderColor='var(--text-faint)';" onmouseout="if('{{ $siteTheme }}' !== '{{ $themeKey }}') this.style.borderColor='var(--border)';"
                        >
                            <span class="block w-full" style="height:72px;background-image:url('{{ asset($theme['image']) }}');background-size:cover;background-position:center;"></span>
                            <p class="text-xs font-medium p-2" style="color:var(--text);">{{ $theme['label'] }}</p>
                        </button>
                    @endif
                @endforeach
            </div>

            <div class="flex items-start justify-between gap-4 py-2 mt-3 pt-4 border-t" style="border-color:var(--border);">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Background blur</p>
                    <p class="text-xs mt-0.5 max-w-sm" style="color:var(--text-muted);">Turn off the blur on the background photo and glass panels. Handy if it bothers your eyes or your device runs it slowly.</p>
                </div>
                <x-toggle wire:click="$toggle('glassBlurEnabled')" :checked="$glassBlurEnabled" role="switch" aria-label="Background blur" />
            </div>

            @if ($siteThemeMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $siteThemeMessage }}</p>
            @endif
        </div>

        {{-- TONE PACK --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Tone Pack</p>
            <p class="text-xs mb-4" style="color:var(--text-muted);">
                Personalise the small phrases and comfort copy you see around the site.
                Other people never see your tone pack — it's just for you.
            </p>

            <div class="grid grid-cols-2 gap-2">
                @foreach ($this->tonePacks as $packKey => $pack)
                    @php $isPackLocked = $pack['locked'] ?? false; @endphp

                    @if ($isPackLocked)
                        <button
                            type="button"
                            @click="lockedPackMessage = 'tone-{{ $packKey }}'"
                            class="p-3 rounded-xl border-2 text-left relative"
                            style="background:var(--bg);border-color:var(--surface-raised);opacity:0.5;cursor:pointer;"
                        >
                            <span style="position:absolute;top:0.5rem;right:0.5rem;background:var(--accent-amber);color:var(--bg);border-radius:var(--radius-pill);padding:0.125rem 0.5rem;font-size:0.6875rem;font-weight:600;">Supporter</span>
                            <p class="text-xs font-medium pr-14" style="color:var(--text-faint);">{{ $pack['label'] }}</p>
                            <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-faint);">{{ $pack['phrases'][0] ?? '' }}</p>
                            <div x-show="lockedPackMessage === 'tone-{{ $packKey }}'" x-cloak style="margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--border);">
                                <p class="text-xs" style="color:var(--text-muted);">This tone pack is available to CommonGrove Supporters. <a href="{{ route('support') }}" wire:navigate style="color:var(--accent);" @click.stop>Upgrade to unlock all packs.</a></p>
                            </div>
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="$set('tonePackKey', '{{ $packKey }}')"
                            class="p-3 rounded-xl border-2 text-left transition"
                            style="background:var(--bg);{{ $tonePackKey === $packKey ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"
                            onmouseover="if('{{ $tonePackKey }}' !== '{{ $packKey }}') this.style.borderColor='var(--text-faint)';" onmouseout="if('{{ $tonePackKey }}' !== '{{ $packKey }}') this.style.borderColor='var(--border)';"
                        >
                            <p class="text-xs font-medium" style="color:var(--text);">{{ $pack['label'] }}</p>
                            <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-faint);">{{ $pack['phrases'][0] ?? '' }}</p>
                        </button>
                    @endif
                @endforeach
            </div>

            @if ($tonePackMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $tonePackMessage }}</p>
            @endif
        </div>

        {{-- COMFORT PROMPTS --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Comfort Prompts</p>
            <p class="text-xs mb-4" style="color:var(--text-muted);">
                Optional conversation starters that appear gently inside rooms. Only you control which packs are shown.
            </p>

            <div class="flex items-start justify-between gap-4 py-2 mb-3">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show conversation prompts</p>
                    <p class="text-xs mt-0.5 max-w-sm" style="color:var(--text-muted);">A gentle prompt card appears at the top of rooms. Dismiss it any time.</p>
                </div>
                <x-toggle wire:click="$toggle('showConversationPrompts')" :checked="$showConversationPrompts" role="switch" aria-label="Show conversation prompts" />
            </div>

            <div class="grid grid-cols-2 gap-2">
                @foreach ($this->promptPacks as $packKey => $pack)
                    @php $isPackLocked = $pack['locked'] ?? false; $isPackEnabled = in_array($packKey, $enabledPromptPacks, true); @endphp

                    @if ($isPackLocked)
                        <button
                            type="button"
                            @click="lockedPackMessage = 'prompt-{{ $packKey }}'"
                            class="p-3 rounded-xl border-2 text-left relative"
                            style="background:var(--bg);border-color:var(--surface-raised);opacity:0.5;cursor:pointer;"
                        >
                            <span style="position:absolute;top:0.5rem;right:0.5rem;background:var(--accent-amber);color:var(--bg);border-radius:var(--radius-pill);padding:0.125rem 0.5rem;font-size:0.6875rem;font-weight:600;">Supporter</span>
                            <p class="text-xs font-medium pr-14" style="color:var(--text-faint);">{{ $pack['label'] }}</p>
                            <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-faint);">{{ $pack['description'] ?? '' }}</p>
                            <div x-show="lockedPackMessage === 'prompt-{{ $packKey }}'" x-cloak style="margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--border);">
                                <p class="text-xs" style="color:var(--text-muted);">This prompt pack is available to CommonGrove Supporters. <a href="{{ route('support') }}" wire:navigate style="color:var(--accent);" @click.stop>Upgrade to unlock all packs.</a></p>
                            </div>
                        </button>
                    @else
                        <button type="button" wire:click="togglePromptPack('{{ $packKey }}')"
                            class="p-3 rounded-xl border-2 text-left transition"
                            style="background:var(--bg);{{ $isPackEnabled ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"
                            onmouseover="this.style.borderColor='{{ $isPackEnabled ? 'var(--accent)' : 'var(--text-faint)' }}';" onmouseout="this.style.borderColor='{{ $isPackEnabled ? 'var(--accent)' : 'var(--border)' }}';"
                        >
                            <p class="text-xs font-medium" style="color:var(--text);">{{ $pack['label'] }}</p>
                            <p class="text-xs mt-0.5 leading-snug" style="color:var(--text-faint);">{{ $pack['description'] ?? '' }}</p>
                        </button>
                    @endif
                @endforeach
            </div>

            @if ($promptPacksMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $promptPacksMessage }}</p>
            @endif
        </div>

        {{-- APPEARANCE --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Appearance</p>

            <div class="flex items-start justify-between gap-4 py-2">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show birthday theme</p>
                    <p class="text-xs mt-0.5" style="color:var(--text-muted);">We'll add a small private birthday touch on your birthday. Only you see it.</p>
                </div>
                <x-toggle wire:click="$toggle('birthdayThemeEnabled')" :checked="$birthdayThemeEnabled" role="switch" aria-label="Show birthday theme" />
            </div>

            <div class="flex items-start justify-between gap-4 py-2 mt-2 pt-4 border-t" style="border-color:var(--border);">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show holiday atmospheres</p>
                    <p class="text-xs mt-0.5 leading-relaxed max-w-sm" style="color:var(--text-muted);">We'll add small seasonal touches on certain days. You can turn this off anytime.</p>
                </div>
                <x-toggle wire:click="$toggle('holidayThemesEnabled')" :checked="$holidayThemesEnabled" role="switch" aria-label="Show holiday atmospheres" />
            </div>

            @if ($appearanceMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $appearanceMessage }}</p>
            @endif
        </div>

        {{-- COMFORT LATELY --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Comfort Lately</p>
            <p class="text-xs mb-4" style="color:var(--text-muted);">Small things you've been spending time with lately. Each field is optional, max 60 characters.</p>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Something you keep returning to</label>
                    <x-input type="text" wire:model="currentlyPlaying" maxlength="60"
                        placeholder="e.g. a game, book, show, hobby…"
                        :error="$errors->first('currentlyPlaying')"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Something comforting lately</label>
                    <x-input type="text" wire:model="currentlyReading" maxlength="60"
                        placeholder="e.g. rainy walks, coffee, Minecraft…"
                        :error="$errors->first('currentlyReading')"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--text-muted);">Something living in your head</label>
                    <x-input type="text" wire:model="currentlyWatching" maxlength="60"
                        placeholder="e.g. a song, story, idea, character…"
                        :error="$errors->first('currentlyWatching')"
                    />
                </div>
            </div>

            @if ($currentlyMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $currentlyMessage }}</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 3 — Comfort
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'comfort'" x-cloak>
        <h2 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:0.5rem;">Comfort Settings</h2>
        <p style="font-size:0.9375rem;color:var(--text-muted);margin-bottom:1.5rem;">Make CommonGrove feel right for you. None of these affect what others see.</p>

        {{-- DISCOVERY --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Discovery</p>

            <div class="flex items-start justify-between gap-4 py-2">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show connection suggestions</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:var(--text-muted);">
                        We'll suggest rooms and people based on your interests.<br>
                        You can turn this off anytime.
                    </p>
                </div>
                <x-toggle wire:click="$toggle('showConnectionSuggestions')" :checked="$showConnectionSuggestions" role="switch" aria-label="Show connection suggestions" class="flex-none" />
            </div>

            <div class="flex items-start justify-between gap-4 py-2 mt-2 pt-4 border-t" style="border-color:var(--border);">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show CommonGrove starter rooms</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:var(--text-muted);">
                        We'll show a few always-open rooms made by CommonGrove.<br>
                        You can hide them anytime.
                    </p>
                </div>
                <x-toggle wire:click="$toggle('showOfficialRooms')" :checked="$showOfficialRooms" role="switch" aria-label="Show CommonGrove starter rooms" />
            </div>

            @if ($discoveryMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $discoveryMessage }}</p>
            @endif
            @if ($officialRoomsMessage)
                <p class="text-sm mt-1" style="color:var(--accent);">{{ $officialRoomsMessage }}</p>
            @endif
        </div>

        {{-- COMFORT --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Comfort</p>

            <div class="flex items-start justify-between gap-4 py-2">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Low-stimulation mode</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:var(--text-muted);">
                        Hides suggestion previews and subtle visual effects.<br>
                        Keeps all core features — rooms, messages, safety tools.
                    </p>
                </div>
                <x-toggle wire:click="$toggle('lowStimulationMode')" :checked="$lowStimulationMode" role="switch" aria-label="Low-stimulation mode" />
            </div>

            <div class="flex items-start justify-between gap-4 py-3 mt-2 pt-4 border-t" style="border-color:var(--border);">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Hide message reactions</p>
                    <p class="text-xs mt-1 leading-relaxed max-w-sm" style="color:var(--text-muted);">
                        Hides emoji reactions on messages — yours and everyone else's.<br>
                        You can still send messages normally.
                    </p>
                </div>
                <x-toggle wire:click="$toggle('hideReactions')" :checked="$hideReactions" role="switch" aria-label="Hide message reactions" />
            </div>

            @if ($comfortMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $comfortMessage }}</p>
            @endif
            @if ($hideReactionsMessage)
                <p class="text-sm mt-1" style="color:var(--accent);">{{ $hideReactionsMessage }}</p>
            @endif
        </div>

        {{-- ADVANCED COMFORT — section itself always visible; individual
             toggles remain supporter-gated server-side (toggleAdvancedComfort()/
             saveAdvancedComfort() untouched), so the locked visual state is
             preserved here rather than removed. --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <div class="flex items-center gap-3 mb-1">
                <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);">Advanced Comfort</p>
                @if (! $this->userIsSupporter)
                    <a href="{{ route('support') }}" wire:navigate class="text-xs transition" style="color:var(--accent);" onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'">Supporter feature</a>
                @endif
            </div>
            <p class="text-xs mb-4 leading-relaxed max-w-prose" style="color:var(--text-muted);">Extra control over how calm and minimal the interface feels. None of these affect other people.</p>

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
                    <div class="flex items-start justify-between gap-4 py-2.5 border-b" style="border-color:var(--bg);">
                        <div>
                            <p class="text-sm font-medium" style="color:var(--text);">{{ $option['label'] }}</p>
                            <p class="text-xs mt-0.5" style="color:var(--text-muted);">{{ $option['desc'] }}</p>
                        </div>
                        @if ($this->userIsSupporter)
                            <x-toggle
                                wire:click="toggleAdvancedComfort('{{ $settingKey }}')"
                                :checked="$isOn"
                                role="switch"
                                aria-label="{{ $option['label'] }}"
                            />
                        @else
                            <x-toggle
                                :checked="$isOn"
                                role="switch"
                                aria-label="{{ $option['label'] }}"
                            />
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($advancedComfortMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $advancedComfortMessage }}</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 4 — Notifications
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'notifications'" x-cloak>
        <h2 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:0.5rem;">Notifications</h2>
        <p style="font-size:0.9375rem;color:var(--text-muted);margin-bottom:1.5rem;">Control when and how CommonGrove reaches out to you.</p>

        @if (auth()->user()->notificationPreferences)
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
                <livewire:profile.notification-preferences />
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 5 — Account
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'account'" x-cloak>
        <h2 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:1.25rem;">Account</h2>

        {{-- PASSWORD --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.5rem;">Password</p>
            <p class="text-xs mb-3" style="color:var(--text-muted);">
                We'll email you a link to set a new password.
            </p>

            <x-button type="button" wire:click="sendPasswordResetLink" wire:loading.attr="disabled" wire:target="sendPasswordResetLink"
                data-no-dirty
                variant="secondary" class="!px-5 !py-2"
            >
                <span wire:loading.remove wire:target="sendPasswordResetLink">Change password</span>
                <span wire:loading wire:target="sendPasswordResetLink">Sending…</span>
            </x-button>

            @if ($passwordResetMessage)
                <p class="text-sm mt-3" style="color:{{ str_starts_with($passwordResetMessage, 'Check your email') ? 'var(--accent)' : 'var(--danger)' }};">
                    {{ $passwordResetMessage }}
                </p>
            @endif
        </div>

        {{-- READ RECEIPTS --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Read Receipts</p>

            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="text-sm font-medium" style="color:var(--text);">Show read receipts</p>
                    <p class="text-xs max-w-sm" style="color:var(--text-muted);">
                        When both you and the other person have this on, you can both see when messages are read.
                    </p>
                </div>
                <x-toggle wire:click="$toggle('showReadReceipts')" :checked="$showReadReceipts" role="switch" aria-label="Show read receipts" />
            </div>

            @if ($readReceiptsMessage)
                <p class="text-sm mt-3" style="color:var(--accent);">{{ $readReceiptsMessage }}</p>
            @endif
        </div>

        {{-- SUPPORTER — summary only; full management (icon toggle, cancel
             subscription) lives on its own page, untouched. --}}
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem;">
            <p style="font-family:var(--font-body);font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;">Supporter</p>

            @if (auth()->user()->is_supporter)
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium" style="background:rgb(var(--accent-rgb) / 0.1);color:var(--accent);border:1px solid rgb(var(--accent-rgb) / 0.25);">
                        <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 14s-6-4.35-6-8A3.5 3.5 0 0 1 8 3.55 3.5 3.5 0 0 1 14 6c0 3.65-6 8-6 8z"/></svg>
                        CommonGrove Supporter
                    </span>
                </div>
                <a href="{{ route('settings.supporter') }}" wire:navigate style="font-size:0.875rem;color:var(--accent);">Manage subscription →</a>
            @else
                <p class="text-sm mb-3" style="color:var(--text-muted);">Support CommonGrove and unlock more personalisation options.</p>
                <x-button :href="route('support')" wire:navigate variant="primary">Learn about supporting CommonGrove</x-button>
            @endif
        </div>
    </div>

    {{-- ── Report a problem — always visible, not tab-scoped ────────────── --}}
    <div class="pt-2 text-center">
        <a
            href="{{ route('report') }}"
            wire:navigate
            class="text-xs transition"
            style="color:var(--text-faint);"
            onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
        >Report a problem</a>
    </div>

    {{-- ── Sticky save bar ─────────────────────────────────────────────────── --}}
    <div
        x-show="dirty || saved"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="cg-settings-savebar"
        data-no-dirty
    >
        <p class="text-sm flex-1">
            <span x-show="saved && !dirty" style="color:var(--accent);">Changes saved.</span>
            <span x-show="dirty" style="color:var(--text-muted);">Unsaved changes</span>
        </p>
        <div class="flex items-center gap-3 flex-none" x-show="dirty">
            <button
                type="button"
                @click="window.location.reload()"
                class="text-sm transition"
                style="color:var(--text-muted);"
                onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
            >Discard</button>
            <x-button
                type="button"
                wire:click="saveAll"
                wire:loading.attr="disabled"
                wire:target="saveAll"
                data-no-dirty
                variant="primary"
            >
                <span wire:loading.remove wire:target="saveAll">Save settings</span>
                <span wire:loading wire:target="saveAll">Saving…</span>
            </x-button>
        </div>
    </div>

</div>
