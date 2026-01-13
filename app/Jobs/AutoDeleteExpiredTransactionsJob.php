<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoDeleteExpiredTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function handle(): void
    {
        try {
            $expiredTransactions = Transaction::where('status', 'pending')
                ->where('payment_expired_at', '<', now())
                ->where('created_at', '<', now()->subDays(7))
                ->get();

            $count = $expiredTransactions->count();

            foreach ($expiredTransactions as $transaction) {
                $transaction->update(['status' => 'cancelled']);
                $transaction->delete();
            }

            Log::info('Expired transactions deleted', [
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Auto delete expired transactions job failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
