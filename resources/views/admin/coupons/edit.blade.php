@extends('admin.layouts.app')
@section('title', 'Sửa Coupon')
@section('page_title', 'Sửa Mã Giảm Giá: ' . $coupon->code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="vibe-admin-card" style="max-width:700px">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Mã (Code)</label>
                <input type="text" class="form-control text-uppercase font-monospace" value="{{ $coupon->code }}" disabled>
                <small class="text-muted">Không thể sửa mã sau khi tạo</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tên chương trình <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $coupon->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Mức giảm <span class="text-danger">*</span></label>
                <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                       value="{{ old('value', $coupon->value) }}" min="0" step="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Giảm tối đa (₫)</label>
                <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount', $coupon->max_discount) }}" min="0">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Giá trị đơn tối thiểu (₫)</label>
                <input type="number" name="min_order" class="form-control" value="{{ old('min_order', $coupon->min_order) }}" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Giới hạn số lượt dùng</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày bắt đầu</label>
                <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày kết thúc</label>
                <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
            </div>
        </div>

        <div class="mb-4 form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                   {{ $coupon->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Kích hoạt mã này</label>
        </div>

        <button type="submit" class="btn btn-dark px-4">
            <i class="fas fa-save me-1"></i> Lưu thay đổi
        </button>
    </form>
</div>
@endsection
