<?php

namespace Database\Seeders;

use App\Models\ProviderSetting;
use Illuminate\Database\Seeder;

class ProviderSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            // Top Up Providers
            [
                'provider_type' => 'topup',
                'provider_name' => 'digiflazz',
                'is_active' => false,
                'is_default' => false,
                'credentials' => [
                    'username' => '',
                    'api_key' => '',
                    'webhook_secret' => '',
                ],
                'config' => [
                    'api_url' => 'https://api.digiflazz.com/v1',
                ],
                'priority' => 10,
            ],
            [
                'provider_type' => 'topup',
                'provider_name' => 'apigames',
                'is_active' => true,
                'is_default' => true,
                'credentials' => [
                    'merchant_id' => config('apigames.merchant_id', ''),
                    'secret_key' => config('apigames.secret_key', ''),
                ],
                'config' => [
                    'api_url' => config('apigames.api_url', 'https://api.apigames.id'),
                    'environment' => config('apigames.environment', 'production'),
                ],
                'priority' => 20,
            ],
            [
                'provider_type' => 'topup',
                'provider_name' => 'manual',
                'is_active' => false,
                'is_default' => false,
                'credentials' => null,
                'config' => null,
                'priority' => 0,
            ],

            // Payment Gateways
            [
                'provider_type' => 'payment',
                'provider_name' => 'ipaymu',
                'is_active' => false,
                'is_default' => false,
                'credentials' => [
                    'api_key' => '',
                    'va_number' => '',
                ],
                'config' => [
                    'api_url' => 'https://my.ipaymu.com/api/v2',
                ],
                'priority' => 10,
            ],
            [
                'provider_type' => 'payment',
                'provider_name' => 'midtrans',
                'is_active' => true,
                'is_default' => true,
                'credentials' => [
                    'server_key' => config('midtrans.server_key', ''),
                    'client_key' => config('midtrans.client_key', ''),
                ],
                'config' => [
                    'environment' => config('midtrans.environment', 'sandbox'),
                    'api_url' => config('midtrans.environment') === 'production'
                        ? 'https://app.midtrans.com/snap/v1'
                        : 'https://app.sandbox.midtrans.com/snap/v1',
                ],
                'priority' => 20,
            ],
        ];

        foreach ($providers as $provider) {
            ProviderSetting::updateOrCreate(
                [
                    'provider_type' => $provider['provider_type'],
                    'provider_name' => $provider['provider_name'],
                ],
                $provider
            );
        }
    }
}
