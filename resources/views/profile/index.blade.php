@extends('layouts.app')
@section('title', 'My Profile — Vibe Fashion')

@section('content')
<div class="vibe-section">
    <div class="container-xl">
        <h1 class="vibe-page-title mb-6">My Profile</h1>

        <div class="row g-5">
            {{-- Left: Profile Form --}}
            <div class="col-lg-7">
                {{-- Personal Info --}}
                <div class="vibe-form-card mb-4">
                    <h2 class="vibe-form-section-title">Personal Information</h2>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 text-center mb-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-dark d-inline-flex align-items-center justify-content-center text-white mb-2" style="width: 100px; height: 100px; font-size: 2rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <label for="avatar" class="vibe-link vibe-text-sm" style="cursor: pointer;">Change Avatar</label>
                                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Full Name</label>
                                <input type="text" name="name" class="vibe-input"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Email</label>
                                <input type="email" name="email" class="vibe-input"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Phone Number</label>
                                <input type="tel" name="phone" class="vibe-input"
                                    value="{{ old('phone', $user->phone) }}" placeholder="e.g., 0901234567">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">City/Province</label>
                                <input type="text" name="city" class="vibe-input"
                                    value="{{ old('city', $user->city) }}" placeholder="e.g., Ho Chi Minh City">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Default Address</label>
                                <input type="text" name="address" class="vibe-input"
                                    value="{{ old('address', $user->address) }}" placeholder="House Number, Street Name, Ward/Commune...">
                            </div>
                        </div>
                        <button type="submit" class="vibe-btn-dark mt-4">Save Changes</button>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="vibe-form-card">
                    <h2 class="vibe-form-section-title">Change Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="vibe-label">Current Password</label>
                                <input type="password" name="current_password" class="vibe-input" required>
                                @error('current_password')
                                    <div class="text-danger vibe-text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">New Password</label>
                                <input type="password" name="password" class="vibe-input" placeholder="Minimum 8 characters" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="vibe-input" required>
                            </div>
                        </div>
                        <button type="submit" class="vibe-btn-dark mt-4">Update Password</button>
                    </form>
                </div>
            </div>

            {{-- Right: Recent Orders --}}
            <div class="col-lg-5">
                <div class="vibe-form-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="vibe-form-section-title mb-0">Recent Orders</h2>
                        <a href="{{ route('orders.index') }}" class="vibe-link vibe-text-xs">View All</a>
                    </div>

                    @if($orders->isEmpty())
                        <p class="text-muted fst-italic vibe-text-sm">No orders yet.</p>
                    @else
                        <div class="vstack gap-3">
                        @foreach($orders as $order)
                        <div class="vibe-order-mini p-3 border rounded" style="border-color: #eee !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="vibe-mono vibe-text-sm fw-bold mb-0">#{{ $order->order_number }}</p>
                                    <p class="text-muted vibe-text-xs mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_label }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <p class="vibe-mono fw-bold text-dark mb-0">{{ number_format($order->total, 0, '.', ',') }}₫</p>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark vibe-text-xs">Details</a>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
