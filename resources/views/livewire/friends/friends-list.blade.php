<div class="px-6 py-8 max-w-2xl mx-auto">

    {{-- Flash --}}
    @if ($flash)
        <div class="mb-4 px-3 py-2.5 rounded-lg text-sm border" style="background:rgba(var(--accent-rgb),0.1);border-color:rgba(var(--accent-rgb),0.4);color:var(--accent);">
            {{ $flash }}
        </div>
    @endif

    @if (! $this->hasAnyContent)

        {{-- Part E — whole-page empty state: an invitation, not an error --}}
        <div class="flex flex-col items-center justify-center text-center gap-3" style="min-height:50vh;">
            <h1 class="font-display" style="font-size:clamp(1.5rem,4vw,2rem);color:var(--text);font-weight:700;">You haven't crossed paths with anyone yet.</h1>
            <p class="text-sm max-w-sm" style="color:var(--text-muted);">Step into a room and say hello — that's how it starts.</p>
            <x-button :href="route('explore')" wire:navigate variant="primary" class="mt-2" aria-label="Find a room"><x-arrow-icon label="Find a room" /></x-button>
        </div>

    @else

        {{-- Part A — page header --}}
        {{-- Glass UI: light tier — hero heading over the forest photo, matching Home/Explore. --}}
        <x-glass-panel tier="light" style="border-radius:var(--radius-lg);padding:1.5rem 1.75rem;margin-bottom:2rem;">
            <h1 class="font-display" style="font-size:clamp(1.75rem,4vw,2.5rem);color:var(--text);font-weight:700;line-height:1.2;">People</h1>
            <p class="mt-2" style="font-family:var(--font-body);font-size:1rem;color:var(--text-muted);">People you've crossed paths with, and those you might get along with.</p>
        </x-glass-panel>

        {{-- Section 1 — Familiar faces --}}
        @if ($this->familiarFaces->isNotEmpty())
            <section class="mb-8">
                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">Familiar faces</h2>
                <div class="space-y-2">
                    @foreach ($this->familiarFaces as $row)
                        <x-person-card
                            :user="$row['user']"
                            action="Say hello again"
                            :href="route('messages.show', $row['conversationId'])"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 2 — People you've met recently --}}
        @if ($this->recentlyMet->isNotEmpty())
            <section class="mb-8">
                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">People you've met recently</h2>
                <div class="space-y-2">
                    @foreach ($this->recentlyMet as $person)
                        <x-person-card
                            :user="$person"
                            action="Stay connected"
                            wireClick="sendRequest('{{ $person->id }}')"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 3 — People you may get along with --}}
        @if ($this->suggestedPeople->isNotEmpty())
            <section class="mb-8">
                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">People you may get along with</h2>
                <div class="space-y-2">
                    @foreach ($this->suggestedPeople as $row)
                        <x-person-card
                            :user="$row['user']"
                            :explanation="$row['explanation']"
                            action="Stay connected"
                            wireClick="sendRequest('{{ $row['user']->id }}')"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 4 — Connection requests --}}
        @if ($this->pendingRequests->isNotEmpty())
            <section class="mb-8">
                <h2 style="font-family:var(--font-body);font-size:1.5rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">Connection requests</h2>
                <div class="space-y-2">
                    @foreach ($this->pendingRequests as $friendship)
                        <x-person-card
                            :user="$friendship->requester"
                            action="Accept"
                            wireClick="accept('{{ $friendship->id }}')"
                            secondAction="Decline"
                            secondWireClick="decline('{{ $friendship->id }}')"
                        />
                    @endforeach
                </div>
            </section>
        @endif

    @endif

</div>
