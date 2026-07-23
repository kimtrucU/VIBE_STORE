<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Request $request, Product $product)
    {
        $userId = auth()->id();
        $exists = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($exists) {
            $exists->delete();
            $message  = "{$product->name} removed from wishlist.";
            $wishlisted = false;
        } else {
            Wishlist::create(['user_id' => $userId, 'product_id' => $product->id]);
            $message  = "{$product->name} added to wishlist!";
            $wishlisted = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'wishlisted' => $wishlisted,
                'message'    => $message,
                'count'      => $count,
            ]);
        }

        return back()->with($wishlisted ? 'success' : 'info', $message);
    }

    public function data()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'items' => $wishlists->map(fn($w) => [
                'id'         => $w->id,
                'product_id' => $w->product_id,
                'name'       => $w->product->name,
                'image'      => $w->product->primary_image,
                'price'      => $w->product->price,
                'slug'       => $w->product->slug,
            ])->values()->toArray(),
            'count' => $wishlists->count(),
        ]);
    }
}
