@extends('layouts.policy')

@section('title', 'Privacy Policy')

@section('content')

    {{-- Page heading --}}
    <div class="mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:#3d4451;">Legal</p>
        <h1 class="text-3xl font-bold mb-3" style="color:#E6EDF3;letter-spacing:-0.02em;">Privacy Policy</h1>
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            Plain English. We respect your data and your right to understand what happens to it.
        </p>
        <p class="text-xs mt-3" style="color:#3d4451;">Effective: when your account is created &middot; Beta — may change as the platform develops</p>
    </div>

    {{-- Table of contents --}}
    <nav class="rounded-xl border p-5 mb-10 space-y-1.5" style="background:#161B22;border-color:#30363D;">
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:#3d4451;">On this page</p>
        @foreach ([
            'what-is-commonground' => 'What is CommonGround?',
            'what-we-collect'      => 'What we collect',
            'how-we-use-it'        => 'How we use it',
            'what-we-dont-do'      => 'What we don\'t do',
            'retention'            => 'Message retention',
            'reports'              => 'Reports',
            'third-parties'        => 'Third-party services',
            'security'             => 'Data security',
            'beta'                 => 'Beta notice',
            'deletion'             => 'Account deletion & contact',
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

        <section id="what-is-commonground">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">What is CommonGround?</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround is a closed, private platform for introverts and gamers to find genuine friendships — no ads, no bots, no public profiles. It is operated by Coldev Enterprises.</p>
                <p>Nothing on CommonGround is visible to people who are not logged in. This policy explains what data we collect when you use it and how that data is handled.</p>
            </div>
        </section>

        <section id="what-we-collect">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">What we collect</h2>
            <div class="space-y-5 text-sm leading-relaxed" style="color:#C9D1D9;">
                <div>
                    <p class="font-medium mb-1.5" style="color:#E6EDF3;">Account information</p>
                    <p>Your gamertag, display name (if you set one), email address, and a hashed copy of your password. We never store your password in plain text — it is hashed using Argon2id before being saved.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:#E6EDF3;">Profile information</p>
                    <p>Your avatar, bio, interests and tags you have selected, identity mode setting, and any preferences you configure.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:#E6EDF3;">Activity</p>
                    <p>Rooms you join or create, hangout posts you publish, messages you send and receive, and interactions with other users.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:#E6EDF3;">Reports</p>
                    <p>Problem reports or safety reports you submit, including any context you provide.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:#E6EDF3;">Technical data</p>
                    <p>IP address, browser type, device type, timestamps, and session data. This is collected automatically as part of operating any web service and is used for security and debugging.</p>
                </div>
            </div>
        </section>

        <section id="how-we-use-it">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">How we use it</h2>
            <div class="space-y-2 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>We use your data to:</p>
                <ul class="space-y-2 mt-3">
                    @foreach ([
                        'Operate the platform — authentication, rooms, messaging, profiles.',
                        'Match you with relevant rooms and hangout posts based on your interests and tags.',
                        'Moderate the community and respond to safety reports.',
                        'Investigate and fix bugs.',
                        'Detect and prevent abuse, spam, and bots.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#1D9E75;"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section id="what-we-dont-do">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">What we don't do</h2>
            <div class="space-y-2 text-sm leading-relaxed" style="color:#C9D1D9;">
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#1D9E75;"></span>
                        <span><span style="color:#E6EDF3;">We do not sell your personal data.</span> Not to advertisers, not to data brokers, not to anyone.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#1D9E75;"></span>
                        <span><span style="color:#E6EDF3;">There is no advertising on CommonGround.</span> No ad targeting, no tracking pixels, no behavioural profiling for commercial purposes.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:#1D9E75;"></span>
                        <span>Your data is not shared with third parties beyond the infrastructure providers described below, or where legally required.</span>
                    </li>
                </ul>
            </div>
        </section>

        <section id="retention">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Message retention</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>Messages in persistent rooms are stored for approximately 14 days, after which older messages may be removed automatically. You should not rely on CommonGround as long-term message storage.</p>
                <p>Hangout posts expire after 6 hours by design — that is intentional and part of how the feature works.</p>
                <p>Direct messages are stored as long as the conversation exists. If you delete your account, your messages will be removed or anonymised.</p>
                <p>Moderation logs may retain message content for longer periods where there is a legitimate safety or review reason to do so.</p>
            </div>
        </section>

        <section id="reports">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Reports</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>When you submit a problem report or safety report, its contents — including any related user, room, message context, or screenshot — are reviewed by the site administrator. Reports are never shown to the user being reported.</p>
                <p>Reports may be retained as part of a moderation record. If a report leads to action against an account, a summary of the evidence is kept in case it is needed for review.</p>
            </div>
        </section>

        <section id="third-parties">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Third-party services</h2>
            <div class="space-y-5 text-sm leading-relaxed" style="color:#C9D1D9;">
                <div>
                    <p class="font-medium mb-1" style="color:#E6EDF3;">Cloudflare</p>
                    <p>CommonGround uses Cloudflare for DDoS protection, DNS, and network routing. Cloudflare may process your IP address and request data. Cloudflare has its own privacy policy.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:#E6EDF3;">Server infrastructure</p>
                    <p>The platform runs on third-party server infrastructure. These providers process data only as needed to host and operate the service, under data processing agreements.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:#E6EDF3;">Email provider</p>
                    <p>Your email address is shared with a transactional email provider solely to send verification emails and password reset links. It is not used for marketing.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:#E6EDF3;">Payment provider (future)</p>
                    <p>If supporter subscriptions are introduced, payment processing will be handled by a third-party provider. CommonGround will not store your card details. We'll update this policy before that feature launches.</p>
                </div>
            </div>
        </section>

        <section id="security">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Data security</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>Passwords are stored using Argon2id, a strong one-way hashing algorithm. They are never readable by anyone, including the site owner. Sessions use secure, HTTP-only cookies with strict same-site settings.</p>
                <p>Reasonable technical measures are in place to protect your data. That said, no online system is perfectly secure. If you suspect your account has been compromised, please contact us immediately via the <a href="{{ route('report') }}" style="color:#1D9E75;">Report a problem</a> page.</p>
            </div>
        </section>

        <section id="beta">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Beta notice</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>CommonGround is in beta. Features, data practices, and this policy may evolve as the platform develops. If something significant changes, you'll be notified within the app.</p>
                <p>Using CommonGround during beta means you accept that some things are still being built and refined.</p>
            </div>
        </section>

        <section id="deletion">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:#E6EDF3;border-color:#21262D;">Account deletion & contact</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:#C9D1D9;">
                <p>You can request account deletion or ask any question about your data by using the <a href="{{ route('report') }}" style="color:#1D9E75;">Report a problem</a> page. Requests are handled personally by the site administrator.</p>
                <p>Deleted accounts are soft-deleted initially rather than immediately wiped. If you want your data fully removed, say so in your request and it will be done.</p>
            </div>
        </section>

    </div>

    <div class="mt-12 pt-6 border-t" style="border-color:#21262D;">
        <p class="text-xs" style="color:#3d4451;">CommonGround · Coldev Enterprises · <a href="{{ route('report') }}" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Contact</a></p>
    </div>

@endsection
