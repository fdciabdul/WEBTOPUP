<?php

namespace App\Jobs;

use App\Models\LogActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoDeleteOldLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function handle(): void
    {
        try {
            $daysToKeep = 30;

            $deleted = LogActivity::where('created_at', '<', now()->subDays($daysToKeep))
                ->delete();

            Log::info('Old log activities deleted', [
                'count' => $deleted,
                'days_kept' => $daysToKeep,
            ]);
        } catch (\Exception $e) {
            Log::error('Auto delete old logs job failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
