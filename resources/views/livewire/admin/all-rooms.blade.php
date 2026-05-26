<div class="p-8">
    <h1 class="text-2xl font-bold mb-6" style="color:#E6EDF3;">All Rooms</h1>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search title, content, or gamertag…"
            class="rounded-xl px-4 py-2.5 text-sm focus:outline-none w-72"
            style="background:#1C2333;border:1px solid #30363D;color:#E6EDF3;"
            onfocus="this.style.boxShadow='0 0 0 2px #1D9E75'" onblur="this.style.boxShadow=''"
        >

        <div class="flex gap-1 rounded-xl p-1" style="background:#161B22;border:1px solid #30363D;">
            @foreach (['all' => 'All', 'active' => 'Active', 'expired' => 'Expired'] as $val => $label)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                    style="{{ $statusFilter === $val ? 'background:#21262D;color:#E6EDF3;' : 'color:#8B949E;' }}"
                >{{ $label }}</button>
            @endforeach
        </div>

        <div class="flex gap-1 rounded-xl p-1" style="background:#161B22;border:1px solid #30363D;">
            @foreach (['all' => 'All types', 'official' => 'CommonGrove', 'user' => 'User-created'] as $val => $label)
                <button
                    type="button"
                    wire:click="$set('typeFilter', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                    style="{{ $typeFilter === $val ? 'background:#21262D;color:#E6EDF3;' : 'color:#8B949E;' }}"
                >{{ $label }}</button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider border-b" style="color:#8B949E;border-color:#30363D;">
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
            <tbody class="divide-y" style="divide-color:#21262D;">
                @forelse ($this->rooms as $room)
                    @php
                        $isExpired = !$room->is_persistent && $room->expires_at && $room->expires_at->isPast();
                    @endphp
                    <tr wire:key="room-{{ $room->id }}" class="align-top" style="border-color:#21262D;">

                        {{-- Room title/content + type badges --}}
                        <td class="py-3 pr-4 max-w-xs">
                            <div class="flex flex-wrap gap-1 mb-1">
                                @if ($room->is_official)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgba(29,158,117,0.12);color:#1D9E75;">CommonGrove</span>
                                @endif
                                @if ($room->is_persistent)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgba(29,158,117,0.08);color:#1D9E75;">Always-open</span>
                                @else
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium" style="background:rgba(210,153,34,0.10);color:#D29922;">Temporary</span>
                                @endif
                            </div>
                            @if ($room->title)
                                <p class="font-medium text-sm leading-snug" style="color:#E6EDF3;">{{ $room->title }}</p>
                                <p class="text-xs leading-snug mt-0.5" style="color:#6B737C;">{{ Str::limit($room->content, 80) }}</p>
                            @else
                                <p class="text-sm leading-snug" style="color:#C9D1D9;">{{ Str::limit($room->content, 100) }}</p>
                            @endif
                        </td>

                        {{-- Creator --}}
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">
                            @if ($room->is_official)
                                <span style="color:#1D9E75;">CommonGrove</span>
                            @elseif ($room->user)
                                <a href="{{ route('admin.users') }}" class="transition" style="color:#8B949E;" onmouseover="this.style.color='#E6EDF3'" onmouseout="this.style.color='#8B949E'">
                                    {{ $room->user->gamertag }}
                                </a>
                            @else
                                <span style="color:#30363D;">—</span>
                            @endif
                        </td>

                        {{-- Tags --}}
                        <td class="py-3 pr-4">
                            <div class="flex flex-wrap gap-1 max-w-[12rem]">
                                @forelse ($room->tags->take(4) as $tag)
                                    <span class="px-1.5 py-0.5 text-xs rounded-full" style="background:#21262D;color:#8B949E;">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-xs" style="color:#30363D;">No tags</span>
                                @endforelse
                                @if ($room->tags->count() > 4)
                                    <span class="text-xs" style="color:#3d4451;">+{{ $room->tags->count() - 4 }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Participants --}}
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">
                            {{ $room->participant_count ?? 0 }}
                        </td>

                        {{-- Messages --}}
                        <td class="py-3 pr-4 text-xs" style="color:#8B949E;">
                            {{ $room->message_count ?? 0 }}
                        </td>

                        {{-- Created --}}
                        <td class="py-3 pr-4 text-xs whitespace-nowrap" style="color:#8B949E;">
                            {{ $room->created_at->format('Y-m-d') }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 pr-4">
                            @if (! $room->is_active)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(226,75,74,0.12);color:#E24B4A;">Inactive</span>
                            @elseif ($isExpired)
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(107,115,124,0.15);color:#6B737C;">Ended</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full" style="background:rgba(29,158,117,0.12);color:#1D9E75;">Active</span>
                            @endif
                        </td>

                        {{-- Link to room --}}
                        <td class="py-3">
                            @if ($room->conversation)
                                <a
                                    href="{{ route('room.show', $room->conversation->id) }}"
                                    target="_blank"
                                    class="text-xs transition"
                                    style="color:#1D9E75;"
                                    onmouseover="this.style.color='#22B88A'" onmouseout="this.style.color='#1D9E75'"
                                >Open ↗</a>
                            @else
                                <span class="text-xs" style="color:#30363D;">No room yet</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-sm" style="color:#8B949E;">No rooms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $this->rooms->links() }}</div>
</div>
