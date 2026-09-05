<div class="space-y-4">
    @if ($prefsMessage)
        <p class="text-sm text-accent">{{ $prefsMessage }}</p>
    @endif

    @php
        $toggles = [
            'friendRequest'     => 'Friend requests',
            'friendAccepted'    => 'Friend request accepted',
            'newMessage'        => 'New messages',
            'messageRequest'    => 'Message requests',
            'hangoutFromFriend' => 'Hangout posts from friends',
            'weeklyMatch'       => 'Weekly match suggestion',
            'milestone'         => 'Friendship milestones',
        ];
    @endphp

    @foreach ($toggles as $prop => $label)
        <div class="flex items-center justify-between py-1.5">
            <p class="text-sm" style="color:var(--text);">{{ $label }}</p>
            <x-toggle wire:click="$toggle('{{ $prop }}')" :checked="$this->$prop" role="switch" aria-checked="{{ $this->$prop ? 'true' : 'false' }}" />
        </div>
    @endforeach

    {{-- Quiet hours --}}
    <div class="pt-2">
        <p class="text-sm font-medium mb-1" style="color:var(--text);">Quiet hours</p>
        <p class="text-xs mb-3" style="color:var(--text-muted);">No notifications will be sent during these hours.</p>
        <div class="flex items-center gap-3">
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-muted);">From</label>
                <x-input type="time" wire:model="quietHoursStart" />
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-muted);">To</label>
                <x-input type="time" wire:model="quietHoursEnd" />
            </div>
        </div>
        @error('quietHoursStart') <p class="mt-1 text-xs" style="color:var(--danger);">{{ $message }}</p> @enderror
        @error('quietHoursEnd') <p class="mt-1 text-xs" style="color:var(--danger);">{{ $message }}</p> @enderror
    </div>

    <x-button type="button" wire:click="saveNotificationPreferences" wire:loading.attr="disabled" wire:target="saveNotificationPreferences" variant="primary" class="!px-5">
        <span wire:loading.remove wire:target="saveNotificationPreferences">Save preferences</span>
        <span wire:loading wire:target="saveNotificationPreferences">Saving…</span>
    </x-button>
</div>
