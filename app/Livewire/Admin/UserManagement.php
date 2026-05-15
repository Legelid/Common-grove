<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Strike;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';

    /** User whose strike history is expanded. */
    public ?string $viewingStrikesId = null;

    /** User currently receiving an inline action. */
    public ?string $actionUserId = null;

    /** Current inline action: warn | restrict | suspend | unsuspend */
    public ?string $pendingAction = null;

    public string $strikeReason = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::when($this->search !== '', function ($q): void {
            $q->where(function ($inner): void {
                $inner->where('gamertag', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(25);
    }

    public function toggleStrikes(string $userId): void
    {
        $this->viewingStrikesId = $this->viewingStrikesId === $userId ? null : $userId;
    }

    public function startAction(string $userId, string $action): void
    {
        $this->actionUserId   = $userId;
        $this->pendingAction  = $action;
        $this->strikeReason   = '';
    }

    public function cancelAction(): void
    {
        $this->actionUserId  = null;
        $this->pendingAction = null;
        $this->strikeReason  = '';
    }

    public function executeAction(): void
    {
        if ($this->actionUserId === null || $this->pendingAction === null) {
            return;
        }

        $this->validate(['strikeReason' => ['required_unless:pendingAction,unsuspend', 'string', 'max:500']]);

        $target  = User::findOrFail($this->actionUserId);
        $admin   = Auth::user();

        if ($target->is($admin)) {
            $this->cancelAction();
            return;
        }

        match ($this->pendingAction) {
            'warn' => Strike::create([
                'user_id'   => $target->id,
                'level'     => 1,
                'reason'    => $this->strikeReason,
                'issued_by' => $admin->id,
                'expires_at' => now()->addDays(30),
            ]),
            'restrict' => Strike::create([
                'user_id'   => $target->id,
                'level'     => 2,
                'reason'    => $this->strikeReason,
                'issued_by' => $admin->id,
                'expires_at' => now()->addDays(30),
            ]),
            'suspend' => (function () use ($target, $admin): void {
                Strike::create([
                    'user_id'   => $target->id,
                    'level'     => 3,
                    'reason'    => $this->strikeReason,
                    'issued_by' => $admin->id,
                    'expires_at' => null,
                ]);
                $target->update(['suspended_at' => now()]);
            })(),
            'unsuspend' => (function () use ($target): void {
                $target->update(['suspended_at' => null]);
                Strike::where('user_id', $target->id)
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now())
                    ->delete();
            })(),
            default => null,
        };

        $this->cancelAction();
        unset($this->users);
    }

    public function toggleAdmin(string $userId): void
    {
        $target = User::findOrFail($userId);

        // Prevent self-demotion
        if ($target->is(Auth::user())) {
            return;
        }

        $target->update(['is_admin' => ! $target->is_admin]);
        unset($this->users);
    }

    public function render(): View
    {
        return view('livewire.admin.user-management')
            ->layout('layouts.admin', ['title' => 'User Management']);
    }
}
