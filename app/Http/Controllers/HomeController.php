<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Faq;
use App\Models\MediaCoverage;
use App\Models\Page;
use App\Services\MVStoreService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected MVStoreService $mvStoreService;

    public function __construct(MVStoreService $mvStoreService)
    {
        $this->mvStoreService = $mvStoreService;
    }

    public function index()
    {
        // Get formatted products from MVStore API
        $categories = $this->mvStoreService->getFormattedProducts();

        // Load dynamic content from database
        $reviews = Review::active()->sorted()->get();
        $faqs = Faq::active()->sorted()->get();
        $medias = MediaCoverage::active()->sorted()->get();
        $pages = Page::active()->get();

        return view('homepage', compact('categories', 'reviews', 'faqs', 'medias', 'pages'));
    }

    public function category($slug)
    {
        // Get product details by slug
        $productData = $this->mvStoreService->getProductBySlug($slug);

        if (empty($productData)) {
            abort(404, 'Game tidak ditemukan');
        }

        // Product info is in data.dataProduct
        $product = $productData['data']['dataProduct'] ?? [];
        // Items are in dataDetail.dataItem
        $items = $productData['dataDetail']['dataItem'] ?? [];

        if (empty($product)) {
            abort(404, 'Game tidak ditemukan');
        }

        // Check if this is a Games category (only Games can have account verification)
        $categoryName = $product['categories']['category_name'] ?? '';
        $isGameCategory = strtolower($categoryName) === 'games';

        // Build category info from product data
        $category = [
            'id' => $product['id'] ?? 0,
            'code' => $product['product_code'] ?? '',
            'name' => $product['product_name'] ?? 'Unknown',
            'slug' => $slug,
            'image' => $this->mvStoreService->getImageUrl($product['product_image'] ?? ''),
            'background' => $this->mvStoreService->getImageUrl($product['product_banner'] ?? ''),
            'description' => $product['product_desc'] ?? '',
            'tutorial' => $product['product_tutor'] ?? '',
            'info_form' => '',
            // Only enable check_id for Games category
            'check_id' => $isGameCategory && !empty($product['product_cekid']),
            'game_code' => $product['product_cekid'] ?? '',
            'input_type' => $product['product_input'] ?? 'text',
            'form_fields' => $this->parseFormFieldsFromInputIgn($productData['dataDetail']['dataInputIgn'] ?? []),
            'category_type' => $categoryName,
        ];

        // Format product variants (items)
        $variants = $this->formatVariants($items);

        // Get payment methods (hardcoded since MVStore doesn't expose payment API)
        $paymentMethods = $this->mvStoreService->getPaymentMethods();

        return view('mv-category', compact('category', 'variants', 'paymentMethods'));
    }

    /**
     * Parse form fields from dataInputIgn
     */
    protected function parseFormFieldsFromInputIgn($inputIgn): array
    {
        $fields = [];

        // Handle the nested input_details structure from MVStore
        $inputDetails = $inputIgn['input_details'] ?? [];

        if (!empty($inputDetails)) {
            foreach ($inputDetails as $index => $input) {
                $placeholder = $input['input_detail_placeholder'] ?? '';
                $type = $input['input_detail_type'] ?? 'text';
                $required = ($input['input_detail_required'] ?? 0) == 1;

                // Determine field name based on placeholder
                $placeholderLower = strtolower($placeholder);
                if (str_contains($placeholderLower, 'id') && !str_contains($placeholderLower, 'server')) {
                    $name = 'user_id';
                    $label = 'User ID';
                } elseif (str_contains($placeholderLower, 'server') || str_contains($placeholderLower, 'zone')) {
                    $name = 'server_id';
                    $label = 'Server/Zone ID';
                } else {
                    $name = 'field_' . $index;
                    $label = $placeholder ?: 'Field ' . ($index + 1);
                }

                $fields[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => $type === 'number' ? 'text' : $type,
                    'placeholder' => $placeholder ?: "Masukkan {$label}",
                    'required' => $required,
                ];
            }
        }

        // Default fields if none found
        if (empty($fields)) {
            $fields[] = [
                'name' => 'user_id',
                'label' => 'User ID',
                'type' => 'text',
                'placeholder' => 'Masukkan User ID',
                'required' => true,
            ];
        }

        return $fields;
    }

    /**
     * Format variants/items for display
     */
    protected function formatVariants(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            // MVStore uses item_cat for category
            $categoryName = $item['item_cat'] ?? 'Produk';

            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = [
                    'name' => $categoryName,
                    'items' => [],
                ];
            }

            $grouped[$categoryName]['items'][] = [
                'id' => $item['id'] ?? 0,
                'sku' => $item['item_sku'] ?? '',
                'name' => $item['item_name'] ?? '',
                'price' => $item['item_price'] ?? 0,
                'original_price' => $item['item_manual_price'] ?? $item['item_price'] ?? 0,
                'image' => $item['item_image'] ?? '',
            ];
        }

        return array_values($grouped);
    }


    /**
     * Search games
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $allProducts = $this->mvStoreService->getFormattedProducts();

        $results = collect($allProducts)->filter(function ($product) use ($query) {
            if (empty($query)) return true;
            return stripos($product['name'] ?? '', $query) !== false;
        })->values();

        $page = $request->get('page', 1);
        $perPage = 24;
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $results->forPage($page, $perPage),
            $results->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('search', compact('products', 'query'));
    }
}
