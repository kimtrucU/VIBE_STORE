<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Vibe Fashion — Official Whenever Partner. Premium minimalist streetwear.">
    <title>@yield('title', 'Vibe Fashion — Official Whenever Partner')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="vibe-body">

    <!-- Promo Ticker -->
    <div id="promo-ticker" class="vibe-promo-ticker">
        VIBE &times; WHENEVER: OFF-WHITE &amp; HEAVYWEIGHT ESSENTIALS &nbsp;&bull;&nbsp; FREE SHIPPING ON ORDERS OVER 500,000&#x20AB;
    </div>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Toast Notifications -->
    @include('components.toast')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="vibe-flash vibe-flash-success d-none" data-message="{{ session('success') }}" data-type="success"></div>
    @endif
    @if(session('error'))
        <div class="vibe-flash vibe-flash-error d-none" data-message="{{ session('error') }}" data-type="error"></div>
    @endif
    @if(session('info'))
        <div class="vibe-flash vibe-flash-info d-none" data-message="{{ session('info') }}" data-type="info"></div>
    @endif

    <!-- Main Content -->
    <main class="vibe-main">
        @yield('content')
    </main>

    <!-- Newsletter Section -->
    @unless(request()->routeIs('admin.*'))
    <section class="vibe-newsletter">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h3 class="vibe-newsletter-title">JOIN THE VIBE CLUB</h3>
                    <p class="vibe-newsletter-desc">Subscribe to get <strong>10% off</strong> your first order and early access to new Whenever drops.</p>
                </div>
                <div class="col-lg-6">
                    <form id="newsletter-form" class="vibe-newsletter-form d-flex gap-2">
                        <input id="newsletter-email-input" type="email" class="vibe-newsletter-input flex-grow-1" placeholder="Enter your email address..." required>
                        <button id="newsletter-submit" type="submit" class="vibe-btn-white">Subscribe</button>
                    </form>
                    <div id="newsletter-success" class="vibe-newsletter-success d-none">
                        <i class="fas fa-check-circle me-1"></i>
                        Thanks! Your voucher code: <strong class="font-mono">VIBEWHENEVER10</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endunless

    <!-- Footer -->
    @include('components.footer')

    <!-- Cart Drawer -->
    @include('components.cart-drawer')

    <!-- Wishlist Drawer -->
    @if(auth()->check())
        @include('components.wishlist-drawer')
    @endif

    @stack('scripts')
</body>
</html>
