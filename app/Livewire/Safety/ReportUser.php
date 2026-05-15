<?php

declare(strict_types=1);

namespace App\Livewire\Safety;

use App\Models\User;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportUser extends Component
{
    public bool $open = false;

    #[Locked]
    public ?string $reportedUserId = null;

    #[Locked]
    public ?string $reportableType = null;

    #[Locked]
    public ?string $reportableId = null;

    public string $reason = '';

    public string $detail = '';

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /** @var list<string> */
    public array $reasons = [
        'harassment',
        'hate_speech',
        'spam',
        'impersonation',
        'explicit_content',
        'threats',
        'other',
    ];

    /** @var array<string, string> */
    public array $reasonLabels = [
        'harassment'       => 'Harassment or bullying',
        'hate_speech'      => 'Hate speech or discrimination',
        'spam'             => 'Spam or bot behaviour',
        'impersonation'    => 'Impersonation',
        'explicit_content' => 'Explicit or adult content',
        'threats'          => 'Threats or violence',
        'other'            => 'Other',
    ];

    /**
     * Opens the modal for a given user and optional reportable context.
     */
    #[On('open-report-modal')]
    public function openModal(
        string  $reportedUserId,
        ?string $reportableType = null,
        ?string $reportableId = null,
    ): void {
        $this->resetForm();
        $this->reportedUserId  = $reportedUserId;
        $this->reportableType  = $reportableType;
        $this->reportableId    = $reportableId;
        $this->open            = true;
    }

    public function submit(): void
    {
        $this->validate([
            'reason' => ['required', 'in:' . implode(',', $this->reasons)],
            'detail' => ['required', 'string', 'min:20', 'max:500'],
        ]);

        /** @var User $reporter */
        $reporter = Auth::user();
        $reported = User::findOrFail($this->reportedUserId);

        // Resolve reportable model or fall back to the reported user
        $reportable = $this->resolveReportable() ?? $reported;

        /** @var ReportService $service */
        $service = app(ReportService::class);
        $report  = $service->submit($reporter, $reported, $reportable, $this->reason, $this->detail ?: null);

        if ($report === null) {
            $this->errorMessage = 'You have already reported this content, or you have reached the reporting limit for today.';
            return;
        }

        $this->successMessage = 'Your report has been received. Thank you for helping keep CommonGround safe.';
        $this->reason  = '';
        $this->detail  = '';
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reason         = '';
        $this->detail         = '';
        $this->successMessage = null;
        $this->errorMessage   = null;
        $this->reportedUserId = null;
        $this->reportableType = null;
        $this->reportableId   = null;
    }

    private function resolveReportable(): ?Model
    {
        if ($this->reportableType === null || $this->reportableId === null) {
            return null;
        }

        $allowed = [
            'App\\Models\\Message',
            'App\\Models\\HangoutPost',
            'App\\Models\\User',
        ];

        if (! in_array($this->reportableType, $allowed, true)) {
            return null;
        }

        return ($this->reportableType)::find($this->reportableId);
    }

    public function render(): View
    {
        return view('livewire.safety.report-user');
    }
}
