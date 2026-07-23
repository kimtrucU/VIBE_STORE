<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $customerRoleId = DB::table('roles')->where('name', 'customer')->value('id');

        $stats = [
            'total_revenue'    => Order::whereNotIn('status', ['cancelled', 'returned'])->sum('total'),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'total_products'   => Product::count(),
            'total_customers'  => User::where('role_id', $customerRoleId)->count(),
            'total_categories' => Category::count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Tính doanh thu theo từng tháng cho chart
        $revenueByMonth = array_fill(1, 12, 0);
        foreach ($monthlyRevenue as $item) {
            $revenueByMonth[$item->month] = (float) $item->revenue;
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'monthlyRevenue', 'revenueByMonth'));
    }
}
