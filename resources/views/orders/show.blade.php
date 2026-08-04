@extends('layouts.app')
@section('title', 'Order Details #' . $order->order_number . ' — Vibe Fashion')

@section('content')
<div class="vibe-section">
    <div class="container-xl">
        <div class="mb-5">
            <a href="{{ route('orders.index') }}" class="vibe-link vibe-text-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Order List
            </a>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 border-bottom pb-3">
            <div>
                <h1 class="vibe-page-title mb-2">Order #{{ $order->order_number }}</h1>
                <p class="text-muted mb-0">Order Date: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-{{ $order->status_badge_color }} fs-6 px-3 py-2">{{ $order->status_label }}</span>
            </div>
        </div>

        <div class="row g-5">
            {{-- Order Items --}}
            <div class="col-lg-8">
                <div class="vibe-form-card mb-4">
                    <h2 class="vibe-form-section-title mb-4">Ordered Products</h2>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width: 500px">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" style="width:60px;height:60px;object-fit:cover;border-radius:4px">
                                            @endif
                                            <span class="fw-semibold">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->size }}</span></td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end vibe-mono">{{ number_format($item->price, 0, '.', ',') }}₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="vibe-form-card">
                    <h2 class="vibe-form-section-title mb-4">Delivery History</h2>
                    <div class="position-relative ms-3 border-start border-2 pb-2">
                        @if($order->created_at)
                        <div class="position-relative ms-4 mb-4">
                            <div class="position-absolute bg-dark rounded-circle" style="width:12px;height:12px;left:-23px;top:4px"></div>
                            <h6 class="fw-bold mb-1">Order Placed</h6>
                            <p class="text-muted vibe-text-sm mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif

                        @if($order->confirmed_at)
                        <div class="position-relative ms-4 mb-4">
                            <div class="position-absolute bg-info rounded-circle" style="width:12px;height:12px;left:-23px;top:4px"></div>
                            <h6 class="fw-bold mb-1">Confirmed</h6>
                            <p class="text-muted vibe-text-sm mb-0">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif

                        @if($order->shipped_at)
                        <div class="position-relative ms-4 mb-4">
                            <div class="position-absolute bg-primary rounded-circle" style="width:12px;height:12px;left:-23px;top:4px"></div>
                            <h6 class="fw-bold mb-1">Shipped</h6>
                            <p class="text-muted vibe-text-sm mb-0">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif

                        @if($order->delivered_at || $order->completed_at)
                        <div class="position-relative ms-4 mb-4">
                            <div class="position-absolute bg-success rounded-circle" style="width:12px;height:12px;left:-23px;top:4px"></div>
                            <h6 class="fw-bold mb-1 text-success">Delivered Successfully</h6>
                            <p class="text-muted vibe-text-sm mb-0">{{ ($order->completed_at ?? $order->delivered_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif

                        @if($order->cancelled_at)
                        <div class="position-relative ms-4 mb-4">
                            <div class="position-absolute bg-danger rounded-circle" style="width:12px;height:12px;left:-23px;top:4px"></div>
                            <h6 class="fw-bold mb-1 text-danger">Order Cancelled</h6>
                            <p class="text-muted vibe-text-sm mb-1">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                            @if($order->cancel_reason)<p class="text-muted vibe-text-sm mb-0 fst-italic">Reason: {{ $order->cancel_reason }}</p>@endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Summary & Shipping Details --}}
            <div class="col-lg-4">
                <div class="vibe-form-card mb-4 bg-light">
                    <h2 class="vibe-form-section-title mb-3">Payment Summary</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="vibe-mono">{{ number_format($order->subtotal, 0, '.', ',') }}₫</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount ({{ $order->coupon_code }})</span>
                        <span class="vibe-mono">-{{ number_format($order->discount, 0, '.', ',') }}₫</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Shipping Fee</span>
                        <span class="vibe-mono">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, '.', ',') . '₫' : 'Free' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="vibe-mono fw-bold fs-4">{{ number_format($order->total, 0, '.', ',') }}₫</span>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <span class="text-muted d-block mb-1 vibe-text-sm">Payment Method:</span>
                        <span class="badge bg-dark">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                    </div>
                </div>

                <div class="vibe-form-card">
                    <h2 class="vibe-form-section-title mb-3">Shipping Information</h2>
                    <p class="fw-bold mb-1">{{ $order->shipping_name }}</p>
                    <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>{{ $order->shipping_phone }}</p>
                    <p class="text-muted mb-3"><i class="fas fa-envelope me-2"></i>{{ $order->shipping_email }}</p>
                    <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2"></i>{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
                    @if($order->notes)
                        <hr>
                        <p class="text-muted mb-1 vibe-text-sm">Notes:</p>
                        <p class="mb-0">{{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
