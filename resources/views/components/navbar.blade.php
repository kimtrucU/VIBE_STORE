<nav id="main-header" class="vibe-navbar sticky-top">
    <div class="container-xl">
        <div class="vibe-navbar-inner">

            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="vibe-icon-btn d-md-none" aria-label="Open Menu">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Logo -->
            <div class="vibe-brand">
                <a href="{{ route('home') }}" class="vibe-logo">VIBE</a>
                <span class="vibe-logo-badge d-none d-sm-inline">Whenever Auth</span>
            </div>

            <!-- Desktop Navigation -->
            <nav class="vibe-nav-links d-none d-md-flex">
                <a href="{{ route('home') }}" class="vibe-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop') }}" class="vibe-nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">Shop</a>
                <a href="{{ route('new-arrivals') }}" class="vibe-nav-link {{ request()->routeIs('new-arrivals') ? 'active' : '' }}">New Arrivals</a>
                <a href="{{ route('best-sellers') }}" class="vibe-nav-link {{ request()->routeIs('best-sellers') ? 'active' : '' }}">Best Sellers</a>
                <a href="{{ route('about') }}" class="vibe-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="vibe-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </nav>

            <!-- Action Icons -->
            <div class="vibe-nav-actions">
                <!-- Search -->
                <button id="search-toggle-btn" class="vibe-icon-btn" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Account -->
                @if(auth()->check())
                    <div class="dropdown">
                        <button class="vibe-icon-btn" id="accountDropdown" data-bs-toggle="dropdown" aria-label="Account">
                            <i class="fas fa-user"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end vibe-dropdown">
                            <li><span class="dropdown-item-text vibe-dropdown-name">{{ auth()->user()->name }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="fas fa-box me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}#wishlist"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}#password"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="vibe-icon-btn" aria-label="Login">
                        <i class="fas fa-user"></i>
                    </a>
                @endif

                <!-- Wishlist -->
                @if(auth()->check())
                    <a href="{{ route('profile.index') }}#wishlist" id="wishlist-toggle-btn" class="vibe-icon-btn vibe-icon-btn-relative" aria-label="Wishlist">
                        <i class="far fa-heart"></i>
                        <span id="wishlist-count-badge" class="vibe-badge d-none">0</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="vibe-icon-btn vibe-icon-btn-relative" aria-label="Wishlist">
                        <i class="far fa-heart"></i>
                    </a>
                @endif

                <!-- Cart -->
                <button id="cart-toggle-btn" class="vibe-icon-btn vibe-icon-btn-dark vibe-icon-btn-relative" aria-label="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span id="cart-count-badge" class="vibe-badge-light {{ isset($cartCount) && $cartCount > 0 ? '' : 'd-none' }}">
                        {{ $cartCount ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Expandable Search Bar -->
        <div id="search-bar-container" class="vibe-search-bar collapse">
            <div class="vibe-search-inner">
                <form action="{{ route('shop') }}" method="GET" class="position-relative">
                    <i class="fas fa-search vibe-search-icon"></i>
                    <input id="global-search-input" type="text" name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Whenever products... (e.g., Tee, Hoodie, Jacket, Cap)"
                        class="vibe-search-input"
                        autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('shop') }}" class="vibe-search-clear"><i class="fas fa-times"></i></a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Drawer -->
<div id="mobile-nav-backdrop" class="vibe-drawer-backdrop d-none"></div>
<div id="mobile-nav-sidebar" class="vibe-mobile-nav">
    <div class="vibe-mobile-nav-header">
        <span class="vibe-logo">VIBE</span>
        <button id="close-mobile-menu" class="vibe-icon-btn"><i class="fas fa-times"></i></button>
    </div>
    <div class="vibe-mobile-nav-links">
        <a href="{{ route('home') }}" class="vibe-mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('shop') }}" class="vibe-mobile-nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">Shop</a>
        <a href="{{ route('new-arrivals') }}" class="vibe-mobile-nav-link {{ request()->routeIs('new-arrivals') ? 'active' : '' }}">New Arrivals</a>
        <a href="{{ route('best-sellers') }}" class="vibe-mobile-nav-link {{ request()->routeIs('best-sellers') ? 'active' : '' }}">Best Sellers</a>
        <a href="{{ route('about') }}" class="vibe-mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
        <a href="{{ route('contact') }}" class="vibe-mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
        @if(auth()->check())
            <a href="{{ route('profile.index') }}" class="vibe-mobile-nav-link">Profile</a>
            <a href="{{ route('orders.index') }}" class="vibe-mobile-nav-link">My Orders</a>
        @else
            <a href="{{ route('login') }}" class="vibe-mobile-nav-link">Login</a>
            <a href="{{ route('register') }}" class="vibe-mobile-nav-link">Register</a>
        @endif
    </div>
    <div class="vibe-mobile-nav-footer">
        <p class="mb-0">Official partner of <strong>Whenever</strong></p>
        <p class="vibe-mono vibe-text-xs">© 2026 VIBE STORES</p>
    </div>
</div>
