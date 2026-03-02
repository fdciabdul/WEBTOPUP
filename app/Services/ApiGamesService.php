<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiGamesService
{
    protected string $merchantId;
    protected string $secretKey;
    protected string $baseUrl;
    protected int $timeout;

    /**
     * Game code mapping from short codes to API codes
     * API only supports: mobilelegend, freefire, higgs
     */
    protected array $gameCodeMap = [
        'ml' => 'mobilelegend',
        'mobile-legends' => 'mobilelegend',
        'mobilelegend' => 'mobilelegend',
        'mobilelegends' => 'mobilelegend',
        'ff' => 'freefire',
        'free-fire' => 'freefire',
        'freefire' => 'freefire',
        'higgs' => 'higgs',
        'higgs-domino' => 'higgs',
        'higgsdomino' => 'higgs',
    ];

    public function __construct()
    {
        $this->merchantId = config('apigames.merchant_id');
        $this->secretKey = config('apigames.secret_key');
        $this->baseUrl = config('apigames.api_url', 'https://v1.apigames.id');
        $this->timeout = config('apigames.timeout', 30);
    }

    /**
     * Map short game code to API game code
     */
    protected function mapGameCode(string $gameCode): string
    {
        $normalized = strtolower(trim($gameCode));
        return $this->gameCodeMap[$normalized] ?? $gameCode;
    }

    protected function generateSignature(): string
    {
        return md5($this->merchantId . $this->secretKey);
    }

    protected function makeRequest(string $endpoint, array $params = [], string $method = 'GET')
    {
        try {
            $url = $this->baseUrl . $endpoint;

            if ($method === 'GET') {
                $response = Http::timeout($this->timeout)->get($url, $params);
            } else {
                $response = Http::timeout($this->timeout)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $params);
            }

            Log::info('ApiGames API Request', [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => $params,
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('ApiGames API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('ApiGames API Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check connection to specific engine
     *
     * @param string $engine (higgs, kiosgamer, smileone, unipin, unipinbr, unipinmy, gamepoint)
     * @return array
     * @throws \Exception
     */
    public function checkConnection(string $engine = 'higgs'): array
    {
        $endpoint = "/merchant/{$this->merchantId}/cek-koneksi";
        $params = [
            'engine' => $engine,
            'signature' => $this->generateSignature()
        ];

        return $this->makeRequest($endpoint, $params, 'GET');
    }

    /**
     * Checks the username for a given game.
     *
     * @param string $gameCode The game code (e.g., 'ml', 'ff').
     * @param string $userId The user's game ID.
     * @param string|null $serverId The server ID, if applicable.
     * @return array ['username' => string] or ['message' => string] on failure
     * @throws \Exception
     */
    public function checkUsername(string $gameCode, string $userId, ?string $serverId = null): array
    {
        // Map short game code to API game code
        $apiGameCode = $this->mapGameCode($gameCode);

        $endpoint = "/merchant/{$this->merchantId}/cek-username/{$apiGameCode}";
        $params = [
            'user_id' => $userId,
            'signature' => $this->generateSignature()
        ];

        // Mobile Legends requires zone_id (server_id)
        if ($serverId && $apiGameCode === 'mobilelegend') {
            $params['zone_id'] = $serverId;
        }

        $response = $this->makeRequest($endpoint, $params, 'GET');

        // Parse API response format:
        // Success: { "status": 1, "rc": 0, "data": { "is_valid": true, "username": "xxx" } }
        // Failure: { "status": 1, "rc": 0, "data": { "is_valid": false, "username": "" } }
        // Error: { "status": 0, "rc": 2, "error_msg": "Invalid User ID Or Zone ID" }

        if (isset($response['data']['is_valid']) && $response['data']['is_valid'] === true) {
            return [
                'username' => $response['data']['username'] ?? 'Valid User',
            ];
        }

        // Handle error response from API
        if (isset($response['error_msg'])) {
            return [
                'message' => $response['error_msg'],
            ];
        }

        return [
            'message' => $response['message'] ?? 'User ID tidak ditemukan',
        ];
    }

    /**
     * Creates a new top-up order using v2 API
     *
     * @param array $orderData
     * @return array
     * @throws \Exception
     */
    public function createOrder(array $orderData): array
    {
        $params = [
            'ref_id' => $orderData['ref_id'],
            'merchant_id' => $this->merchantId,
            'produk' => $orderData['product_code'],
            'tujuan' => $orderData['customer_no'],
            'server_id' => $orderData['server_id'] ?? '',
            'signature' => $this->generateSignature()
        ];

        return $this->makeRequest('/v2/transaksi', $params, 'POST');
    }

    /**
     * Get product list (requires implementation based on API docs)
     *
     * @return array
     * @throws \Exception
     */
    public function getProductList(): array
    {
        $endpoint = "/merchant/{$this->merchantId}/produk";
        $params = [
            'signature' => $this->generateSignature()
        ];

        return $this->makeRequest($endpoint, $params, 'GET');
    }

    /**
     * Checks the status of an existing order.
     *
     * NOTE: The Apigames documentation does not provide a specific endpoint for checking
     * order status. This method is a placeholder. The transaction response might contain
     * the final status, or a callback/webhook might be used.
     *
     * @param string $refId
     * @return array
     */
    public function checkOrderStatus(string $refId): array
    {
        Log::warning('ApiGamesService: checkOrderStatus is not implemented as API endpoint is not available.');
        
        // Returning a dummy response. In a real scenario, you might query your
        // local transaction status if a webhook is used to update it.
        return [
            'status' => 'pending',
            'message' => 'Status check is not available for this provider.'
        ];
    }
    
    /**
     * Gets the merchant's account balance.
     *
     * @return array
     * @throws \Exception
     */
    public function getBalance(): array
    {
        $endpoint = "/merchant/{$this->merchantId}";

        // The documentation for `info akun` doesn't include merchant_id in the query, but it is in the path.
        // Let's make a GET request without the merchant_id in the params.
        $params = ['signature' => $this->generateSignature()];

        $response = $this->makeRequest($endpoint, $params);

        return $response['data'] ?? [];
    }
}
