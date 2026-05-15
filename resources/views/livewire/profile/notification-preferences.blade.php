<div class="space-y-4">
    @if ($prefsMessage)
        <p class="text-sm" style="color:#1D9E75;">{{ $prefsMessage }}</p>
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
            <p class="text-sm" style="color:#E6EDF3;">{{ $label }}</p>
            <button
                type="button"
                wire:click="$toggle('{{ $prop }}')"
                class="relative inline-flex h-5 w-9 items-center rounded-full transition"
                style="background:{{ $this->$prop ? '#1D9E75' : '#21262D' }};"
                role="switch"
                aria-checked="{{ $this->$prop ? 'true' : 'false' }}"
            >
                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition {{ $this->$prop ? 'translate-x-5' : 'translate-x-1' }}"></span>
            </button>
        </div>
    @endforeach

    {{-- Quiet hours --}}
    <div class="pt-2">
        <p class="text-sm font-medium mb-1" style="color:#E6EDF3;">Quiet hours</p>
        <p class="text-xs mb-3" style="color:#8B949E;">No notifications will be sent during these hours.</p>
        <div class="flex items-center gap-3">
            <div>
                <label class="block text-xs mb-1" style="color:#8B949E;">From</label>
                <input type="time" wire:model="quietHoursStart"
                    class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:#8B949E;">To</label>
                <input type="time" wire:model="quietHoursEnd"
                    class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                    style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                    onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
                >
            </div>
        </div>
        @error('quietHoursStart') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
        @error('quietHoursEnd') <p class="mt-1 text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
    </div>

    <button type="button" wire:click="saveNotificationPreferences" wire:loading.attr="disabled" wire:target="saveNotificationPreferences"
        class="px-5 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
        style="background:#1D9E75;color:#fff;" onmouseover="this.style.background='#22B88A'" onmouseout="this.style.background='#1D9E75'"
    >
        <span wire:loading.remove wire:target="saveNotificationPreferences">Save preferences</span>
        <span wire:loading wire:target="saveNotificationPreferences">Saving…</span>
    </button>
</div>
