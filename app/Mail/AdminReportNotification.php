<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminReportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function envelope(): Envelope
    {
        $reason = str_replace('_', ' ', $this->report->reason ?? 'unknown');

        return new Envelope(
            subject: 'New CommonGrove report — ' . $reason,
        );
    }

    public function content(): Content
    {
        $this->report->loadMissing(['reporter', 'reportedUser']);

        return new Content(view: 'emails.admin-report');
    }
}
