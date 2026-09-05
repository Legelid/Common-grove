<div class="relative" style="min-height:100vh;">
    @php
        $atmosphereGradients = [
            'night_rain'    => 'linear-gradient(160deg, #163055 0%, #0d1f3a 60%, #0a1828 100%)',
            'forest'        => 'linear-gradient(160deg, #0d3318 0%, #062210 60%, #041a0c 100%)',
            'cozy_room'     => 'radial-gradient(ellipse at bottom right, #5c2a00 0%, #3c1c00 60%, #2a1200 100%)',
            'pixel_sky'     => 'linear-gradient(180deg, #1a2070 0%, #0e1452 60%, #0a1040 100%)',
            'aquarium'      => 'radial-gradient(ellipse at center, #003d5c 0%, #002840 60%, #001e2e 100%)',
            'snowfall'      => 'linear-gradient(160deg, #2a3a60 0%, #1e2a48 60%, #141e38 100%)',
            'sunset_fog'    => 'linear-gradient(160deg, #4a1a44 0%, #300e2c 60%, #200820 100%)',
            'moonlight'     => 'radial-gradient(ellipse at top right, #1a1a40 0%, #10102a 60%, #080814 100%)',
            'coffee_shop'   => 'radial-gradient(ellipse at bottom, #3c2a10 0%, #281a08 60%, #1c1004 100%)',
            'soft_abstract' => 'linear-gradient(135deg, #24183c 0%, #1a2030 60%, #101820 100%)',
            'deep_blue'     => 'linear-gradient(180deg, #042060 0%, #031840 60%, #021028 100%)',
            'warm_lamp'     => 'radial-gradient(ellipse at top left, #3c2800 0%, #281a00 60%, #1a1000 100%)',
        ];
        $atmosphere         = $atmosphereGradients[$profileUser->banner_style ?? ''] ?? null;
        $lowStim            = auth()->user()->low_stimulation_mode;
        $atmosphereOpacity  = $atmosphere ? ($lowStim ? '0.06' : '0.22') : '0';
        $styleLabels        = [
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
        $openToLabels = [
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
    @endphp

    {{-- Atmospheric background overlay --}}
    @if ($atmosphere && $atmosphereOpacity !== '0')
        <div class="fixed inset-0 pointer-events-none" style="background:{{ $atmosphere }};opacity:{{ $atmosphereOpacity }};z-index:0;" aria-hidden="true"></div>
    @endif

    <div class="relative z-10 px-6 py-10 max-w-lg mx-auto space-y-6">

        {{-- Avatar + name --}}
        <div class="flex items-center gap-5">
            @if (auth()->id() === $profileUser->id)
                <div class="relative flex-none" style="width:5rem;height:5rem;">
                    <x-avatar :user="$profileUser" size="xl" />
                    <a href="{{ route('profile.edit') }}#avatar" wire:navigate
                       class="absolute inset-0 rounded-full flex items-center justify-center"
                       style="background:rgba(0,0,0,0.55);opacity:0;transition:opacity 0.15s;"
                       onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'"
                       onfocus="this.style.opacity='1'" onblur="this.style.opacity='0'"
                       aria-label="Change profile photo"
                    >
                        <span class="text-xs font-medium" style="color:var(--text);">Change photo</span>
                    </a>
                </div>
            @else
                <x-avatar :user="$profileUser" size="xl" />
            @endif
            <div class="min-w-0">
                <h1 class="text-xl font-bold inline-flex items-center gap-1.5" style="color:var(--text);">
                    <span class="truncate">{{ $visibleName }}</span>
                    @if ($profileUser->is_supporter && ($profileUser->show_supporter_icon ?? true))<x-supporter-icon />@endif
                </h1>

                {{-- Tagline --}}
                @if ($profileUser->profile_status)
                    <p class="text-xs leading-snug mt-0.5" style="color:var(--text-muted);">{{ $profileUser->profile_status }}</p>
                @endif

                <p class="mt-1 flex items-center gap-1.5 text-xs font-medium" style="color:{{ $profileUser->isOnline() ? 'var(--accent)' : 'var(--text-muted)' }};">
                    @if ($profileUser->isOnline())
                        <span class="w-2 h-2 rounded-full inline-block flex-none" style="background:var(--accent);"></span>
                        Online now
                    @else
                        Last seen {{ $profileUser->last_seen_at?->diffForHumans() ?? 'a while ago' }}
                    @endif
                </p>

                {{-- FirstRoots badge --}}
                @if ($profileUser->is_first_roots)
                    <div class="mt-2 inline-flex items-center gap-1.5">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a7 7 0 0 1 7 7c0 4-3.5 8-7 11C8.5 17 5 13 5 9a7 7 0 0 1 7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                        <span class="text-xs font-semibold" style="color:var(--accent);">FirstRoots</span>
                        <span class="text-xs" style="color:var(--text-faint);">·</span>
                        <span class="text-xs" style="color:var(--text-faint);">Founding Member · Beta {{ $profileUser->first_roots_awarded_at?->year ?? now()->year }}</span>
                    </div>
                @endif

                {{-- Status (Group 2) --}}
                @if ($profileUser->hasActiveStatus())
                    <p class="mt-1 text-xs" style="color:var(--text-muted);">
                        @if ($profileUser->status_mood)
                            <span class="capitalize">{{ $profileUser->status_mood }}</span>
                            @if ($profileUser->status_text) · @endif
                        @endif
                        {{ $profileUser->status_text }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Social style indicators --}}
        @if (!empty($profileUser->social_styles))
            <div class="flex flex-wrap gap-1.5">
                @foreach ($profileUser->social_styles as $style)
                    @if (isset($styleLabels[$style]))
                        <span class="px-2.5 py-1 rounded-full text-xs" style="background:var(--surface);color:var(--text-muted);border:1px solid var(--surface-raised);">{{ $styleLabels[$style] }}</span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Bio --}}
        @if ($profileUser->bio)
            <p class="text-sm leading-relaxed" style="color:var(--text);">{{ $profileUser->bio }}</p>
        @endif

        {{-- Currently Into + comfort things --}}
        @php
            $hasCurrently = $profileUser->currently_playing || $profileUser->currently_reading || $profileUser->currently_watching;
            $hasComfort   = !empty($profileUser->comfort_things);
        @endphp
        @if ($hasCurrently || $hasComfort)
            <div class="rounded-xl border p-4 space-y-1.5" style="background:var(--surface);border-color:var(--border);">
                @if ($hasCurrently)
                    <h2 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted);">Comfort lately</h2>
                    @if ($profileUser->currently_playing)
                        <p class="text-sm" style="color:var(--text);"><span style="color:var(--text-muted);">Returning to</span> · {{ $profileUser->currently_playing }}</p>
                    @endif
                    @if ($profileUser->currently_reading)
                        <p class="text-sm" style="color:var(--text);"><span style="color:var(--text-muted);">Comforting lately</span> · {{ $profileUser->currently_reading }}</p>
                    @endif
                    @if ($profileUser->currently_watching)
                        <p class="text-sm" style="color:var(--text);"><span style="color:var(--text-muted);">On my mind</span> · {{ $profileUser->currently_watching }}</p>
                    @endif
                @endif

                @if ($hasComfort)
                    @if ($hasCurrently)
                        <div class="pt-2 border-t" style="border-color:var(--surface-raised);"></div>
                    @endif
                    @foreach ($profileUser->comfort_things as $thing)
                        @if (!empty(trim($thing['value'] ?? '')))
                            <p class="text-sm" style="color:var(--text);">
                                @if (!empty(trim($thing['label'] ?? '')))
                                    <span style="color:var(--text-muted);">{{ $thing['label'] }}</span> ·
                                @endif
                                {{ $thing['value'] }}
                            </p>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif

        {{-- Open To --}}
        @if (!empty($profileUser->open_to))
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted);">Open To</h2>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($profileUser->open_to as $option)
                        @if (isset($openToLabels[$option]))
                            <span class="px-2.5 py-1 rounded-full text-xs" style="background:rgba(74,139,181,0.08);color:#4A8BB5;border:1px solid rgba(74,139,181,0.2);">{{ $openToLabels[$option] }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Shared interests --}}
        @php $tags = $this->profileTags->take(8); @endphp
        @if ($tags->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Interests</h2>
                    @if ($this->sharedTagCount > 0 && !$profileUser->is(auth()->user()))
                        <span class="text-xs" style="color:var(--accent);">{{ $this->sharedTagCount }} {{ Str::plural('interest', $this->sharedTagCount) }} in common</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $item)
                        <a
                            href="{{ route('feed') }}?tag={{ $item['tag']->id }}"
                            wire:navigate
                            class="px-3 py-1 rounded-full text-xs transition"
                            style="{{ ($item['is_shared'] && !$profileUser->is(auth()->user())) ? 'background:rgba(var(--accent-rgb),0.15);color:var(--accent);outline:1px solid var(--accent);' : 'background:var(--surface);color:var(--text-muted);' }}"
                            onmouseover="this.style.color='var(--text)'" onmouseout=""
                        >{{ $item['tag']->name }}</a>
                    @endforeach
                    @if ($this->profileTags->count() > 8)
                        <span class="px-3 py-1 text-xs" style="color:var(--text-muted);">+{{ $this->profileTags->count() - 8 }} more</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Usually found in --}}
        @if ($this->usualRooms->isNotEmpty())
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted);">Usually Found In</h2>
                <ul class="space-y-1">
                    @foreach ($this->usualRooms as $room)
                        <li class="text-sm flex items-center gap-2" style="color:var(--text);">
                            <span style="color:var(--text-muted);">·</span>
                            {{ $room->title }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Action flash --}}
        @if ($actionFlash)
            <div class="rounded-lg border px-4 py-3 text-sm" style="background:var(--surface-raised);border-color:var(--border);color:var(--text);">
                {{ $actionFlash }}
            </div>
        @endif

        {{-- Primary actions --}}
        @if (!$profileUser->is(auth()->user()))
            <div class="flex flex-wrap gap-3 pt-2">
                <a
                    href="{{ route('feed') }}"
                    wire:navigate
                    class="px-5 py-2.5 text-sm font-semibold rounded-lg transition"
                    style="background:var(--surface-raised);color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                >Back to feed</a>

                @php $friendState = $this->friendshipState; @endphp
                @if ($friendState === 'none')
                    <x-button type="button" wire:click="sendFriendRequest" variant="secondary" class="!px-4">Send friend request</x-button>
                @elseif ($friendState === 'pending_sent')
                    <button type="button" wire:click="cancelFriendRequest"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);"
                    >Request sent · Cancel</button>
                @elseif ($friendState === 'pending_received')
                    <x-button type="button" wire:click="acceptFriendRequest" variant="primary" class="!px-4">Accept friend request</x-button>
                @elseif ($friendState === 'friends')
                    <button type="button" wire:click="unfriend"
                        wire:confirm="Remove {{ $profileUser->gamertag }} from your friends?"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);"
                    >Friends · Unfriend</button>
                @endif

                @if ($this->isBlocked)
                    <button type="button" wire:click="unblock"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);">Unblock</button>
                @else
                    <button type="button" wire:click="block"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                    >Block</button>
                @endif

                @if ($this->isMuted)
                    <button type="button" wire:click="unmute"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);">Unmute</button>
                @else
                    <button type="button" wire:click="mute"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:var(--surface-raised);color:var(--text-muted);">Mute</button>
                @endif

                <button
                    type="button"
                    x-on:click="$dispatch('open-report-modal', { reportedUserId: '{{ $profileUser->id }}', reportableType: 'App\\\\Models\\\\User', reportableId: '{{ $profileUser->id }}' })"
                    class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                    style="background:var(--surface-raised);color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                >Report</button>
            </div>
        @else
            <div class="pt-2 flex flex-wrap gap-3">
                <a href="{{ route('profile.edit') }}" wire:navigate
                    class="px-5 py-2.5 text-sm font-semibold rounded-lg transition inline-block"
                    style="background:var(--surface);color:var(--text);border:1px solid var(--border);"
                    onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border)'"
                >Edit profile</a>
                <a href="{{ route('feed') }}" wire:navigate
                    class="px-5 py-2.5 text-sm font-semibold rounded-lg transition inline-block"
                    style="background:var(--surface-raised);color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                >Back to feed</a>
            </div>
        @endif

        {{-- Report modal --}}
        <livewire:safety.report-user />

    </div>
</div>
