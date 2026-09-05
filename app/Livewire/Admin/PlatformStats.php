<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PlatformStats extends Component
{
    /**
     * New user registrations per day for the last 30 days.
     *
     * @return array<int, array{date: string, count: int}>
     */
    #[Computed]
    public function newUsersPerDay(): array
    {
        return DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNull('deleted_at')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($r) => ['date' => $r->date, 'count' => (int) $r->count])
            ->all();
    }

    /**
     * Top 20 tags by usage_count.
     *
     * @return array<int, array{name: string, usage_count: int}>
     */
    #[Computed]
    public function popularTags(): array
    {
        return DB::table('tags')
            ->select('name', 'usage_count')
            ->where('is_approved', true)
            ->orderByDesc('usage_count')
            ->limit(20)
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'usage_count' => (int) $r->usage_count])
            ->all();
    }

    /**
     * Top 10 users by messages sent (gamertag only — never email).
     *
     * @return array<int, array{gamertag: string, message_count: int}>
     */
    #[Computed]
    public function mostActiveUsers(): array
    {
        return DB::table('messages')
            ->join('users', 'messages.user_id', '=', 'users.id')
            ->selectRaw('users.gamertag, COUNT(messages.id) as message_count')
            ->whereNull('messages.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('users.gamertag')
            ->orderByDesc('message_count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['gamertag' => $r->gamertag, 'message_count' => (int) $r->message_count])
            ->all();
    }

    /**
     * Hangout posts created per day for the last 7 days.
     *
     * @return array<int, array{date: string, count: int}>
     */
    #[Computed]
    public function postsPerDay(): array
    {
        return DB::table('hangout_posts')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNull('deleted_at')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($r) => ['date' => $r->date, 'count' => (int) $r->count])
            ->all();
    }

    /**
     * Average number of tags selected per user.
     */
    #[Computed]
    public function avgTagsPerUser(): float
    {
        $result = DB::table('user_tags')
            ->selectRaw('COUNT(*) as total, COUNT(DISTINCT user_id) as users')
            ->first();

        if ($result === null || (int) $result->users === 0) {
            return 0.0;
        }

        return round((int) $result->total / (int) $result->users, 1);
    }

    /**
     * All-time report counts by status.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function reportCounts(): array
    {
        $rows = DB::table('reports')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total'     => (int) DB::table('reports')->count(),
            'pending'   => (int) ($rows->get('pending')?->count ?? 0),
            'actioned'  => (int) ($rows->get('actioned')?->count ?? 0),
            'dismissed' => (int) ($rows->get('dismissed')?->count ?? 0),
            'reviewed'  => (int) ($rows->get('reviewed')?->count ?? 0),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.platform-stats')
            ->layout('layouts.admin', ['title' => 'Platform Stats']);
    }
}
