<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'name' => 'Budi Santoso',
                'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso',
                'comment' => 'Pelayanan sangat cepat dan harga terjangkau! Recommended banget untuk top up game.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Aminah',
                'avatar' => 'https://ui-avatars.com/api/?name=Siti+Aminah',
                'comment' => 'Sudah langganan di sini lebih dari 6 bulan. Selalu dapat harga terbaik untuk reseller.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'name' => 'Rizki Pratama',
                'avatar' => 'https://ui-avatars.com/api/?name=Rizki+Pratama',
                'comment' => 'Top up diamonds ML selalu instant! Customer service responsif dan helpful.',
                'rating' => 5,
                'sort_order' => 3,
            ],
            [
                'name' => 'Dewi Lestari',
                'avatar' => 'https://ui-avatars.com/api/?name=Dewi+Lestari',
                'comment' => 'Proses transaksi mudah dan aman. Metode pembayaran lengkap!',
                'rating' => 5,
                'sort_order' => 4,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'avatar' => 'https://ui-avatars.com/api/?name=Ahmad+Fauzi',
                'comment' => 'Website yang bagus dan mudah digunakan. Harga kompetitif dan proses cepat.',
                'rating' => 5,
                'sort_order' => 5,
            ],
            [
                'name' => 'Maya Putri',
                'avatar' => 'https://ui-avatars.com/api/?name=Maya+Putri',
                'comment' => 'Sering top up di sini, ga pernah kecewa! Selalu lancar dan tepat waktu.',
                'rating' => 5,
                'sort_order' => 6,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
