<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RequirePasswordReset extends Command
{
    protected $signature = 'users:require-password-reset';

    protected $description = 'Flag users whose passwords were not hashed with Argon2id';

    public function handle(): int
    {
        $count = User::whereNotNull('password')
            ->where('password', 'not like', '$argon2id%')
            ->where('password_reset_required', false)
            ->update(['password_reset_required' => true]);

        $this->info("Flagged {$count} user(s) as requiring a password reset.");

        return self::SUCCESS;
    }
}
