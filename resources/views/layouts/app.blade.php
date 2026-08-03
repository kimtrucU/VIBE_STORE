<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Vibe Fashion — Official Whenever Partner. Premium minimalist streetwear.">
    <title>@yield('title', 'Vibe Fashion — Official Whenever Partner')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="vibe-body">

    <!-- Promo Ticker -->
    <div id="promo-ticker" class="vibe-promo-ticker">
        VIBE &times; WHENEVER: OFF-WHITE &amp; HEAVYWEIGHT ESSENTIALS &nbsp;&bull;&nbsp; FREE SHIPPING ON ORDERS OVER 500,000&#x20AB;
    </div>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Toast Notifications -->
    @include('components.toast')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="vibe-flash vibe-flash-success d-none" data-message="{{ session('success') }}" data-type="success"></div>
    @endif
    @if(session('error'))
        <div class="vibe-flash vibe-flash-error d-none" data-message="{{ session('error') }}" data-type="error"></div>
    @endif
    @if(session('info'))
        <div class="vibe-flash vibe-flash-info d-none" data-message="{{ session('info') }}" data-type="info"></div>
    @endif

    <!-- Main Content -->
    <main class="vibe-main">
        @yield('content')
    </main>

    <!-- Newsletter Section -->
    @unless(request()->routeIs('admin.*'))
    <section class="vibe-newsletter">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h3 class="vibe-newsletter-title">JOIN THE VIBE CLUB</h3>
                    <p class="vibe-newsletter-desc">Subscribe to get <strong>10% off</strong> your first order and early access to new Whenever drops.</p>
                </div>
                <div class="col-lg-6">
                    <form id="newsletter-form" class="vibe-newsletter-form d-flex gap-2">
                        <input id="newsletter-email-input" type="email" class="vibe-newsletter-input flex-grow-1" placeholder="Enter your email address..." required>
                        <button id="newsletter-submit" type="submit" class="vibe-btn-white">Subscribe</button>
                    </form>
                    <div id="newsletter-success" class="vibe-newsletter-success d-none">
                        <i class="fas fa-check-circle me-1"></i>
                        Thanks! Your voucher code: <strong class="font-mono">VIBEWHENEVER10</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endunless

    <!-- Footer -->
    @include('components.footer')

    <!-- Cart Drawer -->
    @include('components.cart-drawer')

    <!-- Wishlist Drawer -->
    @if(auth()->check())
        @include('components.wishlist-drawer')
    @endif

    <!-- Global Background Music -->
    @php
        $musicDir = public_path('nhac_KIMVIBESTORE');
        $musicFiles = [];
        if (is_dir($musicDir)) {
            $files = \Illuminate\Support\Facades\File::files($musicDir);
            foreach ($files as $file) {
                if (in_array(strtolower($file->getExtension()), ['mp3', 'wav', 'ogg'])) {
                    // Encode URL để xử lý file có dấu tiếng Việt
                    $filename = rawurlencode($file->getFilename());
                    $musicFiles[] = asset('nhac_KIMVIBESTORE/' . $filename);
                }
            }
        }
    @endphp

    @if(count($musicFiles) > 0)
        <!-- Music Player UI -->
        <div id="vibe-music-player" title="Bật/Tắt Nhạc" style="position:fixed;bottom:24px;left:24px;z-index:9999;cursor:pointer;background:#1f2937;color:#fff;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:all 0.3s;" onclick="toggleMusic()">
            <svg id="music-icon" style="width:20px;height:20px;fill:currentColor;" viewBox="0 0 512 512">
                <!-- fa-music path -->
                <path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7v72V268c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6V147L192 223.8V428c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6V200 128c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.5 28.3 5z"/>
            </svg>
        </div>

        <audio id="vibe-bg-music" preload="auto">
            <source src="{{ $musicFiles[0] }}" type="audio/mpeg">
        </audio>

        <style>
            @keyframes spin-slow { 100% { transform: rotate(360deg); } }
            .vibe-music-spin { animation: spin-slow 4s linear infinite; }
            #vibe-music-player:hover { transform: scale(1.1); }
        </style>

        <script>
            const playlist = @json($musicFiles);
            let currentTrack = 0;
            const audio = document.getElementById('vibe-bg-music');
            const musicIcon = document.getElementById('music-icon');
            const playerBtn = document.getElementById('vibe-music-player');
            let isPlaying = false;

            // Đổi bài khi kết thúc
            audio.addEventListener('ended', function() {
                currentTrack = (currentTrack + 1) % playlist.length;
                audio.src = playlist[currentTrack];
                audio.play();
            });

            function toggleMusic(e) {
                if(e) e.stopPropagation();
                if (isPlaying) {
                    audio.pause();
                    musicIcon.classList.remove('vibe-music-spin');
                    playerBtn.style.background = '#6b7280'; // xám khi tắt
                } else {
                    audio.play();
                    musicIcon.classList.add('vibe-music-spin');
                    playerBtn.style.background = '#000';
                }
                isPlaying = !isPlaying;
            }

            // Tự động play (hoặc chờ user click đầu tiên nếu trình duyệt chặn autoplay)
            document.addEventListener('DOMContentLoaded', () => {
                // Đặt volume vừa phải
                audio.volume = 0.5;
                
                const playPromise = audio.play();
                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        isPlaying = true;
                        musicIcon.classList.add('vibe-music-spin');
                        playerBtn.style.background = '#000';
                    }).catch(error => {
                        // Trình duyệt chặn autoplay, chờ click bất kỳ vào trang
                        playerBtn.style.background = '#6b7280';
                        const startOnInteraction = () => {
                            if (!isPlaying) {
                                audio.play().then(() => {
                                    isPlaying = true;
                                    musicIcon.classList.add('vibe-music-spin');
                                    playerBtn.style.background = '#000';
                                }).catch(e => console.log('Audio error:', e));
                            }
                            document.removeEventListener('click', startOnInteraction);
                        };
                        document.addEventListener('click', startOnInteraction);
                    });
                }
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>
