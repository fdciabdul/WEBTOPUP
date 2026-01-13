<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTopUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 180, 600];

    protected Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle(OrderService $orderService): void
    {
        try {
            Log::info('Processing top-up', [
                'order_id' => $this->transaction->order_id,
                'attempt' => $this->attempts(),
            ]);

            $orderService->processTopUp($this->transaction);

            Log::info('Top-up processed successfully', [
                'order_id' => $this->transaction->order_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Top-up processing failed', [
                'order_id' => $this->transaction->order_id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->transaction->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'error' => 'Max retry attempts reached',
                        'original_error' => $e->getMessage(),
                    ],
                ]);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Top-up job failed permanently', [
            'order_id' => $this->transaction->order_id,
            'error' => $exception->getMessage(),
        ]);

        $this->transaction->update([
            'status' => 'failed',
            'provider_response' => [
                'error' => 'Job failed permanently',
                'message' => $exception->getMessage(),
            ],
        ]);
    }
}
