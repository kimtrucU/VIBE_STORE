@extends('admin.layouts.app')
@section('title', 'Quản lý thương hiệu')
@section('page_title', 'Thương Hiệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.brands.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-1"></i> Thêm thương hiệu
    </a>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Tên</th>
                    <th class="text-center">Sản phẩm</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td>
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" style="width:48px;height:48px;object-fit:contain;border:1px solid #eee;border-radius:6px;padding:4px">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width:48px;height:48px;border-radius:6px;font-size:0.8rem">No Logo</div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $brand->name }}</td>
                    <td class="text-center">{{ $brand->products_count }}</td>
                    <td>
                        <span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $brand->is_active ? 'Hiển thị' : 'Đã ẩn' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Chưa có thương hiệu nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($brands->hasPages())
        <div class="mt-4">{{ $brands->links() }}</div>
    @endif
</div>
@endsection
