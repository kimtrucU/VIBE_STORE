@extends('layouts.app')
@section('title', 'Checkout — VIBE Store')

@section('content')
<div id="checkout-view" class="vibe-section">
    <div class="container-xl">

        <div class="pb-4 mb-5 border-bottom">
            <h1 class="vibe-page-title">Checkout</h1>
            <p class="vibe-page-subtitle">Ensure your delivery address is correct so Whenever can ship your authenticated order.</p>
        </div>

        <div class="row g-5">
            {{-- Checkout Form --}}
            <div class="col-lg-7">
                <div class="vibe-form-card">
                    <h2 class="vibe-form-section-title">1. Shipping Information</h2>

                    <form id="checkout-main-form" method="POST" action="{{ route('checkout.store') }}">
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="vibe-label">Full Name *</label>
                                <input type="text" name="shipping_name" required
                                    value="{{ old('shipping_name', $user->name ?? '') }}"
                                    class="vibe-input" placeholder="John Smith">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Phone Number *</label>
                                <input type="tel" name="shipping_phone" required
                                    value="{{ old('shipping_phone', $user->phone ?? '') }}"
                                    class="vibe-input" placeholder="+1 (555) 123-4567">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">Email Address *</label>
                                <input type="email" name="shipping_email" required
                                    value="{{ old('shipping_email', $user->email ?? '') }}"
                                    class="vibe-input" placeholder="john@example.com">
                            </div>
                            <div class="col-sm-6">
                                <label class="vibe-label">City / Province *</label>
                                <input type="text" name="shipping_city" required
                                    value="{{ old('shipping_city', $user->city ?? '') }}"
                                    class="vibe-input" placeholder="Ho Chi Minh City">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Delivery Address *</label>
                                <input type="text" name="shipping_address" required
                                    value="{{ old('shipping_address', $user->address ?? '') }}"
                                    class="vibe-input" placeholder="House no., street name, ward, district">
                            </div>
                            <div class="col-12">
                                <label class="vibe-label">Delivery Notes (Optional)</label>
                                <textarea name="notes" rows="3" class="vibe-input vibe-textarea"
                                    placeholder="Leave at door / Ring bell / Special instructions...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mt-5">
                            <h2 class="vibe-form-section-title">2. Payment Method</h2>
                            <div class="row g-3">
                                {{-- COD --}}
                                <div class="col-sm-6 col-lg-4">
                                    <label class="vibe-payment-option {{ old('payment_method', 'COD') === 'COD' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="COD"
                                            {{ old('payment_method', 'COD') === 'COD' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <span style="font-size:20px;">🚚</span>
                                        <span class="fw-bold mt-1">Cash on Delivery</span>
                                        <span class="vibe-text-xs text-muted mt-1">Trả tiền khi nhận hàng</span>
                                    </label>
                                </div>
                                {{-- Bank Transfer --}}
                                <div class="col-sm-6 col-lg-4">
                                    <label class="vibe-payment-option {{ old('payment_method') === 'bank_transfer' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="bank_transfer"
                                            {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <span style="font-size:20px;">🏦</span>
                                        <span class="fw-bold text-primary mt-1">Bank Transfer</span>
                                        <span class="vibe-text-xs text-muted mt-1">QR Mobile Banking</span>
                                    </label>
                                </div>
                                {{-- SePay --}}
                                <div class="col-sm-6 col-lg-4">
                                    <label class="vibe-payment-option vibe-payment-sepay {{ old('payment_method') === 'sepay' ? 'active' : '' }}">
                                        <input type="radio" name="payment_method" value="sepay"
                                            {{ old('payment_method') === 'sepay' ? 'checked' : '' }}
                                            class="vibe-radio-hidden" onchange="updatePaymentSelection(this)">
                                        <img src="https://sepay.vn/img/logo.png"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                                             style="height:26px;object-fit:contain;" alt="SePay">
                                        <span style="display:none;align-items:center;gap:4px;font-weight:700;color:#f97316;font-size:15px;">⚡ SePay</span>
                                        <span class="fw-bold mt-1" style="color:#f97316;">SePay</span>
                                        <span class="vibe-text-xs text-muted mt-1">Thanh toán tự động</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button id="checkout-order-now" type="button" class="vibe-btn-dark w-100 vibe-btn-lg" onclick="handleCheckoutSubmit()">
                                Confirm &amp; Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-5">
                <div class="vibe-form-card mb-4">
                    <h3 class="vibe-form-section-title">Order Summary ({{ $cart->items->count() }} items)</h3>

                    @if($cart->items->isEmpty())
                        <p class="text-muted fst-italic">Your cart is empty.</p>
                    @else
                        <div class="vibe-checkout-items">
                            @foreach($cart->items as $item)
                            <div class="vibe-checkout-item">
                                <div class="vibe-checkout-item-img">
                                    <img src="{{ $item->product->primary_image }}" alt="{{ $item->product->name }}">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h4 class="vibe-checkout-item-name">{{ $item->product->name }}</h4>
                                    <p class="vibe-text-xs vibe-mono text-muted">SIZE: {{ $item->size }} / QTY: {{ $item->quantity }}</p>
                                </div>
                                <div class="vibe-checkout-item-price vibe-mono">
                                    {{ number_format($item->product->price * $item->quantity, 0, '.', ',') }}₫
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <div class="vibe-price-row">
                                <span>Subtotal</span>
                                <span class="vibe-mono">{{ number_format($cart->subtotal, 0, '.', ',') }}₫</span>
                            </div>
                            <div class="vibe-price-row">
                                <span>Shipping</span>
                                <span class="vibe-mono">{{ $cart->shipping_fee > 0 ? number_format($cart->shipping_fee, 0, '.', ',') . '₫' : 'Free' }}</span>
                            </div>
                            <div class="vibe-price-row vibe-price-total border-top pt-2 mt-2">
                                <span>Total</span>
                                <span class="vibe-mono">{{ number_format($cart->grand_total, 0, '.', ',') }}₫</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Trust Badge --}}
                <div class="vibe-trust-card">
                    <h4 class="vibe-trust-title">VIBE Exclusive Guarantee</h4>
                    <p class="vibe-text-xs text-muted">
                        All orders are checked for authentic Whenever tags before sealing. COD customers may inspect items before payment.
                    </p>
                    <div class="vibe-secure-badge mt-2">
                        <i class="fas fa-shield-alt me-1"></i> SECURE WHENEVER CODES VERIFIED
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ═══════════════════════════════════════════════════════════
     PAYMENT QR MODAL
═══════════════════════════════════════════════════════════ --}}

