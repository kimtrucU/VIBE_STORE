import { useState } from 'react';
import { Search, Heart, ShoppingBag, User, Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';

interface NavbarProps {
  currentPage: string;
  onNavigate: (page: string) => void;
  cartCount: number;
  wishlistCount: number;
  onOpenCart: () => void;
  onOpenWishlist: () => void;
  onOpenProfile: () => void;
  searchQuery: string;
  onSearchChange: (query: string) => void;
}

export default function Navbar({
  currentPage,
  onNavigate,
  cartCount,
  wishlistCount,
  onOpenCart,
  onOpenWishlist,
  onOpenProfile,
  searchQuery,
  onSearchChange,
}: NavbarProps) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [showSearchBar, setShowSearchBar] = useState(false);

  const navItems = [
    { id: 'home', label: 'Home' },
    { id: 'shop', label: 'Shop' },
    { id: 'new-arrival', label: 'New Arrival' },
    { id: 'best-seller', label: 'Best Seller' },
    { id: 'about', label: 'About' },
    { id: 'contact', label: 'Contact' },
  ];

  const handleNavClick = (pageId: string) => {
    onNavigate(pageId);
    setMobileMenuOpen(false);
  };

  return (
    <>
      <header id="main-header" className="sticky top-0 z-40 w-full border-b border-neutral-100 bg-white/90 backdrop-blur-md transition-all">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
          
          {/* Mobile Menu Icon */}
          <button
            id="mobile-menu-toggle"
            className="flex h-10 w-10 items-center justify-center rounded-full text-neutral-700 hover:bg-neutral-100 md:hidden"
            onClick={() => setMobileMenuOpen(true)}
            aria-label="Open Menu"
          >
            <Menu className="h-5 w-5" />
          </button>

          {/* Logo VIBE */}
          <div className="flex items-center">
            <button
              id="logo-button"
              onClick={() => handleNavClick('home')}
              className="font-display text-2xl font-black tracking-widest text-black hover:opacity-80 transition"
            >
              VIBE
            </button>
            <span className="hidden sm:inline-block ml-2 text-[9px] tracking-widest text-neutral-400 font-mono border border-neutral-200 px-1.5 py-0.5 rounded uppercase">
              Whenever Auth
            </span>
          </div>

          {/* Desktop Navigation Links */}
          <nav className="hidden md:flex space-x-8">
            {navItems.map((item) => {
              const isActive = currentPage === item.id;
              return (
                <button
                  id={`nav-${item.id}`}
                  key={item.id}
                  onClick={() => handleNavClick(item.id)}
                  className={`relative py-2 font-display text-xs font-semibold tracking-widest uppercase transition ${
                    isActive ? 'text-black' : 'text-neutral-500 hover:text-black'
                  }`}
                >
                  {item.label}
                  {isActive && (
                    <motion.div
                      layoutId="activeNavLine"
                      className="absolute bottom-0 left-0 h-0.5 w-full bg-black"
                      transition={{ type: 'spring', stiffness: 380, damping: 30 }}
                    />
                  )}
                </button>
              );
            })}
          </nav>

          {/* Action Icons */}
          <div className="flex items-center space-x-1 sm:space-x-3">
            {/* Search Icon Toggle */}
            <button
              id="search-toggle-btn"
              onClick={() => setShowSearchBar(!showSearchBar)}
              className={`flex h-10 w-10 items-center justify-center rounded-full text-neutral-700 transition ${
                showSearchBar ? 'bg-neutral-100' : 'hover:bg-neutral-50'
              }`}
              aria-label="Toggle search bar"
            >
              <Search className="h-4 w-5" />
            </button>

            {/* Account Icon */}
            <button
              id="profile-toggle-btn"
              onClick={onOpenProfile}
              className="flex h-10 w-10 items-center justify-center rounded-full text-neutral-700 hover:bg-neutral-50 transition"
              aria-label="Account details"
            >
              <User className="h-4 w-5" />
            </button>

            {/* Wishlist Icon */}
            <button
              id="wishlist-toggle-btn"
              onClick={onOpenWishlist}
              className="relative flex h-10 w-10 items-center justify-center rounded-full text-neutral-700 hover:bg-neutral-50 transition"
              aria-label="Wishlist"
            >
              <Heart className="h-4 w-5" />
              {wishlistCount > 0 && (
                <span className="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-[9px] font-bold font-mono text-white">
                  {wishlistCount}
                </span>
              )}
            </button>

            {/* Cart Icon */}
            <button
              id="cart-toggle-btn"
              onClick={onOpenCart}
              className="relative flex h-10 w-10 items-center justify-center rounded-full bg-black text-white hover:bg-neutral-800 transition"
              aria-label="Shopping Cart"
            >
              <ShoppingBag className="h-4 w-4" />
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 flex h-4.5 w-4.5 items-center justify-center rounded-full bg-neutral-200 text-[9px] font-bold font-mono text-black">
                  {cartCount}
                </span>
              )}
            </button>
          </div>
        </div>

        {/* Global Search Expandable Bar */}
        <AnimatePresence>
          {showSearchBar && (
            <motion.div
              id="search-bar-container"
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: 'auto', opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="overflow-hidden border-t border-neutral-100 bg-neutral-50"
            >
              <div className="mx-auto max-w-3xl px-4 py-4 sm:px-6">
                <div className="relative">
                  <Search className="absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                  <input
                    id="global-search-input"
                    type="text"
                    value={searchQuery}
                    onChange={(e) => onSearchChange(e.target.value)}
                    placeholder="Tìm kiếm sản phẩm Whenever chính hãng... (ví dụ: Tee, Hoodie, Jacket, Cap)"
                    className="w-full rounded-full border border-neutral-200 bg-white py-3 pr-12 pl-12 text-sm text-black placeholder-neutral-400 focus:border-black focus:outline-none focus:ring-1 focus:ring-black"
                    autoFocus
                  />
                  {searchQuery && (
                    <button
                      id="clear-search-btn"
                      onClick={() => onSearchChange('')}
                      className="absolute top-1/2 right-4 flex -translate-y-1/2 items-center justify-center rounded-full bg-neutral-100 p-1 text-neutral-500 hover:bg-neutral-200"
                    >
                      <X className="h-3 w-3" />
                    </button>
                  )}
                </div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </header>

      {/* Mobile Menu Drawer Overlay */}
      <AnimatePresence>
        {mobileMenuOpen && (
          <>
            {/* Backdrop */}
            <motion.div
              id="mobile-nav-backdrop"
              initial={{ opacity: 0 }}
              animate={{ opacity: 0.5 }}
              exit={{ opacity: 0 }}
              onClick={() => setMobileMenuOpen(false)}
              className="fixed inset-0 z-50 bg-black"
            />

            {/* Sidebar drawer */}
            <motion.div
              id="mobile-nav-sidebar"
              initial={{ x: '-100%' }}
              animate={{ x: 0 }}
              exit={{ x: '-100%' }}
              transition={{ type: 'tween', duration: 0.3 }}
              className="fixed inset-y-0 left-0 z-50 flex w-4/5 max-w-sm flex-col bg-white px-6 py-6 shadow-2xl"
            >
              <div className="flex items-center justify-between border-b border-neutral-100 pb-4">
                <span className="font-display text-xl font-black tracking-widest text-black">VIBE</span>
                <button
                  id="close-mobile-menu"
                  onClick={() => setMobileMenuOpen(false)}
                  className="flex h-8 w-8 items-center justify-center rounded-full hover:bg-neutral-100"
                >
                  <X className="h-5 w-5 text-neutral-500" />
                </button>
              </div>

              <div className="mt-8 flex flex-col space-y-5">
                {navItems.map((item) => {
                  const isActive = currentPage === item.id;
                  return (
                    <button
                      id={`mobile-nav-link-${item.id}`}
                      key={item.id}
                      onClick={() => handleNavClick(item.id)}
                      className={`text-left font-display text-sm font-semibold tracking-widest uppercase py-1 ${
                        isActive ? 'text-black border-l-2 border-black pl-3' : 'text-neutral-500 hover:text-black pl-3'
                      }`}
                    >
                      {item.label}
                    </button>
                  );
                })}
              </div>

              <div className="mt-auto border-t border-neutral-100 pt-6 text-center">
                <p className="text-xs text-neutral-400 font-sans tracking-wide">
                  Hợp tác chính thức với <span className="font-semibold text-black">Whenever</span>
                </p>
                <p className="mt-1 text-[10px] text-neutral-400 font-mono">© 2026 VIBE STORES</p>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
}
