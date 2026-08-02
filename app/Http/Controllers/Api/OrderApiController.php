<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    private function getUser(Request $request): ?User
    {
        return User::where('firebase_uid', $request->firebase_uid)->first();
    }

    /**
     * Lấy danh sách đơn hàng của user (gọi sau khi đăng nhập).
     */
    public function index(Request $request)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['orders' => []]);
        }

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        return response()->json(['orders' => $orders]);
    }

    /**
     * Tạo đơn hàng mới từ React checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.size'           => 'required|string',
            'items.*.price'          => 'required|numeric',
            'customer_info.name'     => 'required|string',
            'customer_info.email'    => 'required|email',
            'customer_info.phone'    => 'required|string',
            'customer_info.address'  => 'required|string',
            'customer_info.city'     => 'required|string',
            'payment_method'         => 'required|in:COD,MOMO,VNPAY',
            'total_amount'           => 'required|numeric',
        ]);

        $user = $this->getUser($request);
        $info = $request->customer_info;

        try {
            DB::beginTransaction();

            $subtotal    = collect($request->items)->sum(fn($i) => $i['price'] * $i['quantity']);
            $shippingFee = $subtotal >= 1000000 ? 0 : 30000;

            $order = Order::create([
                'user_id'          => $user?->id,
                'order_number'     => Order::generateOrderNumber(),
                'status'           => 'pending',
                'payment_method'   => strtolower($request->payment_method),
                'subtotal'         => $subtotal,
                'shipping_fee'     => $shippingFee,
                'total'            => $request->total_amount,
                'shipping_name'    => $info['name'],
                'shipping_email'   => $info['email'],
                'shipping_phone'   => $info['phone'],
                'shipping_address' => $info['address'],
                'shipping_city'    => $info['city'],
                'notes'            => $request->get('notes'),
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'] ?? '',
                    'product_image' => $item['product_image'] ?? '',
                    'size'          => $item['size'],
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order'        => $this->formatOrder($order->load('items')),
                'order_number' => $order->order_number,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order.'], 500);
        }
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'status'         => $order->status,
            'payment_method' => strtoupper($order->payment_method),
            'subtotal'       => (float) $order->subtotal,
            'shipping_fee'   => (float) $order->shipping_fee,
            'total'          => (float) $order->total,
            'date'           => $order->created_at->format('d/m/Y H:i'),
            'customer_info'  => [
                'name'    => $order->shipping_name,
                'email'   => $order->shipping_email,
                'phone'   => $order->shipping_phone,
                'address' => $order->shipping_address,
                'city'    => $order->shipping_city,
            ],
            'items'          => $order->items->map(fn($i) => [
                'product_id'    => $i->product_id,
                'product_name'  => $i->product_name,
                'product_image' => $i->product_image,
                'size'          => $i->size,
                'price'         => (float) $i->price,
                'quantity'      => $i->quantity,
            ])->toArray(),
        ];
    }
}
