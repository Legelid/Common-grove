@extends('layouts.policy')

@section('title', 'Community Guidelines')

@section('content')

    {{-- Page heading --}}
    <div class="mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:#3d4451;">Community</p>
        <h1 class="text-3xl font-bold mb-3" style="color:#E6EDF3;letter-spacing:-0.02em;">Community Guidelines</h1>
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            CommonGrove is an 18+ community meant to be calm, low-pressure, and safe enough to actually talk. These guidelines exist to keep it that way.
        </p>
    </div>

    {{-- Rules --}}
    <div class="space-y-px">

        @php
            $rules = [
                [
                    'num'   => '1',
                    'title' => 'Be kind.',
                    'body'  => 'You don\'t have to be best friends with everyone. But basic decency is the floor. Treat people the way you\'d want to be treated in a space where you felt safe.',
                ],
                [
                    'num'   => '2',
                    'title' => 'Do not harass, threaten, or target people.',
                    'body'  => 'This includes following someone across rooms to bother them, sending unwanted messages after being asked to stop, making threats of any kind, or coordinating with others to target a person.',
                ],
                [
                    'num'   => '3',
                    'title' => 'Do not attack people over who they are.',
                    'body'  => 'No hate speech, slurs, or attacks based on identity, disability, neurodivergence, gender, sexuality, religion, politics, ethnicity, background, or personal circumstances. People are allowed to exist here as they are.',
                ],
                [
                    'num'   => '4',
                    'title' => 'This is not a dating app.',
                    'body'  => 'CommonGrove is a platonic friendship platform. Pursuing romantic or sexual connections with other users — especially after they\'ve made it clear they\'re not interested — is not acceptable here.',
                ],
                [
                    'num'   => '5',
                    'title' => 'Respect boundaries.',
                    'body'  => 'If someone says they don\'t want to talk, that\'s enough. You don\'t need a reason. Move on. Continued contact after someone has set a boundary is harassment.',
                ],
                [
                    'num'   => '6',
                    'title' => 'Do not spam, scam, bot, or advertise.',
                    'body'  => 'No mass messages, no referral links, no automated accounts, no pyramid schemes, no "DM me for this amazing opportunity." CommonGrove is not a marketing channel.',
                ],
                [
                    'num'   => '7',
                    'title' => 'Do not share private information.',
                    'body'  => 'Do not share someone\'s real name, location, contact details, or any other personal information they haven\'t made public themselves — including your own if you\'d prefer to keep it private.',
                ],
                [
                    'num'   => '8',
                    'title' => 'Do not pressure people to respond.',
                    'body'  => 'People here may be anxious, busy, exhausted, or just need time. Sending follow-up messages to push someone to reply faster isn\'t okay. A lack of response is also an answer.',
                ],
                [
                    'num'   => '9',
                    'title' => 'Use reports responsibly.',
                    'body'  => 'Reports are for genuine concerns — not for settling arguments or getting back at someone. Submitting false or bad-faith reports wastes moderation time and may itself lead to action.',
                ],
                [
                    'num'   => '10',
                    'title' => 'If someone is in crisis, point them toward real help.',
                    'body'  => 'CommonGrove is a social space for conversation and connection — it is not a crisis service, therapy platform, or emergency support system. If someone seems to be in genuine danger, encourage them to contact emergency services or a crisis line in their area. You can also report the situation to us.',
                ],
            ];
        @endphp

        @foreach ($rules as $rule)
            <div class="flex gap-5 py-6 border-b" style="border-color:#21262D;">
                <div class="flex-none w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold flex-none mt-0.5" style="background:#21262D;color:#8B949E;">{{ $rule['num'] }}</div>
                <div class="flex-1">
                    <p class="text-sm font-semibold mb-1.5" style="color:#E6EDF3;">{{ $rule['title'] }}</p>
                    <p class="text-sm leading-relaxed" style="color:#C9D1D9;">{{ $rule['body'] }}</p>
                </div>
            </div>
        @endforeach

    </div>

    {{-- 18+ notice --}}
    <div class="mt-10 rounded-xl border px-5 py-4 text-sm leading-relaxed" style="background:#161B22;border-color:#30363D;color:#C9D1D9;">
        <p><span style="color:#E6EDF3;font-weight:500;">CommonGrove is an 18+ community.</span> By creating an account you confirm that you are at least 18 years old. Accounts found to belong to users under 18 will be removed.</p>
    </div>

    {{-- Crisis disclaimer --}}
    <div class="mt-4 rounded-xl border px-5 py-4 text-sm leading-relaxed" style="background:#161B22;border-color:#30363D;color:#C9D1D9;">
        <p><span style="color:#E6EDF3;font-weight:500;">CommonGrove is not a crisis service, therapy platform, or emergency support system.</span> It is a social space for conversation and connection. If you or someone you know is in immediate danger, please contact emergency services in your area.</p>
    </div>

    {{-- Room moderators --}}
    <div class="mt-5 rounded-xl border p-6 space-y-3" style="background:#161B22;border-color:#30363D;">
        <h2 class="text-base font-semibold" style="color:#E6EDF3;">Room owners & moderators</h2>
        <p class="text-sm leading-relaxed" style="color:#C9D1D9;">
            People who create rooms can set a tone for their space and guide conversations within it. Room-level rules are allowed as long as they do not contradict these community guidelines. Room owners are not platform administrators — they have authority within their own room, not over other users more broadly.
        </p>
    </div>

    {{-- Enforcement --}}
    <div class="mt-5 rounded-xl border p-6 space-y-3" style="background:#161B22;border-color:#30363D;">
        <h2 class="text-base font-semibold" style="color:#E6EDF3;">Enforcement</h2>
        <p class="text-sm leading-relaxed" style="color:#C9D1D9;">
            Platform administrators (Grovekeepers) can step in anywhere on the platform. Repeated or serious rule-breaking can lead to a warning, a temporary suspension, or a permanent ban — depending on what happened. We try to be fair, but safety comes first.
        </p>
    </div>

    {{-- Reporting --}}
    <div class="mt-5 rounded-xl border p-6 space-y-3" style="background:#161B22;border-color:#30363D;">
        <h2 class="text-base font-semibold" style="color:#E6EDF3;">Reporting a problem</h2>
        <p class="text-sm leading-relaxed" style="color:#C9D1D9;">
            If something is wrong — a rule is being broken, someone made you feel unsafe, or you just noticed a bug — use the <a href="{{ route('report') }}" style="color:#1D9E75;">Report a problem</a> page. Reports go directly to the site administrator. Every report is read personally.
        </p>
    </div>

    {{-- Beta note --}}
    <div class="mt-10 py-6 border-t" style="border-color:#21262D;">
        <p class="text-sm leading-relaxed" style="color:#8B949E;">
            CommonGrove is in beta — things won't be perfect yet. But reports help. If something feels off, say so.
        </p>
    </div>

    <div class="mt-4 pt-4 border-t" style="border-color:#21262D;">
        <p class="text-xs" style="color:#3d4451;">CommonGrove · Coldev Enterprises · <a href="{{ route('report') }}" style="color:#3d4451;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#3d4451'">Contact</a></p>
    </div>

@endsection
