<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipMilestone;
use App\Services\NotificationPreferenceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFriendshipMilestones extends Command
{
    protected $signature = 'friendships:milestones';

    protected $description = 'Notify users of friendship milestones (7, 30, 90 days).';

    public function __construct(
        private readonly NotificationPreferenceService $prefs,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $milestones = [7, 30, 90];
        $notified   = 0;

        Friendship::where('status', 'accepted')
            ->whereNotNull('accepted_at')
            ->with(['requester', 'recipient'])
            ->chunk(100, function ($friendships) use ($milestones, &$notified): void {
                foreach ($friendships as $friendship) {
                    $days = (int) $friendship->accepted_at->diffInDays(now());

                    foreach ($milestones as $milestone) {
                        if ($days < $milestone) {
                            continue;
                        }

                        // Check if already notified for this milestone
                        $alreadySent = DB::table('friendship_milestones')
                            ->where('friendship_id', $friendship->id)
                            ->where('milestone_days', $milestone)
                            ->exists();

                        if ($alreadySent) {
                            continue;
                        }

                        // Record the milestone
                        DB::table('friendship_milestones')->insert([
                            'friendship_id' => $friendship->id,
                            'milestone_days' => $milestone,
                            'notified_at'    => now(),
                        ]);

                        // Notify both users
                        if ($this->prefs->shouldNotify($friendship->requester, 'milestone')) {
                            $friendship->requester->notify(new FriendshipMilestone($friendship->recipient, $milestone));
                        }

                        if ($this->prefs->shouldNotify($friendship->recipient, 'milestone')) {
                            $friendship->recipient->notify(new FriendshipMilestone($friendship->requester, $milestone));
                        }

                        $notified++;
                    }
                }
            });

        $this->info("Processed {$notified} friendship milestones.");

        return self::SUCCESS;
    }
}
