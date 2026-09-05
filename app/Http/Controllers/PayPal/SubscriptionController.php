<?php

declare(strict_types=1);

namespace App\Http\Controllers\PayPal;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Services\PayPalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Redirect the authenticated user to PayPal to approve a new subscription.
     */
    public function redirect(PayPalService $paypal): RedirectResponse
    {
        try {
            $data = $paypal->createSubscription(
                route('support.subscribe.return'),
                route('support.subscribe.cancel'),
            );
        } catch (\Throwable $e) {
            Log::error('PayPal createSubscription failed', ['error' => $e->getMessage()]);

            return redirect()->route('settings.supporter')
                ->with('paypal_error', 'Could not reach PayPal. Please try again shortly.');
        }

        // Persist the subscription record immediately so the webhook can find it.
        $subscriptionId = $data['id'] ?? null;

        if ($subscriptionId) {
            UserSubscription::create([
                'user_id'                  => Auth::id(),
                'provider'                 => 'paypal',
                'provider_subscription_id' => $subscriptionId,
                'status'                   => 'pending',
                'plan_name'                => 'CommonGrove Supporter',
            ]);
        }

        // Find the `approve` link PayPal returns in the links array.
        $approvalUrl = collect($data['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approvalUrl) {
            return redirect()->route('settings.supporter')
                ->with('paypal_error', 'PayPal did not return an approval URL. Please try again.');
        }

        return redirect()->away($approvalUrl);
    }

    /**
     * PayPal redirects here after the user approves the subscription.
     * The real activation comes via webhook — we just acknowledge and wait.
     */
    public function return(Request $request): RedirectResponse
    {
        return redirect()->route('settings.supporter')
            ->with('subscribed', true);
    }

    /**
     * PayPal redirects here if the user cancels on the PayPal side before approving.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('settings.supporter')
            ->with('subscribe_cancelled', true);
    }
}
