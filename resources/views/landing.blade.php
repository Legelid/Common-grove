<!DOCTYPE html>
<html lang="en" class="md:h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>CommonGrove — Find your people</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon2.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/landing.js'])
    <style>
        /*
         * The side-panel lift shadow only makes visual sense on desktop where the
         * auth panel sits to the right of the hero. On mobile (stacked) it would
         * appear as an odd dark band on the left edge of the auth block.
         */
        @media (min-width: 768px) {
            #auth-panel { box-shadow: -8px 0 40px rgba(0,0,0,0.30); }
        }
        [x-cloak] { display: none !important; }
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
                        <div class="flex items-center justify-center gap-2 mb-3" style="padding-left:32px;filter:drop-shadow(0 0 10px rgba(255,255,255,0.18)) drop-shadow(0 0 28px rgba(255,255,255,0.08));">
                            <span class="tracking-tight" style="color:#E6EDF3;font-family:'Cormorant Garamond',serif;font-size:2.25rem;font-weight:400;">Common<span style="font-weight:700;-webkit-text-stroke:0.6px #E6EDF3;">Grove</span></span>
                            <img src="{{ asset('images/logo-icon.png') }}" alt="" style="height:54px;width:auto;" class="-ml-9">
                            <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(29,158,117,0.12);color:#1D9E75;letter-spacing:0.04em;">BETA</span>
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
                                style="color:#6B737C;min-height:1.5rem;line-height:1.65;text-align:center;"
                                aria-live="polite"
                                aria-atomic="true"
                            ></p>
                        </div>
                    </div>

                    {{-- CTA buttons + reassurance --}}
                    <div style="display:flex;flex-direction:column;gap:0.75rem;">

                        {{-- Primary CTA --}}
                        <a
                            href="{{ route('register') }}"
                            class="flex items-center justify-center w-full py-3 text-sm font-semibold rounded-xl transition"
                            style="background:#1D9E75;color:#fff;"
                            onmouseover="this.style.background='#1a9068'"
                            onmouseout="this.style.background='#1D9E75'"
                        >Join the Conversation</a>

                        {{-- Secondary — already have an account --}}
                        <p class="text-xs text-center" style="color:#3A4350;">
                            Already have an account?
                            <a
                                href="{{ route('login') }}"
                                class="transition"
                                style="color:#6B737C;"
                                onmouseover="this.style.color='#8B949E'"
                                onmouseout="this.style.color='#6B737C'"
                            >Log in</a>
                        </p>

                        {{-- Opens the story overlay — wired to the 3-slide intro --}}
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-story'))"
                            class="flex items-center justify-center w-full py-2.5 text-xs font-medium rounded-xl border transition cursor-pointer"
                            style="border-color:#1E2730;color:#3A4350;background:transparent;"
                            onmouseover="this.style.borderColor='#262E38';this.style.color='#6B737C';"
                            onmouseout="this.style.borderColor='#1E2730';this.style.color='#3A4350';"
                        >See what's in store for you</button>

                        {{-- Emotional safety reassurance — one line, very quiet --}}
                        <p class="text-xs text-center" style="color:#2C333C;">
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

    {{-- ── STORY OVERLAY ──────────────────────────────────────────────────────
         3-slide fullscreen intro, triggered by the "See what's in store" button.
         Alpine listens for a custom 'open-story' window event so the button needs
         no shared x-data scope. Slides use CSS translateX for smooth panning.
    --}}
    <div
        x-data="{ open: false, slide: 0 }"
        x-on:open-story.window="open = true; slide = 0"
        x-show="open"
        x-cloak
        style="display:none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex flex-col"
        style="background:rgba(13,17,23,0.98);"
        role="dialog"
        aria-modal="true"
        aria-label="Our story"
    >
        {{-- Skip --}}
        <div class="flex justify-end px-5 pt-5 sm:px-8 sm:pt-6 flex-none">
            <a
                href="{{ route('feed') }}"
                class="text-xs transition-colors duration-200 px-2 py-1 rounded"
                style="color:#3A4350;"
                onmouseover="this.style.color='#8B949E'"
                onmouseout="this.style.color='#3A4350'"
            >Skip</a>
        </div>

        {{-- Slide track — overflow-hidden clips non-active slides horizontally --}}
        <div class="flex-1 overflow-hidden min-h-0">
            <div
                class="flex h-full"
                style="transition:transform 0.45s cubic-bezier(0.4,0,0.2,1);"
                :style="`transform:translateX(calc(-${slide * 100}%))`"
            >

                {{-- ── SLIDE 1 ─────────────────────────────────────────────────── --}}
                <div class="w-full h-full flex-none overflow-y-auto">
                    <div class="min-h-full flex flex-col items-center justify-center px-6 sm:px-10 py-8">
                        <div class="w-full max-w-sm mx-auto flex flex-col items-center gap-7 sm:gap-9">

                            {{-- Illustration: lonely person with unanswered chat bubble --}}
                            <div style="opacity:0.72;">
                                <svg viewBox="0 0 180 160" width="130" height="115" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    {{-- Faded unanswered chat bubble --}}
                                    <rect x="94" y="12" width="62" height="38" rx="9" stroke="#252E3D" stroke-width="1.5" fill="rgba(19,25,32,0.5)" opacity="0.65"/>
                                    <path d="M108 50 L104 60 L116 50" fill="#131920" stroke="#252E3D" stroke-width="1.5" stroke-linejoin="round" opacity="0.65"/>
                                    {{-- Ellipsis dots --}}
                                    <circle cx="112" cy="31" r="2.5" fill="#2A3340" opacity="0.6"/>
                                    <circle cx="123" cy="31" r="2.5" fill="#2A3340" opacity="0.6"/>
                                    <circle cx="134" cy="31" r="2.5" fill="#2A3340" opacity="0.6"/>
                                    {{-- Floor --}}
                                    <line x1="18" y1="148" x2="116" y2="148" stroke="#1E2730" stroke-width="1.5" stroke-linecap="round"/>
                                    {{-- Seated figure, head bowed, knees up --}}
                                    <circle cx="62" cy="80" r="14" stroke="#30363D" stroke-width="1.5"/>
                                    <path d="M62 94 L60 116" stroke="#30363D" stroke-width="1.5" stroke-linecap="round"/>
                                    {{-- Legs bent, sitting on floor --}}
                                    <path d="M60 116 L44 116 L40 134 M60 116 L76 116 L82 134" stroke="#30363D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    {{-- Feet --}}
                                    <line x1="36" y1="142" x2="48" y2="142" stroke="#30363D" stroke-width="1.5" stroke-linecap="round"/>
                                    <line x1="78" y1="142" x2="90" y2="142" stroke="#30363D" stroke-width="1.5" stroke-linecap="round"/>
                                    {{-- Arms resting on knees --}}
                                    <path d="M62 100 L48 110 L44 118" stroke="#30363D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M62 100 L76 110 L80 118" stroke="#30363D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    {{-- Shadow beneath --}}
                                    <ellipse cx="62" cy="148" rx="26" ry="3.5" fill="#0D1117"/>
                                </svg>
                            </div>

                            <div class="flex flex-col items-center gap-4 text-center">
                                <h2
                                    class="text-xl sm:text-2xl font-semibold leading-snug"
                                    style="color:#E6EDF3;letter-spacing:-0.015em;"
                                >"She waited for an hour. Nobody showed up."</h2>

                                <p
                                    class="text-sm sm:text-base leading-relaxed"
                                    style="color:#6B737C;max-width:38ch;"
                                >My wife just wanted someone to play games with. She reached out, sat down, and waited. Nobody responded. She came into the living room on the verge of tears. And I knew she wasn't the only one who'd ever felt that way. Nearly 1 in 3 adults in the US report feeling lonely. That number has been climbing for years. And yet nobody built anything to actually fix it.</p>
                            </div>

                            <button
                                x-on:click="slide = 1"
                                class="px-6 py-2.5 text-sm font-medium rounded-xl border transition-colors duration-200"
                                style="background:transparent;color:#8B949E;border-color:#252E3D;"
                                onmouseover="this.style.borderColor='#30363D';this.style.color='#C9D1D9';"
                                onmouseout="this.style.borderColor='#252E3D';this.style.color='#8B949E';"
                            >Next →</button>
                        </div>
                    </div>
                </div>

                {{-- ── SLIDE 2 ─────────────────────────────────────────────────── --}}
                <div class="w-full h-full flex-none overflow-y-auto">
                    <div class="min-h-full flex flex-col items-center justify-center px-6 sm:px-10 py-8">
                        <div class="w-full max-w-sm mx-auto flex flex-col items-center gap-7 sm:gap-9">

                            {{-- Illustration: overlapping feed cards / notification chaos --}}
                            <div style="opacity:0.72;">
                                <svg viewBox="0 0 180 140" width="130" height="101" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    {{-- Back card, rotated left --}}
                                    <g transform="rotate(-10, 90, 70)">
                                        <rect x="32" y="38" width="98" height="64" rx="10" stroke="#2D3B4A" stroke-width="1.5" fill="rgba(29,40,54,0.45)"/>
                                        <line x1="50" y1="58" x2="114" y2="58" stroke="#253040" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="50" y1="70" x2="92" y2="70" stroke="#1E2836" stroke-width="1" stroke-linecap="round"/>
                                        <circle cx="47" cy="50" r="7" stroke="#253040" stroke-width="1" fill="rgba(18,26,38,0.8)"/>
                                    </g>
                                    {{-- Middle card, rotated right --}}
                                    <g transform="rotate(8, 90, 70)">
                                        <rect x="46" y="32" width="98" height="64" rx="10" stroke="#3D2D4A" stroke-width="1.5" fill="rgba(45,29,55,0.38)"/>
                                        <line x1="64" y1="52" x2="128" y2="52" stroke="#342540" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="64" y1="64" x2="106" y2="64" stroke="#28203A" stroke-width="1" stroke-linecap="round"/>
                                        <circle cx="61" cy="44" r="7" stroke="#342540" stroke-width="1" fill="rgba(28,18,38,0.8)"/>
                                    </g>
                                    {{-- Front card, straight --}}
                                    <rect x="40" y="26" width="98" height="64" rx="10" stroke="#2D4A3D" stroke-width="1.5" fill="rgba(29,55,44,0.32)"/>
                                    <line x1="58" y1="46" x2="122" y2="46" stroke="#254038" stroke-width="1.5" stroke-linecap="round"/>
                                    <line x1="58" y1="58" x2="100" y2="58" stroke="#1E3030" stroke-width="1" stroke-linecap="round"/>
                                    <circle cx="55" cy="38" r="7" stroke="#2D4A3D" stroke-width="1" fill="rgba(18,32,26,0.8)"/>
                                    {{-- Notification badges --}}
                                    <circle cx="138" cy="24" r="8" fill="#2D1A1A" opacity="0.9"/>
                                    <text x="138" y="28" text-anchor="middle" font-size="7.5" fill="#6B2A2A" font-family="sans-serif" font-weight="600">12</text>
                                    <circle cx="30" cy="50" r="8" fill="#2D1A1A" opacity="0.9"/>
                                    <text x="30" y="54" text-anchor="middle" font-size="7.5" fill="#6B2A2A" font-family="sans-serif" font-weight="600">5</text>
                                    {{-- Dismiss X --}}
                                    <line x1="148" y1="64" x2="158" y2="74" stroke="#3A2A2A" stroke-width="2" stroke-linecap="round" opacity="0.55"/>
                                    <line x1="158" y1="64" x2="148" y2="74" stroke="#3A2A2A" stroke-width="2" stroke-linecap="round" opacity="0.55"/>
                                </svg>
                            </div>

                            <div class="flex flex-col items-center gap-4 text-center">
                                <h2
                                    class="text-xl sm:text-2xl font-semibold leading-snug"
                                    style="color:#E6EDF3;letter-spacing:-0.015em;"
                                >Every platform forgot about the quiet ones.</h2>

                                <p
                                    class="text-sm sm:text-base leading-relaxed"
                                    style="color:#6B737C;max-width:38ch;"
                                >Social media isn't social anymore. It's algorithms, ads, and chasing followers. Nobody built something for the person who just wants to quietly find their people and actually talk to them.</p>
                            </div>

                            <button
                                x-on:click="slide = 2"
                                class="px-6 py-2.5 text-sm font-medium rounded-xl border transition-colors duration-200"
                                style="background:transparent;color:#8B949E;border-color:#252E3D;"
                                onmouseover="this.style.borderColor='#30363D';this.style.color='#C9D1D9';"
                                onmouseout="this.style.borderColor='#252E3D';this.style.color='#8B949E';"
                            >Next →</button>
                        </div>
                    </div>
                </div>

                {{-- ── SLIDE 3 ─────────────────────────────────────────────────── --}}
                <div class="w-full h-full flex-none overflow-y-auto">
                    <div class="min-h-full flex flex-col items-center justify-center px-6 sm:px-10 py-8">
                        <div class="w-full max-w-sm mx-auto flex flex-col items-center gap-7 sm:gap-9">

                            {{-- Illustration: soft grove of trees, CommonGrove brand colour --}}
                            <div style="opacity:0.85;">
                                <svg viewBox="0 0 180 148" width="130" height="107" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    {{-- Ground glow --}}
                                    <ellipse cx="90" cy="138" rx="72" ry="9" fill="rgba(29,158,117,0.045)"/>
                                    {{-- Left tree --}}
                                    <polygon points="44,118 26,80 62,80" stroke="#1A3A2A" stroke-width="1.5" fill="rgba(29,158,117,0.07)" stroke-linejoin="round"/>
                                    <polygon points="44,95 30,65 58,65" stroke="#1D402E" stroke-width="1.5" fill="rgba(29,158,117,0.09)" stroke-linejoin="round"/>
                                    <rect x="41" y="118" width="6" height="18" rx="2" fill="#172820"/>
                                    {{-- Center tree (tallest) --}}
                                    <polygon points="90,116 68,84 112,84" stroke="#1D4A36" stroke-width="1.5" fill="rgba(29,158,117,0.1)" stroke-linejoin="round"/>
                                    <polygon points="90,92 72,58 108,58" stroke="#1D4A36" stroke-width="1.5" fill="rgba(29,158,117,0.12)" stroke-linejoin="round"/>
                                    <polygon points="90,68 76,40 104,40" stroke="#1D4A36" stroke-width="1.5" fill="rgba(29,158,117,0.14)" stroke-linejoin="round"/>
                                    <rect x="87" y="116" width="6" height="20" rx="2" fill="#172820"/>
                                    {{-- Right tree --}}
                                    <polygon points="136,118 118,80 154,80" stroke="#1A3A2A" stroke-width="1.5" fill="rgba(29,158,117,0.07)" stroke-linejoin="round"/>
                                    <polygon points="136,95 122,65 150,65" stroke="#1D402E" stroke-width="1.5" fill="rgba(29,158,117,0.09)" stroke-linejoin="round"/>
                                    <rect x="133" y="118" width="6" height="18" rx="2" fill="#172820"/>
                                    {{-- Ground line --}}
                                    <line x1="14" y1="136" x2="166" y2="136" stroke="#1A2820" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>
                                    {{-- Inner base glow --}}
                                    <ellipse cx="90" cy="126" rx="34" ry="5" fill="rgba(29,158,117,0.07)"/>
                                </svg>
                            </div>

                            <div class="flex flex-col items-center gap-4 text-center">
                                <h2
                                    class="text-xl sm:text-2xl font-semibold leading-snug"
                                    style="color:#E6EDF3;letter-spacing:-0.015em;"
                                >So I did. This is for you.</h2>

                                <p
                                    class="text-sm sm:text-base leading-relaxed"
                                    style="color:#6B737C;max-width:38ch;"
                                >No ads. No algorithms. No noise. Just peaceful rooms full of people who like the same things you do. If you've ever felt like she did that night — I built this for you.</p>
                            </div>

                            <a
                                href="{{ route('feed') }}"
                                class="px-7 py-3 text-sm font-semibold rounded-xl transition-colors duration-200"
                                style="background:#1D9E75;color:#fff;"
                                onmouseover="this.style.background='#1a9068';"
                                onmouseout="this.style.background='#1D9E75';"
                            >Take a look around</a>
                        </div>
                    </div>
                </div>

            </div>{{-- end slide track --}}
        </div>{{-- end overflow-hidden --}}

        {{-- Progress dots — also clickable for navigation --}}
        <div class="flex items-center justify-center gap-3 py-5 flex-none" aria-label="Story progress">
            <template x-for="i in [0, 1, 2]" :key="i">
                <button
                    x-on:click="slide = i"
                    class="h-1.5 rounded-full transition-all duration-300 focus:outline-none"
                    :class="slide === i ? 'w-5' : 'w-1.5'"
                    :style="slide === i ? 'background:#1D9E75;' : 'background:#252E3D;cursor:pointer;'"
                    :aria-label="'Slide ' + (i + 1)"
                ></button>
            </template>
        </div>

    </div>{{-- end overlay --}}

</body>
</html>
