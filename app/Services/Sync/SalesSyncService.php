<?php

namespace App\Services\Sync;

use App\Models\Sale;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesSyncService extends LogSyncService
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Synchronizes sales data from the API for a given date range.
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
        $uniqueKeys = ['g_number', 'date', 'warehouse_name'];

        // Define columns to update during upsert
        $updateColumns = [
            'last_change_date',
            'total_price',
            'discount_percent',
            'for_pay',
            'finished_price',
            'price_with_disc',
            'is_storno',
            'updated_at',
        ];

        // Track the number of processed records
        $processed = 0;

        // Record time for logging
        $startTime = microtime(true);

        // Iterate through paginated API data
        foreach ($this->apiService->fetchPaginatedData('sales', $params) as $pageIndex => $pageData) {
            try {
                // Prepare data for database insertion
                $preparedData = $this->prepareSalesData($pageData);

                // Perform upsert within a transaction for data consistency
                DB::transaction(function () use ($preparedData, $uniqueKeys, $updateColumns) {
                    Sale::upsert($preparedData, $uniqueKeys, $updateColumns);
                });

                $processed += count($preparedData);

                // Log progress every 10 pages for large imports
                if ($pageIndex % 10 === 0) {
                    $this->logProgress($pageIndex, $processed, $startTime);
                }

            } catch (\Throwable $e) {
                Log::error('Sales sync error', [
                    'page' => $pageIndex,
                    'error' => $e->getMessage(),
                    'sample_data' => $pageData[0] ?? null,
                ]);
                throw $e;
            }

            // Clear memory by unsetting temporary variables
            unset($preparedData, $pageData);
        }

        $this->logFinalResults($processed, $startTime, 'Sales');
    }

    /**
     * Prepares raw API sales data for database insertion.
     *
     * @param array $pageData Array of sales data from the API.
     * @return array Formatted data ready for upsert.
     */
    protected function prepareSalesData(array $pageData): array
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
                'is_supply' => $item['is_supply'] ?? false,
                'is_realization' => $item['is_realization'] ?? false,
                'promo_code_discount' => $item['promo_code_discount'] ?? null,
                'warehouse_name' => $item['warehouse_name'] ?? null,
                'country_name' => $item['country_name'] ?? null,
                'oblast_okrug_name' => $item['oblast_okrug_name'] ?? null,
                'region_name' => $item['region_name'] ?? null,
                'income_id' => $item['income_id'] ?? null,
                'sale_id' => $item['sale_id'] ?? null,
                'odid' => $item['odid'] ?? null,
                'spp' => $item['spp'] ?? 0,
                'for_pay' => $item['for_pay'] ?? 0,
                'finished_price' => $item['finished_price'] ?? 0,
                'price_with_disc' => $item['price_with_disc'] ?? 0,
                'nm_id' => $item['nm_id'] ?? null,
                'subject' => $item['subject'] ?? null,
                'category' => $item['category'] ?? null,
                'brand' => $item['brand'] ?? null,
                'is_storno' => $item['is_storno'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $preparedData;
    }
}