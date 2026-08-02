<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->get('search')) {
            $query->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_email', 'like', "%{$search}%");
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'        => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,returned',
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        $timestampMap = [
            'confirmed'  => 'confirmed_at',
            'processing' => 'processed_at',
            'shipped'    => 'shipped_at',
            'delivered'  => 'delivered_at',
            'completed'  => 'completed_at',
            'cancelled'  => 'cancelled_at',
            'returned'   => 'returned_at',
        ];

        $updates = ['status' => $newStatus];
        if (isset($timestampMap[$newStatus])) {
            $updates[$timestampMap[$newStatus]] = now();
        }
        if ($newStatus === 'cancelled' && $request->cancel_reason) {
            $updates['cancel_reason'] = $request->cancel_reason;
        }

        $order->update($updates);

        ActivityLog::log(
            'order.status_updated',
            "Order #{$order->order_number} changed from {$oldStatus} to {$newStatus}",
            $order
        );

        return back()->with('success', "Đơn hàng #{$order->order_number} đã chuyển sang trạng thái: " . Order::$statusLabels[$newStatus]);
    }
}
