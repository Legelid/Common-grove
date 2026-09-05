<div class="p-8">
    <h1 class="text-2xl font-bold mb-6 font-display" style="color:var(--text);">User Management</h1>

    <div class="mb-6 max-w-sm">
        <x-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by gamertag or email…" class="!rounded-xl" />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider border-b" style="color:var(--text-muted);border-color:var(--border);">
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
            <tbody class="divide-y" style="divide-color:var(--border);">
                @foreach ($this->users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="align-top" style="border-color:var(--border);">
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-1.5">
                                @if ($user->isOnline())
                                    <span class="w-1.5 h-1.5 rounded-full flex-none" style="background:var(--accent);" title="Online now"></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full flex-none" style="background:var(--border);"></span>
                                @endif
                                <a href="{{ route('profile.show', $user->gamertag) }}" target="_blank"
                                    class="font-medium transition" style="color:var(--accent);" onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'"
                                >{{ $user->gamertag }}</a>
                                @if ($user->dismiss_count >= 5)
                                    <span class="ml-0.5 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(210,153,34,0.15);color:#D29922;">Serial reporter</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">
                            {{ $user->email }}
                            @if (! $user->hasVerifiedEmail())
                                <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full" style="background:rgba(249,115,22,0.15);color:#f97316;">Unverified</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">{{ $user->last_seen_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="py-3 pr-4">
                            @php $level = $user->activeStrikeLevel(); @endphp
                            @if ($level > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                    style="{{ match($level) { 1 => 'background:rgba(210,153,34,0.15);color:#D29922;', 2 => 'background:rgba(249,115,22,0.15);color:#f97316;', 3 => 'background:rgba(var(--danger-rgb),0.15);color:var(--danger);' } }}"
                                >S{{ $level }}</span>
                            @else
                                <span style="color:var(--border);">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            @if ($user->isSuspended())
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(var(--danger-rgb),0.15);color:var(--danger);">Suspended</span>
                            @else
                                <span class="text-xs" style="color:var(--text-muted);">Active</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <button wire:click="toggleAdmin('{{ $user->id }}')"
                                class="px-2 py-0.5 text-xs font-semibold rounded-full transition"
                                style="{{ $user->is_admin ? 'background:rgba(210,153,34,0.15);color:#D29922;' : 'background:var(--surface-raised);color:var(--text-muted);' }}"
                                @if ($user->is(auth()->user())) disabled title="Cannot modify own admin status" @endif
                            >{{ $user->is_admin ? 'Admin' : 'User' }}</button>
                        </td>
                        <td class="py-3">
                            @if ($user->is(auth()->user()))
                                <span class="text-xs" style="color:var(--text-muted);" title="You cannot perform moderation actions on your own account.">
                                    Your account
                                </span>
                            @elseif ($actionUserId === $user->id)
                                <div class="rounded-lg p-3 space-y-2 max-w-xs" style="background:var(--surface-raised);">
                                    @if ($pendingAction !== 'unsuspend')
                                        <x-input type="text" wire:model="strikeReason" placeholder="Reason (required)" class="!text-xs !py-1.5" />
                                        @error('strikeReason') <p class="text-xs" style="color:var(--danger);">{{ $message }}</p> @enderror
                                    @else
                                        <p class="text-xs" style="color:#D29922;">Remove suspension and clear active strikes for {{ $user->gamertag }}?</p>
                                    @endif
                                    <div class="flex gap-2">
                                        <x-button wire:click="executeAction" variant="destructive" class="!px-2.5 !py-1 !text-xs">Confirm</x-button>
                                        <x-button wire:click="cancelAction" variant="secondary" class="!px-2.5 !py-1 !text-xs">Cancel</x-button>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    <x-button wire:click="toggleStrikes('{{ $user->id }}')" variant="secondary" class="!px-2.5 !py-1 !text-xs">Strikes</x-button>
                                    @if (! $user->hasVerifiedEmail())
                                        <button wire:click="resendVerification('{{ $user->id }}')"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);" onmouseover="this.style.background='rgb(var(--accent-rgb) / 0.25)'" onmouseout="this.style.background='rgb(var(--accent-rgb) / 0.15)'"
                                        >Resend verify</button>
                                        @if ($lastSentVerificationId === $user->id)
                                            <span class="text-xs font-medium" style="color:var(--accent);">Sent!</span>
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
                                            style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);"
                                        >Unsuspend</button>
                                    @else
                                        <button wire:click="startAction('{{ $user->id }}', 'suspend')"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition"
                                            style="background:rgba(var(--danger-rgb),0.15);color:var(--danger);"
                                        >Suspend</button>
                                    @endif
                                </div>

                                @if ($viewingStrikesId === $user->id)
                                    <div class="mt-2 space-y-1">
                                        @forelse ($user->strikes()->orderByDesc('created_at')->get() as $strike)
                                            <div class="text-xs rounded px-2 py-1" style="background:var(--surface);color:var(--text-muted);">
                                                S{{ $strike->level }} · {{ $strike->reason }}
                                                @if ($strike->expires_at) · expires {{ $strike->expires_at->format('Y-m-d') }}
                                                @else · permanent @endif
                                            </div>
                                        @empty
                                            <p class="text-xs" style="color:var(--text-muted);">No strikes.</p>
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
