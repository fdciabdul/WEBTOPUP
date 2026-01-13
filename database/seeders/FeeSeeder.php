<?php

namespace Database\Seeders;

use App\Models\Fee;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        Fee::create([
            'name' => 'payment_gateway_fee',
            'type' => 'percentage',
            'amount' => 1,
            'description' => 'Payment gateway fee (1%)',
            'is_active' => true,
        ]);

        Fee::create([
            'name' => 'admin_fee',
            'type' => 'fixed',
            'amount' => 1000,
            'description' => 'Admin fee (Rp 1.000)',
            'is_active' => false,
        ]);
    }
}
