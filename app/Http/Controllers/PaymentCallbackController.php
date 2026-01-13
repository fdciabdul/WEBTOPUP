<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use App\Jobs\ProcessTopUpJob;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function midtrans(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Midtrans callback received', $payload);

            if (!$this->midtransService->validateSignature($payload)) {
                Log::error('Invalid Midtrans signature', $payload);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $orderId = $payload['order_id'];
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $newStatus = $this->midtransService->mapStatus($transactionStatus, $fraudStatus);

            $transaction->update([
                'status' => $newStatus,
                'payment_channel' => $payload['payment_type'] ?? null,
                'provider_response' => $payload,
            ]);

            if ($newStatus === 'paid') {
                $transaction->update(['paid_at' => now()]);

                if ($transaction->user) {
                    $transaction->user->increment('total_transactions');
                    $transaction->user->increment('total_spending', $transaction->total_amount);
                }

                SendWhatsAppNotificationJob::dispatch($transaction, 'payment_success');
                SendEmailNotificationJob::dispatch($transaction, 'payment_success');

                ProcessTopUpJob::dispatch($transaction);
            } elseif (in_array($newStatus, ['cancelled', 'failed'])) {
                SendWhatsAppNotificationJob::dispatch($transaction, 'topup_failed');
                SendEmailNotificationJob::dispatch($transaction, 'topup_failed');
            }

            Log::info('Transaction status updated', [
                'order_id' => $orderId,
                'status' => $newStatus,
            ]);

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans callback error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
