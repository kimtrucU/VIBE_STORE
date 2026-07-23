@extends('admin.layouts.app')
@section('title', 'Quản lý kho hàng')
@section('page_title', 'Kho Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-dark' : 'btn-outline-dark' }}">Tất cả</a>
        <a href="{{ route('admin.inventory.index', ['filter' => 'low']) }}" class="btn btn-sm {{ $filter === 'low' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Sắp hết (< 10)</a>
        <a href="{{ route('admin.inventory.index', ['filter' => 'out']) }}" class="btn btn-sm {{ $filter === 'out' ? 'btn-danger' : 'btn-outline-danger' }}">Hết hàng</a>
    </div>
    
    <form action="{{ route('admin.inventory.index') }}" method="GET" class="input-group" style="max-width: 300px">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <input type="text" name="search" class="form-control" placeholder="Tìm tên sản phẩm..." value="{{ request('search') }}">
        <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th class="text-center">Tồn kho hiện tại</th>
                    <th class="text-center">Cập nhật nhanh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $product->primary_image }}" alt="{{ $product->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px">
                            <div>
                                <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-dark text-decoration-none fw-semibold">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                                @if(!$product->is_active)
                                    <span class="badge bg-secondary ms-2" style="font-size:10px">Đã ẩn</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-center">
                        @if($product->stock == 0)
                            <span class="badge bg-danger fs-6">{{ $product->stock }}</span>
                        @elseif($product->stock <= 10)
                            <span class="badge bg-warning text-dark fs-6">{{ $product->stock }}</span>
                        @else
                            <span class="badge bg-success fs-6">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.inventory.stock', $product) }}" method="POST" class="d-flex align-items-center justify-content-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="stock" class="form-control form-control-sm text-center" style="width:80px" value="{{ $product->stock }}" min="0" required>
                            <button type="submit" class="btn btn-sm btn-dark"><i class="fas fa-save"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
