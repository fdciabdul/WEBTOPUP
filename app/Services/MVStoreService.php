<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MVStoreService
{
    protected string $baseUrl = 'https://app.mvstore.id/api';
    protected string $webUrl = 'https://mvstore.id';
    protected string $cdnUrl = 'https://s3.mvstore.id';
    protected string $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36';

    protected ?string $secureToken = null;
    protected ?string $iv = null;
    protected ?string $buildId = null;

    /**
     * Get current build ID from MVStore website
     */
    protected function getBuildId(): ?string
    {
        if ($this->buildId) {
            return $this->buildId;
        }

        // Cache build ID for 30 minutes
        $this->buildId = Cache::remember('mvstore_build_id', 1800, function () {
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => $this->userAgent,
                        'Accept' => 'text/html,application/xhtml+xml',
                    ])
                    ->get($this->webUrl);

                if (!$response->successful()) {
                    Log::error('MVStore build ID fetch failed', ['status' => $response->status()]);
                    return 'build-v056'; // Fallback
                }

                $html = $response->body();

                // Extract buildId from __NEXT_DATA__ or script tags
                if (preg_match('/"buildId"\s*:\s*"([^"]+)"/', $html, $match)) {
                    Log::info('MVStore build ID fetched', ['buildId' => $match[1]]);
                    return $match[1];
                }

                // Alternative pattern
                if (preg_match('/buildId["\s:]+([^"\/\s,]+)/', $html, $match)) {
                    Log::info('MVStore build ID fetched (alt)', ['buildId' => $match[1]]);
                    return $match[1];
                }

                Log::warning('MVStore build ID not found, using fallback');
                return 'build-v056'; // Fallback
            } catch (\Exception $e) {
                Log::error('MVStore getBuildId error', ['error' => $e->getMessage()]);
                return 'build-v056'; // Fallback
            }
        });

        return $this->buildId;
    }

    /**
     * Get security tokens from MVStore website
     */
    protected function refreshTokens(): void
    {
        try {
            // Cache tokens for 5 minutes
            $tokens = Cache::remember('mvstore_tokens', 300, function () {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => $this->userAgent,
                        'Accept' => 'text/html,application/xhtml+xml',
                    ])
                    ->get("{$this->webUrl}/check-region-ml");

                if (!$response->successful()) {
                    Log::error('MVStore token fetch failed', ['status' => $response->status()]);
                    return null;
                }

                $html = $response->body();

                // Extract tokens from __NEXT_DATA__
                preg_match('/"encryptedDataIgn":"([^"]+)"/', $html, $tokenMatch);
                preg_match('/"finalIcode":"([^"]+)"/', $html, $ivMatch);

                if (empty($tokenMatch[1]) || empty($ivMatch[1])) {
                    Log::error('MVStore tokens not found in HTML');
                    return null;
                }

                return [
                    'token' => $tokenMatch[1],
                    'iv' => $ivMatch[1],
                ];
            });

            if ($tokens) {
                $this->secureToken = $tokens['token'];
                $this->iv = $tokens['iv'];
            }
        } catch (\Exception $e) {
            Log::error('MVStore refreshTokens error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get default headers
     */
    protected function getHeaders(): array
    {
        return [
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.9,id-ID;q=0.8,id;q=0.7',
            'Origin' => $this->webUrl,
            'Referer' => "{$this->webUrl}/",
            'User-Agent' => $this->userAgent,
        ];
    }

    /**
     * Get all products
     */
    public function getProducts(): array
    {
        $buildId = $this->getBuildId();

        return Cache::remember('mvstore_products', 600, function () use ($buildId) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders(array_merge($this->getHeaders(), [
                        'x-nextjs-data' => '1',
                    ]))
                    ->get("{$this->webUrl}/_next/data/{$buildId}/all/product.json");

                if ($response->successful()) {
                    $data = $response->json();
                    $products = $data['pageProps']['dataProps']['data'] ?? [];

                    // Filter active products only
                    return array_filter($products, fn($p) => ($p['product_status'] ?? 0) == 1);
                }

                Log::error('MVStore getProducts failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                    'buildId' => $buildId
                ]);

                // Clear build ID cache if 404 (build ID might be outdated)
                if ($response->status() === 404) {
                    Cache::forget('mvstore_build_id');
                    $this->buildId = null;
                }

                return [];
            } catch (\Exception $e) {
                Log::error('MVStore getProducts error', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get product detail by slug
     */
    public function getProductBySlug(string $slug): ?array
    {
        $buildId = $this->getBuildId();

        return Cache::remember("mvstore_product_{$slug}", 300, function () use ($slug, $buildId) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders(array_merge($this->getHeaders(), [
                        'x-nextjs-data' => '1',
                    ]))
                    ->get("{$this->webUrl}/_next/data/{$buildId}/i/{$slug}.json", [
                        'slug' => $slug
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['pageProps']['dataProps'] ?? null;
                }

                Log::error('MVStore getProductBySlug failed', [
                    'slug' => $slug,
                    'status' => $response->status(),
                    'buildId' => $buildId
                ]);

                // Clear build ID cache if 404 (build ID might be outdated)
                if ($response->status() === 404) {
                    Cache::forget('mvstore_build_id');
                    $this->buildId = null;
                }

                return null;
            } catch (\Exception $e) {
                Log::error('MVStore getProductBySlug error', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Check game account (validate user ID)
     */
    public function checkAccount(string $userId, string $serverId, string $gameCode = 'MLGP'): array
    {
        try {
            // Refresh tokens if needed
            if (!$this->secureToken || !$this->iv) {
                $this->refreshTokens();
            }

            if (!$this->secureToken || !$this->iv) {
                return [
                    'success' => false,
                    'error' => 'Gagal mendapatkan token keamanan'
                ];
            }

            $response = Http::timeout(15)
                ->withHeaders(array_merge($this->getHeaders(), [
                    'X-Secure-Token' => $this->secureToken,
                    'X-IV' => $this->iv,
                ]))
                ->asMultipart()
                ->post("{$this->baseUrl}/ign", [
                    ['name' => 'id', 'contents' => $userId],
                    ['name' => 'serverId', 'contents' => $serverId],
                    ['name' => 'gameCode', 'contents' => $gameCode],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('MVStore checkAccount response', ['response' => $data]);

                if (!empty($data['nickName'])) {
                    // Parse nickname - format: "nickname|RegionXX"
                    $parts = explode('|Region', $data['nickName']);
                    return [
                        'success' => true,
                        'nickname' => $parts[0],
                        'region' => $parts[1] ?? '',
                        'userId' => $data['idGame'] ?? '',
                        'gameCode' => $data['gameCode'] ?? $gameCode,
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Akun tidak ditemukan'
                ];
            }

            Log::error('MVStore checkAccount failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Clear token cache on failure
            Cache::forget('mvstore_tokens');

            return [
                'success' => false,
                'error' => 'Gagal memvalidasi akun'
            ];
        } catch (\Exception $e) {
            Log::error('MVStore checkAccount error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create order
     */
    public function createOrder(array $data): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(array_merge($this->getHeaders(), [
                    'Authorization' => 'Bearer undefined',
                ]))
                ->asMultipart()
                ->post("{$this->baseUrl}/next/prosess", [
                    ['name' => 'sendGameId', 'contents' => $data['game_id']],
                    ['name' => 'sendNicknName', 'contents' => $data['nickname']],
                    ['name' => 'sendItemSku', 'contents' => $data['item_sku']],
                    ['name' => 'sendItemPrice', 'contents' => $data['item_price']],
                    ['name' => 'sendPaymentCode', 'contents' => $data['payment_code']],
                    ['name' => 'sendProductCode', 'contents' => $data['product_code']],
                    ['name' => 'sendAffliate', 'contents' => $data['affiliate'] ?? ''],
                    ['name' => 'sendEmail', 'contents' => $data['email'] ?? ''],
                ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('MVStore createOrder response', ['response' => $result]);

                if (($result['status'] ?? '') === 'success' || !empty($result['data'])) {
                    return [
                        'success' => true,
                        'data' => $result['data'] ?? $result,
                        'invoice' => $result['data']['invoice'] ?? $result['invoice'] ?? null,
                    ];
                }

                return [
                    'success' => false,
                    'error' => $result['message'] ?? 'Gagal membuat pesanan'
                ];
            }

            Log::error('MVStore createOrder failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal membuat pesanan'
            ];
        } catch (\Exception $e) {
            Log::error('MVStore createOrder error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check invoice status
     */
    public function checkInvoice(string $invoiceId): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(array_merge($this->getHeaders(), [
                    'Authorization' => 'Bearer undefined',
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ]))
                ->get("{$this->baseUrl}/next/invoice", [
                    'inv' => $invoiceId
                ]);

            if ($response->successful()) {
                $result = $response->json();

                if (($result['status'] ?? '') === 'success' || ($result['message'] ?? '') === 'success') {
                    return $result['data'] ?? null;
                }
            }

            Log::error('MVStore checkInvoice failed', [
                'invoice' => $invoiceId,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500)
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('MVStore checkInvoice error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get image URL
     */
    public function getImageUrl(string $filename): string
    {
        if (empty($filename)) {
            return '/images/default-game.png';
        }

        // If already a full URL, return as is
        if (str_starts_with($filename, 'http')) {
            return $filename;
        }

        return "{$this->cdnUrl}/{$filename}";
    }

    /**
     * Generate QR code URL from QRIS string
     */
    public function generateQRCodeUrl(string $qrisData, int $size = 300): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size .
               '&data=' . urlencode($qrisData);
    }

    /**
     * Get payment methods from MVStore API
     */
    public function getPaymentMethods(): array
    {
        try {
            // Cache payment methods for 30 minutes
            return Cache::remember('mvstore_payment_methods', 1800, function () {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => $this->userAgent,
                        'Accept' => '*/*',
                        'Origin' => $this->webUrl,
                        'Referer' => $this->webUrl . '/',
                        'Authorization' => 'Bearer undefined',
                    ])
                    ->get("{$this->baseUrl}/next/getpaymentglobal");

                if (!$response->successful()) {
                    Log::error('MVStore payment fetch failed', ['status' => $response->status()]);
                    return $this->getDefaultPaymentMethods();
                }

                $data = $response->json();

                if (!isset($data['data']['dataPayment']) || empty($data['data']['dataPayment'])) {
                    return $this->getDefaultPaymentMethods();
                }

                // Group payments by category
                $grouped = [];
                foreach ($data['data']['dataPayment'] as $payment) {
                    $category = $payment['payment_cat_name'] ?? 'Lainnya';

                    if (!isset($grouped[$category])) {
                        $grouped[$category] = [
                            'name' => $category,
                            'channels' => [],
                        ];
                    }

                    // Calculate fee
                    $fee = (float) ($payment['payment_fix'] ?? 0);
                    $feeType = 'fixed';

                    if (!empty($payment['payment_percent']) && $payment['payment_percent'] != '1') {
                        $fee = (float) $payment['payment_percent'];
                        $feeType = 'percentage';
                    }

                    $grouped[$category]['channels'][] = [
                        'code' => $payment['payment_code'] ?? '',
                        'name' => $payment['payment_name'] ?? '',
                        'image' => $this->getPaymentImageUrl($payment['payment_image'] ?? ''),
                        'fee' => $fee,
                        'fee_type' => $feeType,
                        'min' => (int) ($payment['payment_min'] ?? 1),
                        'max' => (int) ($payment['payment_max'] ?? 100000000),
                    ];
                }

                return array_values($grouped);
            });
        } catch (\Exception $e) {
            Log::error('MVStore payment fetch error', ['error' => $e->getMessage()]);
            return $this->getDefaultPaymentMethods();
        }
    }

    /**
     * Get payment image URL
     */
    protected function getPaymentImageUrl(string $image): string
    {
        if (empty($image)) {
            return asset('images/default-payment.png');
        }

        // If already a full URL, return as is
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // If it's a file ID/name, construct MVStore CDN URL
        return "{$this->cdnUrl}/{$image}";
    }

    /**
     * Fallback payment methods if API fails
     */
    protected function getDefaultPaymentMethods(): array
    {
        return [
            [
                'name' => 'QRIS (E-Wallet & M-Banking)',
                'channels' => [
                    [
                        'code' => 'QRISSPDUIT',
                        'name' => 'QRIS',
                        'image' => asset('images/default-payment.png'),
                        'fee' => 0.7,
                        'fee_type' => 'percentage',
                        'min' => 1,
                        'max' => 5000000,
                    ],
                ],
            ],
            [
                'name' => 'Virtual Account',
                'channels' => [
                    [
                        'code' => 'BCADUIT',
                        'name' => 'BCA Virtual Account',
                        'image' => asset('images/default-payment.png'),
                        'fee' => 4000,
                        'fee_type' => 'fixed',
                        'min' => 10000,
                        'max' => 50000000,
                    ],
                    [
                        'code' => 'ABORADUIT',
                        'name' => 'BRI Virtual Account',
                        'image' => asset('images/default-payment.png'),
                        'fee' => 4000,
                        'fee_type' => 'fixed',
                        'min' => 10000,
                        'max' => 50000000,
                    ],
                ],
            ],
        ];
    }

    /**
     * Format products for homepage display
     */
    public function getFormattedProducts(): array
    {
        $products = $this->getProducts();
        $formatted = [];

        foreach ($products as $product) {
            $formatted[] = [
                'id' => $product['id'] ?? 0,
                'code' => $product['product_code'] ?? '',
                'name' => $product['product_name'] ?? '',
                'slug' => $product['product_slug'] ?? '',
                'image' => $this->getImageUrl($product['product_image'] ?? ''),
                'banner' => $this->getImageUrl($product['product_banner'] ?? ''),
                'discount' => $product['product_disc'] ?? 0,
                'is_popular' => ($product['product_populer'] ?? 0) == 1,
                'category' => $product['categories']['category_name'] ?? 'Lainnya',
                'category_id' => $product['categories_id'] ?? 0,
            ];
        }

        return $formatted;
    }

    /**
     * Clear all cache
     */
    public function clearCache(): void
    {
        Cache::forget('mvstore_products');
        Cache::forget('mvstore_tokens');
        Cache::forget('mvstore_build_id');
        Cache::forget('mvstore_payment_methods');
        $this->buildId = null;

        // Clear product caches
        $products = $this->getProducts();
        foreach ($products as $product) {
            $slug = $product['product_slug'] ?? '';
            if ($slug) {
                Cache::forget("mvstore_product_{$slug}");
            }
        }
    }
}
