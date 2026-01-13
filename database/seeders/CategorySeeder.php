<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'icon' => 'mobile-legends.png', 'sort_order' => 1],
            ['name' => 'Free Fire', 'slug' => 'free-fire', 'icon' => 'free-fire.png', 'sort_order' => 2],
            ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'icon' => 'pubg-mobile.png', 'sort_order' => 3],
            ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'icon' => 'genshin-impact.png', 'sort_order' => 4],
            ['name' => 'Valorant', 'slug' => 'valorant', 'icon' => 'valorant.png', 'sort_order' => 5],
            ['name' => 'Pulsa', 'slug' => 'pulsa', 'icon' => 'pulsa.png', 'sort_order' => 6],
            ['name' => 'Paket Data', 'slug' => 'paket-data', 'icon' => 'paket-data.png', 'sort_order' => 7],
            ['name' => 'E-Wallet', 'slug' => 'e-wallet', 'icon' => 'e-wallet.png', 'sort_order' => 8],
            ['name' => 'Voucher Game', 'slug' => 'voucher-game', 'icon' => 'voucher-game.png', 'sort_order' => 9],
            ['name' => 'PLN', 'slug' => 'pln', 'icon' => 'pln.png', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
