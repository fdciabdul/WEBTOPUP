<?php

namespace App\Jobs;

use App\Services\DigiFlazzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    public function handle(DigiFlazzService $digiFlazzService): void
    {
        try {
            Log::info('Starting product sync from DigiFlazz');

            $result = $digiFlazzService->syncProducts();

            Log::info('Product sync completed', [
                'synced' => $result['synced'],
                'total' => $result['total'],
                'errors' => count($result['errors']),
            ]);
        } catch (\Exception $e) {
            Log::error('Product sync failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Product sync job failed permanently', [
            'error' => $exception->getMessage(),
        ]);
    }
}
