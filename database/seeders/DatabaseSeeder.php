<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            FeeSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            FaqSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
