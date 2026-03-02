<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Jobs\SyncProductsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])
            ->withCount('transactions')
            ->withSum(['transactions' => function ($q) {
                $q->where('status', 'completed');
            }], 'total_amount')
            ->withSum(['transactions' => function ($q) {
                $q->where('status', 'completed');
            }], 'quantity');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('provider_code', 'like', '%' . $search . '%')
                  ->orWhere('provider', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Time filter
        $timeFilter = $request->get('time', 'all');
        if ($timeFilter !== 'all') {
            $dateFrom = match($timeFilter) {
                'today' => now()->startOfDay(),
                'weekly' => now()->startOfWeek(),
                'monthly' => now()->startOfMonth(),
                default => null,
            };
            if ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['name', 'created_at', 'status', 'price_visitor', 'transactions_count', 'transactions_sum_total_amount'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories', 'sortBy', 'sortDir', 'timeFilter'));
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Status produk berhasil diubah');
    }

    public function create()
    {
        $categories = Category::active()->sorted()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'variant_mode' => 'required|in:simple,nested',
            'variants_data' => 'required|json',
            'input_fields' => 'nullable|json',
            'notes' => 'nullable|json',
            'icon' => 'nullable|image|max:2048',
            'icon_url' => 'nullable|url',
            'is_best_seller' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Generate slug if empty
            $slug = $validated['slug'] ?? Str::slug($validated['name']);

            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Handle icon upload
            $iconPath = null;
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('products', 'public');
            } elseif (!empty($validated['icon_url'])) {
                $iconPath = $validated['icon_url'];
            }

            // Parse variants data
            $variantsData = json_decode($validated['variants_data'], true);
            $inputFields = json_decode($validated['input_fields'] ?? '[]', true);
            $notes = json_decode($validated['notes'] ?? '[]', true);

            // Calculate default prices from first variant
            $defaultPrices = $this->getDefaultPricesFromVariants($variantsData, $validated['variant_mode']);

            // Create the product
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'icon' => $iconPath,
                'variant_mode' => $validated['variant_mode'],
                'provider' => $validated['provider'],
                'price_visitor' => $defaultPrices['visitor'],
                'price_reseller' => $defaultPrices['reseller'],
                'price_reseller_vip' => $defaultPrices['vip'],
                'price_reseller_vvip' => $defaultPrices['vvip'],
                'is_unlimited_stock' => true,
                'status' => $request->has('is_active') ? 'active' : 'inactive',
                'is_featured' => $request->boolean('is_featured'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                'input_fields' => $inputFields,
                'notes' => array_filter(array_column($notes, 'text')),
            ]);

            // Create variants
            $this->createVariants($product, $variantsData, $validated['variant_mode']);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dibuat dengan ' . $product->variants()->count() . ' varian');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'transactions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->sorted()->get();
        $product->load('variants');

        // Prepare variants data for Vue
        $variantsForVue = $this->prepareVariantsForEdit($product);

        return view('admin.products.edit', compact('product', 'categories', 'variantsForVue'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'variant_mode' => 'required|in:simple,nested',
            'variants_data' => 'required|json',
            'input_fields' => 'nullable|json',
            'notes' => 'nullable|json',
            'icon' => 'nullable|image|max:2048',
            'icon_url' => 'nullable|url',
            'is_best_seller' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Generate slug if empty
            $slug = $validated['slug'] ?? Str::slug($validated['name']);

            // Ensure unique slug (excluding current product)
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Handle icon upload
            $iconPath = $product->icon;
            if ($request->hasFile('icon')) {
                // Delete old icon if it's a file path
                if ($product->icon && !str_starts_with($product->icon, 'http') && Storage::disk('public')->exists($product->icon)) {
                    Storage::disk('public')->delete($product->icon);
                }
                $iconPath = $request->file('icon')->store('products', 'public');
            } elseif (!empty($validated['icon_url'])) {
                $iconPath = $validated['icon_url'];
            }

            // Parse variants data
            $variantsData = json_decode($validated['variants_data'], true);
            $inputFields = json_decode($validated['input_fields'] ?? '[]', true);
            $notes = json_decode($validated['notes'] ?? '[]', true);

            // Calculate default prices from first variant
            $defaultPrices = $this->getDefaultPricesFromVariants($variantsData, $validated['variant_mode']);

            // Update the product
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'icon' => $iconPath,
                'variant_mode' => $validated['variant_mode'],
                'provider' => $validated['provider'],
                'price_visitor' => $defaultPrices['visitor'],
                'price_reseller' => $defaultPrices['reseller'],
                'price_reseller_vip' => $defaultPrices['vip'],
                'price_reseller_vvip' => $defaultPrices['vvip'],
                'status' => $request->has('is_active') ? 'active' : 'inactive',
                'is_featured' => $request->boolean('is_featured'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                'input_fields' => $inputFields,
                'notes' => array_filter(array_column($notes, 'text')),
            ]);

            // Delete existing variants and create new ones
            $product->variants()->delete();
            $this->createVariants($product, $variantsData, $validated['variant_mode']);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        if ($product->transactions()->count() > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Tidak bisa menghapus produk yang memiliki riwayat transaksi');
        }

        DB::beginTransaction();

        try {
            // Delete icon if it's a file
            if ($product->icon && !str_starts_with($product->icon, 'http') && Storage::disk('public')->exists($product->icon)) {
                Storage::disk('public')->delete($product->icon);
            }

            // Delete variants first
            $product->variants()->delete();

            // Delete the product
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function sync()
    {
        SyncProductsJob::dispatch();

        return redirect()->route('admin.products.index')
            ->with('success', 'Sinkronisasi produk dimulai. Proses ini mungkin memerlukan waktu beberapa menit.');
    }

    /**
     * Create variants from form data
     */
    private function createVariants(Product $product, array $variantsData, string $mode): void
    {
        $sortOrder = 0;

        if ($mode === 'simple') {
            foreach ($variantsData as $variant) {
                if (empty($variant['name'])) continue;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'group_name' => null,
                    'name' => $variant['name'],
                    'provider_code' => $variant['provider_code'] ?? null,
                    'price_visitor' => $this->parsePrice($variant['prices']['visitor'] ?? 0),
                    'price_reseller' => $this->parsePrice($variant['prices']['reseller'] ?? 0),
                    'price_vip' => $this->parsePrice($variant['prices']['vip'] ?? 0),
                    'price_vvip' => $this->parsePrice($variant['prices']['vvip'] ?? 0),
                    'is_unlimited_stock' => true,
                    'stock' => $variant['stock'] ?? 0,
                    'download_link' => $variant['download_link'] ?? null,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]);
            }
        } else {
            // Nested mode (for sosmed products)
            foreach ($variantsData as $category) {
                if (empty($category['name'])) continue;

                foreach ($category['items'] ?? [] as $item) {
                    if (empty($item['name'])) continue;

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'group_name' => $category['name'],
                        'name' => $item['name'],
                        'provider_code' => $item['provider_code'] ?? null,
                        'price_visitor' => $this->parsePrice($item['prices']['visitor'] ?? 0),
                        'price_reseller' => $this->parsePrice($item['prices']['reseller'] ?? 0),
                        'price_vip' => $this->parsePrice($item['prices']['vip'] ?? 0),
                        'price_vvip' => $this->parsePrice($item['prices']['vvip'] ?? 0),
                        'is_unlimited_stock' => true,
                        'stock' => $item['stock'] ?? 0,
                        'download_link' => $item['download_link'] ?? null,
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Get default prices from first variant
     */
    private function getDefaultPricesFromVariants(array $variantsData, string $mode): array
    {
        $defaults = ['visitor' => 0, 'reseller' => 0, 'vip' => 0, 'vvip' => 0];

        if ($mode === 'simple' && !empty($variantsData[0]['prices'])) {
            $prices = $variantsData[0]['prices'];
            $defaults['visitor'] = $this->parsePrice($prices['visitor'] ?? 0);
            $defaults['reseller'] = $this->parsePrice($prices['reseller'] ?? 0);
            $defaults['vip'] = $this->parsePrice($prices['vip'] ?? 0);
            $defaults['vvip'] = $this->parsePrice($prices['vvip'] ?? 0);
        } elseif ($mode === 'nested' && !empty($variantsData[0]['items'][0]['prices'])) {
            $prices = $variantsData[0]['items'][0]['prices'];
            $defaults['visitor'] = $this->parsePrice($prices['visitor'] ?? 0);
            $defaults['reseller'] = $this->parsePrice($prices['reseller'] ?? 0);
            $defaults['vip'] = $this->parsePrice($prices['vip'] ?? 0);
            $defaults['vvip'] = $this->parsePrice($prices['vvip'] ?? 0);
        }

        return $defaults;
    }

    /**
     * Parse price from formatted string
     */
    private function parsePrice($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Remove formatting (dots, commas) and parse
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);
        return (float) ($cleaned ?: 0);
    }

    /**
     * Prepare variants data for Vue edit form
     */
    private function prepareVariantsForEdit(Product $product): array
    {
        $variants = $product->variants;

        if ($product->variant_mode === 'nested') {
            // Group variants by group_name
            $grouped = $variants->groupBy('group_name');
            $result = [];

            foreach ($grouped as $groupName => $items) {
                $category = [
                    'name' => $groupName ?? '',
                    'items' => []
                ];

                foreach ($items as $item) {
                    $category['items'][] = [
                        'name' => $item->name,
                        'stock' => $item->stock,
                        'provider_code' => $item->provider_code,
                        'download_link' => $item->download_link,
                        'prices' => [
                            'visitor' => number_format($item->price_visitor, 0, ',', '.'),
                            'reseller' => number_format($item->price_reseller, 0, ',', '.'),
                            'vip' => number_format($item->price_vip, 0, ',', '.'),
                            'vvip' => number_format($item->price_vvip, 0, ',', '.'),
                        ]
                    ];
                }

                $result[] = $category;
            }

            return $result;
        } else {
            // Simple mode
            return $variants->map(function ($item) {
                return [
                    'name' => $item->name,
                    'stock' => $item->stock,
                    'provider_code' => $item->provider_code,
                    'download_link' => $item->download_link,
                    'prices' => [
                        'visitor' => number_format($item->price_visitor, 0, ',', '.'),
                        'reseller' => number_format($item->price_reseller, 0, ',', '.'),
                        'vip' => number_format($item->price_vip, 0, ',', '.'),
                        'vvip' => number_format($item->price_vvip, 0, ',', '.'),
                    ]
                ];
            })->toArray();
        }
    }
}
