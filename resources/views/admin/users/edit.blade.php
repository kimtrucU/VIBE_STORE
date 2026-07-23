@extends('admin.layouts.app')
@section('title', 'Sửa tài khoản')
@section('page_title', 'Sửa Tài Khoản: ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="vibe-admin-card" style="max-width:600px">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $user->phone) }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required 
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @if($user->id === auth()->id())
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <small class="text-muted">Bạn không thể thay đổi vai trò của chính mình.</small>
            @endif
            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <hr class="my-4">
        
        <h6 class="fw-bold mb-3">Đổi mật khẩu (Bỏ trống nếu không muốn đổi)</h6>
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8">
            </div>
        </div>

        <button type="submit" class="btn btn-dark px-4">
            <i class="fas fa-save me-1"></i> Lưu thay đổi
        </button>
    </form>
</div>
@endsection
