<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CommonGrove | Find your people</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon2.png') }}">
    @include('partials.fonts')
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        [x-cloak] { display: none !important; }

        .cg-cine-bg {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/forest-path.jpg') }}');
            background-size: cover;
            background-position: center 25%;
            z-index: 0;
            opacity: 0;
            transition: opacity 1200ms ease;
        }
        .cg-cine-bg.is-in { opacity: 1; }

        .cg-cine-overlay-flat {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.25);
            z-index: 1;
        }
        .cg-cine-overlay-radial {
            position: fixed;
            inset: 0;
            background: radial-gradient(
                ellipse 55% 45% at 50% 42%,
                rgba(211,221,220,0.35) 0%,
                rgba(211,221,220,0.22) 35%,
                rgba(211,221,220,0.05) 75%,
                rgba(211,221,220,0) 100%
            );
            z-index: 1;
        }
        .cg-cine-overlay-tint {
            position: fixed;
            inset: 0;
            background: rgba(125,163,142,0.20);
            z-index: 1;
            pointer-events: none;
        }
        .cg-cine-content {
            position: relative;
            z-index: 2;
        }

        .cg-cine-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            padding: 2rem;
            transition: transform 500ms ease;
        }
        /* Shifts the whole hero group (logo, tagline, button) together as one
           unit so the remaining visible strip re-centers around the panel's
           actual width — half of .cg-auth-panel's 420px — rather than each
           element guessing its own viewport-relative offset and drifting
           out of alignment with the others. */
        .cg-cine-center.is-shifted {
            transform: translateX(-210px);
        }
        @media (max-width: 767px) {
            .cg-cine-center.is-shifted { transform: none; }
        }

        .cg-logo-lockup {
            position: relative;
            width: 100%;
            height: 120px;
            margin-bottom: 1rem;
        }
        .cg-logo-lockup.is-shifted {
            transform: scale(0.7);
            transition: all 500ms ease;
        }

        /* Anchored to the lockup's own centre point (left/top 50% + negative
           margin of half its box) rather than a transform: translate(-50%,-50%)
           centring hack — that would collide with the translateY/scale/
           translateX values each animation stage needs to own outright. */
        .cg-cine-leaf {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 64px;
            height: 64px;
            margin-left: -32px;
            margin-top: -32px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5)) drop-shadow(0 1px 3px rgba(0,0,0,0.6));
            opacity: 0;
            transform: translateY(-120px) scale(2);
        }
        .cg-cine-leaf.is-dropped {
            opacity: 1;
            transform: translateY(0) scale(2);
            transition: opacity 700ms cubic-bezier(0.34,1.2,0.64,1), transform 700ms cubic-bezier(0.34,1.2,0.64,1);
        }
        .cg-cine-leaf.is-shrunk {
            transform: translateY(0) scale(1);
            transition: transform 500ms cubic-bezier(0.34,0.8,0.64,1);
        }
        .cg-cine-leaf.is-slid {
            transition: transform 700ms cubic-bezier(0.25,0.46,0.45,0.94);
        }

        .cg-cine-wordmark {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateY(-50%);
            font-family: var(--font-display);
            font-size: clamp(3rem, 7vw, 5rem);
            font-weight: 700;
            color: #FFFFFF;
            text-shadow: 0 2px 20px rgba(0,0,0,0.5), 0 1px 4px rgba(0,0,0,0.8);
            letter-spacing: -0.02em;
            white-space: nowrap;
        }

        /* Letters fade in one at a time, left to right, roughly tracking the
           leaf's phase-4 slide (700ms) — as if the leaf is drawing them out. */
        .cg-cine-letter {
            display: inline-block;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 350ms cubic-bezier(0.25,0.46,0.45,0.94), transform 350ms cubic-bezier(0.25,0.46,0.45,0.94);
        }
        .cg-cine-wordmark.is-revealed .cg-cine-letter {
            opacity: 1;
            transform: translateY(0);
        }
        .cg-cine-letter:nth-child(1)  { transition-delay: 150ms; }
        .cg-cine-letter:nth-child(2)  { transition-delay: 200ms; }
        .cg-cine-letter:nth-child(3)  { transition-delay: 250ms; }
        .cg-cine-letter:nth-child(4)  { transition-delay: 300ms; }
        .cg-cine-letter:nth-child(5)  { transition-delay: 350ms; }
        .cg-cine-letter:nth-child(6)  { transition-delay: 400ms; }
        .cg-cine-letter:nth-child(7)  { transition-delay: 450ms; }
        .cg-cine-letter:nth-child(8)  { transition-delay: 500ms; }
        .cg-cine-letter:nth-child(9)  { transition-delay: 550ms; }
        .cg-cine-letter:nth-child(10) { transition-delay: 600ms; }
        .cg-cine-letter:nth-child(11) { transition-delay: 650ms; }

        /* "Common" (letters 1-6) is lighter than "Grove" (letters 7-11, which
           keeps the wordmark's base 700 weight) — matches the weight split
           used for the CommonGrove wordmark elsewhere in the site. */
        .cg-cine-letter:nth-child(-n+6) { font-weight: 400; }

        .cg-cine-tagline {
            font-family: var(--font-body);
            font-size: 1.575rem;
            font-weight: 600;
            color: rgba(255,255,255,0.95);
            text-shadow: 0 1px 6px rgba(0,0,0,0.95), 0 2px 32px rgba(0,0,0,0.9), 0 4px 64px rgba(0,0,0,0.75);
            margin-top: 0.75rem;
            letter-spacing: 0.01em;
            opacity: 0;
            transform: translateY(8px);
            transition: all 600ms ease;
        }
        .cg-cine-tagline.is-in { opacity: 1; transform: translateY(0); }
        .cg-cine-tagline.is-shifted { opacity: 0 !important; transition: opacity 300ms ease; }

        .cg-cine-cta {
            margin-top: 2.5rem;
            background: rgba(125,163,142,0.9);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-pill);
            padding: 1.125rem 3.25rem;
            font-family: var(--font-body);
            font-size: 1.25rem;
            font-weight: 600;
            cursor: pointer;
            backdrop-filter: blur(8px);
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 200ms ease, background 200ms ease, transform 200ms ease, box-shadow 200ms ease;
        }
        .cg-cine-cta.is-in { opacity: 1; transform: translateY(0); transition: all 500ms ease 200ms; }
        .cg-cine-cta:hover {
            background: rgba(125,163,142,1);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        /* ── Auth panel ──────────────────────────────────────────────────── */
        .cg-auth-panel {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: 420px;
            max-width: 100vw;
            background: rgba(30,35,25,0.92);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(125,163,142,0.2);
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            overflow-y: auto;
            transform: translateX(100%);
            opacity: 0;
            transition: all 500ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .cg-auth-panel.is-open {
            transform: translateX(0);
            opacity: 1;
        }

        .cg-auth-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: transparent;
            border: none;
            color: rgba(245,240,232,0.5);
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 150ms ease;
        }
        .cg-auth-close:hover { color: rgba(245,240,232,0.9); }

        .cg-auth-heading {
            font-family: var(--font-display);
            font-size: 1.75rem;
            color: #F5F0E8;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .cg-auth-subtext {
            font-size: 0.9375rem;
            color: rgba(245,240,232,0.6);
            margin-bottom: 2rem;
            text-align: center;
        }

        .cg-auth-switch {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: rgba(245,240,232,0.5);
        }
        .cg-auth-switch button {
            background: none;
            border: none;
            font-family: var(--font-body);
            font-size: 0.875rem;
            color: rgba(125,163,142,0.9);
            cursor: pointer;
            transition: color 150ms ease;
        }
        .cg-auth-switch button:hover { color: #8DB39E; }

        /*
         * The embedded <livewire:auth.login /> / <livewire:auth.register />
         * components keep every bit of their real validation, rate limiting
         * and field set intact — only their appearance is re-skinned here so
         * they match the cinematic glass panel regardless of the visitor's
         * light/dark theme preference (the panel itself is always dark, but
         * --surface/--text flip per theme, so the components' own
         * theme-driven classes can't be relied on inside it).
         */
        .cg-auth-mode > div > div:first-child { display: none; } /* login partial's own duplicate logo block */
        .cg-auth-mode > div > p:last-of-type { display: none; }  /* login/register partial's own nav-away switch link */

        .cg-auth-mode input:not([type="checkbox"]),
        .cg-auth-mode select {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(125,163,142,0.3) !important;
            border-radius: var(--radius-md) !important;
            color: #F5F0E8 !important;
            font-family: var(--font-body) !important;
        }
        .cg-auth-mode input:not([type="checkbox"])::placeholder { color: rgba(245,240,232,0.3) !important; }
        .cg-auth-mode input:not([type="checkbox"]):focus,
        .cg-auth-mode select:focus {
            border-color: rgba(125,163,142,0.8) !important;
            outline: none !important;
            background: rgba(255,255,255,0.12) !important;
            box-shadow: none !important;
        }
        .cg-auth-mode label { color: rgba(245,240,232,0.6) !important; }
        .cg-auth-mode a,
        .cg-auth-mode .text-accent { color: rgba(125,163,142,0.9) !important; }
        .cg-auth-mode p { color: rgba(245,240,232,0.6); }
        .cg-auth-mode .text-text-muted,
        .cg-auth-mode .text-text-faint { color: rgba(245,240,232,0.5) !important; }
        .cg-auth-mode .text-text { color: #F5F0E8 !important; }
        .cg-auth-mode .bg-surface-raised { background: rgba(255,255,255,0.08) !important; }
        .cg-auth-mode button.absolute.inset-y-0.right-3 { color: rgba(245,240,232,0.5) !important; }
        .cg-auth-mode button.absolute.inset-y-0.right-3:hover { color: rgba(245,240,232,0.9) !important; }
        .cg-auth-mode button[type="submit"] {
            background: #7DA38E !important;
            color: #1A1C14 !important;
            border-radius: var(--radius-pill) !important;
            width: 100% !important;
        }
        .cg-auth-mode button[type="submit"]:hover { background: #8DB39E !important; }

        @media (max-width: 767px) {
            .cg-cine-center { padding: 1.5rem; }
            .cg-cine-wordmark { font-size: clamp(2rem, 8vw, 3rem); }
            .cg-cine-tagline { font-size: clamp(0.844rem, 3.375vw, 1.125rem); }

            .cg-auth-panel {
                width: 100vw;
                border-left: none;
                border-top: 1px solid rgba(125,163,142,0.2);
                height: 85vh;
                top: auto;
                bottom: 0;
                right: 0;
                border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                transform: translateY(100%);
            }
            .cg-auth-panel.is-open { transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .cg-cine-bg,
            .cg-cine-center,
            .cg-logo-lockup,
            .cg-cine-leaf,
            .cg-cine-wordmark,
            .cg-cine-letter,
            .cg-cine-tagline,
            .cg-cine-cta,
            .cg-auth-panel {
                transition-duration: 0ms !important;
                transition-delay: 0ms !important;
            }
        }
    </style>
</head>
<body
    class="antialiased"
    x-data="{
        phase: 0,
        loginOpen: false,
        authMode: 'login',

        // The leaf's phase-4 slide distance and the wordmark's resting anchor
        // are computed from the wordmark's *actual* rendered width — not
        // guessed constants — so the leaf+text pair is genuinely centred as
        // a unit regardless of viewport width or how wide 'CommonGrove'
        // renders at a given clamp() font-size. Layout is text-left,
        // leaf-right, matching the logo lockup used in the site header.
        // Falls back to sane defaults until the first measurement runs.
        leafShiftPx: 80,
        textAnchorPx: -280,

        measureLockup() {
            const textEl = this.$refs.wordmark;
            if (!textEl) return;
            const leafHalf = 32; // half of the 64px leaf box
            const gap = 14;      // visual gap between text and leaf
            const textWidth = textEl.offsetWidth;
            const total = textWidth + gap + (leafHalf * 2);
            this.textAnchorPx = -(total / 2);
            this.leafShiftPx = -(total / 2) + textWidth + gap + leafHalf;
        },

        init() {
            this.measureLockup();
            window.addEventListener('resize', () => this.measureLockup());

            const noMotion = window.matchMedia(
              '(prefers-reduced-motion: reduce)').matches;

            if (noMotion) {
              this.phase = 5;
              return;
            }

            // Stage 1: background fades in
            setTimeout(() => this.phase = 1, 200);
            // Stage 2: big leaf drops from top
            setTimeout(() => this.phase = 2, 800);
            // Stage 3: leaf shrinks to logo size
            setTimeout(() => this.phase = 3, 1800);
            // Stage 4: leaf slides left, text reveals
            setTimeout(() => this.phase = 4, 2400);
            // Stage 5: tagline + button fade in
            setTimeout(() => this.phase = 5, 3100);
        },

        openLogin(mode) {
            this.loginOpen = true;
            if (mode) this.authMode = mode;
        },
        closeLogin() {
            this.loginOpen = false;
        }
    }"
>
    <div class="cg-cine-bg" :class="{ 'is-in': phase >= 1 }"></div>
    <div class="cg-cine-overlay-flat"></div>
    <div class="cg-cine-overlay-radial"></div>
    <div class="cg-cine-overlay-tint"></div>

    <div class="cg-cine-content">
        <div class="cg-cine-center" :class="{ 'is-shifted': loginOpen }">

            <div class="cg-logo-lockup" :class="{ 'is-shifted': loginOpen }">
                <span
                    class="cg-cine-wordmark"
                    :class="{ 'is-revealed': phase >= 4 }"
                    :style="'margin-left: ' + textAnchorPx + 'px;'"
                    x-ref="wordmark"
                ><span class="cg-cine-letter">C</span><span class="cg-cine-letter">o</span><span class="cg-cine-letter">m</span><span class="cg-cine-letter">m</span><span class="cg-cine-letter">o</span><span class="cg-cine-letter">n</span><span class="cg-cine-letter">G</span><span class="cg-cine-letter">r</span><span class="cg-cine-letter">o</span><span class="cg-cine-letter">v</span><span class="cg-cine-letter">e</span></span>
                <img
                    src="{{ asset('images/logo-leaf-crop.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="cg-cine-leaf"
                    :class="{ 'is-dropped': phase >= 2, 'is-shrunk': phase >= 3, 'is-slid': phase >= 4 }"
                    :style="phase >= 4 ? ('transform: translateX(' + leafShiftPx + 'px) scale(1);') : ''"
                >
            </div>

            <p class="cg-cine-tagline" :class="{ 'is-in': phase >= 5, 'is-shifted': loginOpen }">
                A quieter corner of the internet.
            </p>

            <button type="button" class="cg-cine-cta" :class="{ 'is-in': phase >= 5 }" @click="openLogin('login')">
                Step in &rarr;
            </button>

        </div>
    </div>

    <div class="cg-auth-panel" :class="{ 'is-open': loginOpen }" role="dialog" aria-modal="true" aria-label="Sign in or join">
        <button type="button" class="cg-auth-close" @click="closeLogin()" aria-label="Close">&#10005;</button>

        <div class="cg-auth-mode" x-show="authMode === 'login'" x-cloak>
            <h2 class="cg-auth-heading">Welcome back.</h2>
            <p class="cg-auth-subtext">Step back into the grove.</p>

            <livewire:auth.login />

            <p class="cg-auth-switch">
                New here?
                <button type="button" @click="authMode = 'register'">Join the grove</button>
            </p>
        </div>

        <div class="cg-auth-mode" x-show="authMode === 'register'" x-cloak>
            <h2 class="cg-auth-heading">Find your people.</h2>
            <p class="cg-auth-subtext">No pressure. Take your time.</p>

            <livewire:auth.register />

            <p class="cg-auth-switch">
                Already here?
                <button type="button" @click="authMode = 'login'">Log in</button>
            </p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
