<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\ProblemReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProblemReports extends Component
{
    use WithPagination;

    public string $filterType     = '';
    public string $filterStatus   = '';
    public string $filterPriority = '';

    public ?string $viewingId = null;

    public function updatedFilterType(): void     { $this->resetPage(); }
    public function updatedFilterStatus(): void   { $this->resetPage(); }
    public function updatedFilterPriority(): void { $this->resetPage(); }

    /**
     * @return LengthAwarePaginator<ProblemReport>
     */
    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        $q = ProblemReport::with('user')->orderByDesc('created_at');

        if ($this->filterType !== '') {
            $q->where('report_type', $this->filterType);
        }
        if ($this->filterStatus !== '') {
            $q->where('status', $this->filterStatus);
        }
        if ($this->filterPriority !== '') {
            $q->where('priority', $this->filterPriority);
        }

        return $q->paginate(25);
    }

    /** @return array<string, int> */
    #[Computed]
    public function statusCounts(): array
    {
        return ProblemReport::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function updateStatus(string $reportId, string $status): void
    {
        if (! in_array($status, ProblemReport::STATUSES, true)) {
            return;
        }

        ProblemReport::findOrFail($reportId)->update(['status' => $status]);
        unset($this->reports, $this->statusCounts);
    }

    public function updatePriority(string $reportId, string $priority): void
    {
        if (! in_array($priority, ProblemReport::PRIORITIES, true)) {
            return;
        }

        ProblemReport::findOrFail($reportId)->update(['priority' => $priority]);
        unset($this->reports);
    }

    public function saveNote(string $reportId, string $note): void
    {
        ProblemReport::findOrFail($reportId)->update(['admin_note' => $note ?: null]);
        unset($this->reports);
    }

    public function toggleView(string $reportId): void
    {
        $this->viewingId = $this->viewingId === $reportId ? null : $reportId;
    }

    public function render(): View
    {
        return view('livewire.admin.problem-reports')
            ->layout('layouts.admin', ['title' => 'Problem Reports']);
    }
}
