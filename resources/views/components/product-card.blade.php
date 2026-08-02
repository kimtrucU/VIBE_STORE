@php
    $isWishlisted = in_array($product->id, $wishlistIds ?? []);
@endphp

<div class="col-6 col-md-4 col-lg-3">
    <div id="product-card-{{ $product->id }}" class="vibe-product-card" data-product-id="{{ $product->id }}">

        {{-- Image Stage --}}
        <div class="vibe-product-image-wrap">
            {{-- Badges --}}
            <div class="vibe-product-badges">
                @if($product->is_new_arrival)
                    <span class="vibe-badge-new">New</span>
                @endif
                @if($product->is_best_seller)
                    <span class="vibe-badge-best">Best</span>
                @endif
                @if($product->discount_percentage)
                    <span class="vibe-badge-sale">-{{ $product->discount_percentage }}%</span>
                @endif
            </div>

            {{-- Wishlist Button --}}
            @if(auth()->check())
            <button
                id="wishlist-btn-{{ $product->id }}"
                class="vibe-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
                data-product-slug="{{ $product->slug }}"
                data-url="{{ route('wishlist.toggle', $product->slug) }}"
                aria-label="Toggle wishlist">
                <i class="fas fa-heart"></i>
            </button>
            @endif

            {{-- Product Images --}}
            <a href="{{ route('products.show', $product->slug) }}" class="vibe-product-image-link">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ asset($product->images[0]) }}"
                         alt="{{ $product->name }}"
                         class="vibe-product-img vibe-product-img-primary"
                         loading="lazy">
                    @if(isset($product->images[1]))
                        <img src="{{ asset($product->images[1]) }}"
                             alt="{{ $product->name }} alternate"
                             class="vibe-product-img vibe-product-img-hover"
                             loading="lazy">
                    @endif
                @else
                    <img src="/images/placeholder.webp" alt="{{ $product->name }}" class="vibe-product-img">
                @endif
            </a>

            {{-- Desktop Quick Action --}}
            <div class="vibe-quick-actions">
                <a href="{{ route('products.show', $product->slug) }}"
                   class="vibe-btn-quick-view">
                    <i class="fas fa-eye me-1"></i> Quick View
                </a>
            </div>
            


            {{-- Size Selector (shown on add-to-cart click) --}}
            <div class="vibe-size-overlay d-none" id="size-overlay-{{ $product->id }}">
                <p class="vibe-size-overlay-title">Select Size</p>
                <div class="vibe-size-grid">
                    @if($product->sizes)
                        @foreach($product->sizes as $size)
                            <button
                                class="vibe-size-btn"
                                data-product-id="{{ $product->id }}"
                                data-size="{{ $size }}"
                                onclick="addToCart({{ $product->id }}, '{{ $size }}', 1, this)">
                                {{ $size }}
                            </button>
                        @endforeach
                    @endif
                </div>
                <button class="vibe-size-cancel" onclick="hideSizeOverlay({{ $product->id }})">Cancel</button>
            </div>
        </div>

        {{-- Product Info --}}
        <div class="vibe-product-info">
            <span class="vibe-product-category">{{ $product->category->name ?? $product->category_id }}</span>
            <h3 class="vibe-product-name">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <div class="vibe-product-meta">
                <div class="vibe-product-rating">
                    <i class="fas fa-star text-warning"></i>
                    <span class="vibe-mono">{{ number_format($product->rating, 1) }}</span>
                </div>
                <span class="vibe-meta-dot">•</span>
                <span class="vibe-product-sizes vibe-mono">{{ implode(', ', $product->sizes ?? []) }}</span>
            </div>

            <div class="vibe-product-price-row">
                <div class="d-flex flex-wrap align-items-baseline gap-2">
                    <span class="vibe-price vibe-mono">{{ number_format($product->price, 0, '.', ',') }}₫</span>
                    @if($product->original_price)
                        <span class="vibe-price-original vibe-mono">{{ number_format($product->original_price, 0, '.', ',') }}₫</span>
                    @endif
                </div>
                <button
                    id="add-to-cart-quick-{{ $product->id }}"
                    class="vibe-add-btn"
                    onclick="handleQuickAdd({{ $product->id }}, {{ json_encode($product->sizes ?? []) }})"
                    aria-label="Add to cart">
                    <i class="fas fa-shopping-bag"></i>
                </button>
            </div>
        </div>

    </div>
</div>
