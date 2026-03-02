<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProperGamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = $this->getGamesData();

        $this->command->info('Processing ' . count($games) . ' game categories...');

        $created = 0;
        $updated = 0;

        foreach ($games as $index => $game) {
            $this->command->info(($index + 1) . '. Processing: ' . $game['name']);

            try {
                // Check if category exists (including soft deleted)
                $existing = Category::withTrashed()->where('slug', $game['slug'])->first();

                // Download image
                $iconPath = $this->downloadImage($game['image_url'], $game['slug']);

                if ($existing) {
                    // Update existing category and restore if soft deleted
                    $existing->name = $game['name'];
                    $existing->icon = $iconPath;
                    $existing->description = $game['description'];
                    $existing->type = $game['type'];
                    $existing->game_code = $game['game_code'];
                    $existing->sort_order = $index + 1;
                    $existing->is_active = true;
                    $existing->deleted_at = null; // Restore if soft deleted
                    $existing->save();

                    $updated++;
                    $this->command->info('   ✓ Updated');
                } else {
                    // Create new category
                    Category::create([
                        'name' => $game['name'],
                        'slug' => $game['slug'],
                        'icon' => $iconPath,
                        'description' => $game['description'],
                        'type' => $game['type'],
                        'game_code' => $game['game_code'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]);

                    $created++;
                    $this->command->info('   ✓ Created');
                }
            } catch (\Exception $e) {
                $this->command->error('   ✗ Failed: ' . $e->getMessage());
            }
        }

        $this->command->info("Done! Created: {$created} | Updated: {$updated} | Total: " . Category::count());
    }

    /**
     * Download image from URL and save to storage
     */
    protected function downloadImage(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $filename = 'categories/' . $slug . '.' . $extension;

                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (\Exception $e) {
            $this->command->warn('   ! Image download failed, using default');
        }

        return null;
    }

    /**
     * Get all games data organized by type
     */
    protected function getGamesData(): array
    {
        return [
            // MOBILE GAMES
            [
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'image_url' => 'https://marspedia.id/storage/assets/image/3aace3d1967740daa1a275ab8116d718.jpg',
                'description' => 'Top up diamonds Mobile Legends tercepat dan termurah',
                'type' => 'mobile',
                'game_code' => 'mobilelegend',
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'image_url' => 'https://marspedia.id/storage/assets/image/2930a8f3e5d37fa2264ad2630f95719e.png',
                'description' => 'Beli diamonds Free Fire murah dan cepat',
                'type' => 'mobile',
                'game_code' => 'freefire',
            ],
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'image_url' => 'https://marspedia.id/storage/assets/image/f70aa10c7e9137b622160cf10972b771.jpg',
                'description' => 'Top up UC PUBG Mobile termurah',
                'type' => 'mobile',
                'game_code' => 'pubg',
            ],
            [
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'image_url' => 'https://marspedia.id/storage/assets/image/3a60e0bc4eab0cf14d88ceec5bcf1645.png',
                'description' => 'Top up Genesis Crystal Genshin Impact',
                'type' => 'mobile',
                'game_code' => 'genshin',
            ],
            [
                'name' => 'Honkai Star Rail',
                'slug' => 'honkai-star-rail',
                'image_url' => 'https://marspedia.id/storage/assets/image/cb598559c02355b1cf322638e25e0537.jpg',
                'description' => 'Top up Oneiric Shard Honkai Star Rail',
                'type' => 'mobile',
                'game_code' => 'hok',
            ],
            [
                'name' => 'Call of Duty Mobile',
                'slug' => 'call-of-duty-mobile',
                'image_url' => 'https://marspedia.id/storage/assets/image/2c0058a5cb033b28452a37f5af94603a.jpg',
                'description' => 'Top up CP Call of Duty Mobile',
                'type' => 'mobile',
                'game_code' => 'codm',
            ],
            [
                'name' => 'Sausage Man',
                'slug' => 'sausage-man',
                'image_url' => 'https://marspedia.id/storage/assets/image/ea8ce112901f4416ee48266c472ea388.png',
                'description' => 'Top up Candy Sausage Man',
                'type' => 'mobile',
                'game_code' => 'sausageman',
            ],
            [
                'name' => 'Arena of Valor',
                'slug' => 'arena-of-valor',
                'image_url' => 'https://marspedia.id/storage/assets/image/70a03f2f74e9f0842144a0675a3c22ec.jpg',
                'description' => 'Top up Voucher Arena of Valor',
                'type' => 'mobile',
                'game_code' => 'aov',
            ],
            [
                'name' => 'Wild Rift',
                'slug' => 'wild-rift',
                'image_url' => 'https://marspedia.id/storage/assets/image/cfa844c8995b2952812ec85ec7ce35f3.png',
                'description' => 'Top up Wild Core League of Legends Wild Rift',
                'type' => 'mobile',
                'game_code' => 'wildrift',
            ],
            [
                'name' => 'Clash of Clans',
                'slug' => 'clash-of-clans',
                'image_url' => 'https://marspedia.id/storage/assets/image/a22394c0e0ab2f3928f7c4da6001e4c2.jpg',
                'description' => 'Top up Gems Clash of Clans',
                'type' => 'mobile',
                'game_code' => 'coc',
            ],
            [
                'name' => 'Roblox',
                'slug' => 'roblox',
                'image_url' => 'https://marspedia.id/storage/assets/image/7a0bf93423da922aefda348de93c0191.jpg',
                'description' => 'Beli Robux Roblox murah',
                'type' => 'mobile',
                'game_code' => 'roblox',
            ],
            [
                'name' => 'Stumble Guys',
                'slug' => 'stumble-guys',
                'image_url' => 'https://marspedia.id/storage/assets/image/a292fc17e384183a6022bbe7c92cdd29.png',
                'description' => 'Top up Gems Stumble Guys',
                'type' => 'mobile',
                'game_code' => 'stumble',
            ],
            [
                'name' => 'Tower of Fantasy',
                'slug' => 'tower-of-fantasy',
                'image_url' => 'https://marspedia.id/storage/assets/image/756e79294b939d02d546fd7ed31438c2.png',
                'description' => 'Top up Tanium Tower of Fantasy',
                'type' => 'mobile',
                'game_code' => 'tof',
            ],
            [
                'name' => 'Higgs Domino',
                'slug' => 'higgs-domino',
                'image_url' => 'https://marspedia.id/storage/assets/image/52d9f89e6c1cf4c16e6ab80f6e68a92a.png',
                'description' => 'Top up Chip Higgs Domino Island',
                'type' => 'mobile',
                'game_code' => 'higgs',
            ],

            // PC GAMES
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'image_url' => 'https://marspedia.id/storage/assets/image/117d226a4c445671ed16bf68a7d22f87.jpg',
                'description' => 'Top up VP Valorant Point',
                'type' => 'pc',
                'game_code' => 'valorant',
            ],
            [
                'name' => 'Steam Wallet',
                'slug' => 'steam-wallet',
                'image_url' => 'https://marspedia.id/storage/assets/image/90ebc66506a3561f988278bef6d3bb1a.jpg',
                'description' => 'Isi saldo Steam Wallet',
                'type' => 'pc',
                'game_code' => 'steam',
            ],
            [
                'name' => 'Point Blank',
                'slug' => 'point-blank',
                'image_url' => 'https://marspedia.id/storage/assets/image/64a584cd47c4a9c4d3681f225d93bacc.jpg',
                'description' => 'Top up Cash Point Blank',
                'type' => 'pc',
                'game_code' => 'pb',
            ],

            // E-WALLET
            [
                'name' => 'Dana',
                'slug' => 'dana',
                'image_url' => 'https://marspedia.id/storage/assets/image/c8d3cee5ca5d1567c61b566465b814b3.png',
                'description' => 'Top up saldo Dana',
                'type' => 'ewallet',
                'game_code' => null,
            ],
            [
                'name' => 'GoPay',
                'slug' => 'gopay',
                'image_url' => 'https://marspedia.id/storage/assets/image/b55d50c12602d3d329dceb9c7f0c3c6c.png',
                'description' => 'Top up saldo GoPay',
                'type' => 'ewallet',
                'game_code' => null,
            ],
            [
                'name' => 'OVO',
                'slug' => 'ovo',
                'image_url' => 'https://marspedia.id/storage/assets/image/0df2c888795bdcb32cf03fb207a03a07.png',
                'description' => 'Top up saldo OVO',
                'type' => 'ewallet',
                'game_code' => null,
            ],
            [
                'name' => 'ShopeePay',
                'slug' => 'shopeepay',
                'image_url' => 'https://marspedia.id/storage/assets/image/719db8770e75ed694f63dc2735e64443.png',
                'description' => 'Top up saldo ShopeePay',
                'type' => 'ewallet',
                'game_code' => null,
            ],

            // PULSA
            [
                'name' => 'Telkomsel',
                'slug' => 'telkomsel',
                'image_url' => 'https://marspedia.id/storage/assets/image/4057f9d93f07a6bb4f1e83d5a52cce59.png',
                'description' => 'Pulsa dan paket data Telkomsel',
                'type' => 'pulsa',
                'game_code' => null,
            ],
            [
                'name' => 'Indosat',
                'slug' => 'indosat',
                'image_url' => 'https://marspedia.id/storage/assets/image/77e81a59db98625bfdd30abe368b75a0.png',
                'description' => 'Pulsa dan paket data Indosat',
                'type' => 'pulsa',
                'game_code' => null,
            ],
            [
                'name' => 'XL Axiata',
                'slug' => 'xl-axiata',
                'image_url' => 'https://marspedia.id/storage/assets/image/e447f28453ffc004eff15b5839d00bbe.png',
                'description' => 'Pulsa dan paket data XL',
                'type' => 'pulsa',
                'game_code' => null,
            ],
            [
                'name' => 'Tri',
                'slug' => 'tri',
                'image_url' => 'https://marspedia.id/storage/assets/image/b3d6513c750e3bf419d66e8e2ac9ec51.png',
                'description' => 'Pulsa dan paket data Tri',
                'type' => 'pulsa',
                'game_code' => null,
            ],
            [
                'name' => 'Axis',
                'slug' => 'axis',
                'image_url' => 'https://marspedia.id/storage/assets/image/358590f9546e99e6aeac0210dbba5f85.png',
                'description' => 'Pulsa dan paket data Axis',
                'type' => 'pulsa',
                'game_code' => null,
            ],
            [
                'name' => 'Smartfren',
                'slug' => 'smartfren',
                'image_url' => 'https://marspedia.id/storage/assets/image/f8d368014d839d528ceaf137471df489.png',
                'description' => 'Pulsa dan paket data Smartfren',
                'type' => 'pulsa',
                'game_code' => null,
            ],

            // VOUCHER
            [
                'name' => 'Google Play',
                'slug' => 'google-play',
                'image_url' => 'https://marspedia.id/storage/assets/image/22236f271016eaa77d643d463fc733f8.png',
                'description' => 'Voucher Google Play Indonesia',
                'type' => 'voucher',
                'game_code' => null,
            ],
            [
                'name' => 'Garena',
                'slug' => 'garena',
                'image_url' => 'https://marspedia.id/storage/assets/image/d1481b2c2bea684615430ef46cfbd1a8.jpg',
                'description' => 'Voucher Garena Shell',
                'type' => 'voucher',
                'game_code' => null,
            ],

            // STREAMING
            [
                'name' => 'Netflix',
                'slug' => 'netflix',
                'image_url' => 'https://marspedia.id/storage/assets/image/4066ce543c0dfc3c0ad093ec34cb133c.png',
                'description' => 'Voucher Netflix Premium',
                'type' => 'streaming',
                'game_code' => null,
            ],
        ];
    }
}
