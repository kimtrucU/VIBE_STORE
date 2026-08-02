/* =============================================================
   VIBE STORE — JAVASCRIPT
   Handles: Cart/Wishlist drawers, AJAX cart ops,
   mobile menu, search bar, toast notifications,
   product size overlays, image hover, animations.
   ============================================================= */

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const BASE_URL   = window.location.origin;

/* ─── Toast Notification System ───────────────────────────── */
function showToast(message, type = 'success') {
    const wrapper  = document.getElementById('toast-wrapper');
    const template = document.getElementById('toast-template');
    if (!wrapper || !template) return;

    const el  = template.content.cloneNode(true).querySelector('.vibe-toast');
    const icon = el.querySelector('.vibe-toast-icon');
    const msg  = el.querySelector('.vibe-toast-message');

    el.classList.add(`toast-${type}`);
    msg.textContent = message;
    icon.className = `vibe-toast-icon fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}`;

    wrapper.appendChild(el);

    setTimeout(() => {
        el.classList.add('fade-out');
        setTimeout(() => el.remove(), 320);
    }, 3500);
}

/* Show flash messages as toasts */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.vibe-flash').forEach(el => {
        showToast(el.dataset.message, el.dataset.type === 'error' ? 'error' : el.dataset.type === 'info' ? 'info' : 'success');
    });
});

/* ─── Drawer Helper ────────────────────────────────────────── */
function openDrawer(panelId, backdropId) {
    const panel    = document.getElementById(panelId);
    const backdrop = document.getElementById(backdropId);
    if (!panel || !backdrop) return;
    backdrop.classList.remove('d-none');
    requestAnimationFrame(() => panel.classList.add('open'));
    document.body.style.overflow = 'hidden';
}

function closeDrawer(panelId, backdropId) {
    const panel    = document.getElementById(panelId);
    const backdrop = document.getElementById(backdropId);
    if (!panel || !backdrop) return;
    panel.classList.remove('open');
    setTimeout(() => {
        backdrop.classList.add('d-none');
        document.body.style.overflow = '';
    }, 380);
}

/* ─── Cart Drawer ──────────────────────────────────────────── */
const CART_PANEL    = 'cart-drawer-panel';
const CART_BACKDROP = 'cart-backdrop';

document.getElementById('cart-toggle-btn')?.addEventListener('click', () => {
    openDrawer(CART_PANEL, CART_BACKDROP);
    fetchCartData();
});
document.getElementById('close-cart-btn')?.addEventListener('click', () => closeDrawer(CART_PANEL, CART_BACKDROP));
document.getElementById('cart-backdrop')?.addEventListener('click', () => closeDrawer(CART_PANEL, CART_BACKDROP));
document.getElementById('cart-continue-shopping')?.addEventListener('click', () => closeDrawer(CART_PANEL, CART_BACKDROP));
document.getElementById('cart-empty-shop-now')?.addEventListener('click', () => {
    closeDrawer(CART_PANEL, CART_BACKDROP);
    window.location.href = `${BASE_URL}/shop`;
});

function fetchCartData() {
    fetch(`${BASE_URL}/cart/data`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => renderCartDrawer(data))
        .catch(() => {});
}

