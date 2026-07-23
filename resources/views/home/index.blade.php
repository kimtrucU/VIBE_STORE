@extends('layouts.app')
@section('title', 'VIBE Store — Official Whenever Partner')

@section('content')

{{-- Hero Section --}}
<section class="vibe-hero">
    <div class="vibe-hero-bg">
        <img src="https://images.unsplash.com/photo-1483347756191-4a2407223bc0?auto=format&fit=crop&q=80&w=1600"
             alt="Whenever Editorial Banner" class="vibe-hero-img">
        <div class="vibe-hero-overlay"></div>
    </div>
    <div class="container-xl position-relative">
        <div class="vibe-hero-content">
            <span class="vibe-hero-eyebrow">
                <i class="fas fa-sparkles text-warning me-1"></i>
                Exclusive Official Partnership
            </span>
            <h1 class="vibe-hero-title">
                THE VIBE<br>
                <span class="vibe-hero-gradient">WHENEVER.</span>
            </h1>
            <p class="vibe-hero-desc">
                Contemporary minimalism meets raw streetwear culture. Whenever's authentic 250gsm heavyweight cotton tees with genuine embossed tag detail — now available at VIBE Store.
            </p>
            <div class="vibe-hero-ctas">
                <a id="hero-shop-now" href="{{ route('shop') }}" class="vibe-btn-hero-primary">
                    Shop Now <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <a id="hero-about-collab" href="{{ route('about') }}" class="vibe-btn-hero-outline">
                    About the Collaboration
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Categories Grid --}}
<section class="vibe-section vibe-section-white">
    <div class="container-xl">
        <div class="vibe-section-header text-center mb-14">
            <span class="vibe-eyebrow">Core Products</span>
            <h2 class="vibe-section-title">VIBE FASHION CATEGORIES</h2>
            <div class="vibe-divider mx-auto mt-3"></div>
        </div>
        <div class="row g-4">
            @php
            $catGrid = [
                ['slug' => 'tshirt',      'label' => 'T-Shirts',    'img' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=400'],
                ['slug' => 'hoodie',      'label' => 'Hoodies',     'img' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&q=80&w=400'],
                ['slug' => 'jacket',      'label' => 'Jackets',     'img' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=400'],
                ['slug' => 'shorts',      'label' => 'Shorts',      'img' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&q=80&w=400'],
                ['slug' => 'accessories', 'label' => 'Accessories', 'img' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&q=80&w=400'],
            ]
            @endphp
            @foreach($catGrid as $cat)
            <div class="col-6 col-lg">
                <a id="quick-cat-{{ $cat['slug'] }}" href="{{ route('shop', ['category' => $cat['slug']]) }}"
                   class="vibe-cat-card">
                    <img src="{{ $cat['img'] }}" alt="{{ $cat['label'] }}" class="vibe-cat-img" loading="lazy">
                    <div class="vibe-cat-overlay"></div>
                    <div class="vibe-cat-label">
                        <span class="vibe-cat-name">{{ $cat['label'] }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="vibe-section vibe-section-gray">
    <div class="container-xl">
        <div class="d-flex flex-column flex-sm-row align-items-baseline justify-content-between mb-5 pb-3 border-bottom">
            <div>
                <span class="vibe-eyebrow">Hot Items</span>
                <h2 class="vibe-section-title">NEW ARRIVALS</h2>
            </div>
            <a id="view-all-shop" href="{{ route('shop') }}" class="vibe-link-arrow mt-2 mt-sm-0">
                View All <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </div>
        <div class="row g-4 g-md-5">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
            @endforeach
        </div>
    </div>
</section>

{{-- Highlight Banner --}}
<section class="vibe-highlight">
    <div class="container-xl">
        <div class="row align-items-center g-8">
            <div class="col-lg-7" data-vibe-reveal="left">
                <span class="vibe-eyebrow-dark" data-vibe-reveal data-vibe-delay="1">Whenever Authentic Denim &amp; Leather</span>
                <h2 class="vibe-highlight-title" data-vibe-reveal data-vibe-delay="2">
                    OVERSIZED BOMBER<br>UTILITY JACKET
                </h2>
                <p class="vibe-highlight-desc" data-vibe-reveal data-vibe-delay="3">
                    An explosive fusion of streetwear spirit and military heritage. Water-resistant nylon outer shell with flat utility zip pockets and an oversized silhouette that commands attention.
                </p>
                <div class="vibe-highlight-stats" data-vibe-reveal data-vibe-delay="4">
                    <div class="vibe-stat">
                        <span class="vibe-stat-value">4.9★</span>
                        <span class="vibe-stat-label">Top Rated</span>
                    </div>
                    <div class="vibe-stat border-start border-secondary ps-4 ms-4">
                        <span class="vibe-stat-value">380gsm</span>
                        <span class="vibe-stat-label">French Terry</span>
                    </div>
                    <div class="vibe-stat border-start border-secondary ps-4 ms-4">
                        <span class="vibe-stat-value">100%</span>
                        <span class="vibe-stat-label">Authentic Tag</span>
                    </div>
                </div>
                <div data-vibe-reveal data-vibe-delay="5">
                    <a id="banner-shop-jacket" href="{{ route('shop', ['category' => 'hoodie']) }}" class="vibe-btn-white mt-4">
                        Shop Jackets <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-5" data-vibe-reveal="right" data-vibe-delay="3">
                <div class="vibe-highlight-image">
                    <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=600"
                         alt="Whenever bomber jacket" class="w-100 h-100 object-fit-cover">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Best Sellers --}}
<section class="vibe-section vibe-section-white">
    <div class="container-xl">
        <div class="text-center mb-5">
            <span class="vibe-eyebrow">Most Popular</span>
            <h2 class="vibe-section-title">BEST SELLERS</h2>
        </div>
        <div class="row g-4 g-md-5">
            @foreach($bestSellers as $product)
                @include('components.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
            @endforeach
        </div>
    </div>
</section>

@endsection
