<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $limit;

    public function __construct()
    {
        // Create a new HTTP client instance
        $this->client = new Client();

        // Load configuration values
        $this->baseUrl = config('services.api.base_url');
        $this->apiKey = config('services.api.key');
        $this->limit = config('services.api.limit');
    }

    /**
     * Fetches data from a specified API endpoint.
     *
     * @param string $endpoint API endpoint to query (e.g., 'sales', 'orders').
     * @param array $params Additional query parameters.
     * @return array Decoded JSON response or empty array on failure.
     */
    public function fetchData(string $endpoint, array $params = []): array
    {
        try {
            // Merge default parameters with provided ones
            $params = array_merge([
                'key' => $this->apiKey,
                'limit' => $this->limit,
                'page' => 1,
            ], $params);

            // Send GET request to the API
            $response = $this->client->get("{$this->baseUrl}/{$endpoint}", [
                'query' => $params,
            ]);

            // Decode and return JSON response
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("API fetch error for {$endpoint}: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Fetches paginated data from an API endpoint using a generator.
     *
     * @param string $endpoint API endpoint to query.
     * @param array $params Additional query parameters.
     * @return \Generator Yields arrays of data for each page.
     */
    public function fetchPaginatedData(string $endpoint, array $params = []): \Generator
    {
        $page = 1;
        do {
            // Set current page in parameters
            $params['page'] = $page;

            // Fetch data for the current page
            $data = $this->fetchData($endpoint, $params);

            if (empty($data['data'])) {
                break;
            }

            // Yield the data array for this page
            yield $data['data'];

            $page++;

            // Continue until last page is reached
        } while (isset($data['meta']['last_page']) && $page <= $data['meta']['last_page']);
    }
}