@extends('admin.layouts.app')
@section('title', 'Thêm Coupon')
@section('page_title', 'Tạo Mã Giảm Giá Mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="vibe-admin-card" style="max-width:700px">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Mã (Code) <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
                       value="{{ old('code') }}" placeholder="VD: SUMMER2024" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tên chương trình <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="VD: Khuyến mãi Hè 2024" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Mức giảm <span class="text-danger">*</span></label>
                <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                       value="{{ old('value') }}" min="0" step="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Giảm tối đa (₫)</label>
                <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}" min="0" placeholder="Chỉ dùng cho loại %">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Giá trị đơn tối thiểu (₫)</label>
                <input type="number" name="min_order" class="form-control" value="{{ old('min_order', 0) }}" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Giới hạn số lượt dùng</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="1" placeholder="Để trống = Không giới hạn">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày bắt đầu</label>
                <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày kết thúc</label>
                <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
            </div>
        </div>

        <div class="mb-4 form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
            <label class="form-check-label" for="is_active">Kích hoạt mã này ngay lập tức</label>
        </div>

        <button type="submit" class="btn btn-dark px-4">
            <i class="fas fa-plus me-1"></i> Tạo mã giảm giá
        </button>
    </form>
</div>
@endsection
