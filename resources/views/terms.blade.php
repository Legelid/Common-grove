@extends('layouts.policy')

@section('title', 'Terms of Service')

@section('content')

    {{-- Page heading --}}
    <div class="mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:#3d4451;">Legal</p>
        <h1 class="text-3xl font-bold mb-3" style="color:#E6EDF3;letter-spacing:-0.02em;">Terms of Service</h1>
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            By using CommonGround, you agree to these terms. We've kept them as readable as possible.
        </p>
        <p class="text-xs mt-3" style="color:#3d4451;">Effective: when your account is created &middot; Beta — may change as the platform develops</p>
    </div>

    {{-- Table of contents --}}
    <nav class="rounded-xl border p-5 mb-10 space-y-1.5" style="background:#161B22;border-color:#30363D;">
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#3d4451;">On this page</p>
        @foreach ([
            'acceptance'    => 'Acceptance of terms',
            'beta'          => 'Beta status',
            'accounts'      => 'Your account',
            'content'       => 'User content',
            'acceptable-use'=> 'Acceptable use',
            'moderation'    => 'Moderation',
            'availability'  => 'Availability',
            'advice'        => 'Not professional advice',
            'crisis'        => 'Crisis disclaimer',
            'supporters'    => 'Supporter subscriptions',
            'liability'     => 'Limitation of liability',
            'changes'       => 'Changes to terms',
            'contact'       => 'Contact',
        ] as $anchor => $label)
            <a
                href="#{{ $anchor }}"
                class="block text-sm transition"
                style="color:#8B949E;"
                onmouseover="this.style.color='#1D9E75'" onmouseout="this.style.color='#8B949E'"
            >{{ $label }}</a>
        @endforeach
    </nav>

    {{-- Sections --}}
    <div class="space-y-12">

        <section id="acceptance">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Acceptance of terms</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>By creating an account and using CommonGround, you agree to these Terms of Service and the <a href="{{ route('guidelines') }}" style="color:#1D9E75;">Community Guidelines</a>. If you do not agree, please do not use the platform.</p>
                <p>These terms apply alongside our <a href="{{ route('privacy') }}" style="color:#1D9E75;">Privacy Policy</a>, which explains how your data is handled.</p>
            </div>
        </section>

        <section id="beta">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Beta status</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround is currently in beta. This means some features are still being built, things may break, and aspects of the platform — including these terms — may change as development continues.</p>
                <p>Using CommonGround during beta means you accept that it is a work in progress. We'll do our best to be transparent when things change.</p>
            </div>
        </section>

        <section id="accounts">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Your account</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <ul class="space-y-3">
                    @foreach ([
                        'CommonGrove is an 18+ community. You must be at least 18 years old to create an account.',
                        'You must provide your accurate date of birth during registration. Providing a false date of birth to bypass the age requirement is a violation of these terms.',
                        'You are responsible for keeping your account credentials secure. Do not share your password.',
                        'You may only have one account. Creating duplicate accounts to evade a moderation action is not permitted.',
                        'You must provide a working email address for account verification.',
                        'Your gamertag must follow the platform\'s gamertag rules — 3 to 20 characters, starting with a letter.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#8B949E;"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section id="content">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">User content</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>You own the content you post on CommonGround. By posting it, you grant CommonGround a limited, non-exclusive licence to store and display it as part of operating the platform.</p>
                <p>You are responsible for what you post. Content you share — messages, hangout posts, bios, room descriptions — is visible to other logged-in users of the platform.</p>
                <p>Do not post content that violates these terms or the <a href="{{ route('guidelines') }}" style="color:#1D9E75;">Community Guidelines</a>. We may remove content that does.</p>
            </div>
        </section>

        <section id="acceptable-use">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Acceptable use</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>You must not use CommonGround to:</p>
                <ul class="space-y-3 mt-3">
                    @foreach ([
                        'Harass, threaten, stalk, or target other users.',
                        'Post hate speech or content that attacks people based on identity, disability, neurodivergence, gender, sexuality, religion, politics, or personal circumstances.',
                        'Spam, run bots, or use automated tools to interact with the platform.',
                        'Post, share, or solicit illegal content of any kind.',
                        'Impersonate other users or attempt to deceive the moderation team.',
                        'Use the platform as a dating or hookup service — CommonGround is strictly platonic.',
                        'Share private information about other users without their consent.',
                        'Attempt to circumvent rate limits, security measures, or access controls.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#E24B4A;"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section id="moderation">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Moderation</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround's administrators (Grovekeepers) reserve the right to:</p>
                <ul class="space-y-3 mt-3">
                    @foreach ([
                        'Remove or hide content that violates these terms or the Community Guidelines.',
                        'Suspend or permanently ban accounts that break the rules.',
                        'Close or remove rooms.',
                        'Take moderation action without prior notice where safety or another user\'s wellbeing is at risk.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#8B949E;"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3">Moderation decisions are made in good faith. At this stage of the beta, there is no formal appeals process, but genuine concerns can be raised via the <a href="{{ route('report') }}" style="color:#1D9E75;">Report a problem</a> page.</p>
            </div>
        </section>

        <section id="availability">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Availability</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround does not guarantee uninterrupted availability, particularly during the beta period. Planned or unplanned downtime may occur. We'll try to give notice when possible.</p>
            </div>
        </section>

        <section id="advice">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Not professional advice</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>Nothing on CommonGround — including conversations between users, anything a room describes itself as, or anything shared by the platform — constitutes professional medical, legal, psychological, or financial advice. If you need support in any of those areas, please speak to a qualified professional.</p>
            </div>
        </section>

        <section id="crisis">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Crisis disclaimer</h2>
            <div class="rounded-xl border px-5 py-4 text-sm leading-relaxed" style="background:#161B22;border-color:#30363D;color:#C9D1D9;">
                <p><span style="color:#E6EDF3;font-weight:500;">CommonGrove is not a crisis service, therapy platform, or emergency support system.</span> It is a social space for conversation and connection. If you or someone you know is in immediate danger, please contact emergency services in your area.</p>
            </div>
        </section>

        <section id="supporters">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Supporter subscriptions</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround may introduce an optional supporter subscription (approximately $1/month) that unlocks small extras, such as a supporter badge or cosmetic features. This is entirely optional.</p>
                <p>Supporter status does not grant moderation authority or any special power over other users. It is a way to help keep the lights on, nothing more.</p>
                <p>When this feature launches, payment processing will be handled by a third-party provider. Refunds and cancellations will be managed through that provider. We'll provide full details at launch.</p>
            </div>
        </section>

        <section id="liability">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Limitation of liability</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>To the fullest extent permitted by applicable law, CommonGround and Coldev Enterprises are not liable for:</p>
                <ul class="space-y-3 mt-3">
                    @foreach ([
                        'Content posted, shared, or communicated by other users.',
                        'Loss of data due to bugs, downtime, or account deletion.',
                        'Any indirect, incidental, or consequential damages arising from your use of the platform.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#8B949E;"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3">You use CommonGround at your own discretion.</p>
            </div>
        </section>

        <section id="changes">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Changes to terms</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>These terms may be updated as CommonGround grows. If something significant changes, you'll be notified within the app. Continued use of CommonGround after a change is published constitutes acceptance of the updated terms.</p>
            </div>
        </section>

        <section id="contact">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Contact</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>Questions, concerns, or legal notices can be submitted via the <a href="{{ route('report') }}" style="color:#1D9E75;">Report a problem</a> page. We read every message.</p>
            </div>
        </section>

    </div>

    <div class="mt-12 pt-6 border-t" style="border-color:#21262D;">
        <p class="text-xs" style="color:#3d4451;">CommonGround · Coldev Enterprises · <a href="{{ route('report') }}" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Contact</a></p>
    </div>

@endsection
