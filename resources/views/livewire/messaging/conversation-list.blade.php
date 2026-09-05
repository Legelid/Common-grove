<div class="px-5 py-8 max-w-lg mx-auto space-y-5">
    <style>
        .cg-convo-card { position: relative; }
        .cg-convo-hover-actions { opacity: 0; pointer-events: none; transition: opacity 150ms ease; }
        .cg-convo-card:hover .cg-convo-hover-actions { opacity: 1; pointer-events: auto; }
    </style>

    {{-- ── Tab pills ────────────────────────────────────────────────────── --}}
    {{-- Glass UI: light tier — constant header bar over the forest photo, matching Home/Explore. --}}
    <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:0.75rem;margin-bottom:1.5rem;width:fit-content;">
    <div style="display:flex;gap:0.5rem;">
        @foreach (['messages' => 'Messages', 'rooms' => 'Rooms', 'archive' => 'Archive'] as $key => $label)
            @php $isTabActive = $tab === $key; @endphp
            <button
                type="button"
                wire:click="setTab('{{ $key }}')"
                style="display:inline-flex;align-items:center;gap:0.375rem;background:{{ $isTabActive ? 'var(--accent)' : 'transparent' }};color:{{ $isTabActive ? 'var(--bg)' : 'var(--text-muted)' }};font-weight:{{ $isTabActive ? '600' : '400' }};border-radius:var(--radius-pill);padding:0.375rem 1rem;font-size:0.875rem;cursor:pointer;transition:all 150ms ease;border:none;"
            >
                {{ $label }}
                @if ($key === 'messages' && ! $isTabActive && $this->unreadMessagesCount > 0)
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border-radius:999px;background:var(--accent-amber);color:#fff;font-size:0.6875rem;font-weight:700;">{{ $this->unreadMessagesCount }}</span>
                @endif
            </button>
        @endforeach
    </div>
    </x-glass-panel>

    {{-- ── MESSAGES TAB ─────────────────────────────────────────────────── --}}
    @if ($tab === 'messages')
        <div class="flex items-center justify-between" style="margin-bottom:1rem;">
            <h1 class="font-display" style="font-size:1.5rem;color:var(--text);">Messages</h1>
            @if ($this->requests->isNotEmpty())
                <button
                    type="button"
                    wire:click="setTab('requests')"
                    style="background:var(--surface-raised);border:1px solid var(--border);border-radius:var(--radius-pill);padding:0.375rem 0.875rem;font-size:0.8125rem;color:var(--text-muted);cursor:pointer;"
                >{{ $this->requests->count() }} {{ Str::plural('request', $this->requests->count()) }}</button>
            @endif
        </div>

        <div class="space-y-3">
            @forelse ($this->messagesTabConversations as $convo)
                @php
                    $other       = $convo->participants->firstWhere('id', '!=', auth()->id());
                    $lastMsg     = $convo->latestMessage;
                    $unreadCount = $this->unreadCountFor($convo);
                    $isMuted     = (bool) ($convo->participants->find(auth()->id())?->pivot->is_muted ?? false);
                @endphp
                <div class="cg-convo-card" wire:key="dm-{{ $convo->id }}">
                    <a
                        href="{{ route('messages.show', $convo->id) }}"
                        wire:navigate
                        class="flex items-center transition"
                        style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:0.875rem 1rem;gap:0.75rem;cursor:pointer;"
                        onmouseover="this.style.background='var(--surface-raised)'"
                        onmouseout="this.style.background='var(--surface)'"
                    >
                        <x-avatar :user="$other" size="md" />
                        <div class="flex-1 min-w-0">
                            <p style="font-weight:600;color:var(--text);" class="flex items-center gap-1.5">
                                <span class="truncate">{{ $other?->display_name ?? 'Unknown' }}</span>
                                @if ($isMuted)
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--text-faint)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-none" aria-label="Quiet mode on" role="img"><path d="M11 5 6 9H2v6h4l5 4V5z"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                                @endif
                            </p>
                            @if ($lastMsg)
                                <p style="font-size:0.875rem;color:var(--text-muted);" class="truncate">{{ Str::limit($lastMsg->content, 60) }}</p>
                            @endif
                        </div>
                        <div class="flex-none text-right">
                            @if ($lastMsg)
                                <p style="font-size:0.75rem;color:var(--text-faint);">{{ $lastMsg->created_at->diffForHumans(short: true) }}</p>
                            @endif
                            @if ($unreadCount > 0)
                                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border-radius:999px;background:var(--accent);color:var(--on-accent);font-size:0.6875rem;font-weight:700;margin-top:0.25rem;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </div>
                    </a>
                    <button
                        type="button"
                        wire:click.stop="archiveConversation('{{ $convo->id }}')"
                        class="cg-convo-hover-actions"
                        style="position:absolute;top:50%;right:1rem;transform:translateY(-50%);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.25rem 0.625rem;font-size:0.75rem;color:var(--text-muted);cursor:pointer;"
                        onmouseover="this.style.color='var(--text)'"
                        onmouseout="this.style.color='var(--text-muted)'"
                    >Archive →</button>
                </div>
            @empty
                <div class="text-center" style="padding:3rem 1rem;">
                    <p style="color:var(--text-muted);">No messages yet.</p>
                    <p style="font-size:0.875rem;color:var(--text-faint);margin-top:0.25rem;">Say hello to someone you've met in a room.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ── ROOMS TAB ────────────────────────────────────────────────────── --}}
    @if ($tab === 'rooms')
        <h1 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:1rem;">Your Rooms</h1>

        <div class="space-y-3">
            @forelse ($this->roomsTabConversations as $convo)
                @php
                    $lastMsg     = $convo->latestMessage;
                    $unreadCount = $this->unreadCountFor($convo);
                    $peopleCount = $convo->people_count ?? 0;
                    $statusText  = $convo->is_active
                        ? $peopleCount . ' ' . Str::plural('person', $peopleCount) . ' here · Active'
                        : 'Quiet now';
                @endphp
                <div class="cg-convo-card" wire:key="room-{{ $convo->id }}">
                    <a
                        href="{{ route('room.show', $convo->id) }}"
                        wire:navigate
                        class="flex items-center transition"
                        style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:0.875rem 1rem;gap:0.75rem;cursor:pointer;"
                        onmouseover="this.style.background='var(--surface-raised)'"
                        onmouseout="this.style.background='var(--surface)'"
                    >
                        <span class="flex-none flex items-center justify-center" style="width:2.5rem;height:2.5rem;border-radius:50%;background:var(--surface-raised);color:var(--text-muted);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p style="font-weight:600;color:var(--text);" class="truncate">{{ $convo->name ?? 'Room' }}</p>
                            <p style="font-size:0.875rem;color:var(--text-muted);" class="truncate">
                                {{ $statusText }}
                                @if ($lastMsg)
                                    · {{ Str::limit($lastMsg->content, 40) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex-none text-right">
                            @if ($lastMsg)
                                <p style="font-size:0.75rem;color:var(--text-faint);">{{ $lastMsg->created_at->diffForHumans(short: true) }}</p>
                            @endif
                            @if ($unreadCount > 0)
                                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border-radius:999px;background:var(--accent);color:var(--on-accent);font-size:0.6875rem;font-weight:700;margin-top:0.25rem;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </div>
                    </a>
                    <button
                        type="button"
                        wire:click.stop="archiveConversation('{{ $convo->id }}')"
                        class="cg-convo-hover-actions"
                        style="position:absolute;top:50%;right:1rem;transform:translateY(-50%);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.25rem 0.625rem;font-size:0.75rem;color:var(--text-muted);cursor:pointer;"
                        onmouseover="this.style.color='var(--text)'"
                        onmouseout="this.style.color='var(--text-muted)'"
                    >Archive →</button>
                </div>
            @empty
                <div class="text-center" style="padding:3rem 1rem;">
                    <p style="color:var(--text-muted);">You haven't stepped into any rooms yet.</p>
                    <a href="{{ route('explore') }}" wire:navigate style="font-size:0.875rem;color:var(--accent);margin-top:0.25rem;display:inline-block;">Explore rooms →</a>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ── ARCHIVE TAB ──────────────────────────────────────────────────── --}}
    @if ($tab === 'archive')
        <h1 class="font-display" style="font-size:1.5rem;color:var(--text);margin-bottom:0.5rem;">Archive</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:1rem;">Conversations you've set aside. Remove them permanently when you're ready.</p>

        <div class="space-y-3">
            @forelse ($this->archivedConversations as $convo)
                @php
                    $isRoomConvo = $convo->isRoom();
                    $other       = $isRoomConvo ? null : $convo->participants->firstWhere('id', '!=', auth()->id());
                    $label       = $isRoomConvo ? ($convo->name ?? 'Room') : ($other?->display_name ?? 'Unknown');
                    $lastMsg     = $convo->latestMessage;
                @endphp
                <div class="cg-convo-card" wire:key="arch-{{ $convo->id }}" x-data="{ confirming: false }">
                    <a
                        href="{{ $isRoomConvo ? route('room.show', $convo->id) : route('messages.show', $convo->id) }}"
                        wire:navigate
                        class="flex items-center transition"
                        style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:0.875rem 1rem;gap:0.75rem;cursor:pointer;"
                        onmouseover="this.style.background='var(--surface-raised)'"
                        onmouseout="this.style.background='var(--surface)'"
                    >
                        @if ($isRoomConvo)
                            <span class="flex-none flex items-center justify-center" style="width:2.5rem;height:2.5rem;border-radius:50%;background:var(--surface-raised);color:var(--text-muted);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                        @else
                            <x-avatar :user="$other" size="md" />
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p style="font-weight:600;color:var(--text);" class="truncate">{{ $label }}</p>
                                <span style="flex-none;background:var(--surface-raised);border-radius:var(--radius-pill);padding:0.125rem 0.5rem;font-size:0.6875rem;color:var(--text-faint);">{{ $isRoomConvo ? 'Room' : 'Message' }}</span>
                            </div>
                            @if ($lastMsg)
                                <p style="font-size:0.875rem;color:var(--text-muted);" class="truncate">{{ Str::limit($lastMsg->content, 60) }}</p>
                            @endif
                        </div>
                        <div class="flex-none text-right">
                            @php $archivedAt = $convo->participants->find(auth()->id())?->pivot->archived_at; @endphp
                            @if ($archivedAt)
                                <p style="font-size:0.75rem;color:var(--text-faint);">{{ $archivedAt->diffForHumans(short: true) }}</p>
                            @endif
                        </div>
                    </a>

                    <div class="cg-convo-hover-actions" style="position:absolute;top:50%;right:1rem;transform:translateY(-50%);">
                        <div x-show="!confirming" style="display:flex;align-items:center;gap:0.75rem;">
                            <button
                                type="button"
                                wire:click="unarchiveConversation('{{ $convo->id }}')"
                                style="background:transparent;border:none;cursor:pointer;font-size:0.75rem;font-weight:600;color:var(--accent);"
                            >Restore</button>
                            <button
                                type="button"
                                @click="confirming = true"
                                style="background:transparent;border:none;cursor:pointer;font-size:0.75rem;font-weight:600;color:var(--danger);"
                            >Delete permanently</button>
                        </div>
                        <div x-show="confirming" x-cloak style="display:flex;align-items:center;gap:0.5rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.25rem 0.5rem;">
                            <span style="font-size:0.75rem;color:var(--text-muted);">Are you sure?</span>
                            <button type="button" wire:click="deleteConversation('{{ $convo->id }}')" style="background:transparent;border:none;cursor:pointer;font-size:0.75rem;font-weight:600;color:var(--danger);">Yes</button>
                            <button type="button" @click="confirming = false" style="background:transparent;border:none;cursor:pointer;font-size:0.75rem;color:var(--text-muted);">Cancel</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center" style="padding:3rem 1rem;">
                    <p style="color:var(--text-muted);">Nothing archived.</p>
                    <p style="font-size:0.875rem;color:var(--text-faint);margin-top:0.25rem;">Archive messages or rooms to keep things tidy.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ── REQUESTS — reached via the badge button on the Messages tab ───── --}}
    @if ($tab === 'requests')
        <div class="flex items-center gap-3" style="margin-bottom:1rem;">
            <button type="button" wire:click="setTab('messages')" style="background:transparent;border:none;cursor:pointer;color:var(--text-muted);font-size:0.875rem;">← Messages</button>
            <h1 class="font-display" style="font-size:1.5rem;color:var(--text);">Requests</h1>
        </div>

        <div class="space-y-3">
            @forelse ($this->requests as $convo)
                @php
                    $requester = $convo->participants->firstWhere('id', '!=', auth()->id());
                    $lastMsg   = $convo->latestMessage;
                @endphp
                <a
                    href="{{ route('messages.show', $convo->id) }}"
                    wire:navigate
                    wire:key="req-{{ $convo->id }}"
                    class="flex items-center gap-4 transition"
                    style="background:var(--surface);border:1px solid rgba(210,153,34,0.3);border-radius:var(--radius-md);padding:0.875rem 1rem;"
                    onmouseover="this.style.background='var(--surface-raised)'"
                    onmouseout="this.style.background='var(--surface)'"
                >
                    <x-avatar :user="$requester" size="md" />
                    <div class="flex-1 min-w-0">
                        <p style="font-weight:600;color:var(--text);" class="truncate">{{ $requester?->display_name ?? 'Unknown' }}</p>
                        @if ($lastMsg)
                            <p style="font-size:0.875rem;color:var(--text-muted);" class="truncate">{{ Str::limit($lastMsg->content, 60) }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="text-center text-sm" style="padding:2.5rem 0;color:var(--text-muted);">No pending requests.</p>
            @endforelse
        </div>
    @endif

</div>
