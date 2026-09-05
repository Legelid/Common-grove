{{-- Root element must always render regardless of auth/room state — a
     Livewire component that renders nothing has no root tag to hydrate. --}}
<div>
@auth
    @if ($this->rooms->isNotEmpty())
        <div
            style="position:fixed;bottom:1.25rem;right:1.25rem;z-index:40;"
            x-data="{
                open: false,
                pillHover: false,
                toggle() { this.open = !this.open },
                close() { this.open = false },
            }"
            @click.outside="close()"
            @keydown.escape.window="close()"
        >
            {{-- Trigger pill --}}
            <button
                type="button"
                @click="toggle()"
                @mouseenter="pillHover = true"
                @mouseleave="pillHover = false"
                :style="`background:${(open || pillHover) ? 'var(--surface-raised)' : 'var(--surface)'};border:1px solid ${open ? 'var(--accent)' : 'var(--border)'};border-radius:var(--radius-pill);padding:8px 16px;min-height:44px;font-size:0.8125rem;font-family:var(--font-body);color:${(open || pillHover) ? 'var(--text)' : 'var(--text-muted)'};box-shadow:${pillHover ? 'var(--shadow-lg)' : 'var(--shadow-md)'};cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;transition:all 150ms ease;white-space:nowrap;`"
                :aria-expanded="open.toString()"
                aria-haspopup="true"
            >
                <span aria-live="polite">{{ $this->rooms->count() }} {{ Str::plural('room', $this->rooms->count()) }}@if ($this->newCount > 0) · {{ $this->newCount }} new @endif</span>
                <span x-text="open ? '↓' : '↑'" aria-hidden="true"></span>
            </button>

            {{-- Upward dropdown menu — widened so room names have room to
                 breathe (was 220-320px, cramped for anything longer than a
                 few words). overflow:hidden removed: it was clipping the
                 Explore icon's own hover tooltip, which pops out sideways
                 past the panel's edge — same "give it the same priority as
                 other panels" fix already applied elsewhere this session. --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                style="display:none;position:absolute;bottom:calc(100% + 8px);right:0;min-width:280px;max-width:400px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:0.5rem;"
                role="menu"
                aria-label="Your rooms"
            >
                {{-- Header row: label + a compact Explore icon, top-right —
                     previously a full-width "Step into something new" row
                     at the bottom, oversized (the arrow-icon's default
                     4.8em/2.75em sizing, meant for hero-scale usage
                     elsewhere, dwarfing this small menu). --}}
                <div class="flex items-center justify-between" style="padding:0.25rem 0.25rem 0.5rem;">
                    <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-faint);">Your rooms</p>
                    <a
                        href="{{ route('explore') }}"
                        wire:navigate
                        @click="close()"
                        aria-label="Step in to new rooms"
                    ><x-arrow-icon label="Step in to new rooms" size="1.1em" bubble="1.75em" /></a>
                </div>

                @foreach ($this->rooms as $room)
                    <a
                        href="{{ route('room.show', $room['id']) }}"
                        wire:navigate
                        @click="open = false"
                        class="flex items-center justify-between transition"
                        style="padding:0.5rem 0.75rem;border-radius:var(--radius-md);cursor:pointer;background:{{ $room['isCurrent'] ? 'rgba(var(--accent-rgb), 0.08)' : 'transparent' }};"
                        @if (! $room['isCurrent'])
                            onmouseover="this.style.background='var(--surface-raised)'"
                            onmouseout="this.style.background='transparent'"
                        @endif
                    >
                        <span class="min-w-0 truncate max-w-[280px]" style="font-size:0.875rem;font-weight:{{ $room['isCurrent'] ? '600' : '400' }};color:{{ $room['isCurrent'] ? 'var(--accent)' : ($room['expiresSoon'] ? 'var(--accent-amber)' : 'var(--text)') }};">
                            {{ $room['name'] }}
                            @if ($room['unreadCount'] > 0)
                                <span style="font-size:0.75rem;color:var(--accent);font-weight:600;margin-left:0.375rem;">{{ $room['unreadDisplay'] }}</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endauth
</div>
