<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Mail\AnnouncementMail;
use App\Models\AnnouncementLog;
use App\Models\User;
use App\Services\BulkMailer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Sends a one-off announcement email to the entire (opted-in) user base.
 * Two-step by design: preview() just validates and flips into a review
 * state — confirmSend() is the only action that actually queues anything.
 */
class Announcements extends Component
{
    public string $subject = '';
    public string $body    = '';

    public bool $previewing = false;

    public ?string $feedback   = null;
    public bool    $feedbackError = false;

    #[Computed]
    public function recipientCount(): int
    {
        return $this->recipients()->count();
    }

    /**
     * Rendered exactly as a recipient would see it — including a real,
     * working unsubscribe link (signed for whoever is previewing it, i.e.
     * the admin themselves).
     */
    #[Computed]
    public function previewHtml(): ?string
    {
        if (! $this->previewing) {
            return null;
        }

        return (new AnnouncementMail($this->subject, $this->body, Auth::user()))->render();
    }

    public function preview(): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:191'],
            'body'    => ['required', 'string', 'max:10000'],
        ]);

        $this->previewing = true;
        $this->feedback   = null;
    }

    public function cancelPreview(): void
    {
        $this->previewing = false;
        unset($this->previewHtml);
    }

    public function confirmSend(BulkMailer $bulkMailer): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:191'],
            'body'    => ['required', 'string', 'max:10000'],
        ]);

        $recipients = $this->recipients()->get(['id', 'email']);
        $count      = $recipients->count();

        if ($count === 0) {
            $this->feedback      = 'No eligible recipients — nothing sent.';
            $this->feedbackError = true;

            return;
        }

        foreach ($recipients as $index => $user) {
            $bulkMailer->queue($user->email, new AnnouncementMail($this->subject, $this->body, $user), $index);
        }

        AnnouncementLog::create([
            'sent_by'         => Auth::id(),
            'subject'         => $this->subject,
            'body'            => $this->body,
            'recipient_count' => $count,
        ]);

        $this->feedback      = "Queued announcement to {$count} user(s).";
        $this->feedbackError = false;
        $this->previewing    = false;
        $this->subject       = '';
        $this->body          = '';

        unset($this->recipientCount, $this->previewHtml);
    }

    /**
     * @return Builder<User>
     */
    private function recipients(): Builder
    {
        return User::query()
            ->where('marketing_emails_opt_out', false)
            ->whereNull('suspended_at')
            ->whereNotNull('email_verified_at');
    }

    public function render(): View
    {
        return view('livewire.admin.announcements')
            ->layout('layouts.admin', ['title' => 'Announcements']);
    }
}
