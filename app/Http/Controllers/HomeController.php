<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('category')
            ->active()
            ->latest()
            ->take(4)
            ->get();

        $bestSellers = Product::with('category')
            ->active()
            ->bestSellers()
            ->take(4)
            ->get();

        $categories = Category::where('is_active', true)->get();

        $wishlistIds = $this->getWishlistIds();

        return view('home.index', compact('featuredProducts', 'bestSellers', 'categories', 'wishlistIds'));
    }

    public function about()
    {
        return view('about.index');
    }

    private function getWishlistIds(): array
    {
        if (auth()->check()) {
            return Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
        }
        return session('wishlist_ids', []);
    }
}
