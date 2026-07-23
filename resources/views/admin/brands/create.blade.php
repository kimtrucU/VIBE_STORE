@extends('admin.layouts.app')
@section('title', 'Thêm thương hiệu')
@section('page_title', 'Thêm Thương Hiệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="vibe-admin-card" style="max-width:600px">
    <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label fw-semibold">Tên thương hiệu <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="VD: Nike, Adidas..." required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Mô tả</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Logo</label>
            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Định dạng hỗ trợ: JPG, PNG, SVG. Tối đa 2MB.</small>
            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4 form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
            <label class="form-check-label" for="is_active">Kích hoạt thương hiệu</label>
        </div>

        <button type="submit" class="btn btn-dark px-4">
            <i class="fas fa-plus me-1"></i> Tạo thương hiệu
        </button>
    </form>
</div>
@endsection
