@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu — Vibe Fashion')

@section('content')
<div class="vibe-section bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="vibe-auth-card bg-white p-5 rounded shadow-sm">
                    <div class="text-center mb-5">
                        <h1 class="vibe-page-title mb-2">Đặt Lại Mật Khẩu</h1>
                        <p class="text-muted">Vui lòng nhập mật khẩu mới của bạn.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token ?? '' }}">
                        <input type="hidden" name="email" value="{{ request('email') }}">
                        
                        <div class="mb-4">
                            <label class="vibe-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="vibe-input" required autofocus minlength="8">
                            @error('password')
                                <div class="text-danger vibe-text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="vibe-label">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="vibe-input" required minlength="8">
                        </div>

                        <button type="submit" class="vibe-btn-dark w-100">Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