function renderCartDrawer(data) {
    const itemsList  = document.getElementById('cart-items-list');
    const emptyState = document.getElementById('cart-empty-state');
    const footer     = document.getElementById('cart-footer');
    const countEl    = document.getElementById('cart-item-count');
    const badgeEl    = document.getElementById('cart-count-badge');
    const shippingBar= document.getElementById('free-shipping-bar');
    const template   = document.getElementById('cart-item-template');

    if (!itemsList) return;

    const items = data.items || [];
    const count = data.item_count || 0;

    if (countEl) countEl.textContent = count;
    if (badgeEl) {
        badgeEl.textContent = count;
        badgeEl.classList.toggle('d-none', count === 0);
    }

    if (items.length === 0) {
        emptyState?.classList.remove('d-none');
        footer?.classList.add('d-none');
        itemsList.innerHTML = '';
        shippingBar?.classList.add('d-none');
        return;
    }

    emptyState?.classList.add('d-none');
    footer?.classList.remove('d-none');

    // Shipping progress bar
    const FREE_THRESHOLD = 1000000;
    const subtotal = data.subtotal || 0;
    if (shippingBar) {
        shippingBar.classList.remove('d-none');
        const remaining = Math.max(0, FREE_THRESHOLD - subtotal);
        const progress  = Math.min(100, (subtotal / FREE_THRESHOLD) * 100);
        const msgEl     = shippingBar.querySelector('#free-shipping-message');
        const fillEl    = shippingBar.querySelector('#shipping-progress-fill');
        if (msgEl) msgEl.textContent = remaining > 0
            ? `Add ${formatVND(remaining)} more for FREE shipping!`
            : '🎉 You qualify for FREE shipping!';
        if (fillEl) fillEl.style.width = `${progress}%`;
    }

    // Render items
    itemsList.innerHTML = '';
    items.forEach(item => {
        if (!template) return;
        const clone = template.content.cloneNode(true);
        const el    = clone.querySelector('.vibe-cart-item');
        el.dataset.itemId = item.id;
        el.querySelector('.vibe-cart-thumb').src        = item.image;
        el.querySelector('.vibe-cart-thumb').alt        = item.name;
        el.querySelector('.vibe-cart-item-name').textContent = item.name;
        el.querySelector('.vibe-cart-item-price').textContent = formatVND(item.total);
        el.querySelector('.vibe-cart-item-size strong').textContent = item.size;
        el.querySelector('.vibe-qty-value').textContent = item.quantity;

        // Qty controls
        el.querySelector('.qty-minus').addEventListener('click', () => updateCartItem(item.id, Math.max(1, item.quantity - 1)));
        el.querySelector('.qty-plus').addEventListener('click', () => updateCartItem(item.id, Math.min(10, item.quantity + 1)));
        el.querySelector('.vibe-remove-btn').addEventListener('click', () => removeCartItem(item.id));
        itemsList.appendChild(clone);
    });

    // Totals
    document.getElementById('cart-subtotal').textContent = formatVND(data.subtotal);
    document.getElementById('cart-shipping').textContent = data.shipping_fee > 0 ? formatVND(data.shipping_fee) : 'Free';
    document.getElementById('cart-total').textContent    = formatVND(data.grand_total);
}

/* Add to cart (called from Blade product card & detail page) */
window.addToCart = function(productId, size, quantity = 1, sourceBtn = null) {
    fetch(`${BASE_URL}/cart/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ product_id: productId, size, quantity }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            renderCartDrawer(data.cart_data);
            // Open drawer after short delay
            setTimeout(() => openDrawer(CART_PANEL, CART_BACKDROP), 600);
        } else {
            showToast(data.message || 'Error adding to cart', 'error');
        }
        // Hide size overlay if visible
        document.querySelectorAll('.vibe-size-overlay').forEach(el => el.classList.add('d-none'));
    })
    .catch(() => showToast('Network error. Please try again.', 'error'));
}

window.updateCartItem = function(itemId, qty) {
    fetch(`${BASE_URL}/cart/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ item_id: itemId, quantity: qty }),
    })
    .then(r => r.json())
    .then(data => { if (data.success) renderCartDrawer(data.cart_data); })
    .catch(() => {});
}

window.removeCartItem = function(itemId) {
    fetch(`${BASE_URL}/cart/remove`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ item_id: itemId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'info');
            renderCartDrawer(data.cart_data);
        }
    })
    .catch(() => {});
}

/* Quick add from product card */
window.handleQuickAdd = function(productId, sizes) {
    if (!sizes || sizes.length === 0) {
        showToast('No sizes available for this product.', 'error');
        return;
    }
    if (sizes.length === 1 || sizes[0] === 'Free Size') {
        window.addToCart(productId, sizes[0], 1, null);
    } else {
        window.showSizeOverlay(productId);
    }
}

window.showSizeOverlay = function(productId) {
    document.querySelectorAll('.vibe-size-overlay').forEach(el => el.classList.add('d-none'));
    const overlay = document.getElementById(`size-overlay-${productId}`);
    if (overlay) overlay.classList.remove('d-none');
}

window.hideSizeOverlay = function(productId) {
    const overlay = document.getElementById(`size-overlay-${productId}`);
    if (overlay) overlay.classList.add('d-none');
}

/* ─── Wishlist ─────────────────────────────────────────────── */
const WISHLIST_PANEL    = 'wishlist-drawer-panel';
const WISHLIST_BACKDROP = 'wishlist-backdrop';

