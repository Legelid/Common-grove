<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestSent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class FriendshipService
{
    /**
     * Send a friend request from $from to $to.
     * Throws if blocked or a friendship record already exists.
     */
    public function sendRequest(User $from, User $to): Friendship
    {
        if ($from->is($to)) {
            throw new \InvalidArgumentException('Cannot send a friend request to yourself.');
        }

        if (app(BlockService::class)->isBlocked($from, $to)) {
            throw new \InvalidArgumentException('Cannot send a friend request to a blocked user.');
        }

        $existing = Friendship::where(function ($q) use ($from, $to): void {
            $q->where('requester_id', $from->id)->where('recipient_id', $to->id);
        })->orWhere(function ($q) use ($from, $to): void {
            $q->where('requester_id', $to->id)->where('recipient_id', $from->id);
        })->first();

        if ($existing) {
            throw new \InvalidArgumentException('A friendship or pending request already exists.');
        }

        $friendship = Friendship::create([
            'requester_id' => $from->id,
            'recipient_id' => $to->id,
            'status'       => 'pending',
        ]);

        if (App::make(NotificationPreferenceService::class)->shouldNotify($to, 'friend_request')) {
            $to->notify(new FriendRequestSent($from));
        }

        return $friendship;
    }

    /**
     * Accept a pending friend request. Only the recipient may accept.
     */
    public function accept(Friendship $friendship, User $acceptor): void
    {
        if ($friendship->recipient_id !== $acceptor->id) {
            throw new \InvalidArgumentException('Only the recipient can accept this request.');
        }

        $friendship->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        if (App::make(NotificationPreferenceService::class)->shouldNotify($friendship->requester, 'friend_accepted')) {
            $friendship->requester->notify(new FriendRequestAccepted($acceptor));
        }
    }

    /**
     * Decline a pending friend request. Only the recipient may decline.
     */
    public function decline(Friendship $friendship, User $decliner): void
    {
        if ($friendship->recipient_id !== $decliner->id) {
            throw new \InvalidArgumentException('Only the recipient can decline this request.');
        }

        $friendship->update(['status' => 'declined']);
    }

    /**
     * Remove an accepted friendship in either direction.
     */
    public function unfriend(User $userA, User $userB): void
    {
        Friendship::where(function ($q) use ($userA, $userB): void {
            $q->where('requester_id', $userA->id)->where('recipient_id', $userB->id);
        })->orWhere(function ($q) use ($userA, $userB): void {
            $q->where('requester_id', $userB->id)->where('recipient_id', $userA->id);
        })->delete();
    }

    /**
     * Returns all accepted friends of $user with their online status loaded.
     *
     * @return Collection<int, User>
     */
    public function getFriends(User $user): Collection
    {
        $sentFriendIds     = Friendship::where('requester_id', $user->id)->where('status', 'accepted')->pluck('recipient_id');
        $receivedFriendIds = Friendship::where('recipient_id', $user->id)->where('status', 'accepted')->pluck('requester_id');

        $allFriendIds = $sentFriendIds->merge($receivedFriendIds)->unique();

        return User::whereIn('id', $allFriendIds)->get();
    }
}
