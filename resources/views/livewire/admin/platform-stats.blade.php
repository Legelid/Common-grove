<div class="p-8 space-y-10">
    <h1 class="text-2xl font-bold font-display" style="color:var(--text);">Platform Stats</h1>

    {{-- Reports overview --}}
    <section>
        <h2 class="text-xs uppercase tracking-wider mb-3" style="color:var(--text-muted);">Reports Overview (all time)</h2>
        <div class="flex flex-wrap gap-4">
            @foreach ([
                'Total'     => ['value' => $this->reportCounts['total'],     'color' => 'var(--text)'],
                'Pending'   => ['value' => $this->reportCounts['pending'],   'color' => $this->reportCounts['pending'] > 0 ? 'var(--danger)' : 'var(--text-muted)'],
                'Actioned'  => ['value' => $this->reportCounts['actioned'],  'color' => '#D29922'],
                'Dismissed' => ['value' => $this->reportCounts['dismissed'], 'color' => 'var(--text-muted)'],
            ] as $label => $meta)
                <x-card padding="px-5 py-4" class="min-w-32">
                    <p class="text-xs uppercase tracking-wider mb-1" style="color:var(--text-muted);">{{ $label }}</p>
                    <p class="text-3xl font-bold" style="color:{{ $meta['color'] }};">{{ number_format($meta['value']) }}</p>
                </x-card>
            @endforeach

            <x-card padding="px-5 py-4" class="min-w-32">
                <p class="text-xs uppercase tracking-wider mb-1" style="color:var(--text-muted);">Avg Tags / User</p>
                <p class="text-3xl font-bold" style="color:var(--accent);">{{ $this->avgTagsPerUser }}</p>
            </x-card>
        </div>
    </section>

    {{-- New users + hangout posts --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:var(--text-muted);">New Users · Last 30 Days</h2>
            <x-card padding="" class="overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:var(--text-muted);border-color:var(--border);">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-right">New Users</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:var(--border);">
                        @forelse ($this->newUsersPerDay as $row)
                            <tr style="border-color:var(--border);">
                                <td class="px-4 py-2 text-xs" style="color:var(--text-muted);">{{ $row['date'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:var(--text);">{{ number_format($row['count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:var(--text-muted);">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>

        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:var(--text-muted);">Hangout Posts · Last 7 Days</h2>
            <x-card padding="" class="overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:var(--text-muted);border-color:var(--border);">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-right">Posts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:var(--border);">
                        @forelse ($this->postsPerDay as $row)
                            <tr style="border-color:var(--border);">
                                <td class="px-4 py-2 text-xs" style="color:var(--text-muted);">{{ $row['date'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:var(--text);">{{ number_format($row['count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:var(--text-muted);">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </section>

    {{-- Top tags + most active users --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:var(--text-muted);">Top 20 Tags by Usage</h2>
            <x-card padding="" class="overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:var(--text-muted);border-color:var(--border);">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Tag</th>
                            <th class="px-4 py-2.5 text-right">Uses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:var(--border);">
                        @forelse ($this->popularTags as $tag)
                            <tr style="border-color:var(--border);">
                                <td class="px-4 py-2 text-xs" style="color:var(--text);">{{ $tag['name'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:var(--text-muted);">{{ number_format($tag['usage_count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:var(--text-muted);">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>

        <div>
            <h2 class="text-xs uppercase tracking-wider mb-3" style="color:var(--text-muted);">Top 10 Most Active Users</h2>
            <x-card padding="" class="overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase border-b" style="color:var(--text-muted);border-color:var(--border);">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Gamertag</th>
                            <th class="px-4 py-2.5 text-right">Messages</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:var(--border);">
                        @forelse ($this->mostActiveUsers as $user)
                            <tr style="border-color:var(--border);">
                                <td class="px-4 py-2 text-xs">
                                    <a href="{{ route('profile.show', $user['gamertag']) }}" target="_blank"
                                        class="transition" style="color:var(--accent);"
                                        onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'"
                                    >{{ $user['gamertag'] }}</a>
                                </td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" style="color:var(--text-muted);">{{ number_format($user['message_count']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-xs" style="color:var(--text-muted);">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </section>
</div>
