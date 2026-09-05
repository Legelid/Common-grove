<?php

declare(strict_types=1);

namespace App\Livewire\Feed;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\RecentRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll(60000)]
class HangoutFeed extends Component
{
    /**
     * Resolved once in mount() and never recomputed — this component polls
     * every 60s, and a #[Computed] greeting would silently change mid-session
     * (e.g. "morning" flipping to "afternoon" while the tab sits open).
     *
     * @var array{heading: string, subtext: string}
     */
    public array $greeting = [];

    /** Set when a query in this component fails, so the view can show a calm error state instead of crashing. */
    public bool $hasError = false;

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->greeting = $this->resolveGreeting();
    }

    /** @return array{heading: string, subtext: string} */
    protected function resolveGreeting(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [
                'heading' => 'Find your people.',
                'subtext' => 'CommonGrove is a calm place to make real friends. Take your time looking around.',
            ];
        }

        $isNewUser = ! $user->onboarding_completed
            || ! RecentRoom::where('user_id', $user->id)->exists();

        if ($isNewUser) {
            return [
                'heading' => 'Welcome to CommonGrove.',
                'subtext' => "You don't have to know what to say yet. Have a look around — there's no pressure to jump in.",
            ];
        }

        $hasJoinedRooms = DB::table('conversation_participants')->where('user_id', $user->id)->exists();

        $subtext = $hasJoinedRooms
            ? 'A few conversations continued while you were away.'
            : 'Where would you like to spend some time today?';

        $name   = $this->resolveGreetingName($user);
        $suffix = $name ? ", {$name}" : '';
        $hour   = now()->hour;

        $heading = match (true) {
            $hour >= 5 && $hour < 11  => "Good morning{$suffix}.",
            $hour >= 11 && $hour < 17 => "Good afternoon{$suffix}.",
            $hour >= 17 && $hour < 22 => "Good evening{$suffix}.",
            default                   => "It's late{$suffix}. Glad you're here.",
        };

        return ['heading' => $heading, 'subtext' => $subtext];
    }

    /**
     * Self-facing name for the greeting only. Deliberately NOT
     * $user->display_name (getDisplayNameAttribute()) — that accessor
     * intentionally returns gamertag-only for identity_mode 2, because mode 2
     * hides the display name from OTHER users. The greeting is shown only to
     * this user, so honoring their chosen name here is correct, not a bug.
     */
    protected function resolveGreetingName(User $user): ?string
    {
        $name = match ($user->identity_mode) {
            2       => $user->preferred_name,
            3       => $user->getAttribute('display_name') ?: null,
            default => null,
        };

        return $name ?: ($user->gamertag ?: null);
    }

    /**
     * "Continue where you left off" — rooms the user currently has an
     * active seat in (conversation_participants, left_at IS NULL), most
     * recently active first. Real joined-room data only, no invented
     * suggestions and no pinned/DM mix — just what you're actually in.
     *
     * @return Collection<int, array{
     *     id: string, name: string, stateLabel: string,
     * }>
     */
    #[Computed]
    public function continuePaths(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        try {
            $userId = Auth::id();

            $conversations = Conversation::where('type', 'room')
                ->where('is_active', true)
                ->whereHas('participants', function (Builder $q) use ($userId): void {
                    $q->where('conversation_participants.user_id', $userId)
                        ->whereNull('conversation_participants.left_at');
                })
                ->with('hangoutPost')
                ->orderByDesc('updated_at')
                ->limit(4)
                ->get();

            return $conversations->map(function (Conversation $conversation) use ($userId) {
                $pivot = $conversation->participants()
                    ->where('conversation_participants.user_id', $userId)
                    ->first()
                    ?->pivot;

                $lastReadAt = $pivot?->last_read_at;

                $unreadCount = Message::where('conversation_id', $conversation->id)
                    ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
                    ->count();

                $participantCount = DB::table('conversation_participants')
                    ->where('conversation_id', $conversation->id)
                    ->whereNull('left_at')
                    ->count();
                $otherCount = max(0, $participantCount - 1);

                $hangoutPost = $conversation->hangoutPost;
                $expiresSoon = $hangoutPost
                    && ! $hangoutPost->is_persistent
                    && $hangoutPost->expires_at
                    && $hangoutPost->expires_at->isFuture()
                    && $hangoutPost->expires_at->diffInHours(now()) <= 2;

                $stateLabel = match (true) {
                    $unreadCount > 0 => $unreadCount . ' ' . Str::plural('message', $unreadCount) . ' waiting',
                    $expiresSoon     => 'Expiring soon',
                    $otherCount === 0 => 'Quiet now',
                    $otherCount === 1 => '1 person here',
                    default          => $otherCount . ' people here',
                };

                return [
                    'id'         => $conversation->id,
                    'name'       => $conversation->name ?? 'Hangout Room',
                    'stateLabel' => $stateLabel,
                ];
            })->values();
        } catch (\Throwable $e) {
            Log::error('HangoutFeed::continuePaths failed', ['exception' => $e]);
            $this->hasError = true;
            return collect();
        }
    }

    public function render(): View
    {
        return view('livewire.feed.hangout-feed')
            ->layout('layouts.app', ['title' => 'Home | CommonGrove']);
    }
}
