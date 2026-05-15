<?php

declare(strict_types=1);

namespace App\Livewire\Messaging;

use App\Models\Conversation;
use App\Models\MessageRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ConversationList extends Component
{
    public string $activeTab = 'messages';

    // -------------------------------------------------------------------------
    // Computed
    // -------------------------------------------------------------------------

    /**
     * Accepted conversations — not a pending request targeting this user.
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function conversations(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->with([
                'latestMessage.user',
                'participants',
            ])
            ->where(function ($q): void {
                // Rooms are always shown in Messages tab
                $q->where('type', 'room')
                    ->orWhere(function ($q): void {
                        $q->where('type', 'direct')
                            ->whereDoesntHave('messageRequests', function ($q): void {
                                $q->where('to_user_id', Auth::id())
                                    ->where('status', 'pending');
                            });
                    });
            })
            ->latest('updated_at')
            ->get();
    }

    /**
     * Pending direct message requests sent to this user.
     *
     * @return Collection<int, Conversation>
     */
    #[Computed]
    public function requests(): Collection
    {
        return Conversation::forUser(Auth::user())
            ->where('type', 'direct')
            ->whereHas('messageRequests', function ($q): void {
                $q->where('to_user_id', Auth::id())
                    ->where('status', 'pending');
            })
            ->with([
                'latestMessage.user',
                'participants',
                'messageRequests' => fn ($q) => $q->where('to_user_id', Auth::id()),
            ])
            ->latest('updated_at')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.messaging.conversation-list')
            ->layout('layouts.app', ['title' => 'Messages — CommonGround']);
    }
}
