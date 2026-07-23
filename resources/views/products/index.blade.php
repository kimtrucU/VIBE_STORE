@extends('layouts.app')
@section('title', 'Shop — VIBE Store')

@section('content')
<div id="shop-view" class="vibe-section">
    <div class="container-xl">

        {{-- Shop Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 pb-4 mb-5 border-bottom">
            <div>
                <h1 class="vibe-page-title">
                    @if(request()->routeIs('new-arrivals')) NEW ARRIVALS
                    @elseif(request()->routeIs('best-sellers')) BEST SELLERS
                    @else VIBE FASHION STORE
                    @endif
                </h1>
                <p class="vibe-page-subtitle">Showing {{ $products->total() }} Whenever designs</p>
            </div>
            @if(request()->hasAny(['search','category','size','max_price']))
            <a href="{{ route('shop') }}" id="reset-filters-btn" class="vibe-btn-reset">
                <i class="fas fa-redo me-1"></i> Reset Filters
            </a>
            @endif
        </div>

        <div class="row g-5">
            {{-- Sidebar Filters --}}
            <div class="col-lg-3">
                <aside class="vibe-filter-panel">
                    <div class="vibe-filter-header">
                        <span><i class="fas fa-sliders-h me-2"></i>Filters</span>
                    </div>

                    <form id="filter-form" method="GET" action="{{ route('shop') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        {{-- Category --}}
                        <div class="vibe-filter-section">
                            <span class="vibe-filter-label">Category</span>
                            <div class="vibe-filter-options">
                                <button type="submit" name="category" value="" class="vibe-filter-btn {{ !request('category') ? 'active' : '' }}">All Products</button>
                                @foreach($categories as $cat)
                                    <button type="submit" name="category" value="{{ $cat->slug }}"
                                        class="vibe-filter-btn {{ request('category') === $cat->slug ? 'active' : '' }}">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Size --}}
                        <div class="vibe-filter-section border-top pt-3">
                            <span class="vibe-filter-label">Size</span>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['', 'S', 'M', 'L', 'XL', 'Free Size'] as $size)
                                    <button type="submit" name="size" value="{{ $size }}"
                                        class="vibe-size-filter-btn {{ request('size') === $size ? 'active' : '' }}">
                                        {{ $size === '' ? 'ALL' : $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Price Range --}}
                        <div class="vibe-filter-section border-top pt-3">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <span class="vibe-filter-label">Max Price</span>
                                <span id="price-display" class="vibe-mono vibe-text-sm fw-bold">
                                    {{ number_format(request('max_price', 1500000), 0, '.', ',') }}₫
                                </span>
                            </div>
                            <input id="price-range-slider" type="range"
                                name="max_price"
                                min="300000" max="1500000" step="50000"
                                value="{{ request('max_price', 1500000) }}"
                                class="vibe-range w-100 mt-2">
                            <div class="d-flex justify-content-between vibe-mono vibe-text-xs text-muted">
                                <span>300K₫</span><span>1.5M₫</span>
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div class="vibe-filter-section border-top pt-3">
                            <span class="vibe-filter-label">Sort By</span>
                            <div class="position-relative">
                                <select id="sorting-select" name="sort"
                                    class="vibe-select w-100"
                                    onchange="this.form.submit()">
                                    <option value="featured" {{ request('sort','featured') === 'featured' ? 'selected' : '' }}>Featured</option>
                                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Best Rated</option>
                                </select>
                                <i class="fas fa-chevron-down vibe-select-icon"></i>
                            </div>
                        </div>

                        <button type="submit" class="vibe-btn-dark w-100 mt-3">Apply Filters</button>
                    </form>
                </aside>
            </div>

            {{-- Products Grid --}}
            <div class="col-lg-9">
                @if($products->isEmpty())
                    <div class="vibe-empty-state">
                        <i class="fas fa-search vibe-empty-icon"></i>
                        <h3 class="vibe-empty-title">No products found</h3>
                        <p class="vibe-empty-desc">Try expanding your price range or switching category/size filters.</p>
                        <a href="{{ route('shop') }}" class="vibe-btn-dark mt-3">Clear Filters</a>
                    </div>
                @else
                    <div class="row g-4 g-md-5">
                        @foreach($products as $product)
                            @include('components.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
                        @endforeach
                    </div>
                    <div class="mt-5">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live price range display
    const slider = document.getElementById('price-range-slider');
    const display = document.getElementById('price-display');
    if (slider && display) {
        slider.addEventListener('input', function() {
            display.textContent = parseInt(this.value).toLocaleString('en') + '₫';
        });
        // Auto-submit on release
        slider.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
</script>
@endpush
