<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class NotificationPreferenceService
{
    /**
     * Returns true if the user should receive a notification of the given type right now.
     * Checks both the per-type toggle and quiet hours.
     */
    public function shouldNotify(User $user, string $type): bool
    {
        $prefs = $user->notificationPreferences;

        if (! $prefs) {
            return true;
        }

        if (isset($prefs->$type) && ! $prefs->$type) {
            return false;
        }

        if ($prefs->quiet_hours_start && $prefs->quiet_hours_end) {
            $now   = now()->format('H:i:s');
            $start = $prefs->quiet_hours_start;
            $end   = $prefs->quiet_hours_end;

            // Handle overnight quiet hours (e.g. 22:00 → 07:00)
            if ($start <= $end) {
                if ($now >= $start && $now <= $end) {
                    return false;
                }
            } else {
                if ($now >= $start || $now <= $end) {
                    return false;
                }
            }
        }

        return true;
    }
}
