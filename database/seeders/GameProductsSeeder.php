<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GameProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gameData = [
            'Mobile Legends' => [
                ['name' => '86 Diamonds', 'code' => 'ML86', 'price' => 20000],
                ['name' => '172 Diamonds', 'code' => 'ML172', 'price' => 40000],
                ['name' => '257 Diamonds', 'code' => 'ML257', 'price' => 59000],
                ['name' => '344 Diamonds', 'code' => 'ML344', 'price' => 78000],
                ['name' => '429 Diamonds', 'code' => 'ML429', 'price' => 97000],
                ['name' => '514 Diamonds', 'code' => 'ML514', 'price' => 116000],
                ['name' => '706 Diamonds', 'code' => 'ML706', 'price' => 157000],
            ],
            'Free Fire' => [
                ['name' => '70 Diamonds', 'code' => 'FF70', 'price' => 9500],
                ['name' => '140 Diamonds', 'code' => 'FF140', 'price' => 19000],
                ['name' => '355 Diamonds', 'code' => 'FF355', 'price' => 47000],
                ['name' => '720 Diamonds', 'code' => 'FF720', 'price' => 95000],
                ['name' => '1450 Diamonds', 'code' => 'FF1450', 'price' => 190000],
            ],
            'PUBG Mobile' => [
                ['name' => '60 UC', 'code' => 'PUBG60', 'price' => 15000],
                ['name' => '325 UC', 'code' => 'PUBG325', 'price' => 79000],
                ['name' => '660 UC', 'code' => 'PUBG660', 'price' => 158000],
                ['name' => '1800 UC', 'code' => 'PUBG1800', 'price' => 395000],
            ],
            'Genshin Impact' => [
                ['name' => '60 Genesis Crystals', 'code' => 'GI60', 'price' => 15000],
                ['name' => '330 Genesis Crystals', 'code' => 'GI330', 'price' => 75000],
                ['name' => '1090 Genesis Crystals', 'code' => 'GI1090', 'price' => 245000],
                ['name' => '2240 Genesis Crystals', 'code' => 'GI2240', 'price' => 490000],
            ],
            'Honor of Kings' => [
                ['name' => '50 Tokens', 'code' => 'HOK50', 'price' => 12000],
                ['name' => '250 Tokens', 'code' => 'HOK250', 'price' => 60000],
                ['name' => '500 Tokens', 'code' => 'HOK500', 'price' => 120000],
            ],
            'Call of Duty Mobile' => [
                ['name' => '60 CP', 'code' => 'CODM60', 'price' => 15000],
                ['name' => '320 CP', 'code' => 'CODM320', 'price' => 75000],
                ['name' => '700 CP', 'code' => 'CODM700', 'price' => 155000],
            ],
            'Valorant' => [
                ['name' => '125 VP', 'code' => 'VAL125', 'price' => 15000],
                ['name' => '420 VP', 'code' => 'VAL420', 'price' => 50000],
                ['name' => '700 VP', 'code' => 'VAL700', 'price' => 85000],
                ['name' => '1375 VP', 'code' => 'VAL1375', 'price' => 165000],
            ],
        ];

        foreach ($gameData as $categoryName => $products) {
            // Create or find category
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => 'Top up ' . $categoryName,
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            echo "Creating products for {$categoryName}...\n";

            foreach ($products as $productData) {
                // Calculate prices with margins
                $basePrice = $productData['price'];
                $visitorPrice = ceil($basePrice * 1.15 / 100) * 100; // 15% margin, rounded to hundreds
                $resellerPrice = ceil($basePrice * 1.10 / 100) * 100; // 10% margin
                $resellerVipPrice = ceil($basePrice * 1.07 / 100) * 100; // 7% margin
                $resellerVvipPrice = ceil($basePrice * 1.05 / 100) * 100; // 5% margin

                Product::updateOrCreate(
                    [
                        'provider' => 'apigames',
                        'provider_code' => $productData['code']
                    ],
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'slug' => Str::slug($categoryName . '-' . $productData['name']),
                        'description' => 'Top up ' . $productData['name'] . ' untuk ' . $categoryName . '. Proses cepat dan otomatis!',
                        'price_visitor' => $visitorPrice,
                        'price_reseller' => $resellerPrice,
                        'price_reseller_vip' => $resellerVipPrice,
                        'price_reseller_vvip' => $resellerVvipPrice,
                        'provider_price' => $basePrice,
                        'status' => 'active',
                        'stock' => 9999,
                        'is_unlimited_stock' => true,
                    ]
                );

                echo "  - Created: {$productData['name']}\n";
            }
        }

        echo "\nTotal categories: " . Category::count() . "\n";
        echo "Total products: " . Product::count() . "\n";
    }
}
