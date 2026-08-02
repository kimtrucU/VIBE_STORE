@extends('admin.layouts.app')
@section('title', 'Cài đặt hệ thống')
@section('page_title', 'Cài Đặt Hệ Thống')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="vibe-admin-card">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="vibe-admin-card-title mb-4 border-bottom pb-2">Thông tin chung</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tên website (Site Name) <span class="text-danger">*</span></label>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name']->value ?? '') }}" required>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email liên hệ <span class="text-danger">*</span></label>
                        <input type="email" name="site_email" class="form-control" value="{{ old('site_email', $settings['site_email']->value ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số điện thoại liên hệ</label>
                        <input type="text" name="site_phone" class="form-control" value="{{ old('site_phone', $settings['site_phone']->value ?? '') }}">
                    </div>
                </div>

                <h5 class="vibe-admin-card-title mb-4 mt-5 border-bottom pb-2">Vận chuyển & Thanh toán</h5>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phí giao hàng mặc định (₫) <span class="text-danger">*</span></label>
                        <input type="number" name="shipping_fee" class="form-control" value="{{ old('shipping_fee', $settings['shipping_fee']->value ?? 30000) }}" required min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngưỡng miễn phí vận chuyển (₫) <span class="text-danger">*</span></label>
                        <input type="number" name="free_shipping_threshold" class="form-control" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']->value ?? 500000) }}" required min="0">
                        <small class="text-muted">Đơn hàng lớn hơn mức này sẽ được freeship</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark px-4 mt-3">
                    <i class="fas fa-save me-1"></i> Lưu cài đặt
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="vibe-admin-card bg-light border-0">
            <h5 class="vibe-admin-card-title mb-3">Thông tin hệ thống</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><strong>Phiên bản Laravel:</strong> {{ app()->version() }}</li>
                <li class="mb-2"><strong>Phiên bản PHP:</strong> {{ phpversion() }}</li>
                <li class="mb-2"><strong>Môi trường:</strong> {{ app()->environment() }}</li>
                <li class="mb-2"><strong>Database:</strong> MySQL</li>
                <li class="mb-2"><strong>Timezone:</strong> {{ config('app.timezone') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
