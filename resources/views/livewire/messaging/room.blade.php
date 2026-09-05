{{--
    Outer wrapper is NOT the transformed element — the "similar rooms" panel
    below needs position:fixed to stay pinned to the true right edge of the
    viewport. A CSS transform on an ancestor creates a new containing block
    for position:fixed descendants, so if the panel lived inside the shifted
    chat card, it would slide along with it instead of staying put. Keeping
    them as siblings under this untransformed wrapper avoids that trap.
--}}
<div class="relative h-full" x-data="{ similarOpen: @entangle('showSimilarRoomsPanel') }">

<div class="flex flex-col h-full max-w-3xl w-full mx-auto cg-room-shift" :class="{ 'is-shifted': similarOpen }">

    @php $activeParticipants = $this->conversation->participants->filter(fn($p) => !$p->pivot->left_at); @endphp

    @php
        $isLowStim          = auth()->user()?->low_stimulation_mode;
        $cgAdvanced         = auth()->user()?->advanced_comfort_settings ?? [];
        $cgHideGradients    = in_array('hide_gradients', $cgAdvanced, true);
        $hideReactions      = auth()->check() && (bool) (auth()->user()?->hide_reactions ?? false);
        $roomGradientDef    = (!$isLowStim && !$cgHideGradients && $roomGradientTheme)
            ? config('gradients.' . $roomGradientTheme)
            : null;
        $chatAreaBg = $roomGradientDef
            ? 'background:' . $roomGradientDef['css'] . ';'
            : '';

        // Room type badge shown in the header's compact info dropdown.
        $noticeBadge = null;
        if ($this->conversation->hangoutPost) {
            $hangoutPost = $this->conversation->hangoutPost;
            if ($hangoutPost->is_official) {
                $noticeBadge = 'CommonGrove room';
            } elseif ($hangoutPost->is_persistent) {
                $noticeBadge = 'Always-open room';
            } else {
                $noticeBadge = 'Temporary hangout';
            }
        }
    @endphp

    {{-- Header --}}
    {{-- Glass UI fix: sits OUTSIDE .cg-room-entry (below), at the same DOM
         level as the sitewide forest photo layer — same fix as the DM header.
         Nested inside .cg-room-entry it was blurring that div's own opaque
         gradient fill instead of the photo. "relative" is kept: the people /
         room-info / atmosphere dropdowns position themselves against this
         panel. --}}
    {{-- z-index:10 (explicit, not just position:relative): the header's own
         backdrop-filter gives it a stacking context, which traps the settings
         dropdown's z-index:50 inside it. .cg-chat-messages below is
         position:absolute (see the floating-prompt wrapper) so without this
         it wins the paint/click order over the header by DOM position alone,
         swallowing clicks on the dropdown. --}}
    <x-glass-panel tier="light" class="relative flex items-center gap-3 px-5 flex-none" style="height:56px;border-radius:var(--radius-lg) var(--radius-lg) 0 0;z-index:10;">

            {{-- LEFT: back + room name --}}
            <a href="{{ auth()->check() ? route('messages.index') : route('explore') }}" wire:navigate class="flex-none transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'" aria-label="Back">←</a>
            <h1 class="font-display flex-1 min-w-0 truncate" style="font-size:1rem;font-weight:600;color:var(--text);margin:0;">{{ $this->conversation->name ?? 'Hangout Room' }}</h1>

            {{-- CENTER: essential status only --}}
            @php
                $centerStatus      = null;
                $centerStatusColor = 'var(--accent-amber)';
                if ($this->conversation->hangoutPost && ! $this->conversation->hangoutPost->is_persistent) {
                    if ($this->conversation->hangoutPost->isExpired()) {
                        $centerStatus      = 'Ended';
                        $centerStatusColor = 'var(--text-faint)';
                    } elseif ($this->conversation->hangoutPost->expires_at && $this->conversation->hangoutPost->expires_at->diffInHours(now()) < 24) {
                        $centerStatus = 'Temporary · ' . $this->conversation->hangoutPost->expiresInFormatted() . ' remaining';
                    }
                }
            @endphp
            @if ($centerStatus)
                <p class="hidden sm:block flex-none text-xs font-medium" style="color:{{ $centerStatusColor }};">{{ $centerStatus }}</p>
            @endif

            {{-- RIGHT: single settings button — condenses what used to be 4
                 separate controls (people pill, info icon, "Leave quietly"
                 text, and the "..." overflow menu) that collided with the
                 nav pills above on narrower viewports. One dropdown now
                 holds the participant list, room info, and room actions. --}}
            <div class="relative flex-none" x-data="{ menuOpen: false }" @click.outside="menuOpen = false" @keydown.escape.window="menuOpen = false">
                <button
                    type="button"
                    @click="menuOpen = !menuOpen"
                    class="w-11 h-11 flex items-center justify-center rounded-lg transition flex-none"
                    style="color:var(--text-muted);"
                    onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                    aria-label="Room settings" aria-haspopup="true" :aria-expanded="menuOpen.toString()"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </button>

                {{-- Consolidated room menu: participants, room info, then actions --}}
                <div
                    x-show="menuOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    style="display:none;position:absolute;top:calc(100% + 8px);right:0;width:260px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);z-index:50;max-height:70vh;overflow-y:auto;"
                    role="menu"
                    aria-label="Room settings"
                >
                    <div class="p-3">
                        <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-faint);margin-bottom:0.5rem;">In this room</p>

                        {{--
                            Participant list. Clicking/focusing a name opens a preview
                            popover (Phase 6, Part D) rather than navigating directly —
                            "View profile" inside the popover is now the path to the
                            full profile page.
                        --}}
                        <div
                            x-data="{
                                previewUserId: @entangle('previewUserId'),
                                previewTrigger: null,
                                openPreview(event, id) {
                                    this.previewTrigger = event.currentTarget;
                                    this.previewUserId = id;
                                },
                                closePreview() {
                                    this.previewUserId = null;
                                    if (this.previewTrigger) this.previewTrigger.focus();
                                },
                                trapPreviewFocus(event) {
                                    const panel = this.$refs.previewPanel;
                                    if (!panel) return;
                                    const focusable = Array.from(panel.querySelectorAll('button, [href], input, [tabindex]:not([tabindex=\'-1\'])')).filter(el => !el.disabled && el.offsetParent !== null);
                                    if (!focusable.length) return;
                                    const first = focusable[0];
                                    const last = focusable[focusable.length - 1];
                                    if (event.shiftKey && document.activeElement === first) {
                                        event.preventDefault();
                                        last.focus();
                                    } else if (!event.shiftKey && document.activeElement === last) {
                                        event.preventDefault();
                                        first.focus();
                                    }
                                },
                            }"
                        >
                            @foreach ($activeParticipants as $participant)
                                @if (auth()->check())
                                    @php $recentlyActive = $participant->last_seen_at && $participant->last_seen_at->isAfter(now()->subMinutes(5)); @endphp
                                    <button
                                        type="button"
                                        @click="openPreview($event, '{{ $participant->id }}')"
                                        @focus="openPreview($event, '{{ $participant->id }}')"
                                        class="w-full flex items-center transition"
                                        style="gap:0.625rem;padding:0.5rem 0.375rem;border-radius:var(--radius-md);background:transparent;border:none;cursor:pointer;text-align:left;"
                                        onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"
                                    >
                                        <x-avatar :user="$participant" size="xs" />
                                        <x-user-name :user="$participant" class="truncate flex-1 min-w-0" style="font-size:0.875rem;color:var(--text);" />
                                        @if ($recentlyActive)
                                            <span style="width:6px;height:6px;border-radius:50%;background:var(--accent);margin-left:auto;flex:none;" aria-label="Recently active"></span>
                                        @endif
                                    </button>
                                @else
                                    <div class="flex items-center" style="gap:0.625rem;padding:0.5rem 0.375rem;">
                                        <span class="rounded-full flex-none" style="width:1.5rem;height:1.5rem;background:var(--surface-raised);"></span>
                                        <span class="truncate" style="font-size:0.875rem;color:var(--text-faint);">Someone</span>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Participant preview popover — content is fully server-computed
                                 and privacy-filtered (App\Services\InterestPrivacyService);
                                 the client only ever receives already-safe data. --}}
                            @if ($this->previewParticipant)
                                @php $preview = $this->previewParticipant; $pu = $preview['user']; @endphp
                                <div
                                    class="fixed inset-0 flex items-center justify-center px-6"
                                    style="z-index:60;"
                                    @click="closePreview()"
                                    @keydown.escape.window="closePreview()"
                                >
                                    <div class="absolute inset-0" style="background:rgba(0,0,0,0.6);"></div>
                                    <div
                                        class="relative w-full max-w-xs rounded-xl p-5"
                                        style="background:var(--surface);border:1px solid var(--border);box-shadow:0 8px 24px rgba(0,0,0,0.5);"
                                        @click.stop
                                        x-ref="previewPanel"
                                        role="dialog" aria-modal="true" aria-label="Preview of {{ $pu->display_name }}"
                                        @keydown.tab="trapPreviewFocus($event)"
                                    >
                                        <button type="button" @click="closePreview()" class="absolute top-2 right-2 w-11 h-11 flex items-center justify-center rounded-lg transition" style="color:var(--text-muted);" aria-label="Close preview">×</button>

                                        <div class="flex flex-col items-center text-center gap-1.5">
                                            <x-avatar :user="$pu" size="lg" />
                                            <x-user-name :user="$pu" class="text-base font-semibold mt-1" style="color:var(--text);" />
                                            <p class="text-xs" style="color:var(--text-faint);">{{ $preview['tenureLabel'] }}</p>

                                            @if ($pu->is_admin || $pu->isSupporter())
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    @if ($pu->is_admin)<x-grovekeeper-badge />@endif
                                                    @if ($pu->isSupporter())<x-supporter-icon />@endif
                                                </div>
                                            @endif

                                            @if ($preview['sharedInterests']->isNotEmpty())
                                                <div class="flex flex-wrap justify-center gap-1.5 mt-2">
                                                    @foreach ($preview['sharedInterests'] as $interestName)
                                                        <x-tag-bubble :label="$interestName" :selected="false" tabindex="-1" class="!text-xs !px-2.5 !py-1" />
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 mt-4">
                                            <x-button :href="route('profile.show', $pu->gamertag)" wire:navigate variant="secondary" class="flex-1 !text-sm justify-center">View profile</x-button>
                                            <x-button type="button" wire:click="sendFriendRequestFromPreview('{{ $pu->id }}')" variant="primary" class="flex-1 !text-sm justify-center">Stay connected</x-button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div style="height:1px;background:var(--border);margin:0.75rem 0;"></div>

                        {{-- Room info --}}
                        <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-faint);margin-bottom:0.5rem;">Room info</p>

                        @if ($noticeBadge)
                            <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium mb-2" style="background:rgba(var(--accent-rgb),0.1);color:var(--accent);border:1px solid rgba(var(--accent-rgb),0.2);">{{ $noticeBadge }}</span>
                        @endif

                        @php $host = $this->conversation->hangoutPost?->user ?? $this->conversation->creator; @endphp
                        @if ($host)
                            <div class="mb-2">
                                <p class="text-xs font-medium uppercase tracking-wider mb-1.5" style="color:var(--text-faint);letter-spacing:0.06em;">Host</p>
                                <div class="flex items-center gap-2 min-w-0">
                                    <x-avatar :user="$host" size="xs" />
                                    <x-user-name :user="$host" class="text-sm truncate" style="color:var(--text);" />
                                </div>
                            </div>
                        @endif

                        <div class="mb-2">
                            <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color:var(--text-faint);letter-spacing:0.06em;">Opened</p>
                            <p class="text-sm" style="color:var(--text-muted);">{{ $this->conversation->created_at->diffForHumans() }}</p>
                        </div>

                        @php
                            $infoTags      = $this->conversation->hangoutPost?->tags ?? collect();
                            $infoTagsShown = $infoTags->take(3);
                            $infoTagsExtra = $infoTags->count() - $infoTagsShown->count();
                        @endphp
                        @if ($infoTags->isNotEmpty())
                            <div class="mb-2">
                                <p class="text-xs font-medium uppercase tracking-wider mb-1.5" style="color:var(--text-faint);letter-spacing:0.06em;">Interests</p>
                                <p class="text-xs mb-1.5" style="color:var(--text-faint);">Tap one to find other rooms like it.</p>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @auth
                                        @foreach ($infoTagsShown as $tag)
                                            <x-tag-bubble
                                                :label="$tag->name"
                                                :selected="false"
                                                class="!text-xs !px-2.5 !py-1"
                                                wire:click="openSimilarRoomsPanel('{{ $tag->id }}', {{ \Illuminate\Support\Js::from($tag->name) }})"
                                                @click="menuOpen = false"
                                            />
                                        @endforeach
                                    @else
                                        @foreach ($infoTagsShown as $tag)
                                            <x-tag-bubble :label="$tag->name" :selected="false" tabindex="-1" class="!text-xs !px-2.5 !py-1" />
                                        @endforeach
                                    @endauth
                                    @if ($infoTagsExtra > 0)
                                        <span class="text-xs" style="color:var(--text-faint);">and {{ $infoTagsExtra }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (auth()->check() && auth()->user()->isSupporter())
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider mb-1.5" style="color:var(--text-faint);letter-spacing:0.06em;">Collections</p>
                                <livewire:rooms.add-to-collection :conversationId="$conversationId" :key="'atc-'.$conversationId" />
                            </div>
                        @endif
                    </div>

                    @auth
                        <div style="height:1px;background:var(--border);"></div>

                        <div class="py-1">
                            <button type="button" wire:click="leaveQuietly" wire:confirm="Leave this room quietly? No one will be notified." @click="menuOpen = false" class="w-full text-left flex items-center px-3 py-2 text-xs transition" style="color:var(--danger);" onmouseover="this.style.background='rgba(var(--danger-rgb),0.08)'" onmouseout="this.style.background='transparent'">Leave quietly</button>

                            <button type="button" wire:click="togglePin" @click="menuOpen = false" class="w-full text-left flex items-center px-3 py-2 text-xs transition" style="color:{{ $this->isRoomPinned ? 'var(--accent)' : 'var(--text-muted)' }};" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">{{ $this->isRoomPinned ? 'Unpin room' : 'Pin room' }}</button>

                            @if ($this->isRoomOwner)
                                <button type="button" @click="menuOpen = false; $wire.set('showGradientPicker', true)" class="w-full text-left flex items-center px-3 py-2 text-xs transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">Appearance</button>
                            @endif

                            @if ($this->canDeleteRoom)
                                <button type="button" wire:click="deleteRoom" wire:confirm="Delete this room? This will permanently remove it and all its messages for everyone." @click="menuOpen = false" class="w-full text-left flex items-center px-3 py-2 text-xs transition" style="color:var(--danger);" onmouseover="this.style.background='rgba(var(--danger-rgb),0.08)'" onmouseout="this.style.background='transparent'">Delete room</button>
                            @endif

                            <button
                                type="button"
                                @click="menuOpen = false; $dispatch('open-report-modal', { reportedUserId: '{{ $this->conversation->hangoutPost?->user_id ?? $this->conversation->created_by }}', reportableType: 'App\\\\Models\\\\HangoutPost', reportableId: '{{ $this->conversation->hangoutPost?->id }}' })"
                                class="w-full text-left flex items-center px-3 py-2 text-xs transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"
                            >Report room</button>
                        </div>
                    @endauth
                </div>
            </div>

            @if ($roomPinMessage)
                <p class="absolute right-5 top-full mt-1 text-xs" style="color:var(--accent-amber);">{{ $roomPinMessage }}</p>
            @endif

            {{-- Room appearance panel (owner / admin only) — trigger lives in the settings menu above, entangled to showGradientPicker --}}
            @if ($this->isRoomOwner)
                <div
                    x-data="{ open: @entangle('showGradientPicker') }"
                    @click.outside="open = false"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-5 top-full mt-2 rounded-xl z-50"
                    style="display:none;width:18rem;background:var(--surface);border:1px solid var(--border);box-shadow:0 8px 24px rgba(0,0,0,0.5);"
                >
                    {{-- Panel header --}}
                    <div class="px-3 pt-3 pb-2 border-b" style="border-color:var(--surface-raised);">
                        <p class="text-xs font-semibold" style="color:var(--text);">Room atmosphere</p>
                        <p class="text-xs mt-0.5" style="color:var(--text-faint);">Choose how this room feels. Everyone inside sees it.</p>
                    </div>

                    <div class="p-3 space-y-3">
                        @php
                            $allGradients       = config('gradients');
                            $supporterKeys      = config('supporter.gradient_packs', []);
                            $freeGradients      = array_filter($allGradients, fn ($k) => ! in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                            $supporterGradients = array_filter($allGradients, fn ($k) => in_array($k, $supporterKeys), ARRAY_FILTER_USE_KEY);
                            $hasLockedAtmosphere = count($this->lockedRoomAtmosphereKeys) > 0;
                        @endphp

                        {{-- Default + free atmospheres --}}
                        <div>
                            <p class="text-xs mb-1.5" style="color:var(--text-faint);">Default</p>
                            <div class="grid grid-cols-4 gap-1.5">
                                <button type="button" wire:click="setRoomGradient('')" class="flex flex-col items-center gap-1">
                                    <div class="w-full h-7 rounded border-2 transition" style="background:#0D1117;{{ $roomGradientTheme === '' ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"></div>
                                    <span class="text-xs" style="color:{{ $roomGradientTheme === '' ? 'var(--text)' : 'var(--text-muted)' }};">None</span>
                                </button>
                                @foreach ($freeGradients as $key => $gradient)
                                    <button type="button" wire:click="setRoomGradient('{{ $key }}')" class="flex flex-col items-center gap-1">
                                        <div class="w-full h-7 rounded border-2 transition" style="background:{{ $gradient['css'] }};{{ $roomGradientTheme === $key ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"></div>
                                        <span class="text-xs text-center leading-tight" style="color:{{ $roomGradientTheme === $key ? 'var(--text)' : 'var(--text-muted)' }};">{{ $gradient['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Supporter atmospheres --}}
                        <div class="pt-2 border-t" style="border-color:var(--surface-raised);">
                            <div class="flex items-center gap-2 mb-1.5">
                                <p class="text-xs" style="color:var(--text-faint);">Supporter</p>
                                @if ($hasLockedAtmosphere)
                                    <a href="{{ route('support') }}" wire:navigate class="text-xs transition" style="color:var(--accent);" onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'">Learn more</a>
                                @endif
                            </div>
                            <div class="grid grid-cols-4 gap-1.5">
                                @foreach ($supporterGradients as $key => $gradient)
                                    @if (! $hasLockedAtmosphere)
                                        <button type="button" wire:click="setRoomGradient('{{ $key }}')" class="flex flex-col items-center gap-1">
                                            <div class="w-full h-7 rounded border-2 transition" style="background:{{ $gradient['css'] }};{{ $roomGradientTheme === $key ? 'border-color:var(--accent);' : 'border-color:var(--border);' }}"></div>
                                            <span class="text-xs text-center leading-tight" style="color:{{ $roomGradientTheme === $key ? 'var(--text)' : 'var(--text-muted)' }};">{{ $gradient['label'] }}</span>
                                        </button>
                                    @else
                                        <div class="flex flex-col items-center gap-1 cursor-default">
                                            <div class="w-full h-7 rounded border-2 relative overflow-hidden" style="background:{{ $gradient['css'] }};border-color:var(--surface-raised);opacity:0.40;">
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <svg width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="var(--text-muted)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="1"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
                                                </div>
                                            </div>
                                            <span class="text-xs text-center leading-tight" style="color:var(--text-faint);">{{ $gradient['label'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
    </x-glass-panel>

    {{-- Glass UI fix: .cg-room-entry (the per-room atmosphere gradient fill)
         now wraps only the thread content area — notices, pinned messages,
         the prompt, messages, typing indicator, crisis banner — between the
         header and compose bar, not the glass chrome itself. The room
         "Appearance" picker above keeps setting $roomGradientTheme/$chatAreaBg
         exactly as before; only where it's painted changed. --}}
    <div class="cg-room-entry flex flex-col flex-1 min-w-0" style="{{ $chatAreaBg }}">

        {{-- Guest preview notice — authenticated users in official rooms only, shown once --}}
        @if (auth()->check() && ($this->conversation->hangoutPost?->is_official ?? false))
            <div
                x-data="{ show: !localStorage.getItem('cg_guest_preview_notice') }"
                x-show="show"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="flex items-center gap-3 px-4 py-2 border-b flex-none"
                style="background:var(--surface);border-color:var(--border);"
            >
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--text-faint)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-none" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="flex-1 text-xs leading-relaxed" style="color:var(--text-faint);">
                    Visitors previewing CommonGrove can see the last 10 messages in this room before they sign up.
                </p>
                <button
                    type="button"
                    @click="show = false; localStorage.setItem('cg_guest_preview_notice', '1')"
                    class="flex-none text-xs px-2.5 py-1 rounded-lg border transition-colors duration-150"
                    style="color:var(--text-faint);border-color:var(--border);"
                    onmouseover="this.style.color='var(--text-muted)';this.style.borderColor='var(--border)';"
                    onmouseout="this.style.color='var(--text-faint)';this.style.borderColor='var(--border)';"
                    aria-label="Dismiss notice"
                >Got it</button>
            </div>
        @endif

        {{-- Pinned messages --}}
        @if ($this->pinnedMessages->isNotEmpty())
            <div x-data="{ open: true }" class="border-b flex-none" style="border-color:var(--border);">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-2 text-xs transition"
                    style="background:var(--surface-raised);color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'"
                >
                    <span class="font-medium">📌 {{ $this->pinnedMessages->count() }} pinned {{ Str::plural('message', $this->pinnedMessages->count()) }}</span>
                    <span x-text="open ? '▲' : '▼'"></span>
                </button>
                <div x-show="open" class="divide-y" style="background:var(--surface);divide-color:var(--border);">
                    @foreach ($this->pinnedMessages as $pinned)
                        <div class="px-4 py-2 flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs mb-0.5" style="color:var(--text-muted);">{{ auth()->check() ? $pinned->user?->gamertag : 'Someone' }}</p>
                                <p class="text-xs truncate" style="color:var(--text);">{{ Str::limit($pinned->content, 100) }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-none">
                                <a href="#msg-{{ $pinned->id }}" class="text-xs underline transition" style="color:var(--accent);">Jump</a>
                                @if (auth()->check() && ($this->conversation->created_by === auth()->id() || auth()->user()->is_admin))
                                    <button
                                        type="button"
                                        wire:click="unpinMessage('{{ $pinned->id }}')"
                                        class="text-xs transition"
                                        style="color:var(--text-muted);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                                    >Unpin</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Pin error --}}
        @if ($pinError)
            <div class="mx-4 mt-2 px-3 py-2 rounded text-xs border flex-none" style="background:rgba(var(--danger-rgb),0.1);border-color:rgba(var(--danger-rgb),0.4);color:var(--danger);">
                {{ $pinError }}
            </div>
        @endif

        {{-- Messages + floating room prompt share this wrapper so the prompt can
             float on top of the thread (absolutely positioned, pinned to the
             top) without reserving its own row — dismissing it no longer
             reflows the message list, and it doesn't scroll away since it
             lives outside the scrollable .cg-chat-messages div. --}}
        <div class="relative flex-1 min-w-0">

        {{-- Room prompt — floats over the top of the message thread. --}}
        @if ($this->enabledPrompts)
            <div
                x-data="{
                    prompts: {{ \Illuminate\Support\Js::from($this->enabledPrompts) }},
                    storageKey: {{ \Illuminate\Support\Js::from('prompts_' . $conversationId) }},
                    current: '',
                    dismissed: false,
                    init() {
                        this.dismissed = sessionStorage.getItem(this.storageKey) === '1';
                        if (this.prompts.length > 0) {
                            this.current = this.prompts[Math.floor(Math.random() * this.prompts.length)];
                        }
                    },
                    dismiss() {
                        sessionStorage.setItem(this.storageKey, '1');
                        this.dismissed = true;
                    }
                }"
                x-show="!dismissed"
                class="absolute inset-x-0 flex justify-center"
                style="top:0.75rem;z-index:5;pointer-events:none;"
            >
                <div style="pointer-events:auto;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.625rem 1.25rem;box-shadow:var(--shadow-md);display:inline-flex;align-items:center;gap:1rem;max-width:560px;">
                    <p style="font-family:var(--font-display);font-style:italic;font-size:0.875rem;color:var(--text-muted);flex:1;margin:0;" x-text="current"></p>

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
                        <button
                            type="button"
                            @click="$wire.setPromptReply(current); window.dispatchEvent(new CustomEvent('focus-composer'))"
                            class="flex-none"
                            style="background:var(--accent);color:var(--bg);border-radius:var(--radius-pill);width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;transition:all 150ms ease;"
                            aria-label="Reply to this prompt"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="9 17 4 12 9 7"/>
                                <path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
                            </svg>
                        </button>
                        <div
                            x-show="show"
                            style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.25rem 0.625rem;font-size:0.6875rem;color:var(--text);white-space:nowrap;pointer-events:none;"
                        >Reply</div>
                    </div>

                    <div class="relative" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
                        <button
                            type="button"
                            @click="dismiss()"
                            class="flex-none"
                            style="background:transparent;border:1px solid var(--border);color:var(--text-faint);border-radius:var(--radius-pill);width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 150ms ease;"
                            onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-faint)'"
                            aria-label="Dismiss prompt"
                        >
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                        <div
                            x-show="show"
                            style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.25rem 0.625rem;font-size:0.6875rem;color:var(--text);white-space:nowrap;pointer-events:none;"
                        >Maybe later</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Messages --}}
        <div
            class="cg-chat-messages absolute inset-0 overflow-y-auto"
            style="background:var(--bg);overflow-x:visible;"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
            x-on:message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
            x-on:message-received.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
        >
        <div style="max-width:820px;margin:0 auto;padding:1rem 1.5rem;">

            {{-- Guest preview label --}}
            @if (! auth()->check())
                <div class="flex items-center gap-2 mb-4 pb-2.5 border-b" style="border-color:var(--border);">
                    <span class="text-xs italic" style="color:var(--text-faint);">A glimpse inside ·</span>
                    <span class="text-xs" style="color:var(--surface-raised);">last 10 messages</span>
                </div>
            @endif

            {{-- Empty / quiet state — an invitation, not a problem --}}
            @if ($this->chatMessages->isEmpty())
                <div class="flex flex-col items-center justify-center text-center gap-3" style="min-height:55vh;">
                    <p class="font-display" style="font-size:1.25rem;color:var(--text-muted);">It's quiet here right now.</p>
                    @auth
                        <p class="text-sm" style="color:var(--text-muted);">You can wait, or say something to get things going.</p>
                        @if ($this->enabledPrompts)
                            @php
                                $prompts     = $this->enabledPrompts;
                                $quietPrompt = $prompts[crc32($conversationId . now()->format('Y-m-d')) % count($prompts)];
                            @endphp
                            <div class="mt-1 px-4 py-3 rounded-xl max-w-sm" style="background:var(--surface);border:1px solid var(--border);">
                                <p class="text-xs" style="color:var(--text-faint);">The room prompt:</p>
                                <p class="text-sm mt-1" style="color:var(--text);">{{ $quietPrompt }}</p>
                                <button
                                    type="button"
                                    wire:click="setPromptReply({{ \Illuminate\Support\Js::from($quietPrompt) }})"
                                    class="mt-2 text-xs underline transition"
                                    style="color:var(--accent);background:transparent;border:none;cursor:pointer;"
                                >Answer the prompt</button>
                            </div>
                        @endif
                    @else
                        <p class="text-sm" style="color:var(--text-muted);">No one has said anything yet — you could be the first once you join.</p>
                    @endauth
                    <p class="mt-1 text-xs" style="color:var(--text-faint);">Waiting quietly is perfectly fine too.</p>
                </div>
            @endif

            @foreach ($this->chatMessages as $message)
                @php
                    $isGuest        = ! auth()->check();
                    $isMine         = ! $isGuest && $message->user_id === auth()->id();
                    $reactionCounts = $message->reactions->groupBy('reaction')->map->count();
                    $myReactions    = $isGuest ? collect() : $message->reactions->where('user_id', auth()->id())->pluck('reaction');
                    $canPin         = ! $isGuest && ($this->conversation->created_by === auth()->id() || auth()->user()->is_admin);
                    $prevMsg        = $loop->index > 0 ? $this->chatMessages[$loop->index - 1] : null;
                    $isGrouped      = $prevMsg
                        && $prevMsg->user_id === $message->user_id
                        && abs($message->created_at->diffInMinutes($prevMsg->created_at)) <= 5;
                    // Group boundary (different user, or a 5+ minute gap) gets a bigger
                    // top margin; grouped continuations get none. Bottom margin is a
                    // tight baseline between every message, tightened further when the
                    // message itself is a continuation.
                    $groupTopMargin = ($loop->first || $isGrouped) ? '0' : '0.75rem';
                    $msgBottomMargin = $isGrouped ? '0.2rem' : '0.375rem';
                    $tooltipName    = $isGuest ? 'Someone' : ($message->user?->display_name ?? $message->author_name);
                @endphp
                <div
                    id="msg-{{ $message->id }}"
                    wire:key="rmsg-{{ $message->id }}"
                    x-data="{ hovered: false }"
                    @mouseenter="hovered = true"
                    @mouseleave="hovered = false"
                    @click.outside="hovered = false"
                    @class(['flex items-end', 'flex-row-reverse' => $isMine])
                    style="margin-top:{{ $groupTopMargin }};margin-bottom:{{ $msgBottomMargin }};gap:0.375rem;"
                >
                    {{-- Avatar column — fixed width so grouped continuation messages' text still aligns under the first message's text --}}
                    <div class="flex-none" style="width:1.5rem;">
                        @if (!$isGrouped)
                            @if ($isMine)
                                @if ($message->user)
                                    <x-avatar :user="$message->user" size="xs" />
                                @endif
                            @elseif ($isGuest)
                                <span class="w-6 h-6 rounded-full inline-block" style="background:var(--surface-raised);"></span>
                            @elseif ($message->user)
                                <x-avatar :user="$message->user" size="xs" />
                            @endif
                        @endif
                    </div>

                    <div class="flex flex-col" style="max-width:75%;{{ $isMine ? 'align-items:flex-end;' : 'align-items:flex-start;' }}">

                    {{-- Bubble + reaction indicator row — the indicator sits beside the bubble, never on top of it --}}
                    <div @class(['flex items-center', 'flex-row-reverse' => $isMine]) style="gap:0.25rem;">
                    <div class="relative min-w-0">
                        <div class="cg-msg-bubble text-sm space-y-0.5"
                            style="padding:0.625rem 0.875rem;{{ $isMine
                                ? 'background:var(--accent);color:var(--bg);border-radius:var(--radius-md) 0 var(--radius-md) var(--radius-md);'
                                : 'background:var(--surface);color:var(--text);border:1px solid var(--border);border-radius:0 var(--radius-md) var(--radius-md) var(--radius-md);' }}"
                            @click="if (!window.matchMedia('(hover: hover)').matches) hovered = !hovered"
                        >
                            @if (!$isMine && !$isGrouped)
                                @if ($isGuest)
                                    <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.25rem;">Someone</p>
                                @elseif ($message->user)
                                    <x-user-name :user="$message->user" class="block" style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.25rem;" />
                                @else
                                    <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.25rem;">{{ $message->author_name }}</p>
                                @endif
                            @endif

                            {{-- Reply quote --}}
                            @if ($message->reply_to_message_id || $message->reply_to_prompt)
                                @php
                                    if ($message->reply_to_message_id) {
                                        $replyParent  = $message->replyToMessage;
                                        $quoteText    = $replyParent
                                            ? Str::limit($replyParent->content, 70)
                                            : null;
                                        $quoteSender  = $isGuest ? 'Someone' : ($replyParent?->user?->display_name ?? null);
                                    } else {
                                        $quoteText   = Str::limit($message->reply_to_prompt, 70);
                                        $quoteSender = null;
                                    }
                                @endphp
                                <div class="mb-1 rounded-md px-2.5 py-1.5 -mx-1"
                                    style="{{ $isMine
                                        ? 'background:rgba(0,0,0,0.18);border-left:2px solid rgba(255,255,255,0.25);'
                                        : 'background:rgba(0,0,0,0.18);border-left:2px solid var(--border);' }}"
                                >
                                    @if ($quoteText !== null)
                                        @if ($quoteSender)
                                            <p class="text-xs mb-0.5 font-medium" style="{{ $isMine ? 'color:rgba(255,255,255,0.55);' : 'color:var(--text-faint);' }}">↪ {{ $quoteSender }}</p>
                                        @else
                                            <p class="text-xs mb-0.5" style="{{ $isMine ? 'color:rgba(255,255,255,0.4);' : 'color:var(--text-faint);' }}">↪ prompt</p>
                                        @endif
                                        <p class="text-xs leading-snug truncate" style="{{ $isMine ? 'color:rgba(255,255,255,0.6);' : 'color:var(--text-muted);' }}">{{ $quoteText }}</p>
                                    @else
                                        <p class="text-xs italic" style="{{ $isMine ? 'color:rgba(255,255,255,0.35);' : 'color:var(--text-faint);' }}">↪ original message unavailable</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Content warning --}}
                            @if ($message->has_cw)
                                <div x-data="{ revealed: false }">
                                    <div x-show="!revealed">
                                        <p class="text-xs font-semibold mb-1 opacity-80">⚠ {{ $message->cw_label ?? 'Content warning' }}</p>
                                        <button type="button" @click="revealed = true" class="text-xs underline opacity-70 hover:opacity-100">
                                            Click to reveal
                                        </button>
                                    </div>
                                    <p x-show="revealed" class="leading-snug">{{ $message->content }}</p>
                                </div>
                            @else
                                <p class="leading-snug">{{ $message->content }}</p>
                            @endif

                        </div>
                    </div>

                    @if (!$isGuest && (!$hideReactions || $canPin))
                        <x-message-reaction-box
                            :message="$message"
                            :is-mine="$isMine"
                            :my-reactions="$myReactions"
                            :hide-reactions="$hideReactions"
                            :can-pin="$canPin"
                            :show-reply="true"
                        />
                    @endif

                    {{-- Sender + timestamp — plain text, only visible on hover (desktop) or tap the bubble (touch) --}}
                    <span
                        style="font-size:0.6875rem;color:var(--text-faint);white-space:nowrap;align-self:flex-end;margin-bottom:4px;padding:0 0.375rem;background:transparent;opacity:0;transition:opacity 150ms ease;"
                        :style="{ opacity: hovered ? '1' : '0' }"
                    >
                        @if ($isMine)
                            {{ $message->created_at->format('H:i') }} · You
                        @else
                            {{ $tooltipName }} · {{ $message->created_at->format('H:i') }}
                        @endif
                    </span>
                    </div>

                    {{-- Reaction pills: mt-4 clears the floating tray --}}
                    @if ($reactionCounts->isNotEmpty() && !$hideReactions)
                        <div class="flex flex-wrap gap-1 mt-4">
                            @foreach ($reactionCounts as $emoji => $count)
                                <button
                                    type="button"
                                    wire:click="reactToMessage('{{ $message->id }}', '{{ $emoji }}')"
                                    class="text-xs px-1.5 py-0.5 rounded-full border transition leading-none"
                                    style="{{ $myReactions->contains($emoji) ? 'background:rgba(var(--accent-rgb),0.18);border-color:rgba(var(--accent-rgb),0.5);color:var(--text);' : 'background:var(--surface-raised);border-color:var(--border);color:var(--text);' }}"
                                    aria-label="{{ $emoji }} reaction{{ $count > 1 ? ', '.$count.' people' : '' }}"
                                >{{ $emoji }}{{ $count > 1 ? ' '.$count : '' }}</button>
                            @endforeach
                        </div>
                    @else
                        {{-- Small spacer so the floating tray doesn't bleed into the next message --}}
                        <div class="h-3 flex-none"></div>
                    @endif

                    </div>
                </div>
            @endforeach

        </div>
        </div>

        </div>

        {{-- Typing indicator --}}
        <div
            class="px-4 pb-1 min-h-[1.25rem] flex-none"
            x-data="{ typingUser: null, timer: null }"
            x-on:typing-received.window="
                typingUser = $event.detail.displayName;
                clearTimeout(timer);
                timer = setTimeout(() => typingUser = null, 3000)
            "
        >
            <p x-show="typingUser" x-text="typingUser + ' is writing…'" class="text-xs italic" style="color:var(--text-muted);"></p>
        </div>

        {{-- Crisis banner --}}
        @include('livewire.partials.crisis-banner')

    </div>

    {{-- Compose --}}
    @php $isExpiredHangout = $this->conversation->hangoutPost && !$this->conversation->hangoutPost->is_persistent && $this->conversation->hangoutPost->isExpired(); @endphp
    @if (! auth()->check())
        {{-- Guest CTA — replaces the compose bar entirely --}}
        <x-glass-panel tier="light" class="flex-none" style="padding:0.75rem 1rem;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 rounded-xl" style="max-width:820px;margin:0 auto;background:var(--surface-raised);border:1px solid var(--border);">
                    <p class="text-sm text-center sm:text-left" style="color:var(--text-muted);">Sign up to join the conversation</p>
                    <x-button :href="route('register')" variant="primary" class="!px-5 whitespace-nowrap">Join the Conversation</x-button>
                </div>
        </x-glass-panel>
        @else
        <x-glass-panel tier="light" class="flex-none" style="padding:0.75rem 1rem;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
        <div style="max-width:820px;margin:0 auto;">
            @if ($verificationBlock)
                <p class="mb-1.5 text-xs" style="color:#C9A83C;">{{ $verificationBlock }}</p>
            @endif
            @error('messageContent')
                <p class="mb-1 text-xs" style="color:var(--danger);">{{ $message }}</p>
            @enderror
            @error('cwLabel')
                <p class="mb-1 text-xs" style="color:var(--danger);">{{ $message }}</p>
            @enderror

            @if ($showCwInput)
                <div class="mb-2">
                    <input
                        type="text"
                        wire:model="cwLabel"
                        maxlength="50"
                        placeholder="Warning label (e.g. spoilers, sensitive topic…)"
                        class="w-full rounded-lg px-3 py-1.5 text-xs"
                        style="background:var(--surface);border:1px solid #D29922;color:var(--text);outline:none;"
                        onfocus="this.style.outline='2px solid var(--accent)';this.style.outlineOffset='2px'"
                        onblur="this.style.outline='none'"
                    >
                </div>
            @endif

            {{-- Reply preview --}}
            @if ($replyingToMessageId || $replyingToPrompt)
                @php
                    if ($replyingToMessageId) {
                        $previewMsg    = $this->chatMessages->firstWhere('id', $replyingToMessageId);
                        $previewText   = $previewMsg ? Str::limit($previewMsg->content, 80) : '…';
                        $previewSender = $previewMsg?->user?->display_name ?? null;
                    } else {
                        $previewText   = Str::limit($replyingToPrompt, 80);
                        $previewSender = null;
                    }
                @endphp
                <div class="flex items-start gap-2 mb-2 px-3 py-2 rounded-lg" style="background:var(--surface);border-left:2px solid var(--border);">
                    <div class="flex-1 min-w-0">
                        @if ($previewSender)
                            <p class="text-xs mb-0.5" style="color:var(--text-muted);">↩ {{ $previewSender }}</p>
                        @else
                            <p class="text-xs mb-0.5" style="color:var(--text-faint);">↩ prompt</p>
                        @endif
                        <p class="text-xs truncate" style="color:var(--text-faint);">{{ $previewText }}</p>
                    </div>
                    <button type="button" wire:click="cancelReply"
                        class="w-5 h-5 flex items-center justify-center rounded transition text-base leading-none flex-none mt-0.5"
                        style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'"
                        aria-label="Cancel reply">×</button>
                </div>
            @endif

            @if ($isExpiredHangout)
                <p class="text-xs text-center py-2" style="color:var(--text-muted);">This hangout has ended — no new messages can be sent.</p>
            @else
            <form wire:submit="sendMessage" class="flex gap-2">
                <button
                    type="button"
                    wire:click="$toggle('showCwInput')"
                    title="Add content warning"
                    class="px-2 py-2 md:px-2.5 md:py-2.5 rounded-xl text-xs font-medium transition flex-none"
                    style="{{ $showCwInput ? 'background:rgba(210,153,34,0.25);color:#D29922;' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                >CW</button>
                <x-input
                    type="text"
                    wire:model="messageContent"
                    wire:keydown.debounce.500ms="broadcastTyping"
                    x-on:focus-composer.window="$el.focus()"
                    maxlength="2000"
                    placeholder="Message the room…"
                    class="flex-1"
                    style="background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.625rem 1rem;font-family:var(--font-body);color:var(--text);"
                    onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 3px rgb(var(--accent-rgb) / 0.15)'"
                    onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"
                    autocomplete="off"
                />
                <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="!rounded-xl !py-3 md:!py-2.5 min-h-11 md:min-h-0 disabled:opacity-50 flex-none">Send</x-button>
            </form>
            @endif
        </div>
        </x-glass-panel>
        @endif {{-- end auth()->check() compose block --}}

    @auth
        <livewire:safety.report-user />
    @endauth

</div>
{{-- /.flex.flex-col (chat card) --}}

{{-- ── "Similar rooms" side panel ──────────────────────────────────────────
     Slides in from the right exactly like the landing page's sign-in panel
     (see resources/views/landing.blade.php .cg-auth-panel): position:fixed,
     translateX(100%) -> 0 with the same bouncy easing, opacity fade. Opened
     by clicking an interest tag in the settings menu's Room Info section. --}}
@auth
    <div
        class="cg-similar-backdrop"
        :class="{ 'is-open': similarOpen }"
        @click="similarOpen = false"
        aria-hidden="true"
    ></div>

    <div
        class="cg-similar-panel"
        :class="{ 'is-open': similarOpen }"
        @keydown.escape.window="similarOpen = false"
        role="dialog"
        aria-modal="true"
        aria-label="Similar rooms"
    >
        {{-- Panel header: Refresh (top-left, per Andrew's request), title, close (top-right) --}}
        <div class="flex items-center justify-between px-4 py-3 border-b flex-none" style="border-color:var(--border);">
            <button
                type="button"
                wire:click="refreshSimilarRooms"
                wire:loading.attr="disabled"
                wire:target="refreshSimilarRooms"
                title="Find other rooms again"
                class="flex items-center justify-center w-9 h-9 rounded-lg transition flex-none"
                style="color:var(--text-muted);"
                onmouseover="this.style.color='var(--text)';this.style.background='var(--surface-raised)'"
                onmouseout="this.style.color='var(--text-muted)';this.style.background='transparent'"
                aria-label="Refresh"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" wire:loading.class="cg-spin" wire:target="refreshSimilarRooms">
                    <polyline points="23 4 23 10 17 10"/>
                    <polyline points="1 20 1 14 7 14"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
            </button>

            <p class="text-sm font-semibold truncate px-2" style="color:var(--text);">Rooms with the same interest:</p>

            <button
                type="button"
                @click="similarOpen = false"
                class="flex items-center justify-center w-9 h-9 rounded-lg transition flex-none"
                style="color:var(--text-muted);"
                onmouseover="this.style.color='var(--text)';this.style.background='var(--surface-raised)'"
                onmouseout="this.style.color='var(--text-muted)';this.style.background='transparent'"
                aria-label="Close"
            >×</button>
        </div>

        <div class="px-4 pt-3 pb-1 flex-none">
            <p class="text-xs" style="color:var(--text-faint);">Tagged</p>
            <p class="text-sm font-medium" style="color:var(--accent);">{{ $similarRoomsTagName }}</p>
        </div>

        {{-- Room list — layout here is a placeholder; revisit once the panel itself is in place. --}}
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-2">
            @forelse ($this->similarRooms as $post)
                <a
                    href="{{ route('room.show', $post->conversation->id) }}"
                    wire:navigate
                    class="block rounded-xl p-3 transition"
                    style="background:var(--surface-raised);border:1px solid var(--border);"
                    onmouseover="this.style.borderColor='var(--text-faint)'"
                    onmouseout="this.style.borderColor='var(--border)'"
                >
                    <p class="text-sm font-medium truncate" style="color:var(--text);">{{ $post->conversation->name ?? 'Hangout Room' }}</p>
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        @foreach ($post->tags->take(3) as $t)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:var(--surface);color:var(--text-muted);border:1px solid var(--border);">{{ $t->name }}</span>
                        @endforeach
                    </div>
                </a>
            @empty
                <p class="text-sm text-center py-8" style="color:var(--text-faint);">No other rooms with this interest right now.</p>
            @endforelse
        </div>
    </div>
@endauth

</div>
{{-- /outer wrapper --}}
