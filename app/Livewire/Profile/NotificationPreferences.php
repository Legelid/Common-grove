<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public bool $friendRequest     = true;
    public bool $friendAccepted    = true;
    public bool $newMessage        = true;
    public bool $messageRequest    = true;
    public bool $hangoutFromFriend = true;
    public bool $weeklyMatch       = true;
    public bool $milestone         = true;

    public string $quietHoursStart = '';
    public string $quietHoursEnd   = '';

    public ?string $prefsMessage = null;

    public function mount(): void
    {
        $prefs = Auth::user()->notificationPreferences;

        if (! $prefs) {
            return;
        }

        $this->friendRequest     = $prefs->friend_request;
        $this->friendAccepted    = $prefs->friend_accepted;
        $this->newMessage        = $prefs->new_message;
        $this->messageRequest    = $prefs->message_request;
        $this->hangoutFromFriend = $prefs->hangout_from_friend;
        $this->weeklyMatch       = $prefs->weekly_match;
        $this->milestone         = $prefs->milestone;
        $this->quietHoursStart   = $prefs->quiet_hours_start ?? '';
        $this->quietHoursEnd     = $prefs->quiet_hours_end ?? '';
    }

    public function saveNotificationPreferences(): void
    {
        $this->validate([
            'quietHoursStart' => ['nullable', 'date_format:H:i'],
            'quietHoursEnd'   => ['nullable', 'date_format:H:i'],
        ]);

        $this->prefsMessage = null;

        NotificationPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'friend_request'     => $this->friendRequest,
                'friend_accepted'    => $this->friendAccepted,
                'new_message'        => $this->newMessage,
                'message_request'    => $this->messageRequest,
                'hangout_from_friend' => $this->hangoutFromFriend,
                'weekly_match'       => $this->weeklyMatch,
                'milestone'          => $this->milestone,
                'quiet_hours_start'  => $this->quietHoursStart ?: null,
                'quiet_hours_end'    => $this->quietHoursEnd ?: null,
            ]
        );

        $this->prefsMessage = 'Notification preferences saved.';
    }

    public function render(): View
    {
        return view('livewire.profile.notification-preferences');
    }
}
