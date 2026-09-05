<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\BetaInviteMail;
use App\Models\BetaInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBetaInviteEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly BetaInvite $invite) {}

    public function handle(): void
    {
        if ($this->invite->isClaimed()) {
            return;
        }

        Mail::to($this->invite->email)->send(new BetaInviteMail($this->invite));
    }
}
