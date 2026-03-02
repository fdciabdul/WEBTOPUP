<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProperGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Structure:
     * - Categories = Individual games (Mobile Legends, Free Fire, etc.)
     * - Each category has: game_code (for username validation), type (mobile/pc/ewallet/etc), icon
     * - Products = Denominations within each game (86 Diamonds, 172 Diamonds, etc.)
     */
    public function run(): void
    {
        $games = [
            // MOBILE GAMES
            [
                'name' => 'Mobile Legends',
                'game_code' => 'ml',
                'type' => 'mobile',
                'icon' => 'games/mobile-legends.jpg', // You need to upload this image
                'description' => 'Top up Mobile Legends diamonds cepat dan aman',
                'products' => [
                    ['name' => '86 Diamonds', 'code' => 'ML86', 'price' => 20000],
                    ['name' => '172 Diamonds', 'code' => 'ML172', 'price' => 40000],
                    ['name' => '257 Diamonds', 'code' => 'ML257', 'price' => 59000],
                    ['name' => '344 Diamonds', 'code' => 'ML344', 'price' => 78000],
                    ['name' => '429 Diamonds', 'code' => 'ML429', 'price' => 97000],
                    ['name' => '514 Diamonds', 'code' => 'ML514', 'price' => 116000],
                    ['name' => '706 Diamonds', 'code' => 'ML706', 'price' => 157000],
                    ['name' => '1412 Diamonds', 'code' => 'ML1412', 'price' => 313000],
                ],
            ],
            [
                'name' => 'Free Fire',
                'game_code' => 'ff',
                'type' => 'mobile',
                'icon' => 'games/free-fire.png',
                'description' => 'Top up Free Fire diamonds murah dan proses otomatis',
                'products' => [
                    ['name' => '70 Diamonds', 'code' => 'FF70', 'price' => 9500],
                    ['name' => '140 Diamonds', 'code' => 'FF140', 'price' => 19000],
                    ['name' => '355 Diamonds', 'code' => 'FF355', 'price' => 47000],
                    ['name' => '720 Diamonds', 'code' => 'FF720', 'price' => 95000],
                    ['name' => '1450 Diamonds', 'code' => 'FF1450', 'price' => 190000],
                    ['name' => '2180 Diamonds', 'code' => 'FF2180', 'price' => 285000],
                ],
            ],
            [
                'name' => 'PUBG Mobile',
                'game_code' => 'pubg',
                'type' => 'mobile',
                'icon' => 'games/pubg-mobile.jpg',
                'description' => 'Top up PUBG Mobile UC termurah',
                'products' => [
                    ['name' => '60 UC', 'code' => 'PUBG60', 'price' => 15000],
                    ['name' => '325 UC', 'code' => 'PUBG325', 'price' => 79000],
                    ['name' => '660 UC', 'code' => 'PUBG660', 'price' => 158000],
                    ['name' => '1800 UC', 'code' => 'PUBG1800', 'price' => 395000],
                    ['name' => '3850 UC', 'code' => 'PUBG3850', 'price' => 790000],
                ],
            ],
            [
                'name' => 'Genshin Impact',
                'game_code' => 'genshin',
                'type' => 'mobile',
                'icon' => 'games/genshin-impact.png',
                'description' => 'Top up Genshin Impact Genesis Crystals',
                'products' => [
                    ['name' => '60 Genesis Crystals', 'code' => 'GI60', 'price' => 15000],
                    ['name' => '330 Genesis Crystals', 'code' => 'GI330', 'price' => 75000],
                    ['name' => '1090 Genesis Crystals', 'code' => 'GI1090', 'price' => 245000],
                    ['name' => '2240 Genesis Crystals', 'code' => 'GI2240', 'price' => 490000],
                    ['name' => '3880 Genesis Crystals', 'code' => 'GI3880', 'price' => 790000],
                ],
            ],
            [
                'name' => 'Honor of Kings',
                'game_code' => 'hok',
                'type' => 'mobile',
                'icon' => 'games/honor-of-kings.jpg',
                'description' => 'Top up Honor of Kings tokens cepat',
                'products' => [
                    ['name' => '50 Tokens', 'code' => 'HOK50', 'price' => 12000],
                    ['name' => '250 Tokens', 'code' => 'HOK250', 'price' => 60000],
                    ['name' => '500 Tokens', 'code' => 'HOK500', 'price' => 120000],
                    ['name' => '1000 Tokens', 'code' => 'HOK1000', 'price' => 240000],
                ],
            ],
            [
                'name' => 'Call of Duty Mobile',
                'game_code' => 'cod',
                'type' => 'mobile',
                'icon' => 'games/codm.jpg',
                'description' => 'Top up Call of Duty Mobile CP',
                'products' => [
                    ['name' => '60 CP', 'code' => 'CODM60', 'price' => 15000],
                    ['name' => '320 CP', 'code' => 'CODM320', 'price' => 75000],
                    ['name' => '700 CP', 'code' => 'CODM700', 'price' => 155000],
                    ['name' => '1500 CP', 'code' => 'CODM1500', 'price' => 320000],
                ],
            ],
            [
                'name' => 'Arena of Valor',
                'game_code' => 'aov',
                'type' => 'mobile',
                'icon' => 'games/aov.jpg',
                'description' => 'Top up Arena of Valor vouchers',
                'products' => [
                    ['name' => '40 Vouchers', 'code' => 'AOV40', 'price' => 10000],
                    ['name' => '110 Vouchers', 'code' => 'AOV110', 'price' => 25000],
                    ['name' => '280 Vouchers', 'code' => 'AOV280', 'price' => 65000],
                    ['name' => '570 Vouchers', 'code' => 'AOV570', 'price' => 130000],
                ],
            ],

            // PC GAMES
            [
                'name' => 'Valorant',
                'game_code' => 'valorant',
                'type' => 'pc',
                'icon' => 'games/valorant.jpg',
                'description' => 'Top up Valorant Points (VP)',
                'products' => [
                    ['name' => '125 VP', 'code' => 'VAL125', 'price' => 15000],
                    ['name' => '420 VP', 'code' => 'VAL420', 'price' => 50000],
                    ['name' => '700 VP', 'code' => 'VAL700', 'price' => 85000],
                    ['name' => '1375 VP', 'code' => 'VAL1375', 'price' => 165000],
                    ['name' => '2400 VP', 'code' => 'VAL2400', 'price' => 280000],
                ],
            ],

            // E-WALLET
            [
                'name' => 'DANA',
                'game_code' => 'dana',
                'type' => 'ewallet',
                'icon' => 'games/dana.png',
                'description' => 'Top up saldo DANA',
                'products' => [
                    ['name' => 'Rp 10.000', 'code' => 'DANA10', 'price' => 11000],
                    ['name' => 'Rp 20.000', 'code' => 'DANA20', 'price' => 21000],
                    ['name' => 'Rp 50.000', 'code' => 'DANA50', 'price' => 51000],
                    ['name' => 'Rp 100.000', 'code' => 'DANA100', 'price' => 101000],
                    ['name' => 'Rp 200.000', 'code' => 'DANA200', 'price' => 201000],
                ],
            ],
            [
                'name' => 'GoPay',
                'game_code' => 'gopay',
                'type' => 'ewallet',
                'icon' => 'games/gopay.png',
                'description' => 'Top up saldo GoPay',
                'products' => [
                    ['name' => 'Rp 10.000', 'code' => 'GOPAY10', 'price' => 11000],
                    ['name' => 'Rp 20.000', 'code' => 'GOPAY20', 'price' => 21000],
                    ['name' => 'Rp 50.000', 'code' => 'GOPAY50', 'price' => 51000],
                    ['name' => 'Rp 100.000', 'code' => 'GOPAY100', 'price' => 101000],
                ],
            ],
            [
                'name' => 'OVO',
                'game_code' => 'ovo',
                'type' => 'ewallet',
                'icon' => 'games/ovo.png',
                'description' => 'Top up saldo OVO',
                'products' => [
                    ['name' => 'Rp 10.000', 'code' => 'OVO10', 'price' => 11000],
                    ['name' => 'Rp 20.000', 'code' => 'OVO20', 'price' => 21000],
                    ['name' => 'Rp 50.000', 'code' => 'OVO50', 'price' => 51000],
                    ['name' => 'Rp 100.000', 'code' => 'OVO100', 'price' => 101000],
                ],
            ],

            // PULSA
            [
                'name' => 'Telkomsel',
                'game_code' => 'telkomsel',
                'type' => 'pulsa',
                'icon' => 'games/telkomsel.png',
                'description' => 'Pulsa Telkomsel',
                'products' => [
                    ['name' => '5.000', 'code' => 'TSEL5', 'price' => 6000],
                    ['name' => '10.000', 'code' => 'TSEL10', 'price' => 11000],
                    ['name' => '20.000', 'code' => 'TSEL20', 'price' => 20500],
                    ['name' => '50.000', 'code' => 'TSEL50', 'price' => 49500],
                    ['name' => '100.000', 'code' => 'TSEL100', 'price' => 98000],
                ],
            ],
            [
                'name' => 'Indosat',
                'game_code' => 'indosat',
                'type' => 'pulsa',
                'icon' => 'games/indosat.png',
                'description' => 'Pulsa Indosat',
                'products' => [
                    ['name' => '5.000', 'code' => 'ISAT5', 'price' => 6000],
                    ['name' => '10.000', 'code' => 'ISAT10', 'price' => 11000],
                    ['name' => '20.000', 'code' => 'ISAT20', 'price' => 20500],
                    ['name' => '50.000', 'code' => 'ISAT50', 'price' => 49500],
                ],
            ],
            [
                'name' => 'XL Axiata',
                'game_code' => 'xl',
                'type' => 'pulsa',
                'icon' => 'games/xl.png',
                'description' => 'Pulsa XL',
                'products' => [
                    ['name' => '5.000', 'code' => 'XL5', 'price' => 6000],
                    ['name' => '10.000', 'code' => 'XL10', 'price' => 11000],
                    ['name' => '25.000', 'code' => 'XL25', 'price' => 25500],
                    ['name' => '50.000', 'code' => 'XL50', 'price' => 50000],
                ],
            ],

            // VOUCHER
            [
                'name' => 'Google Play',
                'game_code' => 'googleplay',
                'type' => 'voucher',
                'icon' => 'games/google-play.png',
                'description' => 'Voucher Google Play Gift Card',
                'products' => [
                    ['name' => 'Rp 10.000', 'code' => 'GP10', 'price' => 11000],
                    ['name' => 'Rp 20.000', 'code' => 'GP20', 'price' => 21000],
                    ['name' => 'Rp 50.000', 'code' => 'GP50', 'price' => 52000],
                    ['name' => 'Rp 100.000', 'code' => 'GP100', 'price' => 102000],
                    ['name' => 'Rp 150.000', 'code' => 'GP150', 'price' => 152000],
                ],
            ],
            [
                'name' => 'Steam Wallet',
                'game_code' => 'steam',
                'type' => 'voucher',
                'icon' => 'games/steam.png',
                'description' => 'Steam Wallet Code',
                'products' => [
                    ['name' => '$5 USD', 'code' => 'STEAM5', 'price' => 80000],
                    ['name' => '$10 USD', 'code' => 'STEAM10', 'price' => 155000],
                    ['name' => '$20 USD', 'code' => 'STEAM20', 'price' => 305000],
                    ['name' => '$50 USD', 'code' => 'STEAM50', 'price' => 755000],
                ],
            ],

            // STREAMING
            [
                'name' => 'Netflix',
                'game_code' => 'netflix',
                'type' => 'streaming',
                'icon' => 'games/netflix.png',
                'description' => 'Netflix Gift Card',
                'products' => [
                    ['name' => '1 Bulan Mobile', 'code' => 'NETFLIX1M', 'price' => 54000],
                    ['name' => '1 Bulan Basic', 'code' => 'NETFLIX1B', 'price' => 65000],
                    ['name' => '1 Bulan Standard', 'code' => 'NETFLIX1S', 'price' => 120000],
                    ['name' => '1 Bulan Premium', 'code' => 'NETFLIX1P', 'price' => 186000],
                ],
            ],
            [
                'name' => 'YouTube Premium',
                'game_code' => 'youtube',
                'type' => 'streaming',
                'icon' => 'games/youtube.png',
                'description' => 'YouTube Premium Membership',
                'products' => [
                    ['name' => '1 Bulan Individual', 'code' => 'YT1M', 'price' => 59000],
                    ['name' => '1 Bulan Family', 'code' => 'YT1F', 'price' => 99000],
                ],
            ],
            [
                'name' => 'Spotify',
                'game_code' => 'spotify',
                'type' => 'streaming',
                'icon' => 'games/spotify.png',
                'description' => 'Spotify Premium',
                'products' => [
                    ['name' => '1 Bulan Individual', 'code' => 'SPOT1M', 'price' => 54900],
                    ['name' => '1 Bulan Duo', 'code' => 'SPOT1D', 'price' => 71900],
                    ['name' => '1 Bulan Family', 'code' => 'SPOT1F', 'price' => 83900],
                ],
            ],
        ];

        echo "Seeding games and products...\n\n";

        foreach ($games as $gameData) {
            // Create category (game)
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($gameData['name'])],
                [
                    'name' => $gameData['name'],
                    'game_code' => $gameData['game_code'],
                    'type' => $gameData['type'],
                    'icon' => $gameData['icon'],
                    'description' => $gameData['description'],
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            echo "✓ Created category: {$gameData['name']} (Type: {$gameData['type']})\n";

            // Create products for this game
            foreach ($gameData['products'] as $productData) {
                $basePrice = $productData['price'];

                // Calculate tiered pricing with margins
                $visitorPrice = ceil($basePrice * 1.15 / 100) * 100; // 15% margin
                $resellerPrice = ceil($basePrice * 1.10 / 100) * 100; // 10% margin
                $resellerVipPrice = ceil($basePrice * 1.07 / 100) * 100; // 7% margin
                $resellerVvipPrice = ceil($basePrice * 1.05 / 100) * 100; // 5% margin

                Product::updateOrCreate(
                    [
                        'provider' => 'apigames',
                        'provider_code' => $productData['code'],
                    ],
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'slug' => Str::slug($gameData['name'] . '-' . $productData['name']),
                        'description' => 'Top up ' . $productData['name'] . ' untuk ' . $gameData['name'] . '. Proses cepat dan otomatis!',
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
            }

            echo "  → Added " . count($gameData['products']) . " products\n";
        }

        echo "\n========================================\n";
        echo "Seeding completed!\n";
        echo "Total categories: " . Category::count() . "\n";
        echo "Total products: " . Product::count() . "\n";
        echo "========================================\n\n";

        echo "⚠️  NEXT STEPS:\n";
        echo "1. Create directory: public/storage/games/\n";
        echo "2. Upload game icons to: public/storage/games/\n";
        echo "3. Icon names needed:\n";

        $uniqueIcons = collect($games)->pluck('icon')->unique()->sort();
        foreach ($uniqueIcons as $icon) {
            echo "   - {$icon}\n";
        }

        echo "\n💡 You can find game icons from:\n";
        echo "   - Official game websites\n";
        echo "   - https://www.flaticon.com/\n";
        echo "   - https://www.freepik.com/\n";
        echo "   - Or download from similar marketplaces\n";
    }
}