{{-- Overlay backdrop --}}
<div id="payment-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:9998;backdrop-filter:blur(4px);" onclick="closePaymentModal()"></div>

{{-- Modal --}}
<div id="payment-qr-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;width:min(480px,92vw);background:#fff;border-radius:20px;box-shadow:0 32px 80px rgba(0,0,0,.25);overflow:hidden;">

    {{-- Header --}}
    <div id="modal-header" style="padding:20px 24px 16px;background:#1a1a1a;color:#fff;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div id="modal-icon" style="width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;"></div>
            <div>
                <div id="modal-title" style="font-weight:700;font-size:15px;letter-spacing:.5px;"></div>
                <div id="modal-subtitle" style="font-size:11px;opacity:.7;margin-top:1px;"></div>
            </div>
        </div>
        <button onclick="closePaymentModal()" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    {{-- Body --}}
    <div style="padding:24px;">

        {{-- Amount info --}}
        <div style="background:#f8f8f8;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;color:#666;">Số tiền cần thanh toán</span>
            <span id="modal-amount" style="font-size:20px;font-weight:800;color:#1a1a1a;font-family:monospace;"></span>
        </div>

        {{-- QR Code --}}
        <div style="text-align:center;margin-bottom:20px;">
            <div style="display:inline-block;background:#fff;border:2px solid #f0f0f0;border-radius:16px;padding:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);">
                <img id="modal-qr-img" src="" alt="QR Code" style="width:220px;height:220px;border-radius:8px;display:block;">
            </div>
            <p id="modal-instruction" style="margin-top:12px;font-size:12px;color:#888;line-height:1.5;"></p>
        </div>

        {{-- Bank Info (SePay only) --}}
        <div id="bank-info-box" style="display:none;background:#f0f6ff;border:1px solid #d0e4ff;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
            <div style="font-size:12px;color:#1a56db;font-weight:700;margin-bottom:10px;letter-spacing:.5px;">THÔNG TIN CHUYỂN KHOẢN</div>
            <div style="display:grid;grid-template-columns:auto 1fr;gap:6px 12px;font-size:13px;">
                <span style="color:#666;">Ngân hàng:</span>  <span id="bk-bank" style="font-weight:600;"></span>
                <span style="color:#666;">Số TK:</span>       <span id="bk-acc" style="font-weight:700;font-family:monospace;letter-spacing:1px;"></span>
                <span style="color:#666;">Chủ TK:</span>      <span id="bk-name" style="font-weight:600;"></span>
                <span style="color:#666;">Nội dung CK:</span> <span id="bk-content" style="font-weight:700;color:#1a56db;font-family:monospace;"></span>
            </div>
        </div>

        {{-- MoMo Info --}}
        <div id="momo-info-box" style="display:none;background:#fff0f7;border:1px solid #ffd0ea;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
            <div style="font-size:12px;color:#ae2d68;font-weight:700;margin-bottom:10px;letter-spacing:.5px;">THÔNG TIN MOMO</div>
            <div style="display:grid;grid-template-columns:auto 1fr;gap:6px 12px;font-size:13px;">
                <span style="color:#666;">Số điện thoại:</span> <span id="mm-phone" style="font-weight:700;font-family:monospace;letter-spacing:1px;"></span>
                <span style="color:#666;">Tên:</span>            <span id="mm-name" style="font-weight:600;"></span>
                <span style="color:#666;">Lời nhắn:</span>       <span id="mm-note" style="font-weight:700;color:#ae2d68;font-family:monospace;"></span>
            </div>
        </div>

        {{-- Timer --}}
        <div style="text-align:center;margin-bottom:20px;">
            <div style="display:inline-flex;align-items:center;gap:6px;background:#fff8e6;border:1px solid #fde68a;border-radius:8px;padding:6px 14px;font-size:12px;color:#92400e;">
                <span>⏱</span>
                <span>Mã hết hạn sau: <strong id="countdown-timer" style="font-family:monospace;font-size:14px;">15:00</strong></span>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:10px;">
            <button onclick="closePaymentModal()" style="flex:1;padding:12px;background:#f5f5f5;border:none;border-radius:10px;font-weight:600;font-size:14px;cursor:pointer;color:#555;">Quay lại</button>
            <button id="confirm-paid-btn" onclick="confirmAndSubmit()" style="flex:2;padding:12px;background:#1a1a1a;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;letter-spacing:.5px;">✓ Đã thanh toán — Đặt hàng</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ─── Config SePay / Bank ─────────────────────────────────────────────────────
