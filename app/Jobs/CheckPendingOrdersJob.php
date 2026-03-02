<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\ApiGamesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckPendingOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function handle(ApiGamesService $apiGamesService): void
    {
        try {
            $pendingOrders = Transaction::where('status', 'processing')
                ->whereNotNull('provider_order_id')
                ->where('created_at', '>=', now()->subHours(24))
                ->get();

            Log::info('Checking pending orders', [
                'count' => $pendingOrders->count(),
            ]);

            foreach ($pendingOrders as $transaction) {
                try {
                    $response = $apiGamesService->checkOrderStatus($transaction->order_id);

                    // NOTE: The implementation of checkOrderStatus in ApiGamesService is a placeholder.
                    // The following logic is based on the previous implementation and may not work
                    // as expected until a real status check endpoint is available.

                    $status = strtolower($response['status'] ?? 'pending');

                    if ($status === 'sukses' || $status === 'success') {
                        $transaction->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'provider_status' => $status,
                            'provider_response' => $response,
                            'result_data' => [
                                'serial_number' => $response['sn'] ?? null,
                                'message' => $response['message'] ?? 'Success',
                            ],
                        ]);

                        SendWhatsAppNotificationJob::dispatch($transaction, 'topup_success');
                        SendEmailNotificationJob::dispatch($transaction, 'topup_success');

                        Log::info('Order status updated to completed', [
                            'order_id' => $transaction->order_id,
                        ]);
                    } elseif ($status === 'gagal' || $status === 'failed') {
                        $transaction->update([
                            'status' => 'failed',
                            'provider_status' => $status,
                            'provider_response' => $response,
                            'result_data' => [
                                'message' => $response['message'] ?? 'Failed',
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

                        Log::info('Order status updated to failed', [
                            'order_id' => $transaction->order_id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to check order status', [
                        'order_id' => $transaction->order_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Pending orders check completed');
        } catch (\Exception $e) {
            Log::error('Check pending orders job failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
