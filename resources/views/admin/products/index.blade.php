@extends('admin.layouts.app')
@section('title', 'Manage Products')
@section('page_title', 'Product List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="vibe-input" placeholder="Search products..." value="{{ request('search') }}" style="max-width:250px">
        <select name="category" class="vibe-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="vibe-btn-dark">Search</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="vibe-btn-dark">
        <i class="fas fa-plus me-1"></i> Add Product
    </a>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <img src="{{ $product->primary_image }}" alt="{{ $product->name }}"
                             style="width:50px;height:60px;object-fit:cover;border-radius:4px;">
                    </td>
                    <td>
                        <p class="mb-0 fw-semibold">{{ $product->name }}</p>
                        @if($product->is_new_arrival) <span class="badge bg-dark vibe-text-xs">New</span> @endif
                        @if($product->is_best_seller) <span class="badge bg-secondary vibe-text-xs">Best</span> @endif
                    </td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td class="vibe-mono">{{ number_format($product->price, 0, '.', ',') }}₫</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection
