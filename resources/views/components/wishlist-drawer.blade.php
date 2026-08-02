<!-- Wishlist Drawer -->
<div id="wishlist-backdrop" class="vibe-drawer-backdrop d-none"></div>
<div id="wishlist-drawer-panel" class="vibe-drawer vibe-drawer-right">

    <div class="vibe-drawer-header">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-heart"></i>
            <h2 class="vibe-drawer-title">Wishlist (<span id="wishlist-item-count">0</span>)</h2>
        </div>
        <button id="close-wishlist-btn" class="vibe-icon-btn"><i class="fas fa-times"></i></button>
    </div>

    <div class="vibe-drawer-body" id="wishlist-items-container">
        <div id="wishlist-empty-state" class="vibe-drawer-empty">
            <div class="vibe-drawer-empty-icon">
                <i class="fas fa-heart"></i>
            </div>
            <p class="vibe-drawer-empty-title">Your wishlist is empty</p>
            <p class="vibe-drawer-empty-desc">Save your favorite Whenever pieces here.</p>
        </div>
        <div id="wishlist-items-list"></div>
    </div>
</div>

<template id="wishlist-item-template">
    <div class="vibe-wishlist-item" data-product-id="">
        <img src="" alt="" class="vibe-wishlist-thumb">
        <div class="flex-grow-1 min-w-0">
            <h3 class="vibe-wishlist-name"></h3>
            <p class="vibe-wishlist-price vibe-mono"></p>
            <a href="" class="vibe-btn-dark vibe-btn-xs mt-2">View Product</a>
        </div>
        <button class="vibe-wishlist-remove"><i class="fas fa-times"></i></button>
    </div>
</template>
