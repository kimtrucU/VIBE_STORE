<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('info', 'Your cart is empty. Add some items first.');
        }

        $cart->load('items.product');
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_email'   => 'required|email|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city'    => 'required|string|max:100',
            'payment_method'   => 'required|in:COD,bank_transfer,momo,sepay',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cart->load('items.product');

        $subtotal    = $cart->subtotal;
        $shippingFee = $cart->shipping_fee;
        $total       = $subtotal + $shippingFee;

        try {
            DB::beginTransaction();

            // Tạo mã nội dung CK cho SePay/bank_transfer
            $transferContent = null;
            if (in_array($validated['payment_method'], ['sepay', 'bank_transfer'])) {
                // Lấy từ form (frontend đã sinh) hoặc tự tạo mới
                $transferContent = $request->input('transfer_content')
                    ?: 'VIBE' . substr(time(), -8);
            }

            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => Order::generateOrderNumber(),
                'status'           => 'pending',
                'payment_method'   => $validated['payment_method'],
                'payment_status'   => 'unpaid',
                'transfer_content' => $transferContent,
                'subtotal'         => $subtotal,
                'shipping_fee'     => $shippingFee,
                'total'            => $total,
                'shipping_name'    => $validated['shipping_name'],
                'shipping_email'   => $validated['shipping_email'],
                'shipping_phone'   => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city'    => $validated['shipping_city'],
                'notes'            => $validated['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->primary_image,
                    'size'          => $item->size,
                    'price'         => $item->product->price,
                    'quantity'      => $item->quantity,
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('order.confirmation', $order)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.')->withInput();
        }
    }

    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::with('items.product')->where('user_id', auth()->id())->first();
        }
        $sessionId = session()->getId();
        return Cart::with('items.product')->where('session_id', $sessionId)->first();
    }
}
