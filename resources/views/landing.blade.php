<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>CommonGround — Find your people</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0D1117] antialiased pt-16">

    {{-- Fixed navigation bar --}}
    <header class="fixed top-0 inset-x-0 z-50 h-16 bg-[#161B22] border-b border-[#30363D] flex items-center px-6">
        <div class="flex items-center flex-1">
            <span class="text-[#E6EDF3] font-bold text-lg tracking-tight">CommonGround</span>
            <span class="bg-[#D29922] text-black text-xs font-bold px-2 py-0.5 rounded-full ml-2">BETA</span>
        </div>
        <nav class="flex items-center gap-3">
            <a href="{{ route('login') }}"
                class="px-4 py-2 text-sm font-medium rounded-xl border border-[#30363D] text-[#8B949E] bg-transparent transition-colors hover:text-[#E6EDF3] hover:border-[#8B949E]"
            >Log in</a>
            <a href="{{ route('register') }}"
                class="px-4 py-2 text-sm font-semibold rounded-xl text-white transition-colors"
                style="background:#1D9E75;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
            >Create account</a>
        </nav>
    </header>

    <main>

        {{-- Hero --}}
        <section class="py-24 px-6 text-center">
            <div class="max-w-4xl mx-auto">
                <span class="inline-block bg-[#1D9E75]/10 text-[#1D9E75] border border-[#1D9E75]/20 rounded-full px-4 py-1 text-sm mb-6">
                    Now in beta — free to join
                </span>

                <h1 class="text-5xl md:text-6xl font-bold text-[#E6EDF3] leading-tight mb-4">
                    A quieter corner of the internet.
                </h1>

                <p class="text-3xl md:text-4xl font-bold text-[#1D9E75] mb-6">
                    Find your people. No pressure.
                </p>

                <p class="text-[#8B949E] text-lg max-w-2xl mx-auto mb-8">
                    No ads. No bots. No fake engagement. CommonGround is a closed, judgment-free space built for
                    people with niche hobbies and interests who struggle to find their people.
                </p>

                <div class="flex gap-4 justify-center flex-wrap mb-8">
                    <a href="{{ route('register') }}"
                        class="px-8 py-3 text-base font-semibold rounded-xl text-white transition-colors"
                        style="background:#1D9E75;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                    >Create free account</a>
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 text-base font-medium rounded-xl border border-[#30363D] text-[#8B949E] transition-colors hover:text-[#E6EDF3] hover:border-[#8B949E]"
                    >Log in</a>
                </div>

                <div class="flex flex-wrap gap-3 justify-center">
                    @foreach (['No real name needed', 'No ads ever', 'Real people only'] as $pill)
                        <span class="bg-[#161B22] border border-[#30363D] rounded-full px-4 py-1.5 text-sm text-[#8B949E]">
                            {{ $pill }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Live stats bar --}}
        @php
            $membersOnline  = \App\Models\User::where('last_seen_at', '>', now()->subMinutes(15))->count();
            $hangoutsToday  = \App\Models\HangoutPost::whereDate('created_at', today())->where('is_active', true)->count();
            $roomsActive    = \App\Models\Conversation::where('type', 'room')->where('is_active', true)->count();
        @endphp
        <div class="bg-[#161B22] border-y border-[#30363D] py-10 mt-4">
            <div class="grid grid-cols-3 max-w-2xl mx-auto divide-x divide-[#30363D] text-center">
                <div class="px-6">
                    <p class="text-3xl font-bold text-[#E6EDF3]">{{ number_format($membersOnline) }}</p>
                    <p class="text-sm text-[#8B949E] mt-1">Members online now</p>
                </div>
                <div class="px-6">
                    <p class="text-3xl font-bold text-[#E6EDF3]">{{ number_format($hangoutsToday) }}</p>
                    <p class="text-sm text-[#8B949E] mt-1">Hangout posts today</p>
                </div>
                <div class="px-6">
                    <p class="text-3xl font-bold text-[#E6EDF3]">{{ number_format($roomsActive) }}</p>
                    <p class="text-sm text-[#8B949E] mt-1">Rooms active right now</p>
                </div>
            </div>
        </div>

        {{-- Features --}}
        <section class="py-20 px-6">
            <p class="text-[#1D9E75] text-sm font-semibold uppercase tracking-wider text-center mb-3">
                Why CommonGround
            </p>
            <h2 class="text-3xl font-bold text-[#E6EDF3] text-center mb-4">
                Everything other platforms get wrong.
            </h2>
            <p class="text-[#8B949E] text-center max-w-2xl mx-auto mb-16">
                We started from scratch and threw out the playbook. No follower counts, no public timelines,
                no algorithmic manipulation. Just genuine connection on your own terms.
            </p>

            @php
            $features = [
                [
                    'title' => 'Interest-based matching',
                    'description' => 'Tag what you love. Find people who get it. Posts and rooms are filtered by your interests automatically.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>',
                ],
                [
                    'title' => 'Private messaging',
                    'description' => 'DMs and group rooms with no public timeline. Everything stays between the people involved.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>',
                ],
                [
                    'title' => 'Hangout posts',
                    'description' => 'Short-lived posts that expire after 6 hours. Say what you are up to and see who wants to join.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                ],
                [
                    'title' => 'Strictly private',
                    'description' => 'Nothing inside is visible to people outside the platform. No lurkers, no anonymous observers, ever.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>',
                ],
                [
                    'title' => 'Friendship milestones',
                    'description' => 'Quiet acknowledgement when a connection becomes a real friendship at 7, 30, and 90 days.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>',
                ],
                [
                    'title' => 'Built-in crisis support',
                    'description' => 'Sensitive keyword detection that quietly surfaces support resources when someone might need them.',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>',
                ],
            ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                @foreach ($features as $feature)
                    <div class="bg-[#161B22] border border-[#30363D] rounded-xl p-6 hover:border-[#1D9E75]/40 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-[#1D9E75]/10 flex items-center justify-center mb-4 text-[#1D9E75]">
                            {!! $feature['icon'] !!}
                        </div>
                        <h3 class="text-lg font-semibold text-[#E6EDF3] mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-[#8B949E] leading-relaxed">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="py-20 px-6 text-center">
            <div class="max-w-2xl mx-auto bg-[#161B22] border border-[#30363D] rounded-2xl p-12">
                <h2 class="text-3xl font-bold text-[#E6EDF3] mb-4">Ready to find your people?</h2>
                <p class="text-[#8B949E] mb-8">
                    Join a growing community of people who are done with noisy social media.
                    It is free, it is private, and it takes less than a minute to get started.
                </p>
                <a href="{{ route('register') }}"
                    class="inline-block w-full sm:w-auto px-10 py-3.5 text-base font-semibold rounded-xl text-white transition-colors"
                    style="background:#1D9E75;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
                >Create your free account</a>
                <p class="mt-4 text-xs text-[#8B949E]">Gamertag only · No real name needed · Takes 60 seconds.</p>
            </div>
        </section>

    </main>

    {{-- Footer --}}
    <footer class="bg-[#161B22] border-t border-[#30363D] py-8 px-6">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-[#8B949E]">
                &copy; {{ date('Y') }} CommonGround. All rights reserved.
            </p>
            <nav class="flex gap-6">
                <a href="{{ route('privacy') }}" class="text-sm text-[#8B949E] transition-colors hover:text-[#1D9E75]">Privacy</a>
                <a href="{{ route('terms') }}" class="text-sm text-[#8B949E] transition-colors hover:text-[#1D9E75]">Terms</a>
            </nav>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
