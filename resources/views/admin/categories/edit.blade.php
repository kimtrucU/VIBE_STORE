@extends('admin.layouts.app')
@section('title', 'Sửa danh mục')
@section('page_title', 'Sửa Danh Mục: ' . $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="vibe-admin-card" style="max-width:600px">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $category->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Slug</label>
            <input type="text" class="form-control" value="{{ $category->slug }}" disabled>
            <small class="text-muted">Tự động cập nhật khi đổi tên</small>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Mô tả</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Ảnh danh mục</label>
            @if($category->image)
                <div class="mb-2">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">Để trống nếu không đổi ảnh</small>
        </div>

        <div class="mb-4 form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                   {{ $category->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Kích hoạt danh mục</label>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark px-4">
                <i class="fas fa-save me-1"></i> Lưu thay đổi
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
