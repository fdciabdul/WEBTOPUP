<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\Services\ApiGamesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [60, 180, 600]; // Retry after 1min, 3min, 10min

    protected ApiGamesService $apiGamesService;

    public function handle(ApiGamesService $apiGamesService): void
    {
        $this->apiGamesService = $apiGamesService;

        Log::info('Starting product sync from ApiGames...');

        try {
            // Check connection first
            $connection = $this->apiGamesService->checkConnection();

            if (!isset($connection['status']) || $connection['status'] !== 'success') {
                throw new \Exception('Failed to connect to ApiGames API');
            }

            // Get product list
            $response = $this->apiGamesService->getProductList();

            if (!isset($response['data']) || !is_array($response['data'])) {
                throw new \Exception('Invalid response from ApiGames API');
            }

            $products = $response['data'];
            $synced = 0;
            $updated = 0;
            $failed = 0;

            foreach ($products as $productData) {
                try {
                    $exists = Product::where('provider', 'apigames')
                        ->where('provider_code', $productData['kode_produk'] ?? '')
                        ->exists();

                    $this->syncProduct($productData);

                    if ($exists) {
                        $updated++;
                    } else {
                        $synced++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    Log::warning('Failed to sync product', [
                        'product' => $productData['nama_produk'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Product sync completed', [
                'new' => $synced,
                'updated' => $updated,
                'failed' => $failed,
                'total' => count($products)
            ]);

        } catch (\Exception $e) {
            Log::error('SyncProductsJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    protected function syncProduct(array $data): void
    {
        // Get or create category
        $categoryName = $this->getCategoryName($data);
        $category = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            [
                'name' => $categoryName,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        // Parse prices
        $basePrice = floatval($data['harga'] ?? 0);

        // Calculate margin for different levels
        $priceVisitor = $basePrice * 1.15;      // 15% markup
        $priceReseller = $basePrice * 1.10;     // 10% markup
        $priceResellerVip = $basePrice * 1.07;  // 7% markup
        $priceResellerVvip = $basePrice * 1.05; // 5% markup

        // Create or update product
        Product::updateOrCreate(
            [
                'provider' => 'apigames',
                'provider_code' => $data['kode_produk']
            ],
            [
                'category_id' => $category->id,
                'name' => $data['nama_produk'],
                'slug' => Str::slug($data['nama_produk'] . '-' . $data['kode_produk']),
                'description' => $data['deskripsi'] ?? null,
                'price_visitor' => $priceVisitor,
                'price_reseller' => $priceReseller,
                'price_reseller_vip' => $priceResellerVip,
                'price_reseller_vvip' => $priceResellerVvip,
                'provider_price' => $basePrice,
                'is_active' => isset($data['status']) && $data['status'] === 'available',
                'is_unlimited_stock' => true,
                'is_featured' => false,
            ]
        );
    }

    protected function getCategoryName(array $data): string
    {
        $productName = strtolower($data['nama_produk'] ?? '');

        $patterns = [
            'Mobile Legends' => ['ml', 'mobile legends', 'mobilelegends', 'mobile legend'],
            'Free Fire' => ['ff', 'free fire', 'freefire', 'garena ff'],
            'PUBG Mobile' => ['pubg', 'pubg mobile', 'pubgm'],
            'Genshin Impact' => ['genshin', 'genshin impact'],
            'Honkai Star Rail' => ['honkai', 'hsr', 'star rail'],
            'Valorant' => ['valorant'],
            'Call of Duty' => ['cod', 'call of duty', 'codm'],
            'Arena of Valor' => ['aov', 'arena of valor'],
            'Higgs Domino' => ['higgs', 'domino', 'higgs domino'],
            'Steam Wallet' => ['steam'],
            'Google Play' => ['google play', 'googleplay', 'play store'],
            'Ragnarok' => ['ragnarok', 'ror'],
            'Sausage Man' => ['sausage', 'sausage man'],
            'Tower of Fantasy' => ['tower of fantasy', 'tof'],
        ];

        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($productName, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Other Games';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncProductsJob permanently failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
