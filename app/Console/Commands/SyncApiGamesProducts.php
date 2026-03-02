<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\ApiGamesService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncApiGamesProducts extends Command
{
    protected $signature = 'apigames:sync-products';
    protected $description = 'Sync products from ApiGames API';

    protected ApiGamesService $apiGamesService;

    public function __construct(ApiGamesService $apiGamesService)
    {
        parent::__construct();
        $this->apiGamesService = $apiGamesService;
    }

    public function handle(): int
    {
        $this->info('Starting ApiGames product sync...');

        try {
            // Check connection first
            $this->info('Checking ApiGames connection...');
            $connection = $this->apiGamesService->checkConnection();
            
            if (!isset($connection['status']) || $connection['status'] !== 'success') {
                $this->error('Failed to connect to ApiGames API');
                return 1;
            }

            $this->info('Connection successful!');

            // Get product list
            $this->info('Fetching products from ApiGames...');
            $response = $this->apiGamesService->getProductList();

            if (!isset($response['data']) || !is_array($response['data'])) {
                $this->error('Invalid response from ApiGames API');
                return 1;
            }

            $products = $response['data'];
            $this->info('Found ' . count($products) . ' products');

            $synced = 0;
            $updated = 0;
            $failed = 0;

            foreach ($products as $productData) {
                try {
                    $exists = Product::where('provider_code', $productData['kode_produk'] ?? '')->exists();
                    
                    $this->syncProduct($productData);
                    
                    if ($exists) {
                        $updated++;
                    } else {
                        $synced++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $this->warn("Failed to sync product: {$e->getMessage()}");
                }
            }

            $this->info("Sync completed!");
            $this->info("New products: {$synced}");
            $this->info("Updated products: {$updated}");
            $this->info("Failed: {$failed}");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    protected function syncProduct(array $data): void
    {
        // Get or create category based on game type
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
        $priceVisitor = $basePrice * 1.15;
        $priceReseller = $basePrice * 1.10;
        $priceResellerVip = $basePrice * 1.07;
        $priceResellerVvip = $basePrice * 1.05;

        // Create or update product
        Product::updateOrCreate(
            [
                'provider' => 'apigames',
                'provider_code' => $data['kode_produk']
            ],
            [
                'category_id' => $category->id,
                'name' => $data['nama_produk'],
                'slug' => Str::slug($data['nama_produk']),
                'description' => $data['deskripsi'] ?? null,
                'price_visitor' => $priceVisitor,
                'price_reseller' => $priceReseller,
                'price_reseller_vip' => $priceResellerVip,
                'price_reseller_vvip' => $priceResellerVvip,
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
            'Mobile Legends' => ['ml', 'mobile legends', 'mobilelegends'],
            'Free Fire' => ['ff', 'free fire', 'freefire', 'garena'],
            'PUBG Mobile' => ['pubg', 'pubg mobile'],
            'Genshin Impact' => ['genshin', 'genshin impact'],
            'Honkai Star Rail' => ['honkai', 'hsr', 'star rail'],
            'Valorant' => ['valorant'],
            'Call of Duty' => ['cod', 'call of duty', 'codm'],
            'Arena of Valor' => ['aov', 'arena of valor'],
            'Higgs Domino' => ['higgs', 'domino'],
            'Steam Wallet' => ['steam'],
            'Google Play' => ['google play', 'googleplay'],
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
}
