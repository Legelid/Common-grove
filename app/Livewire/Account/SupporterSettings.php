<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Models\UserSubscription;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SupporterSettings extends Component
{
    public bool    $showSupporterIcon = true;
    public ?string $iconMessage       = null;
    public ?string $cancelMessage     = null;
    public bool    $confirmingCancel  = false;

    public function mount(): void
    {
        $this->showSupporterIcon = (bool) (Auth::user()->show_supporter_icon ?? true);
    }

    public function saveIconPref(): void
    {
        Auth::user()->update(['show_supporter_icon' => $this->showSupporterIcon]);
        $this->iconMessage = 'Preference saved.';
    }

    public function confirmCancel(): void
    {
        $this->confirmingCancel = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirmingCancel = false;
    }

    public function cancelSubscription(PayPalService $paypal): void
    {
        $user         = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $subscription) {
            $this->cancelMessage    = 'No active subscription found.';
            $this->confirmingCancel = false;
            return;
        }

        try {
            $paypal->cancelSubscription($subscription->provider_subscription_id);
        } catch (\Throwable $e) {
            Log::error('PayPal cancelSubscription failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            $this->cancelMessage    = 'Could not reach PayPal. Please try again or manage via PayPal directly.';
            $this->confirmingCancel = false;
            return;
        }

        // Optimistically mark cancelled; webhook will confirm.
        $subscription->update(['status' => 'cancelled', 'ends_at' => now()]);
        $user->update(['is_supporter' => false]);

        $this->confirmingCancel = false;
        $this->cancelMessage    = 'Subscription cancelled. Thank you for supporting CommonGrove.';
    }

    public function render(): \Illuminate\View\View
    {
        $user         = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'suspended', 'payment_failed'])
            ->latest()
            ->first();

        return view('livewire.account.supporter-settings', [
            'subscription' => $subscription,
        ])->layout('layouts.app', ['title' => 'Supporter | CommonGrove']);
    }
}
