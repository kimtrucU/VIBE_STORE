<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;

        // Doanh thu theo tháng năm nay
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as total_orders')
            ->whereYear('created_at', $currentYear)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $revenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueData[$m] = [
                'revenue'       => isset($monthlyRevenue[$m]) ? (float)$monthlyRevenue[$m]->revenue : 0,
                'total_orders'  => isset($monthlyRevenue[$m]) ? (int)$monthlyRevenue[$m]->total_orders : 0,
            ];
        }

        // Tổng quan
        $summary = [
            'total_revenue_ytd'  => Order::whereYear('created_at', $currentYear)->whereNotIn('status', ['cancelled', 'returned'])->sum('total'),
            'total_orders_ytd'   => Order::whereYear('created_at', $currentYear)->count(),
            'avg_order_value'    => Order::whereYear('created_at', $currentYear)->whereNotIn('status', ['cancelled', 'returned'])->avg('total') ?? 0,
            'this_month_revenue' => Order::whereYear('created_at', $currentYear)->whereMonth('created_at', now()->month)->whereNotIn('status', ['cancelled', 'returned'])->sum('total'),
        ];

        // Top sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(order_items.price * quantity) as revenue'))
            ->whereYear('orders.created_at', $currentYear)
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Doanh thu theo status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Doanh thu năm trước để so sánh
        $lastYearRevenue = Order::whereYear('created_at', $currentYear - 1)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total');

        return view('admin.reports.index', compact(
            'revenueData', 'summary', 'topProducts', 'ordersByStatus', 'currentYear', 'lastYearRevenue'
        ));
    }
}
