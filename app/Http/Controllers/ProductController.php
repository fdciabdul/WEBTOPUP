<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with('category')
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

        return view('product-detail', compact('product', 'relatedProducts', 'price', 'userLevel'));
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
