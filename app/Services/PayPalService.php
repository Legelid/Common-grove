<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;
    private string $planId;

    public function __construct()
    {
        $mode               = config('services.paypal.mode', 'sandbox');
        $this->clientId     = (string) config('services.paypal.client_id');
        $this->clientSecret = (string) config('services.paypal.client_secret');
        $this->planId       = (string) config('services.paypal.supporter_plan_id');
        $this->baseUrl      = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Fetch a short-lived OAuth2 access token.
     */
    private function getAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        $response->throw();

        return (string) $response->json('access_token');
    }

    /**
     * Create a PayPal subscription and return the full response payload.
     * The `links` array will contain an `approve` URL to redirect the user to.
     *
     * @return array<string, mixed>
     */
    public function createSubscription(string $returnUrl, string $cancelUrl): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/v1/billing/subscriptions', [
                'plan_id'             => $this->planId,
                'application_context' => [
                    'return_url'  => $returnUrl,
                    'cancel_url'  => $cancelUrl,
                    'user_action' => 'SUBSCRIBE_NOW',
                ],
            ]);

        $response->throw();

        return (array) $response->json();
    }

    /**
     * Fetch current details for a subscription ID from the PayPal API.
     *
     * @return array<string, mixed>
     */
    public function getSubscription(string $subscriptionId): array
    {
        $token    = $this->getAccessToken();
        $response = Http::withToken($token)
            ->get($this->baseUrl . '/v1/billing/subscriptions/' . $subscriptionId);

        $response->throw();

        return (array) $response->json();
    }

    /**
     * Cancel a subscription via the PayPal API.
     */
    public function cancelSubscription(string $subscriptionId, string $reason = 'Cancelled by user'): void
    {
        $token = $this->getAccessToken();

        Http::withToken($token)
            ->post($this->baseUrl . '/v1/billing/subscriptions/' . $subscriptionId . '/cancel', [
                'reason' => $reason,
            ])
            ->throw();
    }

    /**
     * Verify that an incoming webhook request genuinely came from PayPal.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $webhookId = config('services.paypal.webhook_id');

        if (! $webhookId) {
            return false;
        }

        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/v1/notifications/verify-webhook-signature', [
                'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url'          => $request->header('PAYPAL-CERT-URL'),
                'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id'        => $webhookId,
                'webhook_event'     => $request->json()->all(),
            ]);

        if ($response->failed()) {
            return false;
        }

        return $response->json('verification_status') === 'SUCCESS';
    }
}
