<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\WinbackMail;
use App\Models\User;
use App\Services\BulkMailer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Manual only — never scheduled. Run with --dry-run first to see who would
 * receive it before actually sending anything.
 */
class SendWinbackEmails extends Command
{
    protected $signature = 'users:send-winback
        {--dry-run : List matching users without sending or updating anything}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Email users who have gone quiet (30+ days inactive, never sent before) a win-back message.';

    private const INACTIVE_DAYS = 30;

    public function __construct(private readonly BulkMailer $bulkMailer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            return $this->runDryRun();
        }

        $users = $this->eligibleUsers()->get();
        $count = $users->count();

        if ($count === 0) {
            $this->info('No users match the win-back criteria. Nothing to send.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Send win-back email to {$count} user(s)?")) {
            $this->info('Cancelled — nothing sent.');

            return self::SUCCESS;
        }

        foreach ($users as $index => $user) {
            $this->bulkMailer->queue($user->email, new WinbackMail($user), $index);

            // Set immediately, not on delivery — re-running the command later
            // must never double-queue someone whose email just hasn't gone
            // out yet.
            $user->update(['winback_emailed_at' => now()]);
        }

        $this->info("Queued win-back emails for {$count} user(s), staggered ".BulkMailer::STAGGER_SECONDS." seconds apart.");

        return self::SUCCESS;
    }

    private function runDryRun(): int
    {
        $users = $this->eligibleUsers()->get(['email']);
        $count = $users->count();

        $this->info("{$count} user(s) would receive the win-back email:");

        foreach ($users as $user) {
            $this->line("  - {$user->email}");
        }

        $this->comment('Dry run — nothing was sent, nothing was updated.');

        return self::SUCCESS;
    }

    /**
     * @return Builder<User>
     */
    private function eligibleUsers(): Builder
    {
        return User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '<', now()->subDays(self::INACTIVE_DAYS))
            ->whereNull('winback_emailed_at')
            ->where('marketing_emails_opt_out', false)
            // A quiet ex-user is a win-back candidate; a suspended one is not.
            ->whereNull('suspended_at')
            ->whereNotNull('email_verified_at');
    }
}
