<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExpireStatuses extends Command
{
    protected $signature = 'status:expire';

    protected $description = 'Clear status fields for users whose status has expired.';

    public function handle(): int
    {
        $count = User::whereNotNull('status_expires_at')
            ->where('status_expires_at', '<', now())
            ->update([
                'status_text'       => null,
                'status_mood'       => null,
                'status_expires_at' => null,
            ]);

        $this->info("Expired {$count} statuses.");

        return self::SUCCESS;
    }
}
