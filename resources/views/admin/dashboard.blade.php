@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
{{-- Stat Cards --}}
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card">
            <div class="vibe-stat-icon bg-dark"><i class="fas fa-dollar-sign text-white"></i></div>
            <div>
                <p class="vibe-stat-card-label">Total Revenue</p>
                <h2 class="vibe-stat-card-value vibe-mono">{{ number_format($stats['total_revenue'], 0, '.', ',') }}₫</h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card">
            <div class="vibe-stat-icon bg-primary"><i class="fas fa-box text-white"></i></div>
            <div>
                <p class="vibe-stat-card-label">Total Orders</p>
                <h2 class="vibe-stat-card-value">{{ $stats['total_orders'] }}</h2>
                <small class="text-warning">{{ $stats['pending_orders'] }} pending</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card">
            <div class="vibe-stat-icon bg-success"><i class="fas fa-tshirt text-white"></i></div>
            <div>
                <p class="vibe-stat-card-label">Products</p>
                <h2 class="vibe-stat-card-value">{{ $stats['total_products'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card">
            <div class="vibe-stat-icon bg-info"><i class="fas fa-users text-white"></i></div>
            <div>
                <p class="vibe-stat-card-label">Customers</p>
                <h2 class="vibe-stat-card-value">{{ $stats['total_customers'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Orders --}}
    <div class="col-lg-7">
        <div class="vibe-admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="vibe-admin-card-title">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="vibe-link vibe-text-xs">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm vibe-admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="vibe-mono">{{ $order->order_number }}</td>
                            <td>{{ $order->shipping_name }}</td>
                            <td class="vibe-mono">{{ number_format($order->total, 0, '.', ',') }}₫</td>
                            <td><span class="badge bg-{{ $order->status_badge_color }}">{{ ucfirst($order->status) }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="col-lg-5">
        <div class="vibe-admin-card">
            <h3 class="vibe-admin-card-title mb-4">Top Products</h3>
            @foreach($topProducts as $i => $p)
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="vibe-rank">{{ $i + 1 }}</span>
                <div class="flex-grow-1 min-w-0">
                    <p class="mb-0 vibe-text-sm fw-semibold">{{ Str::limit($p->product_name, 35) }}</p>
                    <p class="mb-0 vibe-text-xs text-muted">{{ $p->total_sold }} sold</p>
                </div>
                <span class="vibe-mono vibe-text-sm fw-bold">{{ number_format($p->revenue, 0, '.', ',') }}₫</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
