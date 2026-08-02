<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }

        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index()
    {
        return redirect()->route('checkout.index');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size'       => 'required|string|max:20',
            'quantity'   => 'sometimes|integer|min:1|max:10',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $cart     = $this->getOrCreateCart();
        $quantity = $request->get('quantity', 1);

        $item = $cart->items()->where('product_id', $product->id)->where('size', $request->size)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'size'       => $request->size,
                'quantity'   => $quantity,
            ]);
        }

        $cart->load('items.product');

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$product->name} (Size: {$request->size}) added to cart!",
                'cart_count' => $cart->item_count,
                'cart_data'  => $this->formatCartData($cart),
            ]);
        }

        return back()->with('success', "{$product->name} added to cart!");
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->getOrCreateCart();
        $item = $cart->items()->findOrFail($request->item_id);
        $item->update(['quantity' => $request->quantity]);
        $cart->load('items.product');

        if ($request->ajax()) {
            return response()->json([
                'success'   => true,
                'cart_count'=> $cart->item_count,
                'cart_data' => $this->formatCartData($cart),
            ]);
        }

        return back();
    }

    public function remove(Request $request)
    {
        $request->validate(['item_id' => 'required|exists:cart_items,id']);

        $cart = $this->getOrCreateCart();
        $item = $cart->items()->findOrFail($request->item_id);
        $name = $item->product->name ?? 'Item';
        $item->delete();
        $cart->load('items.product');

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$name} removed from cart.",
                'cart_count' => $cart->item_count,
                'cart_data'  => $this->formatCartData($cart),
            ]);
        }

        return back()->with('info', "{$name} removed from cart.");
    }

    public function data()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product');
        return response()->json($this->formatCartData($cart));
    }

    private function formatCartData(Cart $cart): array
    {
        return [
            'items'       => $cart->items->map(fn($item) => [
                'id'       => $item->id,
                'name'     => $item->product->name ?? 'Unknown',
                'image'    => $item->product->primary_image ?? '/images/placeholder.webp',
                'size'     => $item->size,
                'price'    => $item->product->price ?? 0,
                'quantity' => $item->quantity,
                'total'    => ($item->product->price ?? 0) * $item->quantity,
            ])->values()->toArray(),
            'subtotal'     => $cart->subtotal,
            'shipping_fee' => $cart->shipping_fee,
            'grand_total'  => $cart->grand_total,
            'item_count'   => $cart->item_count,
        ];
    }
}
