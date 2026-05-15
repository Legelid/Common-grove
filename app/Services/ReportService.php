<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Report;
use App\Models\Strike;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ReportService
{
    /**
     * Submit a report.
     * Rate-limited: 10 per hour per user.
     * Returns the created Report or null if duplicate/rate-limited.
     */
    public function submit(
        User   $reporter,
        User   $reported,
        Model  $reportable,
        string $reason,
        ?string $detail = null,
    ): ?Report {
        if ($reporter->id === $reported->id) {
            return null;
        }

        $key = 'report:' . $reporter->id;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return null;
        }

        $duplicate = Report::where('reporter_id', $reporter->id)
            ->where('reported_user_id', $reported->id)
            ->where('reportable_type', $reportable->getMorphClass())
            ->where('reportable_id', $reportable->getKey())
            ->exists();

        if ($duplicate) {
            return null;
        }

        RateLimiter::hit($key, 3600);

        return Report::create([
            'reporter_id'      => $reporter->id,
            'reported_user_id' => $reported->id,
            'reportable_type'  => $reportable->getMorphClass(),
            'reportable_id'    => $reportable->getKey(),
            'reason'           => $reason,
            'detail'           => $detail,
            'status'           => 'pending',
        ]);
    }

    /**
     * Dismiss a report as unfounded.
     * Increments dismiss_count on the reporter so serial reporters can be flagged.
     */
    public function dismiss(Report $report, User $reviewer): void
    {
        DB::transaction(function () use ($report, $reviewer): void {
            $report->update(['status' => 'dismissed', 'reviewed_by' => $reviewer->id]);
            User::where('id', $report->reporter_id)->increment('dismiss_count');
        });
    }

    /**
     * Action a report: issue a strike to the reported user.
     * Level 3 strike → immediately suspend the account.
     */
    public function action(Report $report, User $reviewer, string $strikeReason): ?Strike
    {
        return DB::transaction(function () use ($report, $reviewer, $strikeReason): ?Strike {
            if ($report->status !== 'pending') {
                return null;
            }

            $reported = User::findOrFail($report->reported_user_id);

            if ($reported->is($reviewer)) {
                return null;
            }

            $report->update(['status' => 'actioned', 'reviewed_by' => $reviewer->id]);
            $level    = min($reported->activeStrikeLevel() + 1, 3);

            $strike = Strike::create([
                'user_id'   => $reported->id,
                'level'     => $level,
                'reason'    => $strikeReason,
                'issued_by' => $reviewer->id,
                'expires_at' => $level < 3 ? now()->addDays(30) : null,
            ]);

            if ($level >= 3) {
                $reported->update(['suspended_at' => now()]);
            }

            return $strike;
        });
    }
}
