<div class="p-8">
    <h1 class="text-2xl font-bold mb-6 font-display" style="color:var(--text);">All Rooms</h1>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <x-input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search title, content, or gamertag…"
            class="!rounded-xl w-72"
        />

        <div class="flex gap-1 rounded-xl p-1" style="background:var(--surface);border:1px solid var(--border);">
            @foreach (['all' => 'All', 'active' => 'Active', 'expired' => 'Expired'] as $val => $label)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                    style="{{ $statusFilter === $val ? 'background:var(--surface-raised);color:var(--text);' : 'color:var(--text-muted);' }}"
                >{{ $label }}</button>
            @endforeach
        </div>

        <div class="flex gap-1 rounded-xl p-1" style="background:var(--surface);border:1px solid var(--border);">
            @foreach (['all' => 'All types', 'official' => 'CommonGrove', 'user' => 'User-created'] as $val => $label)
                <button
                    type="button"
                    wire:click="$set('typeFilter', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                    style="{{ $typeFilter === $val ? 'background:var(--surface-raised);color:var(--text);' : 'color:var(--text-muted);' }}"
                >{{ $label }}</button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider border-b" style="color:var(--text-muted);border-color:var(--border);">
                <tr>
                    <th class="pb-3 pr-4">Room</th>
                    <th class="pb-3 pr-4">Creator</th>
                    <th class="pb-3 pr-4">Tags</th>
                    <th class="pb-3 pr-4">Participants</th>
                    <th class="pb-3 pr-4">Messages</th>
                    <th class="pb-3 pr-4">Created</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Link</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="divide-color:var(--border);">
                @forelse ($this->rooms as $room)
                    @php
                        $isExpired = !$room->is_persistent && $room->expires_at && $room->expires_at->isPast();
                    @endphp
                    <tr wire:key="room-{{ $room->id }}" class="align-top" style="border-color:var(--border);">

                        {{-- Room title/content + type badges --}}
                        <td class="py-3 pr-4 max-w-xs">
                            <div class="flex flex-wrap gap-1 mb-1">
                                @if ($room->is_official)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgb(var(--accent-rgb) / 0.12);color:var(--accent);">CommonGrove</span>
                                @endif
                                @if ($room->is_persistent)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgb(var(--accent-rgb) / 0.08);color:var(--accent);">Always-open</span>
                                @else
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgba(210,153,34,0.10);color:#D29922;">Temporary</span>
                                @endif
                            </div>
                            @if ($room->title)
                                <p class="font-medium text-sm leading-snug" style="color:var(--text);">{{ $room->title }}</p>
                                <p class="text-xs leading-snug mt-0.5" style="color:var(--text-faint);">{{ Str::limit($room->content, 80) }}</p>
                            @else
                                <p class="text-sm leading-snug" style="color:var(--text);">{{ Str::limit($room->content, 100) }}</p>
                            @endif
                        </td>

                        {{-- Creator --}}
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">
                            @if ($room->is_official)
                                <span style="color:var(--accent);">CommonGrove</span>
                            @elseif ($room->user)
                                <a href="{{ route('admin.users') }}" class="transition" style="color:var(--text-muted);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">
                                    {{ $room->user->gamertag }}
                                </a>
                            @else
                                <span style="color:var(--border);">—</span>
                            @endif
                        </td>

                        {{-- Tags --}}
                        <td class="py-3 pr-4">
                            <div class="flex flex-wrap gap-1 max-w-[12rem]">
                                @forelse ($room->tags->take(4) as $tag)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full" style="background:var(--surface-raised);color:var(--text-muted);">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-xs" style="color:var(--border);">No tags</span>
                                @endforelse
                                @if ($room->tags->count() > 4)
                                    <span class="text-xs" style="color:var(--text-faint);">+{{ $room->tags->count() - 4 }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Participants --}}
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">
                            {{ $room->participant_count ?? 0 }}
                        </td>

                        {{-- Messages --}}
                        <td class="py-3 pr-4 text-xs" style="color:var(--text-muted);">
                            {{ $room->message_count ?? 0 }}
                        </td>

                        {{-- Created --}}
                        <td class="py-3 pr-4 text-xs whitespace-nowrap" style="color:var(--text-muted);">
                            {{ $room->created_at->format('Y-m-d') }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 pr-4">
                            @if (! $room->is_active)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(var(--danger-rgb),0.12);color:var(--danger);">Inactive</span>
                            @elseif ($isExpired)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(154,143,126,0.15);color:var(--text-faint);">Ended</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgb(var(--accent-rgb) / 0.12);color:var(--accent);">Active</span>
                            @endif
                        </td>

                        {{-- Link to room --}}
                        <td class="py-3">
                            @if ($room->conversation)
                                <a
                                    href="{{ route('room.show', $room->conversation->id) }}"
                                    target="_blank"
                                    class="text-xs transition"
                                    style="color:var(--accent);"
                                    onmouseover="this.style.color='var(--accent-hover)'" onmouseout="this.style.color='var(--accent)'"
                                >Open ↗</a>
                            @else
                                <span class="text-xs" style="color:var(--border);">No room yet</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-sm" style="color:var(--text-muted);">No rooms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $this->rooms->links() }}</div>
</div>
