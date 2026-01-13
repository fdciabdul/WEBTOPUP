<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Web Top Up', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Platform top up game dan pulsa terpercaya', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_keywords', 'value' => 'top up, game, pulsa, voucher', 'type' => 'text', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'support@webtopup.com', 'type' => 'email', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '081234567890', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_whatsapp', 'value' => '6281234567890', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Jakarta, Indonesia', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/webtopup', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/webtopup', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/webtopup', 'type' => 'url', 'group' => 'social'],
            ['key' => 'enable_registration', 'value' => '1', 'type' => 'boolean', 'group' => 'general'],
            ['key' => 'enable_guest_order', 'value' => '0', 'type' => 'boolean', 'group' => 'general'],
            ['key' => 'min_balance_topup', 'value' => '10000', 'type' => 'number', 'group' => 'payment'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
