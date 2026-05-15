<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsQueue extends Component
{
    use WithPagination;

    public string $tab = 'pending';

    /** ID of the report currently showing a confirmation prompt. */
    public ?string $confirmingId = null;

    /** Which action is being confirmed: dismiss | strike1 | strike2 | strike3 */
    public ?string $confirmingAction = null;

    /** The ID of a report whose context is expanded inline. */
    public ?string $viewingContextId = null;

    public function updatedTab(): void
    {
        $this->resetPage();
        $this->confirmingId     = null;
        $this->confirmingAction = null;
    }

    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        $query = Report::with(['reporter', 'reportedUser', 'reviewer', 'reportable'])
            ->orderBy('created_at', $this->tab === 'pending' ? 'asc' : 'desc');

        return match ($this->tab) {
            'pending'  => $query->where('status', 'pending')->paginate(20),
            'actioned' => $query->where('status', 'actioned')->paginate(20),
            'dismissed' => $query->where('status', 'dismissed')->paginate(20),
            default    => $query->whereIn('status', ['actioned', 'dismissed'])->paginate(20),
        };
    }

    /** @return array<string, int> */
    #[Computed]
    public function tabCounts(): array
    {
        return [
            'pending'   => Report::where('status', 'pending')->count(),
            'actioned'  => Report::where('status', 'actioned')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
        ];
    }

    public function confirmAction(string $reportId, string $action): void
    {
        $this->confirmingId     = $reportId;
        $this->confirmingAction = $action;
    }

    public function cancelConfirm(): void
    {
        $this->confirmingId     = null;
        $this->confirmingAction = null;
    }

    public function toggleContext(string $reportId): void
    {
        $this->viewingContextId = $this->viewingContextId === $reportId ? null : $reportId;
    }

    public function executeAction(): void
    {
        if ($this->confirmingId === null || $this->confirmingAction === null) {
            return;
        }

        $report  = Report::where('status', 'pending')->findOrFail($this->confirmingId);
        $service = app(ReportService::class);

        match ($this->confirmingAction) {
            'dismiss' => $service->dismiss($report, Auth::user()),
            'strike1' => $service->action($report, Auth::user(), 'Admin warning — Strike 1'),
            'strike2' => $service->action($report, Auth::user(), 'Admin restriction — Strike 2'),
            'strike3' => $service->action($report, Auth::user(), 'Admin suspension — Strike 3'),
            default   => null,
        };

        $this->confirmingId     = null;
        $this->confirmingAction = null;
        unset($this->reports, $this->tabCounts);
    }

    public function render(): View
    {
        return view('livewire.admin.reports-queue')
            ->layout('layouts.admin', ['title' => 'Reports Queue']);
    }
}
