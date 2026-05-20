<?php

declare(strict_types=1);

namespace App\Http\Controllers\PayPal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PayPalService $paypal): Response
    {
        // Verify the webhook genuinely came from PayPal before trusting anything.
        if (! $paypal->verifyWebhookSignature($request)) {
            Log::warning('PayPal webhook signature verification failed', [
                'ip' => $request->ip(),
            ]);

            return response('Signature verification failed.', 400);
        }

        $eventType = $request->json('event_type');
        $resource  = $request->json('resource') ?? [];

        Log::info('PayPal webhook received', ['event_type' => $eventType]);

        // PAYMENT.SALE.* events use billing_agreement_id (not resource.id) to reference
        // the subscription — handle them before the generic subscription ID extraction.
        if (in_array($eventType, ['PAYMENT.SALE.COMPLETED', 'PAYMENT.SALE.DENIED', 'PAYMENT.SALE.REFUNDED'], true)) {
            match ($eventType) {
                'PAYMENT.SALE.COMPLETED' => $this->handlePaymentCompleted($resource),
                'PAYMENT.SALE.DENIED'    => $this->handlePaymentDenied($resource),
                'PAYMENT.SALE.REFUNDED'  => $this->handlePaymentRefunded($resource),
            };
            return response('OK', 200);
        }

        $subscriptionId = $resource['id'] ?? null;

        if (! $subscriptionId) {
            return response('Missing resource ID.', 400);
        }

        match ($eventType) {
            'BILLING.SUBSCRIPTION.ACTIVATED'      => $this->handleActivated($subscriptionId, $resource),
            'BILLING.SUBSCRIPTION.CANCELLED'      => $this->handleCancelled($subscriptionId),
            'BILLING.SUBSCRIPTION.EXPIRED'        => $this->handleExpired($subscriptionId),
            'BILLING.SUBSCRIPTION.SUSPENDED'      => $this->handleSuspended($subscriptionId),
            'BILLING.SUBSCRIPTION.PAYMENT.FAILED' => $this->handlePaymentFailed($subscriptionId),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleActivated(string $subscriptionId, array $resource): void
    {
        $subscription = UserSubscription::where('provider_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            Log::warning('PayPal ACTIVATED for unknown subscription', ['id' => $subscriptionId]);
            return;
        }

        $subscription->update([
            'status'          => 'active',
            'started_at'      => now(),
            'last_payment_at' => now(),
        ]);

        $subscription->user->update(['is_supporter' => true]);
    }

    private function handleCancelled(string $subscriptionId): void
    {
        $this->revokeSupporter($subscriptionId, 'cancelled');
    }

    private function handleExpired(string $subscriptionId): void
    {
        $this->revokeSupporter($subscriptionId, 'expired');
    }

    private function handleSuspended(string $subscriptionId): void
    {
        $this->revokeSupporter($subscriptionId, 'suspended');
    }

    private function handlePaymentFailed(string $subscriptionId): void
    {
        $this->revokeSupporter($subscriptionId, 'payment_failed');
    }

    private function handlePaymentCompleted(array $resource): void
    {
        // Subscription payments carry the subscription ID in billing_agreement_id.
        $subscriptionId = $resource['billing_agreement_id'] ?? null;

        if (! $subscriptionId) {
            return; // One-time payment unrelated to a subscription — nothing to do.
        }

        $subscription = UserSubscription::where('provider_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            Log::warning('PayPal PAYMENT.SALE.COMPLETED for unknown subscription', [
                'billing_agreement_id' => $subscriptionId,
            ]);
            return;
        }

        $subscription->update(['last_payment_at' => now()]);
    }

    private function handlePaymentDenied(array $resource): void
    {
        $subscriptionId = $resource['billing_agreement_id'] ?? null;
        $saleId         = $resource['id'] ?? 'unknown';

        Log::warning('PayPal PAYMENT.SALE.DENIED', [
            'sale_id'              => $saleId,
            'billing_agreement_id' => $subscriptionId,
            'amount'               => $resource['amount'] ?? null,
        ]);

        if (! $subscriptionId) {
            return;
        }

        // A denied payment means PayPal couldn't collect — treat like payment failed.
        $this->revokeSupporter($subscriptionId, 'payment_failed');
    }

    private function handlePaymentRefunded(array $resource): void
    {
        $subscriptionId = $resource['billing_agreement_id'] ?? null;
        $saleId         = $resource['id'] ?? 'unknown';

        // Log only — a refund doesn't necessarily mean the subscription is gone.
        Log::info('PayPal PAYMENT.SALE.REFUNDED', [
            'sale_id'              => $saleId,
            'billing_agreement_id' => $subscriptionId,
            'amount'               => $resource['amount'] ?? null,
        ]);
    }

    private function revokeSupporter(string $subscriptionId, string $newStatus): void
    {
        $subscription = UserSubscription::where('provider_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            return;
        }

        $subscription->update([
            'status'  => $newStatus,
            'ends_at' => now(),
        ]);

        // Only revoke supporter flag if there are no other active subscriptions.
        $hasOtherActive = UserSubscription::where('user_id', $subscription->user_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->exists();

        if (! $hasOtherActive) {
            $subscription->user->update(['is_supporter' => false]);
        }
    }
}