const SEPAY_CONFIG = {
    bankCode:      'MB',              // Bank Transfer thông thường
    accountNumber: '080717072006',
    accountName:   'UNG KIM TRUC',
    template:      'compact',
    // ─── SePay (MBBank liên kết) ────────────────────────────────
    sepayBank:     'MB',              // MBBank
    sepayAccount:  '080717072006',    // Số TK liên kết SePay
    sepayName:     'UNG KIM TRUC',    // Tên chủ TK
    sepayPrefix:   'VIBE',            // SePay sẽ lọc giao dịch có nội dung bắt đầu bằng VIBE
};

const MOMO_CONFIG = {
    phone: '0901234567',            // Số điện thoại MoMo của shop
    name:  'VIBE STORE',
};

// ─── Countdown timer ─────────────────────────────────────────────────────────
let countdownInterval = null;

function startCountdown(minutes = 15) {
    clearInterval(countdownInterval);
    let total = minutes * 60;
    updateTimerDisplay(total);
    countdownInterval = setInterval(() => {
        total--;
        updateTimerDisplay(total);
        if (total <= 0) { clearInterval(countdownInterval); closePaymentModal(); }
    }, 1000);
}

function updateTimerDisplay(secs) {
    const m = String(Math.floor(secs / 60)).padStart(2, '0');
    const s = String(secs % 60).padStart(2, '0');
    const el = document.getElementById('countdown-timer');
    if (el) el.textContent = `${m}:${s}`;
}

// ─── Payment selection ────────────────────────────────────────────────────────
function updatePaymentSelection(radio) {
    document.querySelectorAll('.vibe-payment-option').forEach(el => el.classList.remove('active'));
    radio.closest('.vibe-payment-option').classList.add('active');
}

// ─── Get total from page ──────────────────────────────────────────────────────
function getOrderTotal() {
    const totalEl = document.querySelector('.vibe-price-total .vibe-mono');
    return totalEl ? totalEl.textContent.trim() : '0₫';
}

function getOrderNumber() {
    // Generate temp code từ timestamp
    return 'VIBE' + Date.now().toString().slice(-8);
}

// ─── Open modal ───────────────────────────────────────────────────────────────
function handleCheckoutSubmit() {
    const method = document.querySelector('input[name="payment_method"]:checked')?.value;

    if (method === 'bank_transfer') {
        openBankTransferModal();
    } else if (method === 'sepay') {
        openSepayModal();
    } else {
        // COD → submit thẳng
        document.getElementById('checkout-main-form').submit();
    }
}

