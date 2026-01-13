<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Faq;
use App\Models\MediaCoverage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->sorted()
            ->withCount(['products' => function ($query) {
                $query->active()->inStock();
            }])
            ->get();

        $featuredProducts = Product::active()
            ->inStock()
            ->featured()
            ->with('category')
            ->limit(12)
            ->get();

        $reviews = Review::active()
            ->sorted()
            ->limit(6)
            ->get();

        $mediaCoverages = MediaCoverage::active()
            ->sorted()
            ->get();

        $faqs = Faq::active()
            ->sorted()
            ->get();

        return view('homepage', compact(
            'categories',
            'featuredProducts',
            'reviews',
            'mediaCoverages',
            'faqs'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->active()
            ->inStock()
            ->paginate(24);

        return view('category', compact('category', 'products'));
    }
}
