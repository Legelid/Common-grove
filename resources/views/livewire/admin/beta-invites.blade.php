<div class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color:#E6EDF3;">Beta Invites — FirstRoots</h1>
        <p class="text-xs mt-1" style="color:#8B949E;">Manage founding member invites.</p>
    </div>

    {{-- Feedback --}}
    @if ($feedback)
        <div class="mb-5 rounded-lg px-4 py-2.5 text-sm" style="background:{{ $feedbackError ? 'rgba(226,75,74,0.1)' : 'rgba(29,158,117,0.1)' }};color:{{ $feedbackError ? '#E24B4A' : '#1D9E75' }};">
            {{ $feedback }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        @foreach ([['Total', $this->stats['total']], ['Claimed', $this->stats['claimed']], ['Unclaimed', $this->stats['unclaimed']]] as [$label, $count])
        <div class="rounded-xl border p-4" style="background:#161B22;border-color:#30363D;">
            <p class="text-2xl font-bold" style="color:#E6EDF3;">{{ $count }}</p>
            <p class="text-xs mt-1" style="color:#8B949E;">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Generate forms --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Single invite --}}
        <div class="rounded-xl border p-5" style="background:#161B22;border-color:#30363D;">
            <h2 class="text-sm font-semibold mb-3" style="color:#E6EDF3;">Generate single invite</h2>
            <div class="flex gap-2">
                <input
                    type="email"
                    wire:model="singleEmail"
                    placeholder="email@example.com"
                    class="flex-1 rounded-lg px-3 py-2 text-sm"
                    style="background:#0D1117;border:1px solid #30363D;color:#E6EDF3;"
                >
                <button
                    type="button"
                    wire:click="generateSingle"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                    style="background:#1D9E75;color:#fff;"
                >Generate</button>
            </div>
            @error('singleEmail') <p class="text-xs mt-1.5" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>

        {{-- Bulk invites --}}
        <div class="rounded-xl border p-5" style="background:#161B22;border-color:#30363D;">
            <h2 class="text-sm font-semibold mb-3" style="color:#E6EDF3;">Bulk generate (one email per line)</h2>
            <textarea
                wire:model="bulkEmails"
                rows="3"
                placeholder="user1@example.com&#10;user2@example.com"
                class="w-full rounded-lg px-3 py-2 text-sm mb-2 resize-none"
                style="background:#0D1117;border:1px solid #30363D;color:#E6EDF3;"
            ></textarea>
            <button
                type="button"
                wire:click="generateBulk"
                wire:loading.attr="disabled"
                class="w-full py-2 text-sm font-semibold rounded-lg transition disabled:opacity-50"
                style="background:#1D9E75;color:#fff;"
            >Generate all</button>
            @error('bulkEmails') <p class="text-xs mt-1.5" style="color:#E24B4A;">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Invites table --}}
    <div class="rounded-xl border overflow-hidden" style="border-color:#30363D;">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#1C2333;border-bottom:1px solid #30363D;">
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Email</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Token</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Sent</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Claimed</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Claimed by</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold" style="color:#8B949E;">Created</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->invites as $invite)
                <tr style="border-bottom:1px solid #21262D;" class="hover:bg-[#1C2333]/30 transition">
                    <td class="px-4 py-3" style="color:#E6EDF3;">{{ $invite->email }}</td>
                    <td class="px-4 py-3 font-mono text-xs" style="color:#8B949E;">{{ substr($invite->token, 0, 12) }}…</td>
                    <td class="px-4 py-3 text-xs" style="color:#8B949E;">
                        {{ $invite->sent_at ? $invite->sent_at->diffForHumans() : '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if ($invite->isClaimed())
                            <span style="color:#1D9E75;">✓ {{ $invite->claimed_at->diffForHumans() }}</span>
                        @else
                            <span style="color:#3d4451;">Unclaimed</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:#8B949E;">
                        {{ $invite->claimedBy?->gamertag ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:#8B949E;">{{ $invite->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            {{-- Copy link --}}
                            <button
                                type="button"
                                x-data
                                x-on:click="navigator.clipboard.writeText('https://www.common-grove.com/beta/claim/{{ $invite->token }}').then(() => { $el.textContent = 'Copied!'; setTimeout(() => $el.textContent = 'Copy link', 1500); })"
                                class="px-3 py-1.5 text-xs rounded-lg transition"
                                style="border:1px solid #30363D;color:#8B949E;"
                                onmouseover="this.style.borderColor='rgba(139,148,158,0.5)'" onmouseout="this.style.borderColor='#30363D'"
                            >Copy link</button>
                            {{-- Send email --}}
                            @unless ($invite->isClaimed())
                            <button
                                type="button"
                                wire:click="sendEmail('{{ $invite->id }}')"
                                wire:loading.attr="disabled"
                                class="px-3 py-1.5 text-xs rounded-lg transition disabled:opacity-50"
                                style="background:rgba(29,158,117,0.15);color:#1D9E75;border:1px solid rgba(29,158,117,0.3);"
                            >{{ $invite->sent_at ? 'Resend' : 'Send invite' }}</button>
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm" style="color:#3d4451;">No invites generated yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
