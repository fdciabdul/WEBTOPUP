<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\DigiFlazzService;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigiFlazzCallbackController extends Controller
{
    protected DigiFlazzService $digiFlazzService;

    public function __construct(DigiFlazzService $digiFlazzService)
    {
        $this->digiFlazzService = $digiFlazzService;
    }

    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('DigiFlazz callback received', $payload);

            if (!$this->digiFlazzService->validateCallback($payload)) {
                Log::error('Invalid DigiFlazz signature', $payload);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $refId = $payload['ref_id'];
            $status = strtolower($payload['status'] ?? 'pending');

            $transaction = Transaction::where('order_id', $refId)->first();

            if (!$transaction) {
                Log::error('Transaction not found', ['ref_id' => $refId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'provider_status' => $status,
                'provider_response' => $payload,
            ]);

            if ($status === 'sukses' || $status === 'success') {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'result_data' => [
                        'serial_number' => $payload['sn'] ?? null,
                        'message' => $payload['message'] ?? 'Success',
                    ],
                ]);

                $transaction->product->decrementStock($transaction->quantity);

                SendWhatsAppNotificationJob::dispatch($transaction, 'topup_success');
                SendEmailNotificationJob::dispatch($transaction, 'topup_success');

            } elseif ($status === 'gagal' || $status === 'failed') {
                $transaction->update([
                    'status' => 'failed',
                    'result_data' => [
                        'message' => $payload['message'] ?? 'Failed',
                    ],
                ]);

                if ($transaction->user) {
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

            Log::info('Transaction status updated from DigiFlazz callback', [
                'ref_id' => $refId,
                'status' => $status,
            ]);

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('DigiFlazz callback error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
