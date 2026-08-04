@extends('layouts.app')
@section('title', 'Order List — Vibe Fashion')

@section('content')
<div class="vibe-section">
    <div class="container-xl">
        <h1 class="vibe-page-title mb-5">Your Order History</h1>

        @if($orders->isEmpty())
            <div class="text-center py-5 bg-light rounded">
                <i class="fas fa-box-open fs-1 text-muted mb-3"></i>
                <h3 class="fw-bold mb-3">You don't have any orders yet</h3>
                <p class="text-muted mb-4">Explore our latest products!</p>
                <a href="{{ route('shop') }}" class="vibe-btn-dark">Continue Shopping</a>
            </div>
        @else
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order Number</th>
                            <th>Order Date</th>
                            <th>Products</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><span class="vibe-mono fw-bold">#{{ $order->order_number }}</span></td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>{{ $order->items->count() }} item(s)</td>
                            <td class="vibe-mono fw-bold">{{ number_format($order->total, 0, '.', ',') }}₫</td>
                            <td><span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_label }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-dark btn-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="d-md-none">
                @foreach($orders as $order)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="vibe-mono fw-bold">#{{ $order->order_number }}</span>
                        <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_label }}</span>
                    </div>
                    <p class="text-muted vibe-text-sm mb-2">Date: {{ $order->created_at->format('d/m/Y') }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <span class="vibe-mono fw-bold fs-5">{{ number_format($order->total, 0, '.', ',') }}₫</span>
                        <a href="{{ route('orders.show', $order) }}" class="vibe-link">View Details &rarr;</a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