document.getElementById('wishlist-toggle-btn')?.addEventListener('click', () => {
    openDrawer(WISHLIST_PANEL, WISHLIST_BACKDROP);
    fetchWishlistData();
});
document.getElementById('close-wishlist-btn')?.addEventListener('click', () => closeDrawer(WISHLIST_PANEL, WISHLIST_BACKDROP));
document.getElementById('wishlist-backdrop')?.addEventListener('click', () => closeDrawer(WISHLIST_PANEL, WISHLIST_BACKDROP));

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.vibe-wishlist-btn, .vibe-wishlist-full');
    if (!btn) return;
    e.preventDefault();

    const url = btn.dataset.url;
    if (!url) return;

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const isActive = data.wishlisted;
            btn.classList.toggle('active', isActive);
            showToast(data.message, isActive ? 'success' : 'info');

            // Update wishlist count badge
            const badge = document.getElementById('wishlist-count-badge');
            if (badge) {
                badge.textContent = data.count;
                badge.classList.toggle('d-none', data.count === 0);
            }

            // Update full wishlist button text
            if (btn.classList.contains('vibe-wishlist-full')) {
                btn.innerHTML = `<i class="fas fa-heart me-2"></i>${isActive ? 'Remove from Wishlist' : 'Add to Wishlist'}`;
            }
        }
    })
    .catch(() => showToast('Please log in to use the wishlist.', 'error'));
});

function fetchWishlistData() {
    fetch(`${BASE_URL}/wishlist/data`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => renderWishlistDrawer(data))
        .catch(() => {});
}

function renderWishlistDrawer(data) {
    const list     = document.getElementById('wishlist-items-list');
    const empty    = document.getElementById('wishlist-empty-state');
    const countEl  = document.getElementById('wishlist-item-count');
    const badge    = document.getElementById('wishlist-count-badge');
    const template = document.getElementById('wishlist-item-template');

    if (!list) return;

    const items = data.items || [];
    if (countEl) countEl.textContent = items.length;
    if (badge)   { badge.textContent = items.length; badge.classList.toggle('d-none', items.length === 0); }

    if (items.length === 0) {
        empty?.classList.remove('d-none');
        list.innerHTML = '';
        return;
    }

    empty?.classList.add('d-none');
    list.innerHTML = '';

    items.forEach(item => {
        if (!template) return;
        const clone = template.content.cloneNode(true);
        const el    = clone.querySelector('.vibe-wishlist-item');
        el.dataset.productId = item.product_id;
        el.querySelector('img').src = item.image;
        el.querySelector('img').alt = item.name;
        el.querySelector('.vibe-wishlist-name').textContent  = item.name;
        el.querySelector('.vibe-wishlist-price').textContent = formatVND(item.price);
        const link = el.querySelector('a');
        if (link) link.href = `${BASE_URL}/products/${item.slug}`;
        el.querySelector('.vibe-wishlist-remove').addEventListener('click', () => {
            // Toggle removes from wishlist
            const toggleUrl = `${BASE_URL}/wishlist/${item.slug}`;
            fetch(toggleUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(() => fetchWishlistData()).catch(() => {});
        });
        list.appendChild(clone);
    });
}

/* ─── Mobile Navigation ────────────────────────────────────── */
const MOBILE_NAV      = document.getElementById('mobile-nav-sidebar');
const MOBILE_BACKDROP = document.getElementById('mobile-nav-backdrop');

document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
    MOBILE_NAV?.classList.add('open');
    MOBILE_BACKDROP?.classList.remove('d-none');
    document.body.style.overflow = 'hidden';
});

document.getElementById('close-mobile-menu')?.addEventListener('click', closeMobileNav);
MOBILE_BACKDROP?.addEventListener('click', closeMobileNav);

function closeMobileNav() {
    MOBILE_NAV?.classList.remove('open');
    MOBILE_BACKDROP?.classList.add('d-none');
    document.body.style.overflow = '';
}

/* ─── Search Bar ───────────────────────────────────────────── */
document.getElementById('search-toggle-btn')?.addEventListener('click', () => {
    const bar = document.getElementById('search-bar-container');
    if (!bar) return;
    bar.classList.toggle('show');
    if (bar.classList.contains('show')) {
        document.getElementById('global-search-input')?.focus();
    }
});

/* ─── Newsletter ───────────────────────────────────────────── */
document.getElementById('newsletter-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const emailInput = document.getElementById('newsletter-email-input');
    const successEl  = document.getElementById('newsletter-success');
    const btn        = document.getElementById('newsletter-submit');
    if (!emailInput?.value) return;
    btn.textContent = '...';
    btn.disabled = true;
    setTimeout(() => {
        this.classList.add('d-none');
        successEl?.classList.remove('d-none');
        btn.textContent = 'Subscribe';
        btn.disabled = false;
    }, 800);
});

