<div>
    @php
        $accentMap = [
            'green'  => '#1D9E75',
            'blue'   => '#4A8BB5',
            'amber'  => '#D29922',
            'purple' => '#8B5CF6',
            'slate'  => '#8B949E',
        ];
        $bannerGradients = [
            'rain_window' => 'linear-gradient(135deg, #1a1f2e 0%, #252b3a 60%, #1e2332 100%)',
            'forest'      => 'linear-gradient(135deg, #0d1f17 0%, #132a1e 60%, #0f2218 100%)',
            'night_sky'   => 'linear-gradient(135deg, #0a0d1a 0%, #141829 60%, #0d1020 100%)',
            'cozy_room'   => 'linear-gradient(135deg, #1f1510 0%, #2a1c14 60%, #201410 100%)',
            'gradient'    => 'linear-gradient(135deg, #1a1326 0%, #1e1c2e 60%, #13192a 100%)',
        ];
        $accentColor    = $accentMap[$profileUser->accent_color ?? ''] ?? null;
        $bannerGradient = $bannerGradients[$profileUser->banner_style ?? ''] ?? null;
        $styleLabels    = [
            'quiet_chatter'      => 'Quiet chatter',
            'mostly_listening'   => 'Mostly listening',
            'slow_replies'       => 'Slow replies OK',
            'deep_talks'         => 'Deep talks',
            'late_night'         => 'Late-night person',
            'introvert_friendly' => 'Introvert-friendly',
        ];
    @endphp

    {{-- Banner strip --}}
    @if ($bannerGradient)
        <div class="max-w-lg mx-auto h-14 rounded-t-xl" style="background: {{ $bannerGradient }};"></div>
    @endif

    <div class="px-6 max-w-lg mx-auto space-y-6" style="padding-top: {{ $bannerGradient ? '1.5rem' : '2.5rem' }}; padding-bottom: 2.5rem;">

        {{-- Avatar + name --}}
        <div class="flex items-center gap-5">
            <img
                src="{{ $profileUser->avatar_url }}"
                alt="{{ $profileUser->gamertag }}"
                class="w-20 h-20 rounded-full object-cover ring-2"
                style="background:#1C2333;ring-color:{{ $accentColor ?? '#30363D' }};"
            >
            <div>
                <h1 class="text-xl font-bold" style="color:#E6EDF3;">{{ $visibleName }}</h1>

                {{-- Profile tagline --}}
                @if ($profileUser->profile_status)
                    <p class="text-xs leading-relaxed" style="color:#6B7790;margin-top:2px;">{{ $profileUser->profile_status }}</p>
                @endif

                <p class="mt-1 flex items-center gap-1.5 text-xs font-medium" style="color:{{ $profileUser->isOnline() ? '#1D9E75' : '#8B949E' }};">
                    @if ($profileUser->isOnline())
                        <span class="w-2 h-2 rounded-full inline-block" style="background:#1D9E75;"></span>
                        Online now
                    @else
                        Last seen {{ $profileUser->last_seen_at?->diffForHumans() ?? 'a while ago' }}
                    @endif
                </p>

                {{-- Status (Group 2) --}}
                @if ($profileUser->hasActiveStatus())
                    <p class="mt-1 text-xs" style="color:#8B949E;">
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
                        <span class="px-2.5 py-1 rounded-full text-xs" style="background:#1C2333;color:#8B949E;border:1px solid #21262D;">{{ $styleLabels[$style] }}</span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Bio --}}
        @if ($profileUser->bio)
            <p class="text-sm leading-relaxed" style="color:#E6EDF3;">{{ $profileUser->bio }}</p>
        @endif

        {{-- Currently Into + conversation prompts --}}
        @php
            $hasCurrently = $profileUser->currently_playing || $profileUser->currently_reading || $profileUser->currently_watching;
            $hasPrompts   = $profileUser->prompt_comfort_thing || $profileUser->prompt_ramble_topic;
        @endphp
        @if ($hasCurrently || $hasPrompts)
            <div class="rounded-xl border p-4 space-y-1.5" style="background:#161B22;border-color:#30363D;">
                @if ($hasCurrently)
                    <h2 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#8B949E;">Currently Into</h2>
                    @if ($profileUser->currently_playing)
                        <p class="text-sm" style="color:#E6EDF3;"><span style="color:#8B949E;">Playing:</span> {{ $profileUser->currently_playing }}</p>
                    @endif
                    @if ($profileUser->currently_reading)
                        <p class="text-sm" style="color:#E6EDF3;"><span style="color:#8B949E;">Reading:</span> {{ $profileUser->currently_reading }}</p>
                    @endif
                    @if ($profileUser->currently_watching)
                        <p class="text-sm" style="color:#E6EDF3;"><span style="color:#8B949E;">Watching:</span> {{ $profileUser->currently_watching }}</p>
                    @endif
                @endif
                @if ($hasPrompts)
                    @if ($hasCurrently)
                        <div class="pt-1.5 border-t" style="border-color:#21262D;"></div>
                    @endif
                    @if ($profileUser->prompt_comfort_thing)
                        <p class="text-sm" style="color:#E6EDF3;"><span style="color:#8B949E;">Comfort thing:</span> {{ $profileUser->prompt_comfort_thing }}</p>
                    @endif
                    @if ($profileUser->prompt_ramble_topic)
                        <p class="text-sm" style="color:#E6EDF3;"><span style="color:#8B949E;">Could talk about:</span> {{ $profileUser->prompt_ramble_topic }}</p>
                    @endif
                @endif
            </div>
        @endif

        {{-- Shared interests (Group 4) --}}
        @if ($this->profileTags->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xs font-semibold uppercase tracking-wider" style="color:#8B949E;">Interests</h2>
                    @if ($this->sharedTagCount > 0 && !$profileUser->is(auth()->user()))
                        <span class="text-xs" style="color:#1D9E75;">{{ $this->sharedTagCount }} {{ Str::plural('interest', $this->sharedTagCount) }} in common</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->profileTags as $item)
                        <a
                            href="{{ route('feed') }}?tag={{ $item['tag']->id }}"
                            wire:navigate
                            class="px-3 py-1 rounded-full text-xs transition tag--shared"
                            style="{{ ($item['is_shared'] && !$profileUser->is(auth()->user())) ? 'background:rgba(29,158,117,0.15);color:#1D9E75;outline:1px solid #1D9E75;' : 'background:#1C2333;color:#8B949E;' }}"
                            onmouseover="this.style.color='#E6EDF3'" onmouseout=""
                        >{{ $item['tag']->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Action flash --}}
        @if ($actionFlash)
            <div class="rounded-lg border px-4 py-3 text-sm" style="background:#21262D;border-color:#30363D;color:#E6EDF3;">
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
                    style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                >Back to feed</a>

                {{-- Friend request (Group 1) --}}
                @php $friendState = $this->friendshipState; @endphp
                @if ($friendState === 'none')
                    <button type="button" wire:click="sendFriendRequest"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:rgba(29,158,117,0.2);color:#1D9E75;border:1px solid #1D9E75;"
                        onmouseover="this.style.background='#1D9E75';this.style.color='#fff'" onmouseout="this.style.background='rgba(29,158,117,0.2)';this.style.color='#1D9E75'"
                    >Send friend request</button>
                @elseif ($friendState === 'pending_sent')
                    <button type="button" wire:click="cancelFriendRequest"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;"
                    >Request sent · Cancel</button>
                @elseif ($friendState === 'pending_received')
                    <button type="button" wire:click="acceptFriendRequest"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                    >Accept friend request</button>
                @elseif ($friendState === 'friends')
                    <button type="button" wire:click="unfriend"
                        wire:confirm="Remove {{ $profileUser->gamertag }} from your friends?"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;"
                    >Friends · Unfriend</button>
                @endif

                {{-- Block / Unblock --}}
                @if ($this->isBlocked)
                    <button type="button" wire:click="unblock"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;">Unblock</button>
                @else
                    <button type="button" wire:click="block"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                    >Block</button>
                @endif

                {{-- Mute / Unmute --}}
                @if ($this->isMuted)
                    <button type="button" wire:click="unmute"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;">Unmute</button>
                @else
                    <button type="button" wire:click="mute"
                        class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                        style="background:#21262D;color:#8B949E;">Mute</button>
                @endif

                {{-- Report --}}
                <button
                    type="button"
                    x-on:click="$dispatch('open-report-modal', { reportedUserId: '{{ $profileUser->id }}', reportableType: 'App\\\\Models\\\\User', reportableId: '{{ $profileUser->id }}' })"
                    class="px-4 py-2.5 text-sm font-semibold rounded-lg transition"
                    style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E24B4A'" onmouseout="this.style.color='#8B949E'"
                >Report</button>
            </div>
        @else
            <div class="pt-2">
                <a href="{{ route('feed') }}" wire:navigate
                    class="px-5 py-2.5 text-sm font-semibold rounded-lg transition inline-block"
                    style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                >Back to feed</a>
            </div>
        @endif

        {{-- Report modal --}}
        <livewire:safety.report-user />

    </div>
</div>
