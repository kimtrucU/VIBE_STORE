<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filter = $request->get('filter', 'all');
        if ($filter === 'low') {
            $query->where('stock', '<=', 10)->where('stock', '>', 0);
        } elseif ($filter === 'out') {
            $query->where('stock', 0);
        }

        $products = $query->orderBy('stock', 'asc')->paginate(20)->withQueryString();

        return view('admin.inventory.index', compact('products', 'filter'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $oldStock = $product->stock;
        $product->update(['stock' => $request->stock]);

        ActivityLog::log(
            'inventory.updated',
            "Stock for '{$product->name}' changed from {$oldStock} to {$request->stock}",
            $product
        );

        return back()->with('success', "Tồn kho '{$product->name}' đã cập nhật: {$request->stock}");
    }
}
