<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $snapUrl;
    protected string $apiUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.environment') === 'production';
        
        $env = $this->isProduction ? 'production' : 'sandbox';
        $this->snapUrl = config("midtrans.endpoints.snap.{$env}");
        $this->apiUrl = config("midtrans.endpoints.api.{$env}");
    }

    protected function getAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->serverKey . ':');
    }

    public function createTransaction(Transaction $transaction): array
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name,
                'email' => $transaction->customer_email,
                'phone' => $transaction->customer_phone,
            ],
            'item_details' => [
                [
                    'id' => $transaction->product_id,
                    'price' => (int) $transaction->product_price,
                    'quantity' => $transaction->quantity,
                    'name' => $transaction->product_name,
                ],
            ],
            'enabled_payments' => config('midtrans.enabled_payments'),
            'expiry' => [
                'duration' => config('midtrans.payment_expiry', 24),
                'unit' => 'hours',
            ],
        ];

        if ($transaction->admin_fee > 0) {
            $params['item_details'][] = [
                'id' => 'admin-fee',
                'price' => (int) $transaction->admin_fee,
                'quantity' => 1,
                'name' => 'Biaya Admin',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->snapUrl . '/transactions', $params);

            Log::info('Midtrans Create Transaction', [
                'order_id' => $transaction->order_id,
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Midtrans Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Create Transaction Error', [
                'order_id' => $transaction->order_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . '/' . $orderId . '/status');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Midtrans Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Get Status Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function cancelTransaction(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/' . $orderId . '/cancel');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Midtrans Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Transaction Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function refund(string $orderId, float $amount): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/' . $orderId . '/refund', [
                'amount' => (int) $amount,
                'reason' => 'Customer request',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Midtrans Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Refund Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function validateSignature(array $data): bool
    {
        $orderId = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $receivedSignature = $data['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return $receivedSignature === $expectedSignature;
    }

    public function mapStatus(string $transactionStatus, string $fraudStatus = null): string
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                return 'paid';
            }
            return 'pending';
        }

        return match($transactionStatus) {
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny', 'expire', 'cancel' => 'cancelled',
            'failure' => 'failed',
            default => 'pending',
        };
    }
}
