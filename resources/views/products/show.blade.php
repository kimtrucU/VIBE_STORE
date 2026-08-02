@extends('layouts.app')
@section('title', $product->name . ' — VIBE Store')

@section('content')
<div class="vibe-section">
    <div class="container-xl">

        {{-- Breadcrumb --}}
        <nav class="vibe-breadcrumb mb-4" aria-label="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop') }}">Shop</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
            <span class="mx-2">/</span>
            <span>{{ $product->name }}</span>
        </nav>

        <div class="row g-5">
            {{-- Image Gallery --}}
            <div class="col-md-6">
                <div class="vibe-product-gallery">
                    {{-- Main Image --}}
                    <div class="vibe-gallery-main" id="gallery-main">
                        @if($product->images && count($product->images) > 0)
                            <img id="main-product-image" src="{{ asset($product->images[0]) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <img id="main-product-image" src="/images/placeholder.webp" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                        @endif
                    </div>
                    {{-- Thumbnails --}}
                    @if($product->images && count($product->images) > 1)
                    <div class="vibe-gallery-thumbs">
                        @foreach($product->images as $i => $img)
                            <button class="vibe-thumb-btn {{ $i === 0 ? 'active' : '' }}"
                                onclick="setMainImage('{{ asset($img) }}', this)">
                                <img src="{{ asset($img) }}" alt="{{ $product->name }} view {{ $i+1 }}">
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Product Details --}}
            <div class="col-md-6">
                <span class="vibe-eyebrow">{{ $product->category->name }}</span>

                <h1 class="vibe-product-detail-title mt-1">{{ $product->name }}</h1>

                {{-- Rating --}}
                <div class="vibe-product-detail-rating mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($product->rating) ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="vibe-mono ms-2">{{ number_format($product->rating, 1) }}</span>
                    <span class="text-muted ms-1">({{ $product->reviews_count }} reviews)</span>
                </div>

                {{-- Price --}}
                <div class="vibe-product-detail-price mt-3">
                    <span class="vibe-price-lg vibe-mono">{{ number_format($product->price, 0, '.', ',') }}₫</span>
                    @if($product->original_price)
                        <span class="vibe-price-original-lg vibe-mono ms-3">{{ number_format($product->original_price, 0, '.', ',') }}₫</span>
                        <span class="vibe-badge-sale ms-2">-{{ $product->discount_percentage }}%</span>
                    @endif
                </div>

                <hr class="vibe-divider-h my-4">

                {{-- Description --}}
                <p class="vibe-product-desc">{{ $product->description }}</p>

                {{-- Details List --}}
                @if($product->details && count($product->details) > 0)
                <ul class="vibe-product-details mt-3">
                    @foreach($product->details as $detail)
                        <li><i class="fas fa-check me-2 text-dark"></i>{{ $detail }}</li>
                    @endforeach
                </ul>
                @endif

                {{-- Size Selector --}}
                <div class="mt-4">
                    <span class="vibe-filter-label">Select Size <span id="selected-size-display" class="text-dark ms-2 fw-bold"></span></span>
                    <div class="d-flex flex-wrap gap-2 mt-2" id="size-selector">
                        @if($product->sizes)
                            @foreach($product->sizes as $size)
                                <button type="button"
                                    class="vibe-size-btn-detail"
                                    data-size="{{ $size }}"
                                    onclick="selectProductSize(this, '{{ $size }}')">
                                    {{ $size }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                    <p class="vibe-size-error d-none text-danger vibe-text-xs mt-2" id="size-error">Please select a size before adding to cart.</p>
                </div>

                {{-- Quantity --}}
                <div class="mt-4">
                    <span class="vibe-filter-label">Quantity</span>
                    <div class="vibe-qty-control-detail mt-2">
                        <button type="button" onclick="adjustDetailQty(-1)" class="vibe-qty-btn-detail">−</button>
                        <span id="detail-qty" class="vibe-qty-detail-value">1</span>
                        <button type="button" onclick="adjustDetailQty(1)" class="vibe-qty-btn-detail">+</button>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="vibe-product-ctas mt-5">
                    <button id="detail-add-to-cart"
                        class="vibe-btn-dark flex-grow-1"
                        onclick="addToCartDetail({{ $product->id }})">
                        <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                    </button>
                    <a href="{{ route('checkout.index') }}" id="detail-buy-now"
                        class="vibe-btn-outline flex-grow-1"
                        onclick="addToCartDetail({{ $product->id }})">
                        Buy Now
                    </a>
                </div>

                {{-- Wishlist --}}
                @if(auth()->check())
                <button
                    class="vibe-wishlist-full mt-3 w-100 {{ in_array($product->id, $wishlistIds) ? 'active' : '' }}"
                    data-product-slug="{{ $product->slug }}"
                    data-url="{{ route('wishlist.toggle', $product->slug) }}">
                    <i class="fas fa-heart me-2"></i>
                    {{ in_array($product->id, $wishlistIds) ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                </button>
                @endif

                {{-- Secure Badge --}}
                <div class="vibe-secure-badge mt-4">
                    <i class="fas fa-shield-alt me-2"></i>
                    Secure Whenever authentication verified
                </div>

                {{-- Badges --}}
                <div class="d-flex gap-2 mt-3">
                    @if($product->is_new_arrival) <span class="vibe-badge-new">New Arrival</span> @endif
                    @if($product->is_best_seller) <span class="vibe-badge-best">Best Seller</span> @endif
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
        <section class="mt-16 pt-5 border-top">
            <h2 class="vibe-section-title mb-5">YOU MAY ALSO LIKE</h2>
            <div class="row g-4 g-md-5">
                @foreach($relatedProducts as $rp)
                    @include('components.product-card', ['product' => $rp, 'wishlistIds' => $wishlistIds])
                @endforeach
            </div>
        </section>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedSize = null;
let detailQty = 1;

function setMainImage(src, btn) {
    document.getElementById('main-product-image').src = src;
    document.querySelectorAll('.vibe-thumb-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function selectProductSize(btn, size) {
    document.querySelectorAll('.vibe-size-btn-detail').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedSize = size;
    document.getElementById('selected-size-display').textContent = size;
    document.getElementById('size-error').classList.add('d-none');
}

function adjustDetailQty(delta) {
    detailQty = Math.max(1, Math.min(10, detailQty + delta));
    document.getElementById('detail-qty').textContent = detailQty;
}

function addToCartDetail(productId) {
    if (!selectedSize) {
        document.getElementById('size-error').classList.remove('d-none');
        return false;
    }
    addToCart(productId, selectedSize, detailQty, null);
    return true;
}
</script>
@endpush
