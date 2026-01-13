<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Jobs\ProcessTopUpJob;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    protected DigiFlazzService $digiFlazzService;
    protected MidtransService $midtransService;

    public function __construct(
        DigiFlazzService $digiFlazzService,
        MidtransService $midtransService
    ) {
        $this->digiFlazzService = $digiFlazzService;
        $this->midtransService = $midtransService;
    }

    public function createOrder(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);

            if (!$product->isInStock()) {
                throw new \Exception('Product out of stock');
            }

            $user = auth()->user();
            $userLevel = $user ? $user->level : 'visitor';
            $productPrice = $product->getPriceByLevel($userLevel);

            $quantity = $data['quantity'] ?? 1;
            $subtotal = $productPrice * $quantity;

            $adminFee = $this->calculateAdminFee($data['payment_method'] ?? 'balance', $subtotal);
            $discount = $this->calculateDiscount($user, $subtotal);
            $totalAmount = $subtotal + $adminFee - $discount;

            $orderId = $this->generateOrderId();
            $invoiceNumber = $this->generateInvoiceNumber();

            $transaction = Transaction::create([
                'order_id' => $orderId,
                'invoice_number' => $invoiceNumber,
                'user_id' => $user?->id,
                'customer_name' => $data['customer_name'] ?? $user?->name,
                'customer_email' => $data['customer_email'] ?? $user?->email,
                'customer_phone' => $data['customer_phone'] ?? $user?->phone,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category_name' => $product->category->name,
                'order_data' => $data['order_data'] ?? [],
                'quantity' => $quantity,
                'product_price' => $productPrice,
                'admin_fee' => $adminFee,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'] ?? 'balance',
                'status' => 'pending',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::info('Order created', [
                'order_id' => $orderId,
                'user_id' => $user?->id,
                'product_id' => $product->id,
                'total_amount' => $totalAmount,
            ]);

            return $transaction;
        });
    }

    public function processPayment(Transaction $transaction): array
    {
        if ($transaction->payment_method === 'balance') {
            return $this->processBalancePayment($transaction);
        } else {
            return $this->processMidtransPayment($transaction);
        }
    }

    protected function processBalancePayment(Transaction $transaction): array
    {
        $user = $transaction->user;

        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!$user->hasBalance($transaction->total_amount)) {
            throw new \Exception('Insufficient balance');
        }

        DB::transaction(function () use ($transaction, $user) {
            $user->deductBalance(
                $transaction->total_amount,
                'transaction',
                $transaction->id,
                "Payment for order {$transaction->order_id}"
            );

            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_reference' => 'BALANCE-' . time(),
            ]);

            $user->increment('total_transactions');
            $user->increment('total_spending', $transaction->total_amount);
        });

        SendWhatsAppNotificationJob::dispatch($transaction, 'payment_success');
        SendEmailNotificationJob::dispatch($transaction, 'payment_success');

        ProcessTopUpJob::dispatch($transaction);

        return [
            'success' => true,
            'message' => 'Payment successful',
            'transaction' => $transaction->fresh(),
        ];
    }

    protected function processMidtransPayment(Transaction $transaction): array
    {
        $response = $this->midtransService->createTransaction($transaction);

        $transaction->update([
            'payment_reference' => $response['token'] ?? null,
            'payment_expired_at' => now()->addHours(24),
        ]);

        return [
            'success' => true,
            'message' => 'Payment gateway initiated',
            'snap_token' => $response['token'] ?? null,
            'redirect_url' => $response['redirect_url'] ?? null,
            'transaction' => $transaction->fresh(),
        ];
    }

    public function processTopUp(Transaction $transaction): void
    {
        if (!$transaction->isPaid()) {
            throw new \Exception('Transaction is not paid');
        }

        try {
            $transaction->update(['status' => 'processing']);

            $product = $transaction->product;
            $orderData = $transaction->order_data;

            $digiFlazzResponse = $this->digiFlazzService->createOrder([
                'ref_id' => $transaction->order_id,
                'buyer_sku_code' => $product->provider_code,
                'customer_no' => $orderData['customer_no'] ?? $orderData['target'],
                'msg' => $orderData['message'] ?? '',
            ]);

            $transaction->update([
                'provider_order_id' => $digiFlazzResponse['ref_id'] ?? null,
                'provider_status' => $digiFlazzResponse['status'] ?? null,
                'provider_response' => $digiFlazzResponse,
            ]);

            $this->handleProviderResponse($transaction, $digiFlazzResponse);

        } catch (\Exception $e) {
            Log::error('TopUp processing failed', [
                'order_id' => $transaction->order_id,
                'error' => $e->getMessage(),
            ]);

            $transaction->update([
                'status' => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            $this->handleFailedOrder($transaction);
        }
    }

    protected function handleProviderResponse(Transaction $transaction, array $response): void
    {
        $status = strtolower($response['status'] ?? 'pending');

        if ($status === 'sukses' || $status === 'success') {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'result_data' => [
                    'serial_number' => $response['sn'] ?? null,
                    'message' => $response['message'] ?? 'Top up successful',
                ],
            ]);

            $transaction->product->decrementStock($transaction->quantity);

            SendWhatsAppNotificationJob::dispatch($transaction, 'topup_success');
            SendEmailNotificationJob::dispatch($transaction, 'topup_success');

        } elseif ($status === 'gagal' || $status === 'failed') {
            $transaction->update([
                'status' => 'failed',
                'result_data' => [
                    'message' => $response['message'] ?? 'Top up failed',
                ],
            ]);

            $this->handleFailedOrder($transaction);
        }
    }

    protected function handleFailedOrder(Transaction $transaction): void
    {
        if ($transaction->payment_method === 'balance' && $transaction->user) {
            $transaction->user->addBalance(
                $transaction->total_amount,
                'refund',
                $transaction->id,
                "Refund for failed order {$transaction->order_id}"
            );

            $transaction->update([
                'is_refunded' => true,
                'refund_amount' => $transaction->total_amount,
                'refunded_at' => now(),
            ]);
        }

        SendWhatsAppNotificationJob::dispatch($transaction, 'topup_failed');
        SendEmailNotificationJob::dispatch($transaction, 'topup_failed');
    }

    protected function calculateAdminFee(string $paymentMethod, float $amount): float
    {
        if ($paymentMethod === 'balance') {
            return 0;
        }

        $fee = \App\Models\Fee::active()
            ->where('name', 'payment_gateway_fee')
            ->first();

        if ($fee) {
            return $fee->calculate($amount);
        }

        return $amount * 0.01;
    }

    protected function calculateDiscount(?User $user, float $amount): float
    {
        if (!$user) {
            return 0;
        }

        return 0;
    }

    protected function generateOrderId(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }

    public function cancelOrder(Transaction $transaction): void
    {
        if (!$transaction->isPending()) {
            throw new \Exception('Cannot cancel this order');
        }

        $transaction->update(['status' => 'cancelled']);

        if ($transaction->payment_method !== 'balance' && $transaction->payment_reference) {
            try {
                $this->midtransService->cancelTransaction($transaction->order_id);
            } catch (\Exception $e) {
                Log::warning('Failed to cancel Midtrans transaction', [
                    'order_id' => $transaction->order_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function refundOrder(Transaction $transaction): void
    {
        if (!$transaction->canBeRefunded()) {
            throw new \Exception('Cannot refund this order');
        }

        DB::transaction(function () use ($transaction) {
            if ($transaction->payment_method === 'balance' && $transaction->user) {
                $transaction->user->addBalance(
                    $transaction->total_amount,
                    'refund',
                    $transaction->id,
                    "Refund for order {$transaction->order_id}"
                );
            } elseif ($transaction->payment_method !== 'balance') {
                $this->midtransService->refund(
                    $transaction->order_id,
                    $transaction->total_amount
                );
            }

            $transaction->update([
                'is_refunded' => true,
                'refund_amount' => $transaction->total_amount,
                'refund_id' => 'REF-' . time(),
                'refunded_at' => now(),
            ]);
        });
    }
}
