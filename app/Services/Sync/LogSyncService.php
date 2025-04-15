<?php

namespace App\Services\Sync;

use Illuminate\Support\Facades\Log;

abstract class LogSyncService
{

    /**
     * Logs the progress of a sync operation at regular intervals.
     *
     * @param int $pageIndex Current page index of paginated data.
     * @param int $processed Total number of records processed so far.
     * @param float $startTime Start time of the sync operation (in microseconds).
     * @return void
     */
    protected function logProgress(int $pageIndex, int $processed, float $startTime): void
    {
        $duration = microtime(true) - $startTime;
        $recordsPerSecond = $duration > 0 ? round($processed / $duration, 1) : 0;

        Log::info("Sync progress", [
            'page' => $pageIndex,
            'processed' => $processed,
            'speed' => "$recordsPerSecond rec/sec",
        ]);
    }

    /**
     * Logs the final results of a sync operation.
     *
     * @param int $totalProcessed Total number of records processed.
     * @param float $startTime Start time of the sync operation (in microseconds).
     * @param string $serviceName Name of the sync service (e.g., 'Sales', 'Orders').
     * @return void
     */
    protected function logFinalResults(int $totalProcessed, float $startTime, string $serviceName): void
    {
        $duration = round(microtime(true) - $startTime, 2);

        Log::info("Sync {$serviceName} completed", [
            'total_processed' => $totalProcessed,
            'total_duration' => "$duration seconds",
        ]);
    }
}