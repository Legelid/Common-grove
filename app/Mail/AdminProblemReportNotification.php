<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ProblemReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminProblemReportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ProblemReport $problemReport;

    public function __construct(ProblemReport $problemReport)
    {
        $this->problemReport = $problemReport;
    }

    public function envelope(): Envelope
    {
        $typeLabel = ProblemReport::TYPES[$this->problemReport->report_type] ?? $this->problemReport->report_type;
        $subject   = mb_strimwidth($this->problemReport->subject, 0, 60, '…');

        return new Envelope(
            subject: 'New CommonGrove ' . strtolower($typeLabel) . ' — ' . $subject,
        );
    }

    public function content(): Content
    {
        $this->problemReport->loadMissing('user');

        return new Content(view: 'emails.admin-problem-report');
    }
}
