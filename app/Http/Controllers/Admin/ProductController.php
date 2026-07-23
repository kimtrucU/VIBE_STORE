<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'price'          => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description'    => 'required|string',
            'sizes'          => 'required|array',
            'details'        => 'nullable|string',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'stock'          => 'required|integer|min:0',
            'images.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $images[] = '/storage/' . $path;
            }
        }

        Product::create([
            'name'           => $validated['name'],
            'slug'           => Str::slug($validated['name']),
            'category_id'    => $validated['category_id'],
            'price'          => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'description'    => $validated['description'],
            'sizes'          => $validated['sizes'],
            'details'        => $validated['details'] ? explode("\n", $validated['details']) : [],
            'images'         => $images,
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'stock'          => $validated['stock'],
            'is_active'      => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'price'          => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description'    => 'required|string',
            'sizes'          => 'required|array',
            'details'        => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'images.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $images[] = '/storage/' . $path;
            }
        }

        $product->update([
            'name'           => $validated['name'],
            'category_id'    => $validated['category_id'],
            'price'          => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'description'    => $validated['description'],
            'sizes'          => $validated['sizes'],
            'details'        => $validated['details'] ? explode("\n", $validated['details']) : $product->details,
            'images'         => $images,
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_active'      => $request->boolean('is_active'),
            'stock'          => $validated['stock'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }
}