function openBankTransferModal() {
    const total   = getOrderTotal();
    const rawAmt  = total.replace(/[^0-9]/g, '');
    const content = 'VIBE' + Date.now().toString().slice(-6);

    // Header
    document.getElementById('modal-header').style.background = '#1a56db';
    document.getElementById('modal-icon').innerHTML = '🏦';
    document.getElementById('modal-title').textContent = 'Chuyển khoản ngân hàng';
    document.getElementById('modal-subtitle').textContent = 'Quét mã QR để thanh toán';
    document.getElementById('modal-amount').textContent = total;

    // QR VietQR (SePay compatible)
    const qrUrl = `https://img.vietqr.io/image/${SEPAY_CONFIG.bankCode}-${SEPAY_CONFIG.accountNumber}-${SEPAY_CONFIG.template}.png?amount=${rawAmt}&addInfo=${content}&accountName=${encodeURIComponent(SEPAY_CONFIG.accountName)}`;
    document.getElementById('modal-qr-img').src = qrUrl;
    document.getElementById('modal-instruction').textContent = 'Mở app ngân hàng → Quét QR → Kiểm tra thông tin → Xác nhận thanh toán';

    // Bank info
    document.getElementById('bk-bank').textContent = SEPAY_CONFIG.bankCode;
    document.getElementById('bk-acc').textContent  = SEPAY_CONFIG.accountNumber;
    document.getElementById('bk-name').textContent = SEPAY_CONFIG.accountName;
    document.getElementById('bk-content').textContent = content;

    // Show / hide boxes
    document.getElementById('bank-info-box').style.display = 'block';
    document.getElementById('bank-info-box').style.display = 'block';

    // Store content to hidden input
    setHiddenTransferContent(content);

    showModal();
}

function openSepayModal() {
    const total   = getOrderTotal();
    const rawAmt  = total.replace(/[^0-9]/g, '');
    // Nội dung CK chuẩn SePay: prefix + số ngẫu nhiên để webhook nhận dạng
    const content = SEPAY_CONFIG.sepayPrefix + Date.now().toString().slice(-8);

    // Header màu cam SePay
    document.getElementById('modal-header').style.background = 'linear-gradient(135deg,#f97316,#ea580c)';
    document.getElementById('modal-icon').innerHTML = '<span style="font-size:22px;">⚡</span>';
    document.getElementById('modal-title').textContent    = 'SePay — Thanh toán tự động';
    document.getElementById('modal-subtitle').textContent = 'Quét QR · Xác nhận tức thì';
    document.getElementById('modal-amount').textContent   = total;

    // QR VietQR dùng tài khoản liên kết SePay
    const qrUrl = `https://img.vietqr.io/image/${SEPAY_CONFIG.sepayBank}-${SEPAY_CONFIG.sepayAccount}-compact.png`
                + `?amount=${rawAmt}&addInfo=${content}&accountName=${encodeURIComponent(SEPAY_CONFIG.sepayName)}`;
    document.getElementById('modal-qr-img').src = qrUrl;
    document.getElementById('modal-instruction').textContent =
        'Mở app ngân hàng → Quét QR → Kiểm tra số tiền & nội dung → Xác nhận. SePay sẽ tự động ghi nhận!';

    // Hiển thị bank-info với nhãn SePay
    document.getElementById('bk-bank').textContent    = SEPAY_CONFIG.sepayBank + '  (SePay)';
    document.getElementById('bk-acc').textContent     = SEPAY_CONFIG.sepayAccount;
    document.getElementById('bk-name').textContent    = SEPAY_CONFIG.sepayName;
    document.getElementById('bk-content').textContent = content;

    // Override màu header của bank-info box sang cam SePay
    const bankBox = document.getElementById('bank-info-box');
    bankBox.style.background  = '#fff7ed';
    bankBox.style.borderColor = '#fed7aa';
    bankBox.querySelector('div').style.color = '#ea580c';
    bankBox.querySelector('div').textContent = '⚡ THÔNG TIN SEPAY — QUAN TRỌNG: sao chép đúng nội dung CK';

    document.getElementById('bank-info-box').style.display = 'block';

    setHiddenTransferContent(content);
    showModal();
}

function showModal() {
    document.getElementById('payment-modal-backdrop').style.display = 'block';
    document.getElementById('payment-qr-modal').style.display       = 'block';
    document.body.style.overflow = 'hidden';
    startCountdown(15);
}

function closePaymentModal() {
    document.getElementById('payment-modal-backdrop').style.display = 'none';
    document.getElementById('payment-qr-modal').style.display       = 'none';
    document.body.style.overflow = '';
    clearInterval(countdownInterval);
}

function confirmAndSubmit() {
    clearInterval(countdownInterval);
    closePaymentModal();
    document.getElementById('checkout-main-form').submit();
}

function setHiddenTransferContent(content) {
    let inp = document.getElementById('transfer_content_hidden');
    if (!inp) {
        inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'transfer_content';
        inp.id   = 'transfer_content_hidden';
        document.getElementById('checkout-main-form').appendChild(inp);
    }
    inp.value = content;
}
</script>
@endpush
