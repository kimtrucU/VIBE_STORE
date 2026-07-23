@extends('admin.layouts.app')
@section('title', 'Add Product')
@section('page_title', 'Add Product Mới')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Info -->
                    <div class="mb-4">
                        <h5 class="mb-3">Basic Info</h5>
                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categories <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Select category --</option>
                                    @foreach(\App\Models\Category::all() as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Detailed Description</label>
                            <textarea name="details" class="form-control" rows="5">{{ old('details') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Price và Stock -->
                    <div class="mb-4">
                        <h5 class="mb-3">Price & Stock</h5>
                        <div class="mb-3">
                            <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" required min="0">
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="mb-4">
                        <h5 class="mb-3">Images</h5>
                        <div class="mb-3">
                            <label class="form-label">Featured Image <span class="text-danger">*</span></label>
                            <input type="file" name="primary_image" class="form-control @error('primary_image') is-invalid @enderror" accept="image/*" required>
                        </div>
                    </div>

                    <!-- Other Options -->
                    <div class="mb-4">
                        <h5 class="mb-3">Options</h5>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active (Visible)</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" id="isNew" value="1" {{ old('is_new_arrival') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isNew">New Product</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_best_seller" id="isBest" value="1" {{ old('is_best_seller') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isBest">Best Seller</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
