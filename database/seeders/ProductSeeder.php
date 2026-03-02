<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find categories, or skip if they don't exist.
        $mlCategory = Category::where('slug', 'mobile-legends')->first();
        $ffCategory = Category::where('slug', 'free-fire')->first();

        $products = [];

        if ($mlCategory) {
            $products = array_merge($products, [
                [
                    'category_id' => $mlCategory->id,
                    'name' => '86 Diamonds',
                    'provider_code' => 'ML86',
                    'provider' => 'apigames',
                    'price_visitor' => 25000,
                    'price_reseller' => 24000,
                    'price_reseller_vip' => 23500,
                    'price_reseller_vvip' => 23000,
                    'provider_price' => 22000,
                    'status' => 'active',
                ],
                [
                    'category_id' => $mlCategory->id,
                    'name' => '172 Diamonds',
                    'provider_code' => 'ML172',
                    'provider' => 'apigames',
                    'price_visitor' => 50000,
                    'price_reseller' => 49000,
                    'price_reseller_vip' => 48500,
                    'price_reseller_vvip' => 48000,
                    'provider_price' => 47000,
                    'status' => 'active',
                ],
            ]);
        }

        if ($ffCategory) {
            $products = array_merge($products, [
                [
                    'category_id' => $ffCategory->id,
                    'name' => '70 Diamonds',
                    'provider_code' => 'FF70',
                    'provider' => 'apigames',
                    'price_visitor' => 10000,
                    'price_reseller' => 9500,
                    'price_reseller_vip' => 9200,
                    'price_reseller_vvip' => 9000,
                    'provider_price' => 8500,
                    'status' => 'active',
                ],
                [
                    'category_id' => $ffCategory->id,
                    'name' => '140 Diamonds',
                    'provider_code' => 'FF140',
                    'provider' => 'apigames',
                    'price_visitor' => 20000,
                    'price_reseller' => 19500,
                    'price_reseller_vip' => 19200,
                    'price_reseller_vvip' => 19000,
                    'provider_price' => 18500,
                    'status' => 'active',
                ],
            ]);
        }

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['provider_code' => $product['provider_code'], 'provider' => 'apigames'],
                array_merge($product, [
                    'slug' => Str::slug($product['name'] . '-' . $product['provider_code']),
                    'description' => 'Beli ' . $product['name'] . ' untuk ' . ($product['category_id'] === $mlCategory->id ? 'Mobile Legends' : 'Free Fire') . '.',
                    'is_unlimited_stock' => true,
                    'stock' => 999,
                ])
            );
        }
    }
}
