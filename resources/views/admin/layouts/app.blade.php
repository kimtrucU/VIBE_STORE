<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Vibe Fashion')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="vibe-admin-body">

    <div class="vibe-admin-layout">
        {{-- Sidebar --}}
        <aside class="vibe-admin-sidebar">
            <div class="vibe-admin-brand">
                <span class="vibe-logo text-white">VIBE</span>
                <span class="vibe-admin-label">Admin Panel</span>
            </div>

            <nav class="vibe-admin-nav">
                <a href="{{ route('admin.dashboard') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Dashboard
                </a>

                <div class="vibe-admin-nav-group">Sản phẩm</div>
                <a href="{{ route('admin.products.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-tshirt"></i> Sản phẩm
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Danh mục
                </a>
                <a href="{{ route('admin.brands.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="fas fa-trademark"></i> Thương hiệu
                </a>
                <a href="{{ route('admin.inventory.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i> Kho hàng
                </a>

                <div class="vibe-admin-nav-group">Quản lý</div>
                <a href="{{ route('admin.orders.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Đơn hàng
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Khách hàng
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> Tài khoản
                </a>

                <div class="vibe-admin-nav-group">Nội dung</div>
                <a href="{{ route('admin.reviews.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Đánh giá
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i> Coupon
                </a>

                <div class="vibe-admin-nav-group">Thống kê</div>
                <a href="{{ route('admin.reports.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Báo cáo
                </a>

                <div class="vibe-admin-nav-group">Hệ thống</div>
                <a href="{{ route('admin.settings.index') }}"
                   class="vibe-admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
                <a href="{{ route('home') }}" class="vibe-admin-nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Xem Store
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                    @csrf
                    <button type="submit" class="vibe-admin-nav-link text-danger border-0 bg-transparent w-100 text-start">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="vibe-admin-main">
            <header class="vibe-admin-topbar">
                <h1 class="vibe-admin-topbar-title">@yield('page_title', 'Dashboard')</h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="vibe-text-sm text-muted">{{ auth()->user()->name }}</span>
                    <span class="badge bg-dark">Admin</span>
                </div>
            </header>

            <main class="vibe-admin-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- JS handled by Vite --}}
    @stack('scripts')
</body>
</html>
