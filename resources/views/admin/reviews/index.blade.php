@extends('admin.layouts.app')
@section('title', 'Đánh giá sản phẩm')
@section('page_title', 'Đánh Giá Sản Phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm {{ !request()->has('approved') ? 'btn-dark' : 'btn-outline-dark' }}">Tất cả</a>
        <a href="{{ route('admin.reviews.index', ['approved' => '0']) }}" class="btn btn-sm {{ request('approved') === '0' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Chờ duyệt</a>
        <a href="{{ route('admin.reviews.index', ['approved' => '1']) }}" class="btn btn-sm {{ request('approved') === '1' ? 'btn-success' : 'btn-outline-success' }}">Đã duyệt</a>
    </div>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Đánh giá</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td>
                        <p class="mb-0 fw-semibold">{{ $review->user->name ?? 'User Deleted' }}</p>
                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        @if($review->product)
                            <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-dark fw-semibold text-decoration-none">
                                {{ Str::limit($review->product->name, 30) }}
                            </a>
                        @else
                            <span class="text-muted">Sản phẩm đã xóa</span>
                        @endif
                    </td>
                    <td class="text-warning">
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                        @endfor
                    </td>
                    <td>
                        @if($review->title)<p class="mb-1 fw-bold">{{ $review->title }}</p>@endif
                        <p class="mb-0 vibe-text-sm">{{ Str::limit($review->comment, 80) }}</p>
                    </td>
                    <td>
                        <span class="badge {{ $review->is_approved ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $review->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-success" title="Duyệt"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Chưa có đánh giá nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
