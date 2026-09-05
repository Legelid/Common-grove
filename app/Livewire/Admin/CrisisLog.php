<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\CrisisDetection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CrisisLog extends Component
{
    use WithPagination;

    #[Computed]
    public function detections(): LengthAwarePaginator
    {
        return CrisisDetection::with('user')
            ->orderByDesc('detected_at')
            ->paginate(30);
    }

    public function render(): View
    {
        return view('livewire.admin.crisis-log')
            ->layout('layouts.admin', ['title' => 'Crisis Log']);
    }
}
