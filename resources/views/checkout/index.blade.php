@extends('layouts.app')
@section('title', 'Checkout — VIBE Store')

@section('content')
<div id="checkout-view" class="vibe-section">
    <div class="container-xl">

        <div class="pb-4 mb-5 border-bottom">
            <h1 class="vibe-page-title">Checkout</h1>
            <p class="vibe-page-subtitle">Ensure your delivery address is correct so Whenever can ship your authenticated order.</p>
        </div>

        <div class="row g-5">
            {{-- Checkout Form --}}
            <div class="col-lg-7">
                <div class="vibe-form-card">
                    <h2 class="vibe-form-section-title">1. Shipping Information</h2>

                    <form id="checkout-main-form" method="POST" action="{{ route('checkout.store') }}">
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="vibe-label">Full Name *</label>
                                <input type="text" name="shipping_name" required
                                    value="{{ old('shipping_name', $user->name ?? '') }}"
                                    class="vibe-input" placeholder="John Smith">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Phone Number *</label>
                                <input type="tel" name="shipping_phone" required
                                    value="{{ old('shipping_phone', $user->phone ?? '') }}"
                                    class="vibe-input" placeholder="+1 (555) 123-4567">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Email Address *</label>
                                <input type="email" name="shipping_email" required
                                    value="{{ old('shipping_email', $user->email ?? '') }}"
                                    class="vibe-input" placeholder="john@example.com">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">City / Province *</label>
                                <input type="text" name="shipping_city" required
                                    value="{{ old('shipping_city', $user->city ?? '') }}"
                                    class="vibe-input" placeholder="Ho Chi Minh City">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Delivery Address *</label>
                                <input type="text" name="shipping_address" required
                                    value="{{ old('shipping_address', $user->address ?? '') }}"
                                    class="vibe-input" placeholder="House no., street name, ward, district">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Delivery Notes (Optional)</label>
                                <textarea name="notes" rows="3" class="vibe-input vibe-textarea"
                                    placeholder="Leave at door / Ring bell / Special instructions...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mt-5">
                            <h2 class="vibe-form-section-title">2. Payment Method</h2>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <label class="vibe-payment-option {{ old('payment_method', 'COD') === 'COD' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="COD"
                                            {{ old('payment_method', 'COD') === 'COD' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <span class="fw-bold">Cash on Delivery</span>
                                        <span class="vibe-text-xs text-muted mt-1">Pay when you receive (inspect first)</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <label class="vibe-payment-option {{ old('payment_method') === 'momo' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="momo"
                                            {{ old('payment_method') === 'momo' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <span class="fw-bold text-danger">MoMo Wallet</span>
                                        <span class="vibe-text-xs text-muted mt-1">Scan QR code via MoMo app</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <label class="vibe-payment-option {{ old('payment_method') === 'bank_transfer' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="bank_transfer"
                                            {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <span class="fw-bold text-primary">Bank Transfer</span>
                                        <span class="vibe-text-xs text-muted mt-1">QR Mobile Banking</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button id="checkout-order-now" type="submit" class="vibe-btn-dark w-100 vibe-btn-lg">
                                Confirm &amp; Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-5">
                <div class="vibe-form-card mb-4">
                    <h3 class="vibe-form-section-title">Order Summary ({{ $cart->items->count() }} items)</h3>

                    @if($cart->items->isEmpty())
                        <p class="text-muted fst-italic">Your cart is empty.</p>
                    @else
                        <div class="vibe-checkout-items">
                            @foreach($cart->items as $item)
                            <div class="vibe-checkout-item">
                                <div class="vibe-checkout-item-img">
                                    <img src="{{ $item->product->primary_image }}" alt="{{ $item->product->name }}">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h4 class="vibe-checkout-item-name">{{ $item->product->name }}</h4>
                                    <p class="vibe-text-xs vibe-mono text-muted">SIZE: {{ $item->size }} / QTY: {{ $item->quantity }}</p>
                                </div>
                                <div class="vibe-checkout-item-price vibe-mono">
                                    {{ number_format($item->product->price * $item->quantity, 0, '.', ',') }}₫
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <div class="vibe-price-row">
                                <span>Subtotal</span>
                                <span class="vibe-mono">{{ number_format($cart->subtotal, 0, '.', ',') }}₫</span>
                            </div>
                            <div class="vibe-price-row">
                                <span>Shipping</span>
                                <span class="vibe-mono">{{ $cart->shipping_fee > 0 ? number_format($cart->shipping_fee, 0, '.', ',') . '₫' : 'Free' }}</span>
                            </div>
                            <div class="vibe-price-row vibe-price-total border-top pt-2 mt-2">
                                <span>Total</span>
                                <span class="vibe-mono">{{ number_format($cart->grand_total, 0, '.', ',') }}₫</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Trust Badge --}}
                <div class="vibe-trust-card">
                    <h4 class="vibe-trust-title">VIBE Exclusive Guarantee</h4>
                    <p class="vibe-text-xs text-muted">
                        All orders are checked for authentic Whenever tags before sealing. COD customers may inspect items before payment.
                    </p>
                    <div class="vibe-secure-badge mt-2">
                        <i class="fas fa-shield-alt me-1"></i> SECURE WHENEVER CODES VERIFIED
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePaymentSelection(radio) {
    document.querySelectorAll('.vibe-payment-option').forEach(el => el.classList.remove('active'));
    radio.closest('.vibe-payment-option').classList.add('active');
}
</script>
@endpush
