@extends('admin.layouts.app')
@section('title', 'Quản lý tài khoản')
@section('page_title', 'Tài Khoản Hệ Thống')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex gap-2 flex-grow-1" style="max-width: 500px">
        <form action="{{ route('admin.users.index') }}" method="GET" class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email..." value="{{ request('search') }}">
            <select name="role" class="form-select" style="max-width: 150px">
                <option value="">Tất cả vai trò</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-1"></i> Tạo tài khoản
    </a>
</div>

<div class="vibe-admin-card">
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Vai trò</th>
                    <th>Số điện thoại</th>
                    <th>Đơn hàng</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                            @else
                                <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;font-size:14px">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'bg-danger' : 'bg-info' }}">
                            {{ ucfirst($user->role->name ?? 'User') }}
                        </span>
                    </td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->orders_count }}</td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-edit"></i></a>
                            
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                    <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy tài khoản nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</div>
@endsection
