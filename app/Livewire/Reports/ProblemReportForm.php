<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\ProblemReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProblemReportForm extends Component
{
    use WithFileUploads;

    // ── Honeypot ──────────────────────────────────────────────────────────────
    public string $website = '';

    // ── Form fields ───────────────────────────────────────────────────────────
    public string $reportType   = '';
    public string $subject      = '';
    public string $description  = '';
    public string $pageUrl      = '';
    public $screenshot          = null;
    public string $relatedUser  = '';
    public string $relatedRoom  = '';
    public string $contactEmail = '';

    public bool    $submitted    = false;
    public ?string $submitError  = null;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->contactEmail = Auth::user()->email ?? '';
        }
    }

    public function submit(): void
    {
        $this->submitError = null;

        // Silent honeypot check — bots fill hidden fields
        if ($this->website !== '') {
            $this->submitted = true;
            return;
        }

        // Rate limiting: 5/hr for logged-in users, 2/hr by IP for guests
        if (Auth::check()) {
            $key   = 'problem-report.user.' . Auth::id();
            $limit = 5;
        } else {
            $key   = 'problem-report.ip.' . request()->ip();
            $limit = 2;
        }

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->submitError = 'You\'ve submitted several reports recently. Please wait a while before sending another.';
            return;
        }

        $this->validate([
            'reportType'   => ['required', 'string', 'in:bug,safety,user-report,account,feedback,other'],
            'subject'      => ['required', 'string', 'max:120'],
            'description'  => ['required', 'string', 'max:3000'],
            'pageUrl'      => ['nullable', 'url', 'max:500'],
            'screenshot'   => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'relatedUser'  => ['nullable', 'string', 'max:100'],
            'relatedRoom'  => ['nullable', 'string', 'max:200'],
            'contactEmail' => ['nullable', 'email', 'max:255'],
        ]);

        $screenshotPath = null;
        if ($this->screenshot) {
            $screenshotPath = $this->screenshot->store('problem_reports', 'local');
        }

        ProblemReport::create([
            'user_id'        => Auth::id(),
            'report_type'    => $this->reportType,
            'subject'        => $this->subject,
            'description'    => $this->description,
            'page_url'       => $this->pageUrl ?: null,
            'screenshot_path' => $screenshotPath,
            'related_user'   => $this->relatedUser ?: null,
            'related_room'   => $this->relatedRoom ?: null,
            'contact_email'  => $this->contactEmail ?: null,
            'status'         => 'open',
            'priority'       => $this->reportType === 'safety' ? 'high' : 'normal',
        ]);

        RateLimiter::hit($key, 3600);

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.reports.problem-report-form')
            ->layout('layouts.app', ['title' => 'Report a problem — CommonGround']);
    }
}
