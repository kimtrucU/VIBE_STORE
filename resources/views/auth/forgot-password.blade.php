@extends('layouts.app')
@section('title', 'Khôi phục mật khẩu — Vibe Fashion')

@section('content')
<div class="vibe-section bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="vibe-auth-card bg-white p-5 rounded shadow-sm">
                    <div class="text-center mb-5">
                        <h1 class="vibe-page-title mb-2">Khôi Phục Mật Khẩu</h1>
                        <p class="text-muted">Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="vibe-label">Email của bạn</label>
                            <input type="email" name="email" class="vibe-input" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="text-danger vibe-text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="vibe-btn-dark w-100">Gửi Link Khôi Phục</button>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="vibe-link vibe-text-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
