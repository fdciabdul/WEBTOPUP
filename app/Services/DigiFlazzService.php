<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DigiFlazzService
{
    protected string $username;
    protected string $apiKey;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->username = config('digiflazz.username');
        $this->apiKey = config('digiflazz.api_key');
        $environment = config('digiflazz.environment', 'production');
        $this->baseUrl = config("digiflazz.endpoints.{$environment}");
        $this->timeout = config('digiflazz.timeout', 30);
    }

    protected function generateSignature(string $command): string
    {
        return md5($this->username . $this->apiKey . $command);
    }

    protected function makeRequest(string $endpoint, array $data = [])
    {
        $command = $data['cmd'] ?? '';
        $sign = $this->generateSignature($command);

        $payload = array_merge([
            'username' => $this->username,
            'sign' => $sign,
        ], $data);

        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . $endpoint, $payload);

            Log::info('DigiFlazz API Request', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('DigiFlazz API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('DigiFlazz API Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getPriceList(): array
    {
        $response = $this->makeRequest('/price-list', [
            'cmd' => 'prepaid',
        ]);

        return $response['data'] ?? [];
    }

    public function syncProducts(): array
    {
        $priceList = $this->getPriceList();
        $synced = 0;
        $errors = [];

        foreach ($priceList as $item) {
            try {
                $categoryName = $item['category'] ?? 'Lainnya';

                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    [
                        'name' => $categoryName,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]
                );

                $buyerSkuCode = $item['buyer_sku_code'] ?? null;
                $productName = $item['product_name'] ?? null;
                $price = $item['price'] ?? 0;

                if (!$buyerSkuCode || !$productName) {
                    continue;
                }

                $profit = 1000;
                $priceVisitor = $price + $profit;
                $priceReseller = $price + ($profit * 0.9);
                $priceResellerVip = $price + ($profit * 0.8);
                $priceResellerVvip = $price + ($profit * 0.7);

                Product::updateOrCreate(
                    ['provider_code' => $buyerSkuCode],
                    [
                        'category_id' => $category->id,
                        'name' => $productName,
                        'slug' => Str::slug($productName . '-' . $buyerSkuCode),
                        'description' => $item['desc'] ?? null,
                        'provider' => 'digiflazz',
                        'price_visitor' => $priceVisitor,
                        'price_reseller' => $priceReseller,
                        'price_reseller_vip' => $priceResellerVip,
                        'price_reseller_vvip' => $priceResellerVvip,
                        'provider_price' => $price,
                        'is_unlimited_stock' => !($item['buyer_product_status'] === 'empty'),
                        'stock' => $item['stock'] ?? 0,
                        'status' => ($item['buyer_product_status'] === 'available') ? 'active' : 'inactive',
                        'meta_data' => $item,
                    ]
                );

                $synced++;
            } catch (\Exception $e) {
                $errors[] = [
                    'product' => $item['product_name'] ?? 'Unknown',
                    'error' => $e->getMessage(),
                ];
                Log::error('Product sync error', [
                    'product' => $item,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => true,
            'synced' => $synced,
            'total' => count($priceList),
            'errors' => $errors,
        ];
    }

    public function createOrder(array $orderData): array
    {
        $refId = $orderData['ref_id'] ?? 'ORDER-' . time();

        $response = $this->makeRequest('/transaction', [
            'cmd' => 'prepaid',
            'ref_id' => $refId,
            'buyer_sku_code' => $orderData['buyer_sku_code'],
            'customer_no' => $orderData['customer_no'],
            'msg' => $orderData['msg'] ?? '',
        ]);

        return $response['data'] ?? $response;
    }

    public function checkOrderStatus(string $refId, string $buyerSkuCode): array
    {
        $response = $this->makeRequest('/transaction', [
            'cmd' => 'prepaid',
            'ref_id' => $refId,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no' => '',
            'msg' => '',
        ]);

        return $response['data'] ?? $response;
    }

    public function getBalance(): array
    {
        $response = $this->makeRequest('/cek-saldo', [
            'cmd' => 'deposit',
        ]);

        return $response['data'] ?? [];
    }

    public function validateCallback(array $data): bool
    {
        $secret = config('digiflazz.webhook_secret');
        if (!$secret) {
            return true;
        }

        $receivedSign = $data['sign'] ?? '';
        $expectedSign = md5($this->username . $this->apiKey . $data['ref_id']);

        return $receivedSign === $expectedSign;
    }
}
