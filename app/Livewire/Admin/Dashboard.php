<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\HangoutPost;
use App\Models\Message;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll(60000)]
class Dashboard extends Component
{
    /** @return array<string, int> */
    #[Computed]
    public function stats(): array
    {
        return [
            'total_users'          => User::count(),
            'online_now'           => User::where('last_seen_at', '>=', now()->subMinutes(15))->count(),
            'active_posts'         => HangoutPost::active()->count(),
            'total_messages'       => Message::count(),
            'pending_reports'      => Report::where('status', 'pending')->count(),
            'pending_tags'         => Tag::where('is_curated', false)->where('is_approved', false)->count(),
            'suspended_users'      => User::whereNotNull('suspended_at')->count(),
            'serial_reporters'     => User::serialReporters()->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard Overview']);
    }
}
