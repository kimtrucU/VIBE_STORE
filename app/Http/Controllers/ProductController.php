<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        // Size filter
        if ($size = $request->get('size')) {
            $query->whereJsonContains('sizes', $size);
        }

        // Price filter
        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sorting
        $sort = $request->get('sort', 'featured');
        match($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            default      => $query->orderBy('is_best_seller', 'desc')->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $wishlistIds = $this->getWishlistIds();

        return view('products.index', compact('products', 'categories', 'wishlistIds'));
    }

    public function newArrivals(Request $request)
    {
        $query = Product::with('category')->active()->newArrivals();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products    = $query->paginate(12)->withQueryString();
        $categories  = Category::where('is_active', true)->get();
        $wishlistIds = $this->getWishlistIds();

        return view('products.index', compact('products', 'categories', 'wishlistIds'));
    }

    public function bestSellers(Request $request)
    {
        $query = Product::with('category')->active()->bestSellers();

        $products    = $query->paginate(12)->withQueryString();
        $categories  = Category::where('is_active', true)->get();
        $wishlistIds = $this->getWishlistIds();

        return view('products.index', compact('products', 'categories', 'wishlistIds'));
    }

    public function show(Product $product)
    {
        $product->load('category');
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
        $wishlistIds = $this->getWishlistIds();

        return view('products.show', compact('product', 'relatedProducts', 'wishlistIds'));
    }

    private function getWishlistIds(): array
    {
        if (auth()->check()) {
            return Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
        }
        return [];
    }
}
