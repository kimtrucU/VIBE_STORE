@extends('layouts.app')
@section('title', 'Order Confirmed — VIBE Store')

@section('content')
<div id="order-confirm-view" class="vibe-section vibe-section-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">

                <div class="vibe-success-icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>

                <span class="vibe-eyebrow text-success">Order Placed Successfully</span>
                <h1 class="vibe-page-title mt-1">THANK YOU FOR YOUR ORDER!</h1>
                <p class="text-muted">
                    Your order has been received and is being authenticated with Whenever tags before handing to our delivery partner.
                </p>

                {{-- Order Summary Box --}}
                <div class="vibe-confirm-box mt-5 text-start">
                    <div class="d-flex justify-content-between align-items-baseline border-bottom pb-3 mb-3">
                        <span class="vibe-mono fw-bold">ORDER: {{ $order->order_number }}</span>
                        <span class="text-muted vibe-mono vibe-text-xs">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    <div class="mb-4">
                        <span class="vibe-filter-label">Items Ordered</span>
                        @foreach($order->items as $item)
                        <div class="d-flex justify-content-between mt-2 vibe-text-sm">
                            <span>{{ $item->product_name }} <span class="text-muted">({{ $item->size }})</span> × {{ $item->quantity }}</span>
                            <span class="vibe-mono">{{ number_format($item->price * $item->quantity, 0, '.', ',') }}₫</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-top pt-3 row g-2 vibe-text-xs text-muted">
                        <div class="col-sm-6">
                            <strong class="d-block text-dark mb-1">DELIVERY ADDRESS</strong>
                            <p class="mb-0">{{ $order->shipping_name }}</p>
                            <p class="mb-0">{{ $order->shipping_phone }}</p>
                            <p class="mb-0">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
                        </div>
                        <div class="col-sm-6">
                            <strong class="d-block text-dark mb-1">PAYMENT</strong>
                            <p class="mb-0">Method: <strong class="text-dark">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</strong></p>
                            <p class="mb-0">Shipping: {{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, '.', ',') . '₫' : 'Free' }}</p>
                            <p class="mt-2 fw-bold text-dark">TOTAL: {{ number_format($order->total, 0, '.', ',') }}₫</p>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-5">
                    <a href="{{ route('home') }}" class="vibe-btn-dark">
                        Return to Home
                    </a>
                    <a href="{{ route('shop') }}" class="vibe-btn-outline">
                        Continue Shopping
                    </a>
                    @if(auth()->check())
                    <a href="{{ route('orders.index') }}" class="vibe-btn-outline">
                        View My Orders
                    </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
