<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Lấy danh sách sản phẩm với filter/sort (gọi từ React Netlify).
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        if ($size = $request->get('size')) {
            $query->whereJsonContains('sizes', $size);
        }

        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        $sort = $request->get('sort', 'featured');
        match($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            'new'        => $query->where('is_new_arrival', true)->latest(),
            'best'       => $query->where('is_best_seller', true)->latest(),
            default      => $query->orderBy('is_best_seller', 'desc')->latest(),
        };

        $products   = $query->paginate(50)->withQueryString();
        $categories = Category::where('is_active', true)->get(['id', 'name', 'slug']);

        return response()->json([
            'products'   => $products->map(fn($p) => $this->formatProduct($p)),
            'categories' => $categories,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * Lấy chi tiết một sản phẩm.
     */
    public function show(string $slug)
    {
        $product = Product::with('category')->active()->where('slug', $slug)->firstOrFail();

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return response()->json([
            'product' => $this->formatProduct($product),
            'related' => $related,
        ]);
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id'                  => $product->id,
            'name'                => $product->name,
            'slug'                => $product->slug,
            'price'               => (float) $product->price,
            'original_price'      => $product->original_price ? (float) $product->original_price : null,
            'discount_percentage' => $product->discount_percentage,
            'description'         => $product->description,
            'images'              => $product->images ?? [],
            'primary_image'       => $product->primary_image,
            'sizes'               => $product->sizes ?? [],
            'colors'              => $product->colors ?? [],
            'details'             => $product->details ?? [],
            'rating'              => (float) $product->rating,
            'reviews_count'       => $product->reviews_count,
            'is_new_arrival'      => $product->is_new_arrival,
            'is_best_seller'      => $product->is_best_seller,
            'stock'               => $product->stock,
            'category'            => $product->category ? [
                'id'   => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
        ];
    }
}
