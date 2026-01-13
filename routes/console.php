<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncProductsJob;
use App\Jobs\CheckPendingOrdersJob;
use App\Jobs\AutoDeleteExpiredTransactionsJob;
use App\Jobs\AutoDeleteOldLogsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Tasks
Schedule::job(new SyncProductsJob)->hourly();
Schedule::job(new CheckPendingOrdersJob)->everyFiveMinutes();
Schedule::job(new AutoDeleteExpiredTransactionsJob)->daily();
Schedule::job(new AutoDeleteOldLogsJob)->daily();
