<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara melakukan pemesanan?',
                'answer' => 'Pilih produk yang diinginkan, masukkan ID/nomor tujuan, pilih metode pembayaran, lalu selesaikan pembayaran.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Berapa lama proses top up?',
                'answer' => 'Proses top up biasanya instant (1-5 menit) setelah pembayaran berhasil.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Apa saja metode pembayaran yang tersedia?',
                'answer' => 'Kami menyediakan berbagai metode pembayaran seperti Bank Transfer, E-Wallet (GoPay, OVO, Dana, ShopeePay), QRIS, dan Convenience Store.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Apakah aman melakukan transaksi di sini?',
                'answer' => 'Ya, sangat aman. Kami menggunakan sistem keamanan berlapis dan payment gateway terpercaya.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Bagaimana jika pesanan gagal?',
                'answer' => 'Jika pesanan gagal, dana akan otomatis dikembalikan ke saldo akun Anda dalam waktu 1x24 jam.',
                'sort_order' => 5,
            ],
            [
                'question' => 'Apakah ada potongan harga untuk reseller?',
                'answer' => 'Ya, kami memiliki sistem level member dengan harga khusus untuk reseller. Hubungi customer service untuk info lebih lanjut.',
                'sort_order' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
