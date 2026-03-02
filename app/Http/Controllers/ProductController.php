<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'activeVariants'])
            ->active()
            ->firstOrFail();

        if (!$product->isInStock()) {
            abort(404, 'Product out of stock');
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->limit(6)
            ->get();

        $userLevel = auth()->check() ? auth()->user()->level : 'visitor';
        $price = $product->getPriceByLevel($userLevel);
        $variants = $product->activeVariants;
        $whatsappUrl = \App\Models\Setting::get('whatsapp_url', 'https://wa.me/6281234567890');

        return view('product-detail', compact('product', 'relatedProducts', 'price', 'userLevel', 'variants', 'whatsappUrl'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $products = Product::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->active()
            ->inStock()
            ->with('category')
            ->paginate(24);

        return view('search', compact('products', 'query'));
    }
}
