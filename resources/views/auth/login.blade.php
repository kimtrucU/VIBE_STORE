@extends('layouts.app')
@section('title', 'Login — VIBE Store')

@section('content')
<div class="vibe-auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="vibe-auth-card">
                    <div class="text-center mb-5">
                        <span class="vibe-logo">VIBE</span>
                        <h1 class="vibe-auth-title mt-3">Welcome Back</h1>
                        <p class="text-muted vibe-text-sm">Log in to access your VIBE account</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger vibe-text-sm">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="vibe-label">Email Address</label>
                            <input type="email" name="email" required
                                value="{{ old('email') }}"
                                class="vibe-input" placeholder="john@example.com" autofocus>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="vibe-label">Password</label>
                                <a href="{{ route('password.request') }}" class="vibe-link vibe-text-xs">Forgot password?</a>
                            </div>
                            <input type="password" name="password" required
                                class="vibe-input" placeholder="••••••••">
                        </div>
                        <div class="mb-4">
                            <label class="d-flex align-items-center gap-2 vibe-text-sm">
                                <input type="checkbox" name="remember" class="form-check-input"> Remember me
                            </label>
                        </div>
                        <button type="submit" class="vibe-btn-dark w-100">
                            Log In <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-4 vibe-text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="vibe-link">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
