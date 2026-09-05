<?php

declare(strict_types=1);

namespace App\Livewire\Sidebar;

use App\Models\Conversation;
use App\Models\HangoutPost;
use App\Models\User;
use App\Services\BlockService;
use App\Services\FriendshipService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SidebarDiscovery extends Component
{
    public ?string $flash = null;

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    #[Computed]
    public function isEnabled(): bool
    {
        $user = Auth::user();

        return (bool) $user->show_connection_suggestions
            && ! (bool) $user->low_stimulation_mode;
    }

    /**
     * Up to 2 users with the most shared interest tags, excluding friends,
     * pending requests, and blocked users in either direction.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function suggestedPeople(): Collection
    {
        $user       = Auth::user();
        $userTagIds = $user->tags()->pluck('tag_id');

        if ($userTagIds->isEmpty()) {
            return collect();
        }

        $friendIds = DB::table('friendships')
            ->where(function ($q) use ($user): void {
                $q->where('requester_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->where('status', '!=', 'declined')
            ->get(['requester_id', 'recipient_id'])
            ->flatMap(fn ($f) => [$f->requester_id, $f->recipient_id])
            ->reject(fn ($id) => $id === $user->id)
            ->unique();

        $blockedIds = DB::table('blocks')
            ->where('blocker_id', $user->id)
            ->orWhere('blocked_id', $user->id)
            ->get(['blocker_id', 'blocked_id'])
            ->flatMap(fn ($b) => [$b->blocker_id, $b->blocked_id])
            ->reject(fn ($id) => $id === $user->id)
            ->unique();

        $excludeIds = $friendIds->merge($blockedIds)->push($user->id)->unique()->values();

        return User::whereNotIn('id', $excludeIds)
            ->withCount(['tags as shared_tag_count' => fn ($q) => $q->whereIn('user_tags.tag_id', $userTagIds)])
            ->having('shared_tag_count', '>', 0)
            ->orderByDesc('shared_tag_count')
            ->limit(2)
            ->get();
    }

    /**
     * Up to 2 active hangout posts matching the user's interests.
     *
     * @return Collection<int, HangoutPost>
     */
    #[Computed]
    public function suggestedRooms(): Collection
    {
        return HangoutPost::active()
            ->forUser(Auth::user())
            ->with(['user', 'tags'])
            ->latest()
            ->limit(2)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function joinRoom(string $postId): void
    {
        $post = HangoutPost::active()->find($postId);

        if ($post === null) {
            $this->flash = 'That hangout is no longer available.';
            unset($this->suggestedRooms);
            return;
        }

        if (app(BlockService::class)->isBlocked(Auth::user(), $post->user)) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['hangout_post_id' => $post->id],
            [
                'type'       => 'room',
                'name'       => Str::limit($post->content, 80),
                'created_by' => $post->user_id,
                'is_active'  => true,
            ]
        );

        if (! $conversation->participants()->where('user_id', Auth::id())->exists()) {
            $conversation->participants()->attach(Auth::id(), ['joined_at' => now()]);
            $post->increment('joined_count');
        }

        $this->redirect(route('room.show', $conversation->id), navigate: true);
    }

    public function sendRequest(string $userId): void
    {
        $target = User::findOrFail($userId);

        if (app(BlockService::class)->isBlocked(Auth::user(), $target)) {
            return;
        }

        try {
            app(FriendshipService::class)->sendRequest(Auth::user(), $target);
            $this->flash = 'Request sent to ' . $target->gamertag . '.';
        } catch (\InvalidArgumentException) {
            // Already friends, blocked, or duplicate — silently skip
        }

        unset($this->suggestedPeople);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.sidebar.sidebar-discovery');
    }
}
