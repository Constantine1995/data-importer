<?php

namespace App\Console\Commands;

use App\Services\Sync\IncomesSyncService;
use App\Services\Sync\OrdersSyncService;
use App\Services\Sync\SalesSyncService;
use App\Services\Sync\StocksSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncApiData extends Command
{
    protected $signature = 'api:sync {--date-from=} {--date-to=}';
    protected $description = 'Sync data from API';

    public function handle(
        SalesSyncService   $salesSync,
        OrdersSyncService  $ordersSync,
        StocksSyncService  $stocksSync,
        IncomesSyncService $incomesSync
    ): void
    {
        $dateFrom = $this->option('date-from') ? Carbon::parse($this->option('date-from')) : Carbon::today();
        $dateTo = $this->option('date-to') ? Carbon::parse($this->option('date-to')) : Carbon::today();

        $this->info('Starting API sync...');

        // Sync Sales
        $this->info('Syncing sales...');
        $salesSync->sync($dateFrom, $dateTo);
        $this->info('Sales synced.');

        // Sync Orders
        $this->info('Syncing orders...');
        $ordersSync->sync($dateFrom, $dateTo);
        $this->info('Orders synced.');

        // Sync Stocks
        $this->info('Syncing stocks...');
        $stocksSync->sync(Carbon::today());
        $this->info('Stocks synced.');

        // Sync Incomes
        $this->info('Syncing incomes...');
        $incomesSync->sync($dateFrom, $dateTo);
        $this->info('Incomes synced.');

        $this->info('API sync completed.');
    }
}
