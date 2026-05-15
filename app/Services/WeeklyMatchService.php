<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\WeeklyMatch;
use App\Notifications\WeeklyMatchSuggestion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyMatchService
{
    public function __construct(
        private readonly NotificationPreferenceService $prefs,
    ) {}

    /**
     * Generate a match for every active user who does not already have one this week.
     * Runs Monday at 09:00 via the scheduler.
     */
    public function generateMatches(): void
    {
        $weekOf = Carbon::now()->startOfWeek()->toDateString();

        // Users already matched this week
        $alreadyMatchedIds = WeeklyMatch::where('week_of', $weekOf)->pluck('user_id')->all();

        User::whereNull('suspended_at')
            ->whereNull('deleted_at')
            ->whereNotIn('id', $alreadyMatchedIds)
            ->chunk(50, function ($users) use ($weekOf): void {
                foreach ($users as $user) {
                    $this->matchUser($user, $weekOf);
                }
            });
    }

    private function matchUser(User $user, string $weekOf): void
    {
        $userTagIds = $user->tags()->pluck('tags.id');

        if ($userTagIds->isEmpty()) {
            return;
        }

        // IDs to exclude: self, already friends, blocked (either direction), matched in last 4 weeks
        $friendIds = $user->friends()->pluck('id');

        $blockedIds = DB::table('blocks')
            ->where('blocker_id', $user->id)->orWhere('blocked_id', $user->id)
            ->pluck(DB::raw('IF(blocker_id = \'' . $user->id . '\', blocked_id, blocker_id)'));

        $recentMatchIds = WeeklyMatch::where(function ($q) use ($user): void {
            $q->where('user_id', $user->id)->orWhere('matched_user_id', $user->id);
        })
            ->where('week_of', '>=', Carbon::now()->subWeeks(4)->startOfWeek()->toDateString())
            ->pluck(DB::raw('IF(user_id = \'' . $user->id . '\', matched_user_id, user_id)'));

        $excludeIds = collect([$user->id])
            ->merge($friendIds)
            ->merge($blockedIds)
            ->merge($recentMatchIds)
            ->unique()
            ->values();

        // Find the user with the highest tag overlap
        $best = User::whereNull('suspended_at')
            ->whereNull('deleted_at')
            ->whereNotIn('id', $excludeIds)
            ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $userTagIds))
            ->select('users.*')
            ->selectRaw(
                '(SELECT COUNT(*) FROM user_tags WHERE user_id = users.id AND tag_id IN (' . $userTagIds->map(fn ($id) => "'{$id}'")->implode(',') . ')) AS overlap_count'
            )
            ->orderByDesc('overlap_count')
            ->first();

        if (! $best) {
            return;
        }

        $sharedTagCount = (int) ($best->overlap_count ?? 0);

        // Create match records for both users
        WeeklyMatch::firstOrCreate(
            ['user_id' => $user->id, 'week_of' => $weekOf],
            ['matched_user_id' => $best->id]
        );

        WeeklyMatch::firstOrCreate(
            ['user_id' => $best->id, 'week_of' => $weekOf],
            ['matched_user_id' => $user->id]
        );

        // Notify both users
        if ($this->prefs->shouldNotify($user, 'weekly_match')) {
            $user->notify(new WeeklyMatchSuggestion($best, $sharedTagCount));
        }

        if ($this->prefs->shouldNotify($best, 'weekly_match')) {
            $best->notify(new WeeklyMatchSuggestion($user, $sharedTagCount));
        }
    }
}
