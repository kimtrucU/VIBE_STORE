@extends('admin.layouts.app')
@section('title', 'Chi tiết khách hàng')
@section('page_title', 'Khách Hàng: ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="vibe-admin-card text-center">
            <div class="mb-3">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover">
                @else
                    <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center mx-auto" style="width:80px;height:80px">
                        <span class="text-white fs-3 fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted mb-2">{{ $user->email }}</p>
            @if($user->phone)
                <p class="text-muted mb-2"><i class="fas fa-phone me-1"></i>{{ $user->phone }}</p>
            @endif
            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $user->is_active ? 'Đang hoạt động' : 'Bị khóa' }}
            </span>
            <div class="mt-3 text-muted vibe-text-xs">
                Tham gia: {{ $user->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="vibe-admin-card mt-4">
            <h6 class="fw-bold mb-3">Thống kê</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tổng đơn hàng</span>
                <strong>{{ $user->orders->count() }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tổng chi tiêu</span>
                <strong>{{ number_format($user->orders->whereNotIn('status', ['cancelled','returned'])->sum('total'), 0, '.', ',') }}₫</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Đơn hủy</span>
                <strong>{{ $user->orders->where('status', 'cancelled')->count() }}</strong>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="vibe-admin-card">
            <h5 class="vibe-admin-card-title mb-4">Lịch sử đơn hàng</h5>
            @forelse($user->orders as $order)
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <p class="mb-1 fw-semibold vibe-mono">{{ $order->order_number }}</p>
                        <p class="mb-1 vibe-text-xs text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-0 vibe-text-xs">{{ $order->items->count() }} sản phẩm</p>
                    </div>
                    <div class="text-end">
                        <p class="mb-1 fw-bold">{{ number_format($order->total, 0, '.', ',') }}₫</p>
                        <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_label }}</span>
                        <div class="mt-1">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-dark">Xem</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4">Chưa có đơn hàng nào</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
