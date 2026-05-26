<div class="p-8">
    <h1 class="text-2xl font-bold mb-6" style="color:#E6EDF3;">User Management</h1>

    <div class="mb-6 max-w-sm">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by gamertag or email…"
            class="w-full rounded-xl px-4 py-2.5 text-sm focus:outline-none"
            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
            onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
        >
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider border-b" style="color:#8B949E;border-color:#30363D;">
                <tr>
                    <th class="pb-3 pr-4">Gamertag</th>
                    <th class="pb-3 pr-4">Email</th>
                    <th class="pb-3 pr-4">Joined</th>
                    <th class="pb-3 pr-4">Last seen</th>
                    <th class="pb-3 pr-4">Strike</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3 pr-4">Admin</th>
                    <th class="pb-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="divide-color:#30363D;">
                @foreach ($this->users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="align-top" style="border-color:#30363D;">
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-1.5">
                                @if ($user->isOnline())
                                    <span class="w-1.5 h-1.5 rounded-full flex-none" style="background:#1D9E75;" title="Online now"></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full flex-none" style="background:#30363D;"></span>
                                @endif
                                <a href="{{ route('profile.show', $user->gamertag) }}" target="_blank"
                                    class="font-medium transition" style="color:#1D9E75;" onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'"
                                >{{ $user->gamertag }}</a>
                                @if ($user->dismiss_count >= 5)
                                    <span class="ml-0.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(210,153,34,0.15);color:#D29922;">Serial reporter</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">
                            {{ $user->email }}
                            @if (! $user->hasVerifiedEmail())
                                <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(249,115,22,0.15);color:#f97316;">Unverified</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">{{ $user->last_seen_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="py-3 pr-4">
                            @php $level = $user->activeStrikeLevel(); @endphp
                            @if ($level > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                    style="{{ match($level) { 1 => 'background:rgba(210,153,34,0.15);color:#D29922;', 2 => 'background:rgba(249,115,22,0.15);color:#f97316;', 3 => 'background:rgba(226,75,74,0.15);color:#E24B4A;' } }}"
                                >S{{ $level }}</span>
                            @else
                                <span style="color:#30363D;">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            @if ($user->isSuspended())
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(226,75,74,0.15);color:#E24B4A;">Suspended</span>
                            @else
                                <span class="text-xs" style="color:#8B949E;">Active</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <button wire:click="toggleAdmin('{{ $user->id }}')"
                                class="px-2 py-0.5 text-xs font-semibold rounded-full transition"
                                style="{{ $user->is_admin ? 'background:rgba(210,153,34,0.15);color:#D29922;' : 'background:#21262D;color:#8B949E;' }}"
                                @if ($user->is(auth()->user())) disabled title="Cannot modify own admin status" @endif
                            >{{ $user->is_admin ? 'Admin' : 'User' }}</button>
                        </td>
                        <td class="py-3">
                            @if ($user->is(auth()->user()))
                                <span class="text-xs" style="color:#8B949E;" title="You cannot perform moderation actions on your own account.">
                                    Your account
                                </span>
                            @elseif ($actionUserId === $user->id)
                                <div class="rounded-lg p-3 space-y-2 max-w-xs" style="background:#21262D;">
                                    @if ($pendingAction !== 'unsuspend')
                                        <input type="text" wire:model="strikeReason" placeholder="Reason (required)"
                                            class="w-full rounded-lg px-3 py-1.5 text-xs focus:outline-none"
                                            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
                                        >
                                        @error('strikeReason') <p class="text-xs" style="color:#E24B4A;">{{ $message }}</p> @enderror
                                    @else
                                        <p class="text-xs" style="color:#D29922;">Remove suspension and clear active strikes for {{ $user->gamertag }}?</p>
                                    @endif
                                    <div class="flex gap-2">
                                        <button wire:click="executeAction"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:#E24B4A;color:#fff;" onmouseover="this.style.background='#f05252'" onmouseout="this.style.background='#E24B4A'"
                                        >Confirm</button>
                                        <button wire:click="cancelAction"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:#1C2333;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                        >Cancel</button>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    <button wire:click="toggleStrikes('{{ $user->id }}')"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                        style="background:#21262D;color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'"
                                    >Strikes</button>
                                    @if (! $user->hasVerifiedEmail())
                                        <button wire:click="resendVerification('{{ $user->id }}')"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(29,158,117,0.15);color:#1D9E75;" onmouseover="this.style.background='rgba(29,158,117,0.25)'" onmouseout="this.style.background='rgba(29,158,117,0.15)'"
                                        >Resend verify</button>
                                        @if ($lastSentVerificationId === $user->id)
                                            <span class="text-xs font-medium" style="color:#1D9E75;">Sent!</span>
                                        @endif
                                    @endif
                                    <button wire:click="startAction('{{ $user->id }}', 'warn')"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                        style="background:rgba(210,153,34,0.15);color:#D29922;"
                                    >Warn</button>
                                    <button wire:click="startAction('{{ $user->id }}', 'restrict')"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                        style="background:rgba(249,115,22,0.15);color:#f97316;"
                                    >Restrict</button>
                                    @if ($user->isSuspended())
                                        <button wire:click="startAction('{{ $user->id }}', 'unsuspend')"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(29,158,117,0.15);color:#1D9E75;"
                                        >Unsuspend</button>
                                    @else
                                        <button wire:click="startAction('{{ $user->id }}', 'suspend')"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(226,75,74,0.15);color:#E24B4A;"
                                        >Suspend</button>
                                    @endif
                                </div>

                                @if ($viewingStrikesId === $user->id)
                                    <div class="mt-2 space-y-1">
                                        @forelse ($user->strikes()->orderByDesc('created_at')->get() as $strike)
                                            <div class="text-xs rounded px-2 py-1" style="background:#1C2333;color:#8B949E;">
                                                S{{ $strike->level }} · {{ $strike->reason }}
                                                @if ($strike->expires_at) · expires {{ $strike->expires_at->format('Y-m-d') }}
                                                @else · permanent @endif
                                            </div>
                                        @empty
                                            <p class="text-xs" style="color:#8B949E;">No strikes.</p>
                                        @endforelse
                                    </div>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $this->users->links() }}</div>
</div>
