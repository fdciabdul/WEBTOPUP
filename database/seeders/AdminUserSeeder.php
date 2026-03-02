<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'level' => 'reseller_vvip',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Demo User',
            'email' => 'demo@webtopup.com',
            'phone' => '081234567891',
            'password' => Hash::make('demo123'),
            'role' => 'member',
            'level' => 'visitor',
            'balance' => 50000,
            'is_active' => true,
        ]);
    }
}
