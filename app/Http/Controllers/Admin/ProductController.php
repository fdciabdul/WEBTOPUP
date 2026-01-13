<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Jobs\SyncProductsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('provider_code', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
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
            'provider_code' => 'nullable|string',
            'price_visitor' => 'required|numeric|min:0',
            'price_reseller' => 'required|numeric|min:0',
            'price_reseller_vip' => 'required|numeric|min:0',
            'price_reseller_vvip' => 'required|numeric|min:0',
            'provider_price' => 'nullable|numeric|min:0',
            'is_unlimited_stock' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'max_order' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'transactions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'provider_code' => 'nullable|string',
            'price_visitor' => 'required|numeric|min:0',
            'price_reseller' => 'required|numeric|min:0',
            'price_reseller_vip' => 'required|numeric|min:0',
            'price_reseller_vvip' => 'required|numeric|min:0',
            'provider_price' => 'nullable|numeric|min:0',
            'is_unlimited_stock' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'max_order' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->transactions()->count() > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete product with transaction history');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function sync()
    {
        SyncProductsJob::dispatch();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product sync started. This may take a few minutes.');
    }
}
