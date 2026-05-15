<div class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Dashboard Overview</h1>
        <p class="text-xs mt-1" style="color:#8B949E;">Auto-refreshes every 60 seconds.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label' => 'Total Users',     'value' => $this->stats['total_users'],      'color' => '#1D9E75'],
            ['label' => 'Online Now',       'value' => $this->stats['online_now'],        'color' => '#1D9E75'],
            ['label' => 'Active Posts',     'value' => $this->stats['active_posts'],      'color' => '#D29922'],
            ['label' => 'Total Messages',   'value' => number_format($this->stats['total_messages']), 'color' => '#8B949E'],
            ['label' => 'Pending Reports',  'value' => $this->stats['pending_reports'],   'color' => $this->stats['pending_reports'] > 0 ? '#E24B4A' : '#8B949E'],
            ['label' => 'Pending Tags',     'value' => $this->stats['pending_tags'],      'color' => $this->stats['pending_tags'] > 0 ? '#D29922' : '#8B949E'],
            ['label' => 'Suspended Users',  'value' => $this->stats['suspended_users'],   'color' => $this->stats['suspended_users'] > 0 ? '#E24B4A' : '#8B949E'],
            ['label' => 'Serial Reporters', 'value' => $this->stats['serial_reporters'],  'color' => $this->stats['serial_reporters'] > 0 ? '#D29922' : '#8B949E'],
        ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-xl border p-5" style="background:#161B22;border-color:#30363D;">
                <p class="text-xs uppercase tracking-wider mb-1" style="color:#8B949E;">{{ $card['label'] }}</p>
                <p class="text-3xl font-bold" style="color:{{ $card['color'] }};">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>
</div>
