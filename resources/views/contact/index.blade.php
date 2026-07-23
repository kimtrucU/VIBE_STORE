@extends('layouts.app')
@section('title', 'Contact Us — VIBE Store')

@section('content')
<div class="vibe-section">
    <div class="container-xl">
        <div class="text-center mb-10">
            <span class="vibe-eyebrow">Get in Touch</span>
            <h1 class="vibe-page-title">CONTACT US</h1>
        </div>

        <div class="row g-5">
            {{-- Form --}}
            <div class="col-lg-6">
                <div class="vibe-form-card">
                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="vibe-label">Full Name</label>
                                <input type="text" name="name" class="vibe-input" required
                                    value="{{ old('name') }}" placeholder="Your name">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Email Address</label>
                                <input type="email" name="email" class="vibe-input" required
                                    value="{{ old('email') }}" placeholder="your@email.com">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Subject</label>
                                <input type="text" name="subject" class="vibe-input" required
                                    value="{{ old('subject') }}" placeholder="Order inquiry, size chart, etc.">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Message</label>
                                <textarea name="message" rows="5" class="vibe-input vibe-textarea" required
                                    placeholder="Tell us how we can help...">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="vibe-btn-dark w-100 mt-4">Send Message</button>
                    </form>
                </div>
            </div>

            {{-- Info --}}
            <div class="col-lg-6">
                <div class="vibe-contact-info">
                    <h2 class="vibe-form-section-title">Flagship Showroom</h2>
                    <div class="vibe-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Address</strong>
                            <p class="text-muted">86 Fashion Street, District 1<br>Ho Chi Minh City, Vietnam</p>
                        </div>
                    </div>
                    <div class="vibe-contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Hotline</strong>
                            <p class="text-muted">1900 8198 (Mon–Fri 9:00–21:00)</p>
                        </div>
                    </div>
                    <div class="vibe-contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <p class="text-muted">support@vibe.com</p>
                        </div>
                    </div>
                    <div class="vibe-contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Business Hours</strong>
                            <p class="text-muted">Mon – Sun: 9:00 AM – 9:00 PM</p>
                        </div>
                    </div>
                    <div class="vibe-map-placeholder mt-4">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <p class="text-muted vibe-text-sm">📍 Google Maps integration available in production</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
