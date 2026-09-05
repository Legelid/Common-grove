<div class="px-6 py-8 max-w-3xl mx-auto space-y-8" style="position:relative;z-index:1;">

    {{--
        Glass UI (Phase 4 of 6): the hero (greeting + rotating pill) is one
        light-tier glass card — the forest photo behind it now comes from
        the shared layout (layouts/app.blade.php), not a locally-owned
        background here, since the photo also needs to show through the
        header above this component.
    --}}
    <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;">

    {{-- ── Greeting ──────────────────────────────────────────────────────── --}}
    <div style="padding-bottom:0.5rem;">
        <h1 class="font-display" style="font-size:clamp(1.75rem, 4vw, 2.5rem);color:var(--text);font-weight:700;line-height:1.2;">{{ $greeting['heading'] }}</h1>
        <p class="mt-2" style="font-family:var(--font-body);font-size:1rem;color:var(--text-muted);">{{ $greeting['subtext'] }}</p>
    </div>

    {{-- ── Rotating quote bubble — a calm breath between hello and what's next ── --}}
    <div style="text-align:center;width:100%;margin-top:1rem;"
        x-data="{
            phrases: [
                'A place to find your people',
                'You don\'t have to rush here',
                'Just being here is enough',
                'Take your time. There\'s no pressure.',
                'A quieter corner of the internet',
                'Find people who feel familiar',
                'Come as you are',
                'It\'s okay to just exist here',
                'No expectations, just connection',
                'A place to feel a little less alone',
                'You can take things slow here',
                'Not everything has to be said right away',
                'Stay as long as you like',
                'A calm place to connect',
                'You\'re welcome here, however you show up',
                'No pressure to be anything but yourself',
                'Find your pace here',
                'You don\'t have to perform here',
                'A space that moves at your speed',
                'You can just listen if you want',
            ],
            idx: 0,
            visible: true,
            init() {
                this.idx = Math.floor(Math.random() * this.phrases.length);
                const noMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (noMotion) return;
                setInterval(() => {
                    this.visible = false;
                    setTimeout(() => {
                        this.idx = (this.idx + 1) % this.phrases.length;
                        this.visible = true;
                    }, 600);
                }, 10000);
            }
        }"
    >
        <span
            class="inline-flex"
            style="background:var(--surface-raised);border:1px solid var(--border-strong);border-radius:var(--radius-pill);padding:0.625rem 1.5rem;box-shadow:var(--shadow-sm);"
        >
            <span
                style="font-family:var(--font-display);font-style:italic;font-size:0.9375rem;color:var(--text);transition:opacity 600ms ease;"
                :style="{ opacity: visible ? '1' : '0' }"
                x-text="phrases[idx]"
            ></span>
        </span>
    </div>

    </x-glass-panel>

    @auth
        {{-- ── Continue where you left off — only renders when real data exists ── --}}
        @if ($this->hasError)
            <div class="rounded-2xl border px-6 py-8 text-sm space-y-2 text-center" style="background:var(--surface);border-color:var(--border);color:var(--text-muted);">
                <p style="color:var(--text);">Something went a bit wrong.</p>
                <p>Try refreshing — we'll be here when you're back.</p>
            </div>
        @elseif ($this->continuePaths->isNotEmpty())
            {{-- Glass UI (Phase 4): heavy tier — denser text/list than the hero. --}}
            <x-glass-panel tier="heavy" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;">
                <p class="mb-3" style="font-family:var(--font-body);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-faint);">Continue where you left off</p>
                <div class="space-y-2">
                    @foreach ($this->continuePaths as $room)
                        <a
                            href="{{ route('room.show', $room['id']) }}"
                            wire:navigate
                            wire:key="continue-{{ $room['id'] }}"
                            class="flex items-center justify-between gap-3 transition"
                            style="padding:0.875rem 1.25rem;border-radius:var(--radius-md);background:var(--surface);border:1px solid var(--border);"
                            onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='var(--surface)'"
                        >
                            <div class="min-w-0">
                                <p class="text-sm truncate" style="color:var(--text);font-weight:500;">{{ $room['name'] }}</p>
                                <p class="mt-0.5 text-xs" style="color:var(--text-muted);">{{ $room['stateLabel'] }}</p>
                            </div>
                            <span class="flex-none"><x-arrow-icon label="Continue" /></span>
                        </a>
                    @endforeach
                </div>
            </x-glass-panel>
        @endif

        {{-- ── Explore transition ── Glass UI (Phase 4): heavy tier ────────────── --}}
        <x-glass-panel tier="heavy" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;text-align:center;">
            <p class="font-display" style="font-size:1.25rem;color:var(--text);">Explore somewhere new</p>
            <p class="mt-1" style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);">Browse by interest, atmosphere, or conversation style.</p>
            <a
                href="{{ route('explore') }}"
                wire:navigate
                class="inline-block mt-3"
                aria-label="Explore rooms"
            ><x-arrow-icon label="Explore rooms" /></a>
        </x-glass-panel>
    @endauth

    @guest
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem 1.75rem;text-align:center;">
            <p class="font-display" style="font-size:1.25rem;color:var(--text);">Take a look around</p>
            <p class="mt-1" style="font-family:var(--font-body);font-size:0.875rem;color:var(--text-muted);">See what CommonGrove's community spaces feel like before joining.</p>
            <a
                href="{{ route('explore') }}"
                wire:navigate
                class="inline-block mt-3"
                aria-label="Explore rooms"
            ><x-arrow-icon label="Explore rooms" /></a>
        </div>
    @endguest

    </div>
