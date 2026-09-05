<div class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold font-display" style="color:var(--text);">Beta Invites | FirstRoots</h1>
        <p class="text-xs mt-1" style="color:var(--text-muted);">Manage founding member invites.</p>
    </div>

    {{-- Feedback --}}
    @if ($feedback)
        <div class="mb-5 rounded-lg px-4 py-2.5 text-sm" style="background:{{ $feedbackError ? 'rgba(var(--danger-rgb),0.1)' : 'rgb(var(--accent-rgb) / 0.1)' }};color:{{ $feedbackError ? 'var(--danger)' : 'var(--accent)' }};">
            {{ $feedback }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        @foreach ([['Total', $this->stats['total']], ['Claimed', $this->stats['claimed']], ['Unclaimed', $this->stats['unclaimed']]] as [$label, $count])
        <x-card padding="p-4">
            <p class="text-2xl font-bold" style="color:var(--text);">{{ $count }}</p>
            <p class="text-xs mt-1" style="color:var(--text-muted);">{{ $label }}</p>
        </x-card>
        @endforeach
    </div>

    {{-- Generate forms --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Single invite --}}
        <x-card padding="p-5">
            <h2 class="text-sm font-semibold mb-3" style="color:var(--text);">Generate single invite</h2>
            <div class="flex gap-2">
                <x-input
                    type="email"
                    wire:model="singleEmail"
                    placeholder="email@example.com"
                    class="flex-1"
                />
                <x-button type="button" wire:click="generateSingle" wire:loading.attr="disabled" variant="primary">Generate</x-button>
            </div>
            @error('singleEmail') <p class="text-xs mt-1.5" style="color:var(--danger);">{{ $message }}</p> @enderror
        </x-card>

        {{-- Bulk invites --}}
        <x-card padding="p-5">
            <h2 class="text-sm font-semibold mb-3" style="color:var(--text);">Bulk generate (one email per line)</h2>
            <x-textarea
                wire:model="bulkEmails"
                rows="3"
                placeholder="user1@example.com&#10;user2@example.com"
                class="w-full mb-2"
            />
            <x-button type="button" wire:click="generateBulk" wire:loading.attr="disabled" variant="primary" class="w-full">Generate all</x-button>
            @error('bulkEmails') <p class="text-xs mt-1.5" style="color:var(--danger);">{{ $message }}</p> @enderror
        </x-card>
    </div>

    {{-- Invites table --}}
    <div class="rounded-xl border overflow-hidden" style="border-color:var(--border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:var(--surface);border-bottom:1px solid var(--border);">
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Email</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Token</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Sent</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Claimed</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Claimed by</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--text-muted);">Created</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->invites as $invite)
                <tr style="border-bottom:1px solid var(--border);" class="hover:bg-surface-raised/30 transition">
                    <td class="px-4 py-3" style="color:var(--text);">{{ $invite->email }}</td>
                    <td class="px-4 py-3 font-mono text-xs" style="color:var(--text-muted);">{{ substr($invite->token, 0, 12) }}…</td>
                    <td class="px-4 py-3 text-xs" style="color:var(--text-muted);">
                        {{ $invite->sent_at ? $invite->sent_at->diffForHumans() : '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if ($invite->isClaimed())
                            <span style="color:var(--accent);">✓ {{ $invite->claimed_at->diffForHumans() }}</span>
                        @else
                            <span style="color:var(--text-faint);">Unclaimed</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:var(--text-muted);">
                        {{ $invite->claimedBy?->gamertag ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:var(--text-muted);">{{ $invite->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            {{-- Copy link --}}
                            <button
                                type="button"
                                x-data
                                x-on:click="navigator.clipboard.writeText('https://www.common-grove.com/beta/claim/{{ $invite->token }}').then(() => { $el.textContent = 'Copied!'; setTimeout(() => $el.textContent = 'Copy link', 1500); })"
                                class="px-3 py-1.5 text-xs rounded-lg transition"
                                style="border:1px solid var(--border);color:var(--text-muted);"
                                onmouseover="this.style.borderColor='var(--text-faint)'" onmouseout="this.style.borderColor='var(--border)'"
                            >Copy link</button>
                            {{-- Send email --}}
                            @unless ($invite->isClaimed())
                            <button
                                type="button"
                                wire:click="sendEmail('{{ $invite->id }}')"
                                wire:loading.attr="disabled"
                                class="px-3 py-1.5 text-xs rounded-lg transition disabled:opacity-50"
                                style="background:rgb(var(--accent-rgb) / 0.15);color:var(--accent);border:1px solid rgb(var(--accent-rgb) / 0.3);"
                            >{{ $invite->sent_at ? 'Resend' : 'Send invite' }}</button>
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm" style="color:var(--text-faint);">No invites generated yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
