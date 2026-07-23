<!-- Cart Drawer -->
<div id="cart-backdrop" class="vibe-drawer-backdrop d-none"></div>
<div id="cart-drawer-panel" class="vibe-drawer vibe-drawer-right">

    <!-- Header -->
    <div class="vibe-drawer-header">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-shopping-bag"></i>
            <h2 class="vibe-drawer-title">Your Cart (<span id="cart-item-count">0</span>)</h2>
        </div>
        <button id="close-cart-btn" class="vibe-icon-btn"><i class="fas fa-times"></i></button>
    </div>

    <!-- Free Shipping Bar -->
    <div id="free-shipping-bar" class="vibe-shipping-bar d-none">
        <div id="free-shipping-message"></div>
        <div class="vibe-shipping-progress mt-1">
            <div id="shipping-progress-fill" class="vibe-shipping-fill" style="width:0%"></div>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="vibe-drawer-body" id="cart-items-container">
        <!-- Empty State -->
        <div id="cart-empty-state" class="vibe-drawer-empty">
            <div class="vibe-drawer-empty-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <p class="vibe-drawer-empty-title">Your cart is empty</p>
            <p class="vibe-drawer-empty-desc">Explore Whenever tees, hoodies and jackets to find your perfect style.</p>
            <button id="cart-empty-shop-now" class="vibe-btn-dark mt-3">
                <i class="fas fa-arrow-left me-2"></i> Continue Shopping
            </button>
        </div>
        <!-- Items dynamically rendered by JS -->
        <div id="cart-items-list"></div>
    </div>

    <!-- Footer -->
    <div id="cart-footer" class="vibe-drawer-footer d-none">
        <div class="vibe-price-breakdown">
            <div class="vibe-price-row">
                <span>Subtotal</span>
                <span id="cart-subtotal" class="vibe-mono">0₫</span>
            </div>
            <div class="vibe-price-row">
                <span>Shipping</span>
                <span id="cart-shipping" class="vibe-mono">30,000₫</span>
            </div>
            <div class="vibe-price-row vibe-price-total">
                <span>Total</span>
                <span id="cart-total" class="vibe-mono">0₫</span>
            </div>
        </div>
        <div class="vibe-drawer-actions mt-3">
            <a id="cart-proceed-checkout" href="{{ route('checkout.index') }}" class="vibe-btn-dark w-100 d-flex align-items-center justify-content-center gap-2">
                Proceed to Checkout <i class="fas fa-arrow-right"></i>
            </a>
            <button id="cart-continue-shopping" class="vibe-btn-outline w-100 mt-2">
                Continue Shopping
            </button>
        </div>
        <div class="vibe-secure-badge">
            <i class="fas fa-shield-alt me-1"></i>
            Secure Whenever authentication verified
        </div>
    </div>
</div>

<!-- Cart Item Template (hidden, cloned by JS) -->
<template id="cart-item-template">
    <div class="vibe-cart-item" data-item-id="">
        <div class="vibe-cart-item-image">
            <img src="" alt="" class="vibe-cart-thumb">
        </div>
        <div class="vibe-cart-item-info flex-grow-1">
            <div class="d-flex justify-content-between">
                <h3 class="vibe-cart-item-name"></h3>
                <span class="vibe-cart-item-price vibe-mono"></span>
            </div>
            <p class="vibe-cart-item-size">Size: <strong></strong></p>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <div class="vibe-qty-control">
                    <button class="vibe-qty-btn qty-minus">−</button>
                    <span class="vibe-qty-value">1</span>
                    <button class="vibe-qty-btn qty-plus">+</button>
                </div>
                <button class="vibe-remove-btn">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>
