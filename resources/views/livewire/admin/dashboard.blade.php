<div class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color:var(--text);">Dashboard Overview</h1>
        <p class="text-xs mt-1" style="color:var(--text-muted);">Auto-refreshes every 60 seconds.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label' => 'Total Users',     'value' => $this->stats['total_users'],      'color' => 'var(--accent)'],
            ['label' => 'Online Now',       'value' => $this->stats['online_now'],        'color' => 'var(--accent)'],
            ['label' => 'Active Posts',     'value' => $this->stats['active_posts'],      'color' => '#D29922'],
            ['label' => 'Total Messages',   'value' => number_format($this->stats['total_messages']), 'color' => 'var(--text-muted)'],
            ['label' => 'Pending Reports',  'value' => $this->stats['pending_reports'],   'color' => $this->stats['pending_reports'] > 0 ? 'var(--danger)' : 'var(--text-muted)'],
            ['label' => 'Pending Tags',     'value' => $this->stats['pending_tags'],      'color' => $this->stats['pending_tags'] > 0 ? '#D29922' : 'var(--text-muted)'],
            ['label' => 'Suspended Users',  'value' => $this->stats['suspended_users'],   'color' => $this->stats['suspended_users'] > 0 ? 'var(--danger)' : 'var(--text-muted)'],
            ['label' => 'Serial Reporters', 'value' => $this->stats['serial_reporters'],  'color' => $this->stats['serial_reporters'] > 0 ? '#D29922' : 'var(--text-muted)'],
        ];
        @endphp

        @foreach ($cards as $card)
            <x-card padding="p-5">
                <p class="text-xs uppercase tracking-wider mb-1" style="color:var(--text-muted);">{{ $card['label'] }}</p>
                <p class="text-3xl font-bold" style="color:{{ $card['color'] }};">{{ $card['value'] }}</p>
            </x-card>
        @endforeach
    </div>

    {{-- Quick links --}}
    <div class="mt-8 flex flex-wrap gap-3">
        <x-button variant="secondary" :href="route('admin.rooms')" wire:navigate class="font-medium">🏠 All Rooms</x-button>
        <x-button variant="secondary" :href="route('admin.users')" wire:navigate class="font-medium">👥 Users</x-button>
        <x-button variant="secondary" :href="route('admin.reports')" wire:navigate class="font-medium">🚩 Reports</x-button>
        <x-button variant="secondary" :href="route('admin.crisis')" wire:navigate class="font-medium">🆘 Crisis Log</x-button>
    </div>
</div>
