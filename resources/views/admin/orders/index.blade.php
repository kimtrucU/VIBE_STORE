@extends('admin.layouts.app')
@section('title', 'Manage Orders')
@section('page_title', 'Order List')

@section('content')
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach(['','pending','processing','shipped','delivered','cancelled'] as $s)
        <a href="{{ route('admin.orders.index', $s ? ['status' => $s] : []) }}"
           class="btn btn-sm {{ request('status') === $s || (!request('status') && !$s) ? 'btn-dark' : 'btn-outline-dark' }}">
            {{ $s ? ucfirst($s) : 'All' }}
        </a>
    @endforeach
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="vibe-mono fw-bold">{{ $order->order_number }}</td>
                    <td>
                        <p class="mb-0">{{ $order->shipping_name }}</p>
                        <small class="text-muted">{{ $order->shipping_email }}</small>
                    </td>
                    <td class="vibe-text-xs text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="vibe-mono">{{ number_format($order->total, 0, '.', ',') }}₫</td>
                    <td><span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span></td>
                    <td><span class="badge bg-{{ $order->status_badge_color }}">{{ ucfirst($order->status) }}</span></td>
                    <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
