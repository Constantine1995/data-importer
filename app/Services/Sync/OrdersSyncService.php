<?php

namespace App\Services\Sync;

use App\Models\Order;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersSyncService extends LogSyncService
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Synchronizes orders data from the API for a given date range.
     *
     * @param Carbon $dateFrom Start date for the sync period.
     * @param Carbon $dateTo End date for the sync period.
     * @return void
     */
    public function sync(Carbon $dateFrom, Carbon $dateTo): void
    {
        // Prepare API request parameters
        $params = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
        ];

        // Define unique keys for upsert operation
        $uniqueKeys = ['g_number'];

        // Define columns to update during upsert
        $updateColumns = [
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'total_price',
            'discount_percent',
            'warehouse_name',
            'oblast',
            'income_id',
            'odid',
            'nm_id',
            'subject',
            'category',
            'brand',
            'is_cancel',
            'cancel_dt',
        ];

        // Track the number of processed records
        $processed = 0;

        // Record time for logging
        $startTime = microtime(true);

        // Iterate through paginated API data
        foreach ($this->apiService->fetchPaginatedData('orders', $params) as $pageIndex => $pageData) {
            try {
                // Prepare data for database insertion
                $preparedData = $this->prepareOrdersData($pageData);

                // Perform upsert within a transaction for data consistency
                DB::transaction(function () use ($preparedData, $uniqueKeys, $updateColumns) {
                    Order::upsert($preparedData, $uniqueKeys, $updateColumns);
                });

                $processed += count($preparedData);

                // Log progress every 10 pages for large imports
                if ($pageIndex % 10 === 0) {
                    $this->logProgress($pageIndex, $processed, $startTime);
                }

            } catch (\Throwable $e) {
                Log::error('Orders sync error', [
                    'page' => $pageIndex,
                    'error' => $e->getMessage(),
                    'sample_data' => $pageData[0] ?? null,
                ]);
                throw $e;
            }

            // Clear memory by unsetting temporary variables
            unset($preparedData, $pageData);
        }

        $this->logFinalResults($processed, $startTime, 'Order');
    }

    /**
     * Prepares raw API orders data for database insertion.
     *
     * @param array $pageData Array of sales data from the API.
     * @return array Formatted data ready for upsert.
     */
    protected function prepareOrdersData(array $pageData): array
    {
        // Get current timestamp
        $now = now();
        $preparedData = [];

        foreach ($pageData as $item) {
            $preparedData[] = [
                'g_number' => $item['g_number'] ?? null,
                'date' => $item['date'] ?? null,
                'last_change_date' => $item['last_change_date'] ?? null,
                'supplier_article' => $item['supplier_article'] ?? null,
                'tech_size' => $item['tech_size'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'total_price' => $item['total_price'] ?? 0,
                'discount_percent' => $item['discount_percent'] ?? 0,
                'warehouse_name' => $item['warehouse_name'] ?? null,
                'oblast' => $item['oblast'] ?? null,
                'income_id' => $item['income_id'] ?? null,
                'odid' => $item['odid'] ?? null,
                'nm_id' => $item['nm_id'] ?? null,
                'subject' => $item['subject'] ?? null,
                'category' => $item['category'] ?? null,
                'brand' => $item['brand'] ?? null,
                'is_cancel' => $item['is_cancel'] ?? false,
                'cancel_dt' => $item['cancel_dt'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $preparedData;
    }
}