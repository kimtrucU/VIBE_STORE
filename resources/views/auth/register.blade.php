@extends('layouts.app')
@section('title', 'Create Account — VIBE Store')

@section('content')
<div class="vibe-auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="vibe-auth-card">
                    <div class="text-center mb-5">
                        <span class="vibe-logo">VIBE</span>
                        <h1 class="vibe-auth-title mt-3">Create Account</h1>
                        <p class="text-muted vibe-text-sm">Join the VIBE Club and get 10% off your first order</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger vibe-text-sm">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="vibe-label">Full Name</label>
                            <input type="text" name="name" required
                                value="{{ old('name') }}"
                                class="vibe-input" placeholder="John Smith" autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="vibe-label">Email Address</label>
                            <input type="email" name="email" required
                                value="{{ old('email') }}"
                                class="vibe-input" placeholder="john@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="vibe-label">Password</label>
                            <input type="password" name="password" required
                                class="vibe-input" placeholder="Min. 8 characters">
                        </div>
                        <div class="mb-4">
                            <label class="vibe-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                class="vibe-input" placeholder="Repeat your password">
                        </div>
                        <button type="submit" class="vibe-btn-dark w-100">
                            Create Account <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-4 vibe-text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="vibe-link">Log In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