/* ─── Utils ────────────────────────────────────────────────── */
function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(amount) + '₫';
}

/* ─── Init on Load ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count on page load
    fetch(`${BASE_URL}/cart/data`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('cart-count-badge');
            const count = data.item_count || 0;
            if (badge) {
                badge.textContent = count;
                badge.classList.toggle('d-none', count === 0);
            }
        }).catch(() => {});
});

/* ══════════════════════════════════════════════════════════════
   VIBE — BOOTSTRAP EFFECTS & ANIMATIONS
   ══════════════════════════════════════════════════════════════ */

/* ── Scroll Reveal (Intersection Observer) ─────────────────── */
(function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    // Auto-attach to all sections, product cards, stat blocks
    const selectors = [
        '.vibe-product-card',
        '.vibe-cat-card',
        '.vibe-stat',
        '.vibe-filter-panel',
        '.vibe-highlight-title',
        '.vibe-highlight-desc',
        '.vibe-section-header',
        '[data-vibe-reveal]',
    ];
    document.querySelectorAll(selectors.join(',')).forEach((el, i) => {
        // Skip if already has explicit direction
        if (!el.hasAttribute('data-vibe-reveal')) {
            el.setAttribute('data-vibe-reveal', 'true');
        }
        // Auto stagger for grid children (product cards, cat cards)
        if (el.classList.contains('vibe-product-card') || el.classList.contains('vibe-cat-card')) {
            const siblings = Array.from(el.parentElement?.children || []);
            const idx = siblings.indexOf(el) + 1;
            if (idx <= 5) el.setAttribute('data-vibe-delay', idx);
        }
        observer.observe(el);
    });
})();

/* ── Navbar Shrink on Scroll ───────────────────────────────── */
(function initNavbarScroll() {
    const navbar = document.getElementById('main-header');
    if (!navbar) return;
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 60);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();

/* ── Scroll To Top Button ──────────────────────────────────── */
(function initScrollTop() {
    // Create button dynamically
    const btn = document.createElement('button');
    btn.id = 'scroll-top-btn';
    btn.setAttribute('aria-label', 'Scroll to top');
    btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

/* ── Promo Ticker Auto-scroll ──────────────────────────────── */
(function initPromoTicker() {
    const ticker = document.getElementById('promo-ticker');
    if (!ticker) return;
    const text = ticker.textContent.trim();
    // Duplicate content for seamless loop
    ticker.innerHTML = `<span class="vibe-promo-ticker-inner">${text} &nbsp;&nbsp;•&nbsp;&nbsp; ${text} &nbsp;&nbsp;•&nbsp;&nbsp; ${text} &nbsp;&nbsp;•&nbsp;&nbsp; ${text}</span>`;
})();

/* ── Lazy Image Loading ────────────────────────────────────── */
(function initLazyImages() {
    const imgs = document.querySelectorAll('img[loading="lazy"]');
    if ('IntersectionObserver' in window) {
        const imgObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('vibe-lazy');
                    img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
                    if (img.complete) img.classList.add('loaded');
                    imgObserver.unobserve(img);
                }
            });
        });
        imgs.forEach(img => imgObserver.observe(img));
    } else {
        imgs.forEach(img => img.classList.add('vibe-lazy', 'loaded'));
    }
})();

/* ── Hero Subtle Parallax ──────────────────────────────────── */
(function initParallax() {
    const heroImg = document.querySelector('.vibe-hero-img');
    if (!heroImg) return;
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        if (scrollY < window.innerHeight) {
            heroImg.style.transform = `translateY(${scrollY * 0.25}px)`;
        }
    }, { passive: true });
})();

/* ── Bootstrap Tooltip Init ────────────────────────────────── */
(function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el, { trigger: 'hover' });
        });
    }
})();

/* ── Add to cart button loading state ─────────────────────── */
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.vibe-add-to-cart-btn, .vibe-btn-cart-detail');
    if (!btn || btn.dataset.loading) return;
    // Briefly show spinner on button click
    const originalHTML = btn.innerHTML;
    btn.dataset.loading = '1';
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';
    btn.disabled = true;
    // Re-enable after 1.5s (actual response will override earlier)
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        delete btn.dataset.loading;
    }, 1500);
});

