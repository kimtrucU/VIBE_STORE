@extends('admin.layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->order_number)
@section('page_title', 'Đơn Hàng #' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
    <span class="badge bg-{{ $order->status_badge_color }} fs-6">{{ $order->status_label }}</span>
</div>

<div class="row g-4">
    {{-- Thông tin đơn hàng --}}
    <div class="col-lg-8">
        <div class="vibe-admin-card mb-4">
            <h5 class="vibe-admin-card-title mb-4">Sản phẩm đặt mua</h5>
            <div class="table-responsive">
                <table class="table vibe-admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                                    @endif
                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $item->size }}</span></td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end vibe-mono">{{ number_format($item->price, 0, '.', ',') }}₫</td>
                            <td class="text-end vibe-mono fw-bold">{{ number_format($item->price * $item->quantity, 0, '.', ',') }}₫</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Tạm tính</td>
                            <td class="text-end vibe-mono">{{ number_format($order->subtotal, 0, '.', ',') }}₫</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="4" class="text-end text-success">Giảm giá ({{ $order->coupon_code }})</td>
                            <td class="text-end vibe-mono text-success">-{{ number_format($order->discount, 0, '.', ',') }}₫</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end">Phí vận chuyển</td>
                            <td class="text-end vibe-mono">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, '.', ',') . '₫' : 'Miễn phí' }}</td>
                        </tr>
                        <tr class="table-dark">
                            <td colspan="4" class="text-end fw-bold">TỔNG CỘNG</td>
                            <td class="text-end vibe-mono fw-bold fs-5">{{ number_format($order->total, 0, '.', ',') }}₫</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Cập nhật trạng thái --}}
        <div class="vibe-admin-card">
            <h5 class="vibe-admin-card-title mb-4">Cập nhật trạng thái</h5>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Trạng thái mới</label>
                        <select name="status" class="form-select" id="status-select">
                            @foreach(\App\Models\Order::$statusLabels as $val => $label)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5" id="cancel-reason-group" style="display:none">
                        <label class="form-label fw-semibold">Lý do hủy</label>
                        <input type="text" name="cancel_reason" class="form-control" placeholder="Nhập lý do...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Cập nhật</button>
                    </div>
                </div>
            </form>

            {{-- Timeline --}}
            <div class="mt-4">
                <h6 class="fw-semibold mb-3">Lịch sử trạng thái</h6>
                <div class="vstack gap-2">
                    @if($order->created_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-warning text-dark">Đặt hàng</span><small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->confirmed_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-info">Xác nhận</span><small class="text-muted">{{ $order->confirmed_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->processed_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-primary">Xử lý</span><small class="text-muted">{{ $order->processed_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->shipped_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-info">Đang giao</span><small class="text-muted">{{ $order->shipped_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->delivered_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-success">Đã giao</span><small class="text-muted">{{ $order->delivered_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->completed_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-success">Hoàn thành</span><small class="text-muted">{{ $order->completed_at->format('d/m/Y H:i') }}</small></div> @endif
                    @if($order->cancelled_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-danger">Đã hủy</span><small class="text-muted">{{ $order->cancelled_at->format('d/m/Y H:i') }}</small>@if($order->cancel_reason)<small class="text-danger ms-2">({{ $order->cancel_reason }})</small>@endif</div> @endif
                    @if($order->returned_at) <div class="d-flex gap-2 align-items-center"><span class="badge bg-secondary">Hoàn trả</span><small class="text-muted">{{ $order->returned_at->format('d/m/Y H:i') }}</small></div> @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Thông tin khách hàng & giao hàng --}}
    <div class="col-lg-4">
        <div class="vibe-admin-card mb-4">
            <h5 class="vibe-admin-card-title mb-3">Thông tin khách hàng</h5>
            @if($order->user)
                <p class="mb-1 fw-semibold">{{ $order->user->name }}</p>
                <p class="mb-1 text-muted">{{ $order->user->email }}</p>
                <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-sm btn-outline-dark mt-2">Xem hồ sơ</a>
            @else
                <p class="text-muted">Khách vãng lai</p>
            @endif
        </div>

        <div class="vibe-admin-card mb-4">
            <h5 class="vibe-admin-card-title mb-3">Địa chỉ giao hàng</h5>
            <p class="mb-1 fw-semibold">{{ $order->shipping_name }}</p>
            <p class="mb-1 text-muted">{{ $order->shipping_phone }}</p>
            <p class="mb-1 text-muted">{{ $order->shipping_email }}</p>
            <p class="mb-0 text-muted">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
        </div>

        <div class="vibe-admin-card">
            <h5 class="vibe-admin-card-title mb-3">Thanh toán</h5>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Phương thức</span>
                <span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            @if($order->notes)
            <div class="mt-3">
                <p class="text-muted mb-1"><small>Ghi chú:</small></p>
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('status-select').addEventListener('change', function() {
    const cancelGroup = document.getElementById('cancel-reason-group');
    cancelGroup.style.display = (this.value === 'cancelled' || this.value === 'returned') ? 'block' : 'none';
});
</script>
@endpush
@endsection
