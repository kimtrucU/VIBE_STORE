@extends('layouts.app')
@section('title', 'About VIBE — Official Whenever Partner')

@section('content')
{{-- Hero --}}
<section class="vibe-about-hero">
    <div class="vibe-hero-bg">
        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=1600" alt="VIBE Store" class="vibe-hero-img">
        <div class="vibe-hero-overlay"></div>
    </div>
    <div class="container position-relative">
        <div class="vibe-about-hero-content text-center">
            <span class="vibe-eyebrow-white">Our Story</span>
            <h1 class="vibe-hero-title">VIBE × WHENEVER</h1>
            <p class="vibe-hero-desc mx-auto" style="max-width:500px">
                The official VIBE store, the exclusive authentic partner of Whenever — delivering premium minimalist streetwear to a new generation of fashion-forward individuals.
            </p>
        </div>
    </div>
</section>

{{-- Brand Values --}}
<section class="vibe-section vibe-section-white">
    <div class="container-xl">
        <div class="text-center mb-10">
            <span class="vibe-eyebrow">Our Values</span>
            <h2 class="vibe-section-title">WHAT WE STAND FOR</h2>
            <div class="vibe-divider mx-auto mt-3"></div>
        </div>
        <div class="row g-5 text-center">
            <div class="col-md-4">
                <div class="vibe-value-card">
                    <div class="vibe-value-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="vibe-value-title">100% AUTHENTIC</h3>
                    <p class="text-muted">Every product carries a genuine Whenever embossed tag, purchased directly from the brand — never replicas, never compromises.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vibe-value-card">
                    <div class="vibe-value-icon"><i class="fas fa-palette"></i></div>
                    <h3 class="vibe-value-title">MINIMAL DESIGN</h3>
                    <p class="text-muted">Clean lines, neutral tones, and purposeful details define the Whenever aesthetic — fashion that endures beyond seasonal trends.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vibe-value-card">
                    <div class="vibe-value-icon"><i class="fas fa-gem"></i></div>
                    <h3 class="vibe-value-title">PREMIUM QUALITY</h3>
                    <p class="text-muted">250gsm heavyweight French terry cotton, YKK zippers, and meticulous stitching — built to outlast fast fashion by decades.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="vibe-highlight">
    <div class="container-xl">
        <div class="row g-5 text-center">
            <div class="col-6 col-md-3">
                <p class="vibe-stat-big">2,500+</p>
                <p class="text-secondary vibe-text-sm">Orders Delivered</p>
            </div>
            <div class="col-6 col-md-3">
                <p class="vibe-stat-big">26</p>
                <p class="text-secondary vibe-text-sm">Exclusive Products</p>
            </div>
            <div class="col-6 col-md-3">
                <p class="vibe-stat-big">4.9★</p>
                <p class="text-secondary vibe-text-sm">Average Rating</p>
            </div>
            <div class="col-6 col-md-3">
                <p class="vibe-stat-big">48h</p>
                <p class="text-secondary vibe-text-sm">Delivery Window</p>
            </div>
        </div>
    </div>
</section>
@endsection
