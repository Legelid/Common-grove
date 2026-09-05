@extends('layouts.policy')

@section('title', 'Privacy Policy')

@section('content')

    {{-- Page heading --}}
    <div class="mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:var(--text-faint);">Legal</p>
        <h1 class="text-3xl font-bold mb-3" style="color:var(--text);letter-spacing:-0.02em;">Privacy Policy</h1>
        <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
            Plain English. We respect your data and your right to understand what happens to it.
        </p>
        <p class="text-xs mt-3" style="color:var(--text-faint);">Effective: when your account is created &middot; Beta — may change as the platform develops</p>
    </div>

    {{-- Table of contents --}}
    <nav class="rounded-xl border p-5 mb-10 space-y-1.5" style="background:var(--surface);border-color:var(--border);">
        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--text-faint);">On this page</p>
        @foreach ([
            'what-is-commonground' => 'What is CommonGrove?',
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
                style="color:var(--text-muted);"
                onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'"
            >{{ $label }}</a>
        @endforeach
    </nav>

    {{-- Sections --}}
    <div class="space-y-12">

        <section id="what-is-commonground">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">What is CommonGrove?</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>CommonGrove is a closed, private platform for introverts and gamers to find genuine friendships — no ads, no bots, no public profiles. It is operated by Coldev Enterprises.</p>
                <p>Nothing on CommonGrove is visible to people who are not logged in. This policy explains what data we collect when you use it and how that data is handled.</p>
            </div>
        </section>

        <section id="what-we-collect">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">What we collect</h2>
            <div class="space-y-5 text-sm leading-relaxed" style="color:var(--text);">
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Account information</p>
                    <p>Your gamertag, display name (if you set one), email address, and a hashed copy of your password. We never store your password in plain text — it is hashed using Argon2id before being saved.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Profile information</p>
                    <p>Your avatar, bio, interests and tags you have selected, identity mode setting, and any preferences you configure.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Activity</p>
                    <p>Rooms you join or create, hangout posts you publish, messages you send and receive, and interactions with other users.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Reports</p>
                    <p>Problem reports or safety reports you submit, including any context you provide.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Date of birth</p>
                    <p>Your date of birth is collected during registration. We use it to verify that you meet the minimum age requirement (18 years old) and for optional account features. Your date of birth is never displayed on your profile and is not visible to other users.</p>
                </div>
                <div>
                    <p class="font-medium mb-1.5" style="color:var(--text);">Technical data</p>
                    <p>IP address, browser type, device type, timestamps, and session data. This is collected automatically as part of operating any web service and is used for security and debugging.</p>
                </div>
            </div>
        </section>

        <section id="how-we-use-it">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">How we use it</h2>
            <div class="space-y-2 text-sm leading-relaxed" style="color:var(--text);">
                <p>We use your data to:</p>
                <ul class="space-y-2 mt-3">
                    @foreach ([
                        'Operate the platform — authentication, rooms, messaging, profiles.',
                        'Match you with relevant rooms and hangout posts based on your interests and tags.',
                        'Verify that you meet the minimum age requirement of 18 years old.',
                        'Enable optional account features tied to your date of birth.',
                        'Moderate the community and respond to safety reports.',
                        'Investigate and fix bugs.',
                        'Detect and prevent abuse, spam, and bots.',
                    ] as $item)
                        <li class="flex gap-3">
                            <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:var(--accent);"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section id="what-we-dont-do">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">What we don't do</h2>
            <div class="space-y-2 text-sm leading-relaxed" style="color:var(--text);">
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:var(--accent);"></span>
                        <span><span style="color:var(--text);">We do not sell your personal data.</span> Not to advertisers, not to data brokers, not to anyone.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:var(--accent);"></span>
                        <span><span style="color:var(--text);">There is no advertising on CommonGrove.</span> No ad targeting, no tracking pixels, no behavioural profiling for commercial purposes.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:var(--accent);"></span>
                        <span>Your data is not shared with third parties beyond the infrastructure providers described below, or where legally required.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1 h-1 rounded-full flex-none mt-2" style="background:var(--accent);"></span>
                        <span><span style="color:var(--text);">Your date of birth is never made public.</span> It is not shown on your profile and is not visible to other users. CommonGrove is an 18+ community and does not allow accounts for users under 18.</span>
                    </li>
                </ul>
            </div>
        </section>

        <section id="retention">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Message retention</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>Messages in persistent rooms are stored for approximately 14 days, after which older messages may be removed automatically. You should not rely on CommonGrove as long-term message storage.</p>
                <p>Hangout posts expire after 6 hours by design — that is intentional and part of how the feature works.</p>
                <p>Direct messages are stored as long as the conversation exists. If you delete your account, your messages will be removed or anonymised.</p>
                <p>Moderation logs may retain message content for longer periods where there is a legitimate safety or review reason to do so.</p>
            </div>
        </section>

        <section id="reports">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Reports</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>When you submit a problem report or safety report, its contents — including any related user, room, message context, or screenshot — are reviewed by the site administrator. Reports are never shown to the user being reported.</p>
                <p>Reports may be retained as part of a moderation record. If a report leads to action against an account, a summary of the evidence is kept in case it is needed for review.</p>
            </div>
        </section>

        <section id="third-parties">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Third-party services</h2>
            <div class="space-y-5 text-sm leading-relaxed" style="color:var(--text);">
                <div>
                    <p class="font-medium mb-1" style="color:var(--text);">Cloudflare</p>
                    <p>CommonGrove uses Cloudflare for DDoS protection, DNS, and network routing. Cloudflare may process your IP address and request data. Cloudflare has its own privacy policy.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:var(--text);">Server infrastructure</p>
                    <p>The platform runs on third-party server infrastructure. These providers process data only as needed to host and operate the service, under data processing agreements.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:var(--text);">Email provider</p>
                    <p>Your email address is shared with a transactional email provider solely to send verification emails and password reset links. It is not used for marketing.</p>
                </div>
                <div>
                    <p class="font-medium mb-1" style="color:var(--text);">Payment provider (future)</p>
                    <p>If supporter subscriptions are introduced, payment processing will be handled by a third-party provider. CommonGrove will not store your card details. We'll update this policy before that feature launches.</p>
                </div>
            </div>
        </section>

        <section id="security">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Data security</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>Passwords are stored using Argon2id, a strong one-way hashing algorithm. They are never readable by anyone, including the site owner. Sessions use secure, HTTP-only cookies with strict same-site settings.</p>
                <p>Reasonable technical measures are in place to protect your data. That said, no online system is perfectly secure. If you suspect your account has been compromised, please contact us immediately via the <a href="{{ route('report') }}" style="color:var(--accent);">Report a problem</a> page.</p>
            </div>
        </section>

        <section id="beta">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Beta notice</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>CommonGrove is in beta. Features, data practices, and this policy may evolve as the platform develops. If something significant changes, you'll be notified within the app.</p>
                <p>Using CommonGrove during beta means you accept that some things are still being built and refined.</p>
            </div>
        </section>

        <section id="deletion">
            <h2 class="text-lg font-semibold mb-4 pb-3 border-b" style="color:var(--text);border-color:var(--border);">Account deletion & contact</h2>
            <div class="space-y-3 text-sm leading-relaxed" style="color:var(--text);">
                <p>You can request account deletion or ask any question about your data by using the <a href="{{ route('report') }}" style="color:var(--accent);">Report a problem</a> page. Requests are handled personally by the site administrator.</p>
                <p>Deleted accounts are soft-deleted initially rather than immediately wiped. If you want your data fully removed, say so in your request and it will be done.</p>
            </div>
        </section>

    </div>

    <div class="mt-12 pt-6 border-t" style="border-color:var(--border);">
        <p class="text-xs" style="color:var(--text-faint);">CommonGrove · Coldev Enterprises · Logo by <a href="https://atccreative.com/" target="_blank" rel="noopener" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">AC Creative</a> · <a href="{{ route('report') }}" style="color:var(--text-faint);" onmouseover="this.style.color='var(--text-muted)'" onmouseout="this.style.color='var(--text-faint)'">Contact</a></p>
    </div>

@endsection
