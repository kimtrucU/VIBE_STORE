@extends('admin.layouts.app')
@section('title', 'Quản lý Coupon')
@section('page_title', 'Mã Giảm Giá (Coupons)')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-1"></i> Thêm mã giảm giá
    </a>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>Mã (Code)</th>
                    <th>Tên chương trình</th>
                    <th>Loại</th>
                    <th>Giảm</th>
                    <th>Đã dùng</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td><span class="badge bg-dark fs-6 font-monospace">{{ $coupon->code }}</span></td>
                    <td class="fw-semibold">{{ $coupon->name }}</td>
                    <td>{{ $coupon->type === 'percent' ? 'Phần trăm (%)' : 'Số tiền cố định' }}</td>
                    <td class="vibe-mono text-danger fw-bold">
                        {{ $coupon->type === 'percent' ? $coupon->value . '%' : number_format($coupon->value, 0, '.', ',') . '₫' }}
                    </td>
                    <td>
                        {{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}
                    </td>
                    <td class="vibe-text-xs">
                        Từ: {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y') : 'Không giới hạn' }}<br>
                        Đến: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Không giới hạn' }}
                    </td>
                    <td>
                        <span class="badge {{ $coupon->isValid() ? 'bg-success' : 'bg-secondary' }}">
                            {{ $coupon->isValid() ? 'Khả dụng' : 'Hết hạn/Vô hiệu' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa coupon này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
        <div class="mt-4">{{ $coupons->links() }}</div>
    @endif
</div>
@endsection
