<div class="p-8 space-y-10">
    <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Platform Stats</h1>

    {{-- Reports overview --}}
    <section>
        <h2 class="text-xs uppercase tracking-wider mb-3" style="color:#8B949E;">Reports Overview (all time)</h2>
        <div class="flex flex-wrap gap-4">
            @foreach ([
                'Total'     => ['value' => $this->reportCounts['total'],     'color' => '#E6EDF3'],
                'Pending'   => ['value' => $this->reportCounts['pending'],   'color' => $this->reportCounts['pending'] > 0 ? '#E24B4A' : '#8B949E'],
                'Actioned'  => ['value' => $this->reportCounts['actioned'],  'color' => '#D29922'],
                'Dismissed' => ['value' => $this->reportCounts['dismissed'], 'color' => '#8B949E'],
            ] as $label => $meta)
                <div class="rounded-xl border px-5 py-4 min-w-32" style="background:#161B22;border-color:#30363D;">
                    <p class="text-xs uppercase tracking-wider mb-1" style="color:#8B949E;">{{ $label }}</p>
                    <p class="text-3xl font-bold" style="color:{{ $meta['color'] }};">{{ number_format($meta['value']) }}</p>
                </div>
            @endforeach

            <div class="rounded-xl border px-5 py-4 min-w-32" style="background:#161B22;border-color:#30363D;">
                <p class="text-xs uppercase tracking-wider mb-1" style="color:#8B949E;">Avg Tags / User</p>
                <p class="text-3xl font-bold" style="color:#1D9E75;">{{ $this->avgTagsPerUser }}</p>
            </div>
        </div>
    </section>

    {{-- New users + hangout posts --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:#8B949E;">New Users — Last 30 Days</h2>
            <div class="rounded-xl border overflow-hidden" style="background:#161B22;border-color:#30363D;">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:#8B949E;border-color:#30363D;">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-right">New Users</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:#30363D;">
                        @forelse ($this->newUsersPerDay as $row)
                            <tr style="border-color:#30363D;">
                                <td class="px-4 py-2 text-xs" style="color:#8B949E;">{{ $row['date'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:#E6EDF3;">{{ number_format($row['count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:#8B949E;">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:#8B949E;">Hangout Posts — Last 7 Days</h2>
            <div class="rounded-xl border overflow-hidden" style="background:#161B22;border-color:#30363D;">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:#8B949E;border-color:#30363D;">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-right">Posts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:#30363D;">
                        @forelse ($this->postsPerDay as $row)
                            <tr style="border-color:#30363D;">
                                <td class="px-4 py-2 text-xs" style="color:#8B949E;">{{ $row['date'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:#E6EDF3;">{{ number_format($row['count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:#8B949E;">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Top tags + most active users --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:#8B949E;">Top 20 Tags by Usage</h2>
            <div class="rounded-xl border overflow-hidden" style="background:#161B22;border-color:#30363D;">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:#8B949E;border-color:#30363D;">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Tag</th>
                            <th class="px-4 py-2.5 text-right">Uses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:#30363D;">
                        @forelse ($this->popularTags as $tag)
                            <tr style="border-color:#30363D;">
                                <td class="px-4 py-2 text-xs" style="color:#E6EDF3;">{{ $tag['name'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:#8B949E;">{{ number_format($tag['usage_count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:#8B949E;">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:#8B949E;">Top 10 Most Active Users</h2>
            <div class="rounded-xl border overflow-hidden" style="background:#161B22;border-color:#30363D;">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:#8B949E;border-color:#30363D;">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Gamertag</th>
                            <th class="px-4 py-2.5 text-right">Messages</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:#30363D;">
                        @forelse ($this->mostActiveUsers as $user)
                            <tr style="border-color:#30363D;">
                                <td class="px-4 py-2 text-xs">
                                    <a href="{{ route('profile.show', $user['gamertag']) }}" target="_blank"
                                        class="transition" style="color:#1D9E75;"
                                        onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'"
                                    >{{ $user['gamertag'] }}</a>
                                </td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:#8B949E;">{{ number_format($user['message_count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:#8B949E;">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
