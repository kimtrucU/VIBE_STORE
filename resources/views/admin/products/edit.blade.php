@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa sản phẩm')
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="vibe-admin-card mb-4">
                <h5 class="vibe-admin-card-title mb-4">Thông tin cơ bản</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thương hiệu</label>
                        <select name="brand_id" class="form-select">
                            <option value="">-- Không có --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả ngắn <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Chi tiết sản phẩm (mỗi dòng 1 mục)</label>
                    <textarea name="details" class="form-control" rows="5" placeholder="100% Cotton&#10;Made in Vietnam&#10;...">{{ old('details', is_array($product->details) ? implode("\n", $product->details) : $product->details) }}</textarea>
                </div>
            </div>

            <div class="vibe-admin-card mb-4">
                <h5 class="vibe-admin-card-title mb-4">Ảnh sản phẩm</h5>
                @if(!empty($product->images))
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @foreach($product->images as $img)
                            <img src="{{ $img }}" alt="Product" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #eee">
                        @endforeach
                    </div>
                @endif
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                <small class="text-muted">Thêm ảnh mới (ảnh cũ sẽ được giữ lại). JPG, PNG, WebP, tối đa 5MB/ảnh</small>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="vibe-admin-card mb-4">
                <h5 class="vibe-admin-card-title mb-4">Giá & Tồn kho</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giá bán (VND) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $product->price) }}" required min="0" step="1000">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giá gốc (VND)</label>
                    <input type="number" name="original_price" class="form-control"
                           value="{{ old('original_price', $product->original_price) }}" min="0" step="1000">
                    <small class="text-muted">Để trống nếu không có giảm giá</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                           value="{{ old('stock', $product->stock) }}" required min="0">
                </div>
            </div>

            <div class="vibe-admin-card mb-4">
                <h5 class="vibe-admin-card-title mb-3">Kích cỡ</h5>
                @foreach(['XS','S','M','L','XL','XXL','XXXL','Free Size'] as $size)
                    <div class="form-check mb-1">
                        <input type="checkbox" name="sizes[]" value="{{ $size }}" class="form-check-input"
                               id="size_{{ $size }}"
                               {{ in_array($size, old('sizes', $product->sizes ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="size_{{ $size }}">{{ $size }}</label>
                    </div>
                @endforeach
            </div>

            <div class="vibe-admin-card mb-4">
                <h5 class="vibe-admin-card-title mb-3">Trạng thái</h5>

                <div class="form-check mb-2">
                    <input type="hidden" name="is_new_arrival" value="0">
                    <input type="checkbox" name="is_new_arrival" value="1" class="form-check-input" id="is_new_arrival"
                           {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_new_arrival">Hàng mới về</label>
                </div>

                <div class="form-check mb-2">
                    <input type="hidden" name="is_best_seller" value="0">
                    <input type="checkbox" name="is_best_seller" value="1" class="form-check-input" id="is_best_seller"
                           {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_best_seller">Bán chạy</label>
                </div>

                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Kích hoạt (hiển thị)</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-dark">
                    <i class="fas fa-save me-1"></i> Lưu thay đổi
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </div>
    </div>
</form>
@endsection
