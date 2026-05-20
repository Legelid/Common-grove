<!DOCTYPE html>
<html lang="en" class="md:h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>CommonGrove — Find your people</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /*
         * The side-panel lift shadow only makes visual sense on desktop where the
         * auth panel sits to the right of the hero. On mobile (stacked) it would
         * appear as an odd dark band on the left edge of the auth block.
         */
        @media (min-width: 768px) {
            #auth-panel { box-shadow: -8px 0 40px rgba(0,0,0,0.30); }
        }
    </style>
</head>
<body
    class="antialiased md:h-full md:overflow-hidden"
    style="background:#0D1117;color:#E6EDF3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;"
>

    {{--
        Mobile:  panels stack vertically — hero first, auth card second.
        Desktop: side by side, hero 60% / auth 40%, full viewport height.

        DOM order matches the desired mobile order so no order utilities are needed.
    --}}
    <div class="flex flex-col md:flex-row md:h-full">

        {{-- ── HERO PANEL ──────────────────────────────────────────────────────── --}}
        {{--
            Soft radial atmosphere — a faint forest-green glow anchored bottom-left
            and a muted deep-ocean tint top-right. Neither colour is bright or neon;
            they simply break the flatness of the dark field.
        --}}
        <div
            class="flex flex-col justify-center px-6 sm:px-10 py-14 md:py-16 md:w-3/5"
            style="
                background:
                    radial-gradient(ellipse 65% 55% at 10% 90%, rgba(29,158,117,0.07) 0%, transparent 68%),
                    radial-gradient(ellipse 55% 45% at 90% 8%,  rgba(10,38,65,0.30)   0%, transparent 62%),
                    #0D1117;
            "
        >
            {{-- Slight rightward nudge on desktop creates subtle asymmetry --}}
            <div class="max-w-md mx-auto md:mx-0 md:ml-14">

                <h1
                    class="text-3xl sm:text-4xl md:text-5xl font-bold mb-5 md:mb-6"
                    style="color:#E6EDF3;letter-spacing:-0.025em;line-height:1.2;"
                >
                    {{--
                        Always break here. Without the break, the inline text reads
                        "cornerof" when the browser collapses the adjacent inline nodes.
                    --}}
                    A quieter corner<br>of the internet.
                </h1>

                <p
                    class="text-base sm:text-lg font-normal mb-10 md:mb-14"
                    style="color:#8B949E;line-height:1.75;"
                >
                    Find your people. No pressure.
                </p>

                <ul class="space-y-5">
                    <li class="flex items-center gap-4">
                        <span class="w-1 h-1 rounded-full flex-none" style="background:#1D9E75;opacity:0.65;"></span>
                        <span class="text-sm" style="color:#8B949E;line-height:1.65;">Small, interest-based rooms</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-1 h-1 rounded-full flex-none" style="background:#1D9E75;opacity:0.65;"></span>
                        <span class="text-sm" style="color:#8B949E;line-height:1.65;">Low-pressure conversations</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-1 h-1 rounded-full flex-none" style="background:#1D9E75;opacity:0.65;"></span>
                        <span class="text-sm" style="color:#8B949E;line-height:1.65;">A place to just exist</span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ── AUTH PANEL ───────────────────────────────────────────────────────
             Slightly darker base than the hero so the two planes read as distinct.
             A faint green warmth rises from below — barely there, just enough.
             On mobile: top border separates it from the hero above.
             On desktop: left border + inset shadow lifts it off the hero panel.
        --}}
        <div
            id="auth-panel"
            class="flex flex-col justify-between px-6 sm:px-10 py-10 md:py-12 md:w-2/5 border-t md:border-t-0 md:border-l"
            style="
                background:
                    radial-gradient(ellipse 100% 55% at 50% 115%, rgba(29,158,117,0.055) 0%, transparent 65%),
                    #131920;
                border-color: #1E2730;
            "
        >

            {{-- Main content block — centred vertically on desktop, naturally spaced on mobile --}}
            <div class="flex-1 flex flex-col justify-center py-4 md:py-0">
                <div class="max-w-xs mx-auto w-full" style="display:flex;flex-direction:column;gap:2.25rem;">

                    {{-- Logo + rotating phrase --}}
                    <div>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span
                                class="font-semibold"
                                style="color:#C9D1D9;font-size:1.1rem;letter-spacing:-0.015em;"
                            >CommonGrove</span>
                            <span
                                class="text-xs px-1.5 py-0.5 rounded font-semibold"
                                style="background:rgba(29,158,117,0.12);color:#1D9E75;letter-spacing:0.04em;"
                            >BETA</span>
                        </div>

                        <div
                            x-data="{
                                phrases: [
                                    'A place to find your people',
                                    'You don\'t have to rush here',
                                    'Just being here is enough',
                                    'Take your time — there\'s no pressure',
                                    'Find people who feel familiar',
                                    'Come as you are',
                                    'It\'s okay to just exist here',
                                    'No expectations, just connection',
                                    'A place to feel a little less alone',
                                    'You can take things slow here',
                                    'Find your pace here',
                                    'A calm place to connect',
                                    'No need to perform here',
                                    'You don\'t have to say anything right away',
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
                                        }, 700);
                                    }, 14000);
                                }
                            }"
                        >
                            <p
                                x-text="phrases[idx]"
                                :style="{ opacity: visible ? '1' : '0', transition: 'opacity 0.7s ease' }"
                                class="text-sm"
                                style="color:#6B737C;min-height:1.5rem;line-height:1.65;"
                                aria-live="polite"
                                aria-atomic="true"
                            ></p>
                        </div>
                    </div>

                    {{-- CTA buttons + reassurance --}}
                    <div>
                        <div style="display:flex;flex-direction:column;gap:0.625rem;">
                            <a
                                href="{{ route('register') }}"
                                class="flex items-center justify-center w-full py-3 text-sm font-semibold rounded-xl transition"
                                style="background:#1D9E75;color:#fff;"
                                onmouseover="this.style.background='#1a9068'"
                                onmouseout="this.style.background='#1D9E75'"
                            >Create account</a>

                            <a
                                href="{{ route('login') }}"
                                class="flex items-center justify-center w-full py-3 text-sm font-medium rounded-xl border transition"
                                style="border-color:#262E38;color:#6B737C;background:rgba(255,255,255,0.018);"
                                onmouseover="this.style.borderColor='#3A4350';this.style.color='#C9D1D9';"
                                onmouseout="this.style.borderColor='#262E38';this.style.color='#6B737C';"
                            >Log in</a>
                        </div>

                        {{-- Emotional safety reassurance — one line, very quiet --}}
                        <p class="mt-4 text-xs text-center" style="color:#3A4350;">
                            You can take your time here.
                        </p>
                    </div>

                    {{-- Trust line — even quieter than reassurance --}}
                    <p class="text-xs text-center" style="color:#2C333C;">
                        No real name needed &nbsp;&bull;&nbsp; No ads &nbsp;&bull;&nbsp; Real people
                    </p>

                </div>
            </div>

            {{-- Footer — stacks vertically on very small screens, row on sm+ --}}
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between pt-5"
                style="color:#2A3038;border-top:1px solid #1A2028;"
            >
                <p class="text-xs">&copy; {{ date('Y') }} CommonGrove</p>
                <nav class="flex flex-wrap gap-x-4 gap-y-1">
                    <a
                        href="{{ route('privacy') }}"
                        class="text-xs transition"
                        onmouseover="this.style.color='#6B737C'"
                        onmouseout="this.style.color='#2A3038'"
                    >Privacy</a>
                    <a
                        href="{{ route('terms') }}"
                        class="text-xs transition"
                        onmouseover="this.style.color='#6B737C'"
                        onmouseout="this.style.color='#2A3038'"
                    >Terms</a>
                    <a
                        href="{{ route('guidelines') }}"
                        class="text-xs transition"
                        onmouseover="this.style.color='#6B737C'"
                        onmouseout="this.style.color='#2A3038'"
                    >Community Guidelines</a>
                </nav>
            </div>

        </div>

    </div>

    @livewireScripts
</body>
</html>
