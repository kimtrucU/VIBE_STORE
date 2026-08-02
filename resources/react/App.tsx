import React, { useState, useEffect } from 'react';
import { PRODUCTS } from './data/products';
import { Product, CartItem, CustomerInfo, Order } from './types';
import Navbar from './components/Navbar';
import ProductCard from './components/ProductCard';
import QuickViewModal from './components/QuickViewModal';
import CartDrawer from './components/CartDrawer';
import WishlistDrawer from './components/WishlistDrawer';
import ProfileModal from './components/ProfileModal';
import { initAuth, googleSignIn, logout } from './lib/firebase';
import { apiFetch } from './lib/api';
import {
  ArrowRight,
  ShieldCheck,
  Send,
  MapPin,
  Phone,
  Mail,
  Clock,
  ChevronRight,
  Sparkles,
  Info,
  CheckCircle2,
  AlertCircle,
  Copy,
  SlidersHorizontal,
  ChevronDown,
  RotateCcw
} from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';

export default function App() {
  // Page routing state
  const [currentPage, setCurrentPage] = useState<string>('home');

  // Firebase auth state
  const [user, setUser] = useState<any>(null);
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [idToken, setIdToken] = useState<string | null>(null);

  
  // Modal / drawer toggles
  const [cartOpen, setCartOpen] = useState(false);
  const [wishlistOpen, setWishlistOpen] = useState(false);
  const [profileOpen, setProfileOpen] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);

  // E-Commerce Data state
  const [cartItems, setCartItems] = useState<CartItem[]>(() => {
    const saved = localStorage.getItem('vibe_cart');
    return saved ? JSON.parse(saved) : [];
  });

  const [wishlist, setWishlist] = useState<Product[]>(() => {
    const saved = localStorage.getItem('vibe_wishlist');
    return saved ? JSON.parse(saved) : [];
  });

  const [customerInfo, setCustomerInfo] = useState<CustomerInfo>(() => {
    const saved = localStorage.getItem('vibe_customer_info');
    return saved ? JSON.parse(saved) : {
      fullName: 'Trần Minh Vỹ',
      email: 'vy.tran@gmail.com',
      phone: '0912345678',
      address: '86 Lê Lợi, Bến Nghé, Quận 1',
      city: 'Hồ Chí Minh',
    };
  });

  const [pastOrders, setPastOrders] = useState<Order[]>(() => {
    const saved = localStorage.getItem('vibe_past_orders');
    return saved ? JSON.parse(saved) : [];
  });

  const [currentOrder, setCurrentOrder] = useState<Order | null>(null);

  // Shop Filters and search
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [selectedSize, setSelectedSize] = useState<string>('all');
  const [maxPrice, setMaxPrice] = useState<number>(1500000);
  const [sortBy, setSortBy] = useState<string>('featured');

  // Interactive checkout states
  const [checkoutForm, setCheckoutForm] = useState<CustomerInfo>({ ...customerInfo });
  const [selectedPayment, setSelectedPayment] = useState<'COD' | 'MOMO' | 'VNPAY'>('COD');
  const [showPaymentSimulation, setShowPaymentSimulation] = useState(false);
  const [simulationCountdown, setSimulationCountdown] = useState(15);

  // Contact form state
  const [contactForm, setContactForm] = useState({ name: '', email: '', subject: '', message: '' });
  const [contactSubmitted, setContactSubmitted] = useState(false);

  // Newsletter signup
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [newsletterSubscribed, setNewsletterSubscribed] = useState(false);

  // Custom visual feedback / Toast system
  const [toasts, setToasts] = useState<{ id: string; message: string; type: 'success' | 'info' | 'error' }[]>([]);

  // Persistent storage synchronizer
  useEffect(() => {
    localStorage.setItem('vibe_cart', JSON.stringify(cartItems));
  }, [cartItems]);

  useEffect(() => {
    localStorage.setItem('vibe_wishlist', JSON.stringify(wishlist));
  }, [wishlist]);

  useEffect(() => {
    localStorage.setItem('vibe_customer_info', JSON.stringify(customerInfo));
  }, [customerInfo]);

  useEffect(() => {
    localStorage.setItem('vibe_past_orders', JSON.stringify(pastOrders));
  }, [pastOrders]);

  // Initialize Auth state listener
  useEffect(() => {
    const unsubscribe = initAuth(
      async (firebaseUser, token) => {
        setUser(firebaseUser);
        setAccessToken(token);
        try {
          const t = await firebaseUser.getIdToken();
          setIdToken(t);
          syncUserProfile(t, firebaseUser);
        } catch (err) {
          console.error('Error getting id token:', err);
        }
      },
      () => {
        setUser(null);
        setAccessToken(null);
        setIdToken(null);
      }
    );
    return () => unsubscribe();
  }, []);

  const syncUserProfile = async (tokenStr: string, firebaseUser: any) => {
    try {
      // 1. Sync user profile on backend (Render)
      const syncRes = await apiFetch('/api/auth/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${tokenStr}`
        },
        body: JSON.stringify({
          uid: firebaseUser.uid,
          email: firebaseUser.email,
          display_name: firebaseUser.displayName,
        })
      });
      if (!syncRes.ok) throw new Error('Failed to sync auth with backend');

      // Update customer info with Google profile info if empty
      setCustomerInfo(prev => ({
        ...prev,
        fullName: prev.fullName === 'Trần Minh Vỹ' && firebaseUser.displayName ? firebaseUser.displayName : prev.fullName,
        email: prev.email === 'vy.tran@gmail.com' && firebaseUser.email ? firebaseUser.email : prev.email,
      }));

      // 2. Fetch user's orders from Render API
      const ordersRes = await apiFetch('/api/orders', {
        headers: { 'Authorization': `Bearer ${tokenStr}` }
      });
      if (ordersRes.ok) {
        const data = await ordersRes.json();
        const dbOrders = data.orders ?? data;
        if (dbOrders && dbOrders.length > 0) {
          setPastOrders(dbOrders);
        }
      }

      // 3. Fetch user's wishlist from Render API
      const wishlistRes = await apiFetch('/api/wishlist', {
        headers: { 'Authorization': `Bearer ${tokenStr}` }
      });
      if (wishlistRes.ok) {
        const dbWishlistIds: number[] = await wishlistRes.json();
        const syncedWishlist = PRODUCTS.filter(p => dbWishlistIds.includes(p.id));
        setWishlist(syncedWishlist);
      }
    } catch (err) {
      console.error('Error syncing user database:', err);
    }
  };

  const handleGoogleSignIn = async () => {
    try {
      addToast('Đang kết nối tới Google Auth...', 'info');
      const result = await googleSignIn();
      if (result) {
        setUser(result.user);
        setAccessToken(result.accessToken);
        const t = await result.user.getIdToken();
        setIdToken(t);
        addToast('Đăng nhập thành công với Google!', 'success');
        syncUserProfile(t, result.user);
      }
    } catch (err) {
      console.error(err);
      addToast('Đăng nhập Google thất bại.', 'error');
    }
  };

  const handleLogout = async () => {
    try {
      await logout();
      setUser(null);
      setAccessToken(null);
      setIdToken(null);
      
      // Reset state back to local storage defaults
      const localOrders = localStorage.getItem('vibe_past_orders');
      setPastOrders(localOrders ? JSON.parse(localOrders) : []);
      const localWishlist = localStorage.getItem('vibe_wishlist');
      setWishlist(localWishlist ? JSON.parse(localWishlist) : []);
      
      addToast('Đã đăng xuất tài khoản Google.', 'info');
    } catch (err) {
      console.error(err);
      addToast('Đăng xuất thất bại.', 'error');
    }
  };


  // Synchronize checkoutForm prefill when customerInfo changes
  useEffect(() => {
    setCheckoutForm({ ...customerInfo });
  }, [customerInfo]);

  // Toast helper
  const addToast = (message: string, type: 'success' | 'info' | 'error' = 'success') => {
    const id = Date.now().toString();
    setToasts((prev) => [...prev, { id, message, type }]);
    setTimeout(() => {
      setToasts((prev) => prev.filter((t) => t.id !== id));
    }, 3500);
  };

  // Cart operations
  const handleAddToCart = (product: Product, size: string, qty: number = 1) => {
    setCartItems((prev) => {
      const existingIdx = prev.findIndex((item) => item.product.id === product.id && item.selectedSize === size);
      if (existingIdx > -1) {
        const updated = [...prev];
        updated[existingIdx].quantity += qty;
        return updated;
      } else {
        return [...prev, { product, selectedSize: size, quantity: qty }];
      }
    });
    addToast(`Đã thêm ${product.name} (Size: ${size}) vào giỏ hàng!`, 'success');
  };

  const handleBuyNow = (product: Product, size: string, qty: number = 1) => {
    // Check if item is already in cart, if not add it
    const existingIdx = cartItems.findIndex((item) => item.product.id === product.id && item.selectedSize === size);
    if (existingIdx === -1) {
      setCartItems((prev) => [...prev, { product, selectedSize: size, quantity: qty }]);
    }
    setSelectedProduct(null);
    setCurrentPage('checkout');
  };

  const handleUpdateCartQuantity = (index: number, qty: number) => {
    setCartItems((prev) => {
      const updated = [...prev];
      updated[index].quantity = qty;
      return updated;
    });
  };

  const handleRemoveCartItem = (index: number) => {
    const item = cartItems[index];
    setCartItems((prev) => prev.filter((_, idx) => idx !== index));
    addToast(`Đã xóa ${item.product.name} khỏi giỏ hàng.`, 'info');
  };

  // Wishlist operations
  const handleToggleWishlist = async (product: Product) => {
    const isPresent = wishlist.some((item) => item.id === product.id);
    if (isPresent) {
      setWishlist((prev) => prev.filter((item) => item.id !== product.id));
      addToast(`Đã xóa khỏi danh sách yêu thích`, 'info');

      if (idToken) {
        try {
          await apiFetch(`/api/wishlist/${product.id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${idToken}` }
          });
        } catch (err) {
          console.error('Failed to sync wishlist deletion:', err);
        }
      }
    } else {
      setWishlist((prev) => [...prev, product]);
      addToast(`Đã thêm vào danh sách yêu thích!`, 'success');

      if (idToken) {
        try {
          await apiFetch('/api/wishlist', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${idToken}`
            },
            body: JSON.stringify({ productId: product.id })
          });
        } catch (err) {
          console.error('Failed to sync wishlist insertion:', err);
        }
      }
    }
  };

  // Profile operations
  const handleSaveProfile = (info: CustomerInfo) => {
    setCustomerInfo(info);
    addToast('Đã cập nhật thông tin tài khoản thành viên!', 'success');
  };

  // Checkout submit
  const handlePlaceOrder = (e: React.FormEvent) => {
    e.preventDefault();
    if (cartItems.length === 0) {
      addToast('Giỏ hàng trống, không thể thanh toán.', 'error');
      return;
    }

    const subtotal = cartItems.reduce((acc, item) => acc + item.product.price * item.quantity, 0);
    const shipping = subtotal >= 1000000 ? 0 : 30000;
    const total = subtotal + shipping;

    const newOrder: Order = {
      id: `WV-${Math.floor(100000 + Math.random() * 900000)}`,
      items: [...cartItems],
      customerInfo: { ...checkoutForm },
      paymentMethod: selectedPayment,
      totalAmount: total,
      date: new Date().toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    };

    if (selectedPayment === 'COD') {
      processOrderSuccess(newOrder);
    } else {
      // Simulate Electronic payment scanning
      setCurrentOrder(newOrder);
      setShowPaymentSimulation(true);
      setSimulationCountdown(15);
    }
  };

  // Simulation countdown for MOMO/VNPAY QR codes
  useEffect(() => {
    let timer: NodeJS.Timeout;
    if (showPaymentSimulation && simulationCountdown > 0) {
      timer = setTimeout(() => {
        setSimulationCountdown((prev) => prev - 1);
      }, 1000);
    } else if (showPaymentSimulation && simulationCountdown === 0) {
      // Auto approve mock payment
      if (currentOrder) {
        processOrderSuccess(currentOrder);
      }
    }
    return () => clearTimeout(timer);
  }, [showPaymentSimulation, simulationCountdown]);

  const forceApprovePayment = () => {
    if (currentOrder) {
      processOrderSuccess(currentOrder);
    }
  };

  const processOrderSuccess = async (order: Order) => {
    setPastOrders((prev) => [order, ...prev]);
    setCurrentOrder(order);
    setCartItems([]); // Clear cart
    setShowPaymentSimulation(false);
    setCurrentPage('order-confirmation');
    addToast('Đơn hàng đã được đặt thành công!', 'success');

    if (idToken) {
      try {
        const response = await apiFetch('/api/orders', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${idToken}`
          },
          body: JSON.stringify({
            items: order.items.map(i => ({
              product_id: i.product.id,
              product_name: i.product.name,
              product_image: i.product.images?.[0] ?? '',
              size: i.selectedSize,
              price: i.product.price,
              quantity: i.quantity,
            })),
            customer_info: order.customerInfo,
            payment_method: order.paymentMethod,
            total_amount: order.totalAmount,
          })
        });
        if (response.ok) {
          addToast('Đơn hàng đã được lưu lên hệ thống!', 'success');
        }
      } catch (err) {
        console.error('Failed to sync order:', err);
      }
    }
  };

  // Filter Products based on sidebar choices
  const getFilteredProducts = () => {
    return PRODUCTS.filter((product) => {
      // Search check
      const query = searchQuery.toLowerCase().trim();
      if (query) {
        const matchesName = product.name.toLowerCase().includes(query);
        const matchesCat = product.category.toLowerCase().includes(query);
        const matchesDesc = product.description.toLowerCase().includes(query);
        if (!matchesName && !matchesCat && !matchesDesc) return false;
      }

      // Category check
      if (selectedCategory !== 'all' && product.category !== selectedCategory) {
        return false;
      }

      // Size check
      if (selectedSize !== 'all' && !product.sizes.includes(selectedSize)) {
        return false;
      }

      // Price check
      if (product.price > maxPrice) {
        return false;
      }

      // Quick tab check
      if (currentPage === 'new-arrival' && !product.isNewArrival) return false;
      if (currentPage === 'best-seller' && !product.isBestSeller) return false;

      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-asc') return a.price - b.price;
      if (sortBy === 'price-desc') return b.price - a.price;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0; // featured/default
    });
  };

  // Reset filters
  const handleResetFilters = () => {
    setSelectedCategory('all');
    setSelectedSize('all');
    setMaxPrice(1500000);
    setSortBy('featured');
    setSearchQuery('');
    addToast('Đã đặt lại các bộ lọc tìm kiếm', 'info');
  };

  // Handlers for footer / home page categories quick navigate
  const handleCategoryQuickLink = (catId: string) => {
    setSelectedCategory(catId);
    setCurrentPage('shop');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleContactSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setContactSubmitted(true);
    addToast('Cảm ơn bạn đã gửi liên hệ! VIBE sẽ phản hồi trong 2 giờ.', 'success');
    setContactForm({ name: '', email: '', subject: '', message: '' });
    setTimeout(() => setContactSubmitted(false), 5000);
  };

  const handleNewsletterSignup = (e: React.FormEvent) => {
    e.preventDefault();
    if (newsletterEmail) {
      setNewsletterSubscribed(true);
      addToast('Đã đăng ký nhận bản tin thành công! Nhận voucher 10%', 'success');
      setNewsletterEmail('');
      setTimeout(() => setNewsletterSubscribed(false), 5000);
    }
  };

  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
  };

  return (
    <div className="min-h-screen bg-slate-50 text-neutral-900 flex flex-col font-sans select-none antialiased">
      {/* Promo banner ticker at top */}
      <div id="promo-ticker" className="bg-black text-white text-[10px] font-mono tracking-widest uppercase py-2 px-4 text-center">
        VIBE x WHENEVER: OFF-WHITE & HEAVYWEIGHT ESSENTIALS ONSTAGE • MIỄN PHÍ VẬN CHUYỂN ĐƠN TỪ 1.000.000Đ
      </div>

      {/* Navigation menu */}
      <Navbar
        currentPage={currentPage}
        onNavigate={(page) => {
          setCurrentPage(page);
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }}
        cartCount={cartItems.reduce((sum, i) => sum + i.quantity, 0)}
        wishlistCount={wishlist.length}
        onOpenCart={() => setCartOpen(true)}
        onOpenWishlist={() => setWishlistOpen(true)}
        onOpenProfile={() => setProfileOpen(true)}
        searchQuery={searchQuery}
        onSearchChange={(q) => {
          setSearchQuery(q);
          if (currentPage !== 'shop') {
            setCurrentPage('shop');
          }
        }}
      />

      {/* Main Container Stage */}
      <main className="flex-grow">
        <AnimatePresence mode="wait">
          <motion.div
            key={currentPage}
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -15 }}
            transition={{ duration: 0.3 }}
          >
            {/* 1. HOME PAGE VIEW */}
            {currentPage === 'home' && (
              <div id="home-view">
                {/* Hero Editorial Banner */}
                <section className="relative h-[80vh] min-h-[500px] w-full bg-neutral-900 flex items-center overflow-hidden">
                  <div className="absolute inset-0 z-0">
                    <img
                      src="https://images.unsplash.com/photo-1483347756191-4a2407223bc0?auto=format&fit=crop&q=80&w=1600"
                      alt="Whenever Editorial Banner"
                      className="h-full w-full object-cover object-center opacity-45 mix-blend-luminosity scale-105 animate-pulse-slow"
                      referrerPolicy="no-referrer"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent" />
                  </div>

                  <div className="relative z-10 mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 text-white flex flex-col justify-center">
                    <motion.span
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      transition={{ delay: 0.2 }}
                      className="text-[10px] sm:text-xs font-bold font-mono tracking-widest text-neutral-300 uppercase flex items-center gap-2 mb-4"
                    >
                      <Sparkles className="h-4 w-4 text-amber-400" />
                      Hợp tác chính thức độc quyền
                    </motion.span>
                    
                    <motion.h1
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: 0.3, duration: 0.6 }}
                      className="font-display text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight uppercase leading-none"
                    >
                      THE VIBE <br />
                      <span className="text-transparent bg-clip-text bg-gradient-to-r from-neutral-200 to-neutral-400">
                        WHENEVER.
                      </span>
                    </motion.h1>

                    <motion.p
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: 0.4 }}
                      className="mt-6 text-sm sm:text-base text-neutral-300 max-w-lg leading-relaxed font-sans"
                    >
                      Chủ nghĩa tối giản đương đại kết hợp cùng văn hóa đường phố bụi bặm. Phiên bản áo thun cotton 250gsm dập nổi chuẩn nhãn tag Whenever chính hãng đã có mặt tại hệ thống VIBE Store.
                    </motion.p>

                    <motion.div
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: 0.5 }}
                      className="mt-10 flex flex-wrap gap-4"
                    >
                      <button
                        id="hero-shop-now"
                        onClick={() => setCurrentPage('shop')}
                        className="flex items-center justify-center gap-2 bg-white hover:bg-neutral-100 text-black font-semibold text-xs tracking-widest uppercase py-4 px-8 transition rounded-sm shadow-lg group"
                      >
                        Khám phá ngay
                        <ArrowRight className="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                      </button>
                      <button
                        id="hero-about-collab"
                        onClick={() => setCurrentPage('about')}
                        className="flex items-center justify-center gap-2 bg-transparent hover:bg-white/10 text-white border border-white/30 hover:border-white py-4 px-8 font-semibold text-xs tracking-widest uppercase transition rounded-sm"
                      >
                        Về sự hợp tác
                      </button>
                    </motion.div>
                  </div>
                </section>

                {/* Categories Grid Section */}
                <section className="py-20 bg-white">
                  <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="text-center max-w-xl mx-auto mb-14">
                      <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                        Sản phẩm cốt lõi
                      </span>
                      <h2 className="mt-2 font-display text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 uppercase">
                        DANH MỤC THỜI TRANG VIBE
                      </h2>
                      <div className="h-1 w-12 bg-black mx-auto mt-4" />
                    </div>

                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                      {[
                        { id: 'Áo Len', label: 'Áo Len', count: '2 sản phẩm', img: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=400' },
                        { id: 'Bomber', label: 'Bomber', count: '2 sản phẩm', img: 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&q=80&w=400' },
                        { id: 'Phụ kiện', label: 'Phụ kiện', count: '2 sản phẩm', img: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=400' },
                        { id: 'flannel', label: 'flannel', count: '2 sản phẩm', img: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&q=80&w=400' },
                        { id: 'Áo thun', label: 'Áo thun', count: '4 sản phẩm', img: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&q=80&w=400' },
                      ].map((cat) => (
                        <div
                          id={`quick-cat-${cat.id}`}
                          key={cat.id}
                          onClick={() => handleCategoryQuickLink(cat.id)}
                          className="group relative aspect-square overflow-hidden bg-neutral-100 rounded-sm cursor-pointer shadow-sm hover:shadow-md transition duration-300"
                        >
                          <img
                            src={cat.img}
                            alt={cat.label}
                            className="h-full w-full object-cover object-center group-hover:scale-110 transition duration-700"
                            loading="lazy"
                            referrerPolicy="no-referrer"
                          />
                          <div className="absolute inset-0 bg-black/35 group-hover:bg-black/55 transition duration-300" />
                          <div className="absolute inset-0 p-4 flex flex-col justify-end text-white text-center">
                            <span className="font-display font-bold text-xs tracking-widest uppercase">{cat.label}</span>
                            <span className="text-[10px] font-mono text-neutral-300 mt-0.5">{cat.count}</span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </section>

                {/* Featured Products Showcase */}
                <section className="py-16 bg-neutral-50 border-t border-b border-neutral-200/50">
                  <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col sm:flex-row items-baseline justify-between mb-10 border-b border-neutral-200 pb-4">
                      <div>
                        <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                          Hot Items
                        </span>
                        <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
                          BỘ SƯU TẬP MỚI RA MẮT
                        </h2>
                      </div>
                      <button
                        id="view-all-shop"
                        onClick={() => {
                          setSelectedCategory('all');
                          setCurrentPage('shop');
                        }}
                        className="text-xs text-neutral-500 hover:text-black font-semibold uppercase tracking-wider flex items-center gap-1 hover:underline mt-2 sm:mt-0"
                      >
                        Xem tất cả cửa hàng <ChevronRight className="h-4 w-4" />
                      </button>
                    </div>

                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                      {PRODUCTS.slice(0, 4).map((product) => (
                        <ProductCard
                          key={product.id}
                          product={product}
                          onViewDetails={(p) => setSelectedProduct(p)}
                          onAddToCart={(p, sz) => handleAddToCart(p, sz, 1)}
                          onToggleWishlist={handleToggleWishlist}
                          isWishlisted={wishlist.some((w) => w.id === product.id)}
                        />
                      ))}
                    </div>
                  </div>
                </section>

                {/* Highlight banner split screen */}
                <section className="relative py-24 bg-black text-white overflow-hidden">
                  <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                      <div className="lg:col-span-7 space-y-6">
                        <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase border border-neutral-800 px-3 py-1 rounded-full bg-neutral-900/60 w-fit block">
                          Whenever Authentic Denim & Leather
                        </span>
                        <h2 className="font-display text-3xl sm:text-5xl font-black tracking-tight uppercase leading-none">
                          ÁO KHOÁC BOMBER <br />
                          UTILITY OVERSIZED
                        </h2>
                        <p className="text-sm text-neutral-400 leading-relaxed max-w-xl">
                          Sự kết hợp bùng nổ giữa tinh thần đường phố sành điệu và thiết kế quân đội cổ điển. Lớp ngoài dệt sợi nylon dù chống thấm nước, khóa kéo utility dẹt gầm tay áo chính hãng mang lại form phồng cực chất.
                        </p>
                        <div className="flex gap-8 py-2 font-mono">
                          <div>
                            <span className="block text-2xl font-bold text-white">4.9★</span>
                            <span className="text-[10px] text-neutral-500 uppercase tracking-widest">Đánh giá cao</span>
                          </div>
                          <div className="border-l border-neutral-800 pl-8">
                            <span className="block text-2xl font-bold text-white">380gsm</span>
                            <span className="text-[10px] text-neutral-500 uppercase tracking-widest">Nỉ chân cua Pháp</span>
                          </div>
                          <div className="border-l border-neutral-800 pl-8">
                            <span className="block text-2xl font-bold text-white">100%</span>
                            <span className="text-[10px] text-neutral-500 uppercase tracking-widest">Chính hãng tag</span>
                          </div>
                        </div>
                        <div className="pt-4">
                          <button
                            id="banner-shop-jacket"
                            onClick={() => {
                              setSelectedCategory('Bomber');
                              setCurrentPage('shop');
                              window.scrollTo({ top: 0, behavior: 'smooth' });
                            }}
                            className="bg-white hover:bg-neutral-100 text-black font-semibold text-xs tracking-widest uppercase py-4 px-8 rounded-sm transition flex items-center gap-2"
                          >
                            Mua bộ sưu tập Jacket <ArrowRight className="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                      <div className="lg:col-span-5 relative aspect-square bg-neutral-900/40 rounded-sm overflow-hidden border border-neutral-800">
                        <img
                          src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=600"
                          alt="Whenever bomber jacket close up"
                          className="w-full h-full object-cover object-center scale-100 hover:scale-105 transition duration-700 opacity-80"
                          referrerPolicy="no-referrer"
                        />
                      </div>
                    </div>
                  </div>
                </section>

                {/* Best Sellers block on home */}
                <section className="py-16 bg-white">
                  <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="text-center max-w-xl mx-auto mb-12">
                      <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                        Most Popular
                      </span>
                      <h2 className="mt-1 font-display text-2xl font-bold text-neutral-900 uppercase">
                        SẢN PHẨM BÁN CHẠY NHẤT
                      </h2>
                    </div>

                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                      {PRODUCTS.filter(p => p.isBestSeller).slice(0, 4).map((product) => (
                        <ProductCard
                          key={product.id}
                          product={product}
                          onViewDetails={(p) => setSelectedProduct(p)}
                          onAddToCart={(p, sz) => handleAddToCart(p, sz, 1)}
                          onToggleWishlist={handleToggleWishlist}
                          isWishlisted={wishlist.some((w) => w.id === product.id)}
                        />
                      ))}
                    </div>
                  </div>
                </section>
              </div>
            )}

            {/* 2. SHOP PAGE VIEW / 3. NEW ARRIVALS / 4. BEST SELLERS */}
            {(currentPage === 'shop' || currentPage === 'new-arrival' || currentPage === 'best-seller') && (
              <div id="shop-view" className="py-10">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                  {/* Shop header banner */}
                  <div className="border-b border-neutral-200 pb-6 mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                      <h1 className="font-display text-2xl sm:text-3xl font-extrabold text-neutral-900 uppercase tracking-tight">
                        {currentPage === 'new-arrival'
                          ? 'BỘ SƯU TẬP MỚI'
                          : currentPage === 'best-seller'
                          ? 'SẢN PHẨM BÁN CHẠY'
                          : 'CỬA HÀNG THỜI TRANG VIBE'}
                      </h1>
                      <p className="text-xs text-neutral-500 mt-1 font-sans">
                        Hiển thị {getFilteredProducts().length} thiết kế áo thun, hoodie, jacket Whenever cao cấp.
                      </p>
                    </div>

                    {/* Reset filter button if filters are active */}
                    {(selectedCategory !== 'all' || selectedSize !== 'all' || maxPrice < 1500000 || searchQuery) && (
                      <button
                        id="reset-filters-btn"
                        onClick={handleResetFilters}
                        className="flex items-center gap-1.5 text-xs text-red-600 hover:text-red-700 font-semibold border border-red-200 hover:border-red-300 bg-red-50/50 py-2 px-3 rounded-sm transition self-start md:self-auto"
                      >
                        <RotateCcw className="h-3.5 w-3.5" /> Đặt lại bộ lọc
                      </button>
                    )}
                  </div>

                  {/* Sidebar Filters & Grid Layout split */}
                  <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    {/* Filters column panel */}
                    <aside className="lg:col-span-1 space-y-6 bg-white p-6 border border-neutral-100 rounded-sm shadow-xs self-start">
                      <div className="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <span className="text-xs font-bold font-display uppercase text-neutral-800 flex items-center gap-1.5">
                          <SlidersHorizontal className="h-4 w-4 text-neutral-800" />
                          Bộ lọc sản phẩm
                        </span>
                      </div>

                      {/* Filter by category */}
                      <div className="space-y-2">
                        <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">Danh mục</span>
                        <div className="flex flex-col gap-1.5">
                          {[
                            { id: 'all', label: 'Tất cả sản phẩm' },
                            { id: 'Áo Len', label: 'Áo Len' },
                            { id: 'Bomber', label: 'Bomber' },
                            { id: 'Phụ kiện', label: 'Phụ kiện' },
                            { id: 'flannel', label: 'flannel' },
                            { id: 'handmade', label: 'handmade' },
                            { id: 'slippers', label: 'slippers' },
                            { id: 'Áo khoác nỉ', label: 'Áo khoác nỉ' },
                            { id: 'Áo thun', label: 'Áo thun' },
                            { id: 'lougewear', label: 'lougewear' },
                            { id: 'Quần ngắn', label: 'Quần ngắn' },
                          ].map((cat) => (
                            <button
                              id={`filter-cat-${cat.id}`}
                              key={cat.id}
                              onClick={() => setSelectedCategory(cat.id)}
                              className={`text-left text-xs font-semibold py-1 px-2 rounded-sm transition flex justify-between items-center ${
                                selectedCategory === cat.id
                                  ? 'bg-black text-white'
                                  : 'text-neutral-600 hover:bg-neutral-50 hover:text-black'
                              }`}
                            >
                              <span>{cat.label}</span>
                            </button>
                          ))}
                        </div>
                      </div>

                      {/* Filter by size */}
                      <div className="space-y-2 pt-2 border-t border-neutral-100">
                        <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">Kích cỡ</span>
                        <div className="flex flex-wrap gap-2">
                          {['all', 'S', 'M', 'L', 'XL', 'Free Size'].map((size) => (
                            <button
                              id={`filter-size-${size}`}
                              key={size}
                              onClick={() => setSelectedSize(size)}
                              className={`h-9 min-w-[2.25rem] px-1.5 font-mono text-xs font-bold transition flex items-center justify-center rounded-sm border ${
                                selectedSize === size
                                  ? 'border-black bg-black text-white'
                                  : 'border-neutral-200 bg-white text-neutral-700 hover:border-black'
                              }`}
                            >
                              {size === 'all' ? 'TẤT CẢ' : size}
                            </button>
                          ))}
                        </div>
                      </div>

                      {/* Filter by price */}
                      <div className="space-y-3 pt-2 border-t border-neutral-100">
                        <div className="flex justify-between items-baseline">
                          <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">Khoảng Giá</span>
                          <span className="text-xs font-mono font-bold text-neutral-800">{formatVND(maxPrice)}</span>
                        </div>
                        <input
                          id="price-range-slider"
                          type="range"
                          min="300000"
                          max="1500000"
                          step="50000"
                          value={maxPrice}
                          onChange={(e) => setMaxPrice(Number(e.target.value))}
                          className="w-full accent-black cursor-pointer bg-neutral-200 h-1 rounded-lg"
                        />
                        <div className="flex justify-between text-[10px] text-neutral-400 font-mono">
                          <span>300Kđ</span>
                          <span>1.5Mđ</span>
                        </div>
                      </div>

                      {/* Sorting filter */}
                      <div className="space-y-2 pt-2 border-t border-neutral-100">
                        <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">Sắp xếp theo</span>
                        <div className="relative">
                          <select
                            id="sorting-select"
                            value={sortBy}
                            onChange={(e) => setSortBy(e.target.value)}
                            className="w-full appearance-none rounded-sm border border-neutral-200 bg-white py-2 px-3 pr-10 text-xs text-neutral-800 focus:border-black focus:outline-none"
                          >
                            <option value="featured">Mặc định / Nổi bật</option>
                            <option value="price-asc">Giá từ thấp đến cao</option>
                            <option value="price-desc">Giá từ cao đến thấp</option>
                            <option value="rating">Đánh giá tốt nhất</option>
                          </select>
                          <ChevronDown className="absolute top-1/2 right-3 h-3.5 w-3.5 -translate-y-1/2 pointer-events-none text-neutral-400" />
                        </div>
                      </div>
                    </aside>

                    {/* Products Grid column */}
                    <div className="lg:col-span-3">
                      {getFilteredProducts().length === 0 ? (
                        <div className="flex flex-col items-center justify-center text-center py-24 bg-white border border-neutral-100 rounded-sm">
                          <AlertCircle className="h-10 w-10 text-neutral-300" />
                          <h3 className="mt-4 font-display font-semibold text-sm uppercase text-neutral-800">Không tìm thấy sản phẩm</h3>
                          <p className="mt-1 text-xs text-neutral-500 max-w-sm">
                            Hãy thử nới lỏng mức giá lọc hoặc chuyển đổi danh mục, kích thước để tìm sản phẩm mong muốn.
                          </p>
                          <button
                            id="reset-filter-fallback"
                            onClick={handleResetFilters}
                            className="mt-6 bg-black hover:bg-neutral-800 text-white py-2.5 px-6 font-semibold text-xs tracking-widest uppercase transition rounded-sm"
                          >
                            Xóa bộ lọc tìm kiếm
                          </button>
                        </div>
                      ) : (
                        <div className="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-12">
                          {getFilteredProducts().map((product) => (
                            <ProductCard
                              key={product.id}
                              product={product}
                              onViewDetails={(p) => setSelectedProduct(p)}
                              onAddToCart={(p, sz) => handleAddToCart(p, sz, 1)}
                              onToggleWishlist={handleToggleWishlist}
                              isWishlisted={wishlist.some((w) => w.id === product.id)}
                            />
                          ))}
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* 5. ABOUT PAGE VIEW */}
            {currentPage === 'about' && (
              <div id="about-view" className="py-16">
                <div className="mx-auto max-w-5xl px-4 sm:px-6">
                  {/* Editorial layout */}
                  <div className="text-center max-w-2xl mx-auto mb-16">
                    <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                      Brand Philosophy
                    </span>
                    <h1 className="mt-2 font-display text-3xl sm:text-5xl font-extrabold tracking-tight text-neutral-900 uppercase">
                      VỀ THƯƠNG HIỆU VIBE
                    </h1>
                    <p className="mt-4 text-xs sm:text-sm text-neutral-500 font-mono tracking-widest uppercase">
                      EST. 2026 / CHỦ NGHĨA TỐI GIẢN CHÍNH HÃNG WHENEVER
                    </p>
                    <div className="h-1 w-12 bg-black mx-auto mt-6" />
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                    <div className="space-y-6">
                      <h2 className="font-display text-xl sm:text-2xl font-bold text-neutral-800 uppercase tracking-tight">
                        SỰ HỢP TÁC KHỞI NGUỒN TỪ CHẤT LIỆU
                      </h2>
                      <p className="text-sm text-neutral-600 leading-relaxed font-sans">
                        <b>VIBE</b> ra đời như một không gian trưng bày cao cấp dành riêng cho những tín đồ say mê chủ nghĩa tối giản và văn hóa đường phố sành điệu. Qua mối quan hệ đối tác chính thức với <b>Whenever</b>, chúng tôi mang tới những sản phẩm may mặc có chất lượng khắt khe nhất.
                      </p>
                      <p className="text-sm text-neutral-600 leading-relaxed font-sans">
                        Chúng tôi tin rằng, một sản phẩm thời trang xuất sắc không cần quá nhiều chi tiết cầu kỳ để thu hút sự chú ý. Tinh thần thời thượng đích thực nằm ở chất vải dệt nặng dặn (heavyweight), form dáng rủ chuẩn xác và kỹ thuật thêu nổi dập nhãn mác dẻo không bết dính.
                      </p>
                    </div>
                    <div className="aspect-video sm:aspect-square bg-neutral-100 rounded-sm overflow-hidden border border-neutral-100 shadow-lg">
                      <img
                        src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=800"
                        alt="Clothing showroom styling"
                        className="w-full h-full object-cover object-center scale-100 hover:scale-105 transition duration-700"
                        referrerPolicy="no-referrer"
                      />
                    </div>
                  </div>

                  {/* Brand core values block */}
                  <div className="bg-neutral-950 text-white rounded-md p-8 sm:p-12 mb-16 border border-neutral-900 relative overflow-hidden">
                    <div className="absolute top-0 right-0 h-64 w-64 bg-radial from-neutral-800/20 to-transparent -mr-20 -mt-20 rounded-full" />
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8 text-center relative z-10">
                      <div className="space-y-3">
                        <span className="text-2xl font-black font-display text-neutral-400">01</span>
                        <h3 className="font-display text-sm font-bold uppercase tracking-widest text-white">Chính Hãng 100%</h3>
                        <p className="text-xs text-neutral-400 leading-relaxed">
                          Toàn bộ sản phẩm Whenever đều được dập tag vải, mác sườn chống hàng giả chính hãng, có mã kiểm duyệt đầy đủ.
                        </p>
                      </div>
                      <div className="space-y-3 border-t md:border-t-0 md:border-l md:border-r border-neutral-800 pt-6 md:pt-0 px-4">
                        <span className="text-2xl font-black font-display text-neutral-400">02</span>
                        <h3 className="font-display text-sm font-bold uppercase tracking-widest text-white">Chất Lượng Vượt Trội</h3>
                        <p className="text-xs text-neutral-400 leading-relaxed">
                          Sử dụng các loại vải nhập khẩu từ 250gsm đến 380gsm dày dặn, bền bỉ và giữ form nguyên vẹn sau nhiều lần giặt.
                        </p>
                      </div>
                      <div className="space-y-3 border-t md:border-t-0 pt-6 md:pt-0">
                        <span className="text-2xl font-black font-display text-neutral-400">03</span>
                        <h3 className="font-display text-sm font-bold uppercase tracking-widest text-white">Trải Nghiệm Đỉnh Cao</h3>
                        <p className="text-xs text-neutral-400 leading-relaxed">
                          Quy trình đóng gói hộp carton cứng cao cấp, bảo hành đổi trả size linh hoạt và tích điểm thành viên Black Card.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* 6. CONTACT PAGE VIEW */}
            {currentPage === 'contact' && (
              <div id="contact-view" className="py-16">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                  <div className="text-center max-w-xl mx-auto mb-14">
                    <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                      Get In Touch
                    </span>
                    <h1 className="mt-2 font-display text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 uppercase">
                      LIÊN HỆ VỚI VIBE STORES
                    </h1>
                    <div className="h-1 w-12 bg-black mx-auto mt-4" />
                  </div>

                  <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    {/* Information panel */}
                    <div className="lg:col-span-5 space-y-6">
                      <h2 className="font-display text-lg font-bold text-neutral-900 uppercase tracking-widest mb-4">
                        HỆ THỐNG TRUNG TÂM KHÁCH HÀNG
                      </h2>

                      <div className="space-y-4">
                        <div className="flex gap-4 items-start p-4 bg-white border border-neutral-100 rounded-sm">
                          <MapPin className="h-5 w-5 text-neutral-800 flex-shrink-0 mt-0.5" />
                          <div className="text-xs space-y-1">
                            <h4 className="font-bold text-neutral-800">Trụ sở Flagship VIBE Sài Gòn:</h4>
                            <p className="text-neutral-500">86 Lê Lợi, Bến Nghé, Quận 1, Tp. Hồ Chí Minh</p>
                          </div>
                        </div>

                        <div className="flex gap-4 items-start p-4 bg-white border border-neutral-100 rounded-sm">
                          <Phone className="h-5 w-5 text-neutral-800 flex-shrink-0 mt-0.5" />
                          <div className="text-xs space-y-1">
                            <h4 className="font-bold text-neutral-800">Đường dây nóng hỗ trợ 24/7:</h4>
                            <p className="text-neutral-500">1900 8198 (Nhánh 2 - Khiếu nại, đổi trả size)</p>
                          </div>
                        </div>

                        <div className="flex gap-4 items-start p-4 bg-white border border-neutral-100 rounded-sm">
                          <Mail className="h-5 w-5 text-neutral-800 flex-shrink-0 mt-0.5" />
                          <div className="text-xs space-y-1">
                            <h4 className="font-bold text-neutral-800">Hòm thư điện tử chính thức:</h4>
                            <p className="text-neutral-500">contact@vibe.vn / support@whenever.com</p>
                          </div>
                        </div>

                        <div className="flex gap-4 items-start p-4 bg-white border border-neutral-100 rounded-sm">
                          <Clock className="h-5 w-5 text-neutral-800 flex-shrink-0 mt-0.5" />
                          <div className="text-xs space-y-1">
                            <h4 className="font-bold text-neutral-800">Thời gian mở cửa showroom:</h4>
                            <p className="text-neutral-500">Hàng ngày từ 09:30 AM - 10:00 PM (Kể cả ngày Lễ)</p>
                          </div>
                        </div>
                      </div>

                      {/* Interactive Mock Map */}
                      <div className="relative h-64 w-full bg-neutral-900 rounded-sm overflow-hidden border border-neutral-800">
                        {/* Styled raw dark-mode grid map */}
                        <div className="absolute inset-0 bg-neutral-950 opacity-90 p-4 flex flex-col justify-between font-mono text-[9px] text-neutral-500">
                          <div className="flex justify-between">
                            <span>GRID UNIT: 12-N</span>
                            <span>LAT: 10.7769° N / LNF: 106.7009° E</span>
                          </div>
                          {/* Pulsing flag tag */}
                          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
                            <span className="relative flex h-3 w-3 mb-1">
                              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                              <span className="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                            </span>
                            <span className="bg-white text-black font-sans font-bold text-[10px] py-1 px-2.5 rounded shadow-md border uppercase tracking-widest">
                              VIBE FLAGSHIP
                            </span>
                          </div>
                          <div className="flex justify-between items-end">
                            <span>SCALE: 1:500</span>
                            <span className="text-neutral-400">Hồ Chí Minh City Map v4.1</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Contact form panel */}
                    <div className="lg:col-span-7 bg-white p-6 sm:p-8 border border-neutral-100 rounded-sm shadow-xs">
                      <h2 className="font-display text-lg font-bold text-neutral-900 uppercase tracking-widest mb-6">
                        GỬI TIN NHẮN CHO VIBE
                      </h2>

                      {contactSubmitted ? (
                        <div className="p-6 text-center bg-green-50 text-green-800 rounded-sm space-y-3">
                          <CheckCircle2 className="h-10 w-10 text-green-600 mx-auto" />
                          <h4 className="font-bold text-sm">Gửi liên hệ thành công!</h4>
                          <p className="text-xs max-w-sm mx-auto">
                            Nhân viên chăm sóc khách hàng của VIBE sẽ nhanh chóng liên lạc hỗ trợ bạn thông qua Email và điện thoại trong vòng 2 tiếng tới.
                          </p>
                        </div>
                      ) : (
                        <form onSubmit={handleContactSubmit} className="space-y-4">
                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                              <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Họ và Tên</label>
                              <input
                                type="text"
                                required
                                value={contactForm.name}
                                onChange={(e) => setContactForm({ ...contactForm, name: e.target.value })}
                                className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                                placeholder="Nguyễn Văn A"
                              />
                            </div>
                            <div>
                              <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Hòm thư Email</label>
                              <input
                                type="email"
                                required
                                value={contactForm.email}
                                onChange={(e) => setContactForm({ ...contactForm, email: e.target.value })}
                                className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                                placeholder="nguyenvana@gmail.com"
                              />
                            </div>
                          </div>

                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Chủ đề cần tư vấn</label>
                            <input
                              type="text"
                              required
                              value={contactForm.subject}
                              onChange={(e) => setContactForm({ ...contactForm, subject: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="Yêu cầu đổi trả hàng / Đăng ký CTV / Tư vấn size"
                            />
                          </div>

                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Nội dung tin nhắn</label>
                            <textarea
                              rows={5}
                              required
                              value={contactForm.message}
                              onChange={(e) => setContactForm({ ...contactForm, message: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="Nhập chi tiết thông điệp của bạn..."
                            />
                          </div>

                          <div className="pt-2">
                            <button
                              id="contact-form-submit"
                              type="submit"
                              className="w-full sm:w-auto flex items-center justify-center gap-2 bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-3.5 px-8 transition rounded-sm"
                            >
                              Gửi thông điệp ngay
                              <Send className="h-4 w-4" />
                            </button>
                          </div>
                        </form>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* 7. CHECKOUT PAGE VIEW */}
            {currentPage === 'checkout' && (
              <div id="checkout-view" className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                  <div className="border-b border-neutral-200 pb-5 mb-8">
                    <h1 className="font-display text-2xl font-extrabold text-neutral-900 uppercase tracking-tight">Thanh toán đơn hàng</h1>
                    <p className="text-xs text-neutral-500 mt-1">Đảm bảo nhập chính xác địa chỉ giao hàng để Whenever gửi mác tag bảo vệ.</p>
                  </div>

                  <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    {/* Information input panel */}
                    <div className="lg:col-span-7 bg-white p-6 sm:p-8 border border-neutral-100 rounded-sm shadow-xs">
                      <h2 className="font-display text-xs font-bold text-neutral-900 uppercase tracking-widest mb-6 pb-2 border-b border-neutral-100">
                        1. Thông tin giao hàng nhận mã
                      </h2>

                      <form id="checkout-main-form" onSubmit={handlePlaceOrder} className="space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Họ và tên người nhận</label>
                            <input
                              type="text"
                              required
                              value={checkoutForm.fullName}
                              onChange={(e) => setCheckoutForm({ ...checkoutForm, fullName: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="Trần Minh Vỹ"
                            />
                          </div>
                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Số điện thoại liên hệ</label>
                            <input
                              type="tel"
                              required
                              value={checkoutForm.phone}
                              onChange={(e) => setCheckoutForm({ ...checkoutForm, phone: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="0912345678"
                            />
                          </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Địa chỉ hòm thư Email</label>
                            <input
                              type="email"
                              required
                              value={checkoutForm.email}
                              onChange={(e) => setCheckoutForm({ ...checkoutForm, email: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="vy.tran@gmail.com"
                            />
                          </div>
                          <div>
                            <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Tỉnh / Thành phố</label>
                            <input
                              type="text"
                              required
                              value={checkoutForm.city}
                              onChange={(e) => setCheckoutForm({ ...checkoutForm, city: e.target.value })}
                              className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                              placeholder="Hồ Chí Minh"
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Địa chỉ nhận hàng chi tiết</label>
                          <input
                            type="text"
                            required
                            value={checkoutForm.address}
                            onChange={(e) => setCheckoutForm({ ...checkoutForm, address: e.target.value })}
                            className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                            placeholder="Số nhà, tên đường, phường/xã, quận/huyện"
                          />
                        </div>

                        <div>
                          <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Ghi chú giao hàng (Tùy chọn)</label>
                          <textarea
                            rows={3}
                            value={checkoutForm.notes}
                            onChange={(e) => setCheckoutForm({ ...checkoutForm, notes: e.target.value })}
                            className="mt-1 w-full border border-neutral-200 bg-slate-50 px-3.5 py-2.5 text-xs rounded-sm focus:bg-white focus:border-black focus:outline-none"
                            placeholder="Ghi chú về thời gian nhận, hướng dẫn rẽ hẻm..."
                          />
                        </div>

                        {/* Payment Method Selector */}
                        <div className="pt-4">
                          <h2 className="font-display text-xs font-bold text-neutral-900 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-100">
                            2. Phương thức thanh toán cao cấp
                          </h2>

                          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {/* COD option */}
                            <label className={`relative flex flex-col p-4 border rounded-sm cursor-pointer hover:bg-neutral-50 transition ${
                              selectedPayment === 'COD' ? 'border-black bg-neutral-50/50' : 'border-neutral-200 bg-white'
                            }`}>
                              <input
                                type="radio"
                                name="payment_method"
                                value="COD"
                                checked={selectedPayment === 'COD'}
                                onChange={() => setSelectedPayment('COD')}
                                className="sr-only"
                              />
                              <span className="text-xs font-bold uppercase tracking-wider text-neutral-800">Thanh toán COD</span>
                              <span className="text-[10px] text-neutral-500 mt-1">Trả tiền mặt khi nhận hàng (Được xem hàng)</span>
                            </label>

                            {/* MOMO Option */}
                            <label className={`relative flex flex-col p-4 border rounded-sm cursor-pointer hover:bg-neutral-50 transition ${
                              selectedPayment === 'MOMO' ? 'border-black bg-neutral-50/50' : 'border-neutral-200 bg-white'
                            }`}>
                              <input
                                type="radio"
                                name="payment_method"
                                value="MOMO"
                                checked={selectedPayment === 'MOMO'}
                                onChange={() => setSelectedPayment('MOMO')}
                                className="sr-only"
                              />
                              <span className="text-xs font-bold uppercase tracking-wider text-pink-600 flex items-center gap-1">
                                Ví MoMo
                              </span>
                              <span className="text-[10px] text-neutral-500 mt-1">Quét mã QR qua ví MoMo cực kỳ tiện lợi</span>
                            </label>

                            {/* VNPAY Option */}
                            <label className={`relative flex flex-col p-4 border rounded-sm cursor-pointer hover:bg-neutral-50 transition ${
                              selectedPayment === 'VNPAY' ? 'border-black bg-neutral-50/50' : 'border-neutral-200 bg-white'
                            }`}>
                              <input
                                type="radio"
                                name="payment_method"
                                value="VNPAY"
                                checked={selectedPayment === 'VNPAY'}
                                onChange={() => setSelectedPayment('VNPAY')}
                                className="sr-only"
                              />
                              <span className="text-xs font-bold uppercase tracking-wider text-blue-600">Cổng VNPay</span>
                              <span className="text-[10px] text-neutral-500 mt-1">Chuyển khoản / quét mã QR Mobile Banking</span>
                            </label>
                          </div>
                        </div>

                        {/* Order action */}
                        <div className="pt-6">
                          <button
                            id="checkout-order-now"
                            type="submit"
                            className="w-full flex items-center justify-center gap-2 bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-4 transition rounded-sm shadow-md"
                          >
                            Xác nhận đặt hàng ngay
                          </button>
                        </div>
                      </form>
                    </div>

                    {/* Order summary panel */}
                    <div className="lg:col-span-5 space-y-6">
                      <div className="bg-white p-6 border border-neutral-100 rounded-sm shadow-xs">
                        <h3 className="font-display text-xs font-bold text-neutral-900 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-100">
                          Sản phẩm mua sắm ({cartItems.length})
                        </h3>

                        {cartItems.length === 0 ? (
                          <p className="text-xs text-neutral-400 italic py-4">Giỏ hàng của bạn đang trống.</p>
                        ) : (
                          <div className="divide-y divide-neutral-100 max-h-60 overflow-y-auto pr-1">
                            {cartItems.map((item, idx) => (
                              <div key={idx} className="flex py-3 gap-3">
                                <div className="h-14 w-11 bg-neutral-50 border rounded-sm overflow-hidden flex-shrink-0">
                                  <img src={item.product.images[0]} alt={item.product.name} className="h-full w-full object-cover" referrerPolicy="no-referrer" />
                                </div>
                                <div className="flex-grow text-xs min-w-0">
                                  <h4 className="font-bold text-neutral-800 truncate">{item.product.name}</h4>
                                  <p className="text-[10px] text-neutral-400 font-mono mt-0.5">SIZE: {item.selectedSize} / SL: {item.quantity}</p>
                                </div>
                                <div className="text-right text-xs font-mono font-bold text-neutral-800 flex-shrink-0">
                                  {formatVND(item.product.price * item.quantity)}
                                </div>
                              </div>
                            ))}
                          </div>
                        )}

                        {/* Pricing details */}
                        <div className="border-t border-neutral-100 pt-4 mt-4 space-y-2 text-xs">
                          <div className="flex justify-between text-neutral-500">
                            <span>Tạm tính hàng</span>
                            <span className="font-mono">{formatVND(cartItems.reduce((sum, item) => sum + item.product.price * item.quantity, 0))}</span>
                          </div>
                          <div className="flex justify-between text-neutral-500">
                            <span>Phí vận chuyển chuẩn</span>
                            <span className="font-mono">
                              {cartItems.reduce((sum, item) => sum + item.product.price * item.quantity, 0) >= 1000000 ? 'Miễn phí' : formatVND(30000)}
                            </span>
                          </div>
                          <div className="flex justify-between border-t border-neutral-100 pt-3 font-bold text-sm text-black">
                            <span>Tổng thanh toán</span>
                            <span className="font-mono">
                              {formatVND(
                                cartItems.reduce((sum, item) => sum + item.product.price * item.quantity, 0) +
                                (cartItems.reduce((sum, item) => sum + item.product.price * item.quantity, 0) >= 1000000 ? 0 : 30000)
                              )}
                            </span>
                          </div>
                        </div>
                      </div>

                      {/* Store pledge */}
                      <div className="bg-neutral-900 text-white p-6 rounded-sm border border-neutral-800 text-xs space-y-3.5">
                        <h4 className="font-display font-semibold uppercase tracking-widest text-neutral-200">Cam kết độc quyền VIBE</h4>
                        <p className="text-neutral-400 leading-relaxed text-[11px]">
                          Tất cả đơn đặt hàng đều được kiểm tra tag bảo vệ thương hiệu Whenever tỉ mỉ trước khi niêm phong. Khách hàng được quyền kiểm tra sản phẩm trước khi thanh toán cho đơn vị COD.
                        </p>
                        <div className="flex items-center gap-1.5 text-[10px] text-neutral-300 font-mono">
                          <ShieldCheck className="h-4 w-4 text-white" />
                          <span>SECURE WHENEVER CODES VERIFIED</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* 8. ORDER CONFIRMATION PAGE VIEW */}
            {currentPage === 'order-confirmation' && currentOrder && (
              <div id="order-confirm-view" className="py-20 bg-white">
                <div className="mx-auto max-w-2xl px-4 text-center space-y-6">
                  {/* Icon anim */}
                  <div className="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-50 border border-green-100 mb-2">
                    <CheckCircle2 className="h-8 w-8 text-green-600 fill-current bg-white rounded-full" />
                  </div>

                  <div className="space-y-2">
                    <span className="text-[10px] font-bold font-mono tracking-widest text-green-600 uppercase">Đặt hàng thành công</span>
                    <h1 className="font-display text-2xl sm:text-3xl font-black text-neutral-900 uppercase tracking-tight">CẢM ƠN BẠN ĐÃ MUA SẮM</h1>
                    <p className="text-xs text-neutral-500 max-w-md mx-auto">
                      Đơn hàng của bạn đã được tiếp nhận và đang trong quá trình đóng mác Whenever tiêu chuẩn để chuyển cho đối tác vận chuyển.
                    </p>
                  </div>

                  {/* Order Summary details Box */}
                  <div className="border border-neutral-100 bg-neutral-50 p-6 rounded-sm text-left text-xs space-y-4">
                    <div className="flex justify-between items-baseline border-b border-neutral-200 pb-3">
                      <span className="font-mono font-bold text-neutral-800">MÃ ĐƠN: {currentOrder.id}</span>
                      <span className="text-neutral-400 font-mono">{currentOrder.date}</span>
                    </div>

                    <div className="space-y-2">
                      <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">Sản phẩm đã đặt</span>
                      <div className="space-y-1.5">
                        {currentOrder.items.map((item, idx) => (
                          <div key={idx} className="flex justify-between">
                            <span className="text-neutral-700">
                              {item.product.name} (Size: <b className="text-neutral-900">{item.selectedSize}</b>) x <b className="font-mono">{item.quantity}</b>
                            </span>
                            <span className="font-mono text-neutral-800">{formatVND(item.product.price * item.quantity)}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div className="border-t border-neutral-200 pt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 text-[11px] text-neutral-600">
                      <div>
                        <span className="block font-bold text-neutral-800 uppercase mb-0.5">Địa chỉ giao nhận:</span>
                        <p>{currentOrder.customerInfo.fullName}</p>
                        <p>{currentOrder.customerInfo.phone}</p>
                        <p>{currentOrder.customerInfo.address}, {currentOrder.customerInfo.city}</p>
                      </div>
                      <div>
                        <span className="block font-bold text-neutral-800 uppercase mb-0.5">Thanh toán:</span>
                        <p>Phương thức: <b className="text-neutral-900 uppercase">{currentOrder.paymentMethod}</b></p>
                        <p className="mt-1 font-bold text-black text-sm font-mono">TỔNG: {formatVND(currentOrder.totalAmount)}</p>
                      </div>
                    </div>
                  </div>

                  {/* CTA Buttons */}
                  <div className="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                    <button
                      id="confirm-go-home"
                      onClick={() => {
                        setCurrentPage('home');
                        setCurrentOrder(null);
                      }}
                      className="bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-3.5 px-8 transition rounded-sm shadow-md"
                    >
                      Quay về trang chủ
                    </button>
                    <button
                      id="confirm-go-shop"
                      onClick={() => {
                        setCurrentPage('shop');
                        setCurrentOrder(null);
                      }}
                      className="bg-white hover:bg-neutral-100 text-neutral-700 border border-neutral-200 hover:border-black font-semibold text-xs tracking-widest uppercase py-3.5 px-8 transition rounded-sm"
                    >
                      Tiếp tục mua hàng
                    </button>
                  </div>
                </div>
              </div>
            )}
          </motion.div>
        </AnimatePresence>
      </main>

      {/* Brand newsletter sign up footer bar */}
      <section className="bg-neutral-900 text-white py-14 border-t border-neutral-800">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center sm:text-left">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div className="lg:col-span-6 space-y-2">
              <h3 className="font-display text-lg font-bold uppercase tracking-widest">ĐĂNG KÝ THÀNH VIÊN VIBE CLUB</h3>
              <p className="text-xs text-neutral-400 max-w-md font-sans">
                Đăng ký hòm thư của bạn để nhận ngay <b>Mã giảm giá 10%</b>, cập nhật sớm các bộ sưu tập giới hạn Whenever.
              </p>
            </div>
            <div className="lg:col-span-6">
              {newsletterSubscribed ? (
                <div className="flex items-center gap-2 bg-neutral-800 border border-neutral-700 rounded-sm py-3 px-4 text-xs text-amber-400">
                  <Sparkles className="h-4.5 w-4.5 flex-shrink-0" />
                  <span>Cảm ơn! Mã voucher 10% của bạn là: <b className="font-mono bg-neutral-950 px-2 py-0.5 rounded text-white tracking-widest">VIBEWHENEVER10</b></span>
                </div>
              ) : (
                <form onSubmit={handleNewsletterSignup} className="flex flex-col sm:flex-row gap-2 max-w-md ml-auto">
                  <input
                    id="newsletter-email-input"
                    type="email"
                    required
                    value={newsletterEmail}
                    onChange={(e) => setNewsletterEmail(e.target.value)}
                    placeholder="Nhập địa chỉ email của bạn..."
                    className="flex-grow bg-neutral-950 border border-neutral-800 py-3 px-4 text-xs text-white placeholder-neutral-500 rounded-sm focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                  />
                  <button
                    id="newsletter-submit"
                    type="submit"
                    className="bg-white hover:bg-neutral-100 text-black font-semibold text-xs tracking-widest uppercase py-3 px-6 rounded-sm transition"
                  >
                    Đăng ký
                  </button>
                </form>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Main Footer layout */}
      <footer className="bg-black text-neutral-400 text-xs py-12 border-t border-neutral-900">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {/* Column 1: Brand details */}
            <div className="col-span-2 md:col-span-1 space-y-4">
              <span className="font-display text-2xl font-black tracking-widest text-white block">VIBE.</span>
              <p className="text-[11px] leading-relaxed text-neutral-500">
                Showroom trưng bày và phân phối chính thức các sản phẩm quần áo thời trang tối giản, streetwear cao cấp từ Whenever độc quyền.
              </p>
              <div className="flex space-x-3.5">
                <a href="#instagram" className="hover:text-white transition" aria-label="Instagram link">
                  <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="#facebook" className="hover:text-white transition" aria-label="Facebook link">
                  <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#twitter" className="hover:text-white transition" aria-label="Twitter link">
                  <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
              </div>

            </div>

            {/* Column 2: Quick Links */}
            <div className="space-y-4">
              <h4 className="font-display font-bold text-white uppercase text-xs tracking-widest">SẢN PHẨM CHÍNH</h4>
              <ul className="space-y-2 text-[11px]">
                <li><button onClick={() => handleCategoryQuickLink('Áo thun')} className="hover:text-white hover:underline transition">Áo thun</button></li>
                <li><button onClick={() => handleCategoryQuickLink('Áo khoác nỉ')} className="hover:text-white hover:underline transition">Áo khoác nỉ</button></li>
                <li><button onClick={() => handleCategoryQuickLink('Bomber')} className="hover:text-white hover:underline transition">Bomber</button></li>
                <li><button onClick={() => handleCategoryQuickLink('Quần ngắn')} className="hover:text-white hover:underline transition">Quần ngắn</button></li>
                <li><button onClick={() => handleCategoryQuickLink('lougewear')} className="hover:text-white hover:underline transition">Loungewear</button></li>
                <li><button onClick={() => handleCategoryQuickLink('Áo Len')} className="hover:text-white hover:underline transition">Áo Len</button></li>
                <li><button onClick={() => handleCategoryQuickLink('Phụ kiện')} className="hover:text-white hover:underline transition">Phụ kiện</button></li>
              </ul>
            </div>

            {/* Column 3: Corporate Policy */}
            <div className="space-y-4">
              <h4 className="font-display font-bold text-white uppercase text-xs tracking-widest">CHÍNH SÁCH VIBE</h4>
              <ul className="space-y-2 text-[11px]">
                <li><a href="#policy" className="hover:text-white transition">Chính sách bảo mật thanh toán</a></li>
                <li><a href="#return" className="hover:text-white transition">Chính sách đổi trả size trong 7 ngày</a></li>
                <li><a href="#shipping" className="hover:text-white transition">Chính sách giao nhận hỏa tốc</a></li>
                <li><a href="#membership" className="hover:text-white transition">Đặc quyền thẻ VIBE Black Card</a></li>
              </ul>
            </div>

            {/* Column 4: Contact address footer */}
            <div className="space-y-4 col-span-2 md:col-span-1">
              <h4 className="font-display font-bold text-white uppercase text-xs tracking-widest">FLAGSHIP SHOWROOM</h4>
              <p className="text-[11px] leading-relaxed">
                86 Lê Lợi, Bến Nghé, Quận 1, Tp. Hồ Chí Minh <br />
                SĐT: 1900 8198 <br />
                Email: support@vibe.vn
              </p>
              <span className="block text-[10px] text-neutral-600 font-mono">
                MÃ SỐ THUẾ ĐĂNG KÝ: 031782245
              </span>
            </div>
          </div>

          <div className="border-t border-neutral-900 mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center text-[10px] text-neutral-500 font-mono gap-4">
            <p>© 2026 VIBE STORES CO., LTD. CHÍNH HÃNG WHENEVER CODES VERIFIED.</p>
            <p>DESIGNED FOR PREMIUM LUXURY STREETWEAR IN VIETNAM.</p>
          </div>
        </div>
      </footer>

      {/* Quick View detailed modal overlay */}
      <QuickViewModal
        product={selectedProduct}
        onClose={() => setSelectedProduct(null)}
        onAddToCart={(product, size, qty) => {
          handleAddToCart(product, size, qty);
          setSelectedProduct(null);
        }}
        onBuyNow={(product, size, qty) => {
          handleBuyNow(product, size, qty);
        }}
        onToggleWishlist={handleToggleWishlist}
        isWishlisted={selectedProduct ? wishlist.some((w) => w.id === selectedProduct.id) : false}
      />

      {/* Cart Drawer overlay panel */}
      <CartDrawer
        isOpen={cartOpen}
        onClose={() => setCartOpen(false)}
        cartItems={cartItems}
        onUpdateQuantity={handleUpdateCartQuantity}
        onRemoveItem={handleRemoveCartItem}
        onCheckout={() => setCurrentPage('checkout')}
        onNavigateToShop={() => setCurrentPage('shop')}
      />

      {/* Wishlist Drawer overlay panel */}
      <WishlistDrawer
        isOpen={wishlistOpen}
        onClose={() => setWishlistOpen(false)}
        wishlistItems={wishlist}
        onRemoveFromWishlist={handleToggleWishlist}
        onAddToCart={handleAddToCart}
        onViewProduct={(p) => setSelectedProduct(p)}
      />

      {/* Profile Details Modal */}
      <ProfileModal
        isOpen={profileOpen}
        onClose={() => setProfileOpen(false)}
        customerInfo={customerInfo}
        onSaveProfile={handleSaveProfile}
        pastOrders={pastOrders}
        wishlist={wishlist}
        user={user}
        accessToken={accessToken}
        onGoogleSignIn={handleGoogleSignIn}
        onLogout={handleLogout}
      />

      {/* MOMO / VNPAY SCANNING PAYMENT SIMULATOR DIALOG */}
      <AnimatePresence>
        {showPaymentSimulation && currentOrder && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 0.6 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 bg-black/80 backdrop-blur-md"
            />

            {/* QR Simulation Card */}
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="relative z-10 w-full max-w-md bg-white rounded-md p-6 shadow-2xl space-y-4 border border-neutral-100"
            >
              <div className="text-center space-y-2">
                <span className={`inline-block text-[10px] font-bold font-mono tracking-widest text-white px-2.5 py-1 rounded-sm uppercase ${
                  selectedPayment === 'MOMO' ? 'bg-pink-600' : 'bg-blue-600'
                }`}>
                  {selectedPayment === 'MOMO' ? 'MOMO SECURE QR' : 'VNPAY SECURE QR'}
                </span>
                <h3 className="font-display text-base font-bold text-neutral-900 uppercase">Quét mã QR thanh toán</h3>
                <p className="text-xs text-neutral-500">
                  Mở ứng dụng {selectedPayment === 'MOMO' ? 'Ví MoMo' : 'Ngân hàng của bạn'} để quét mã chuyển tiền chính hãng Whenever.
                </p>
              </div>

              {/* QR Image Simulation */}
              <div className="bg-neutral-50 border border-neutral-100 p-6 rounded flex flex-col items-center justify-center space-y-4 relative overflow-hidden">
                <div className="w-48 h-48 bg-white border border-neutral-200 p-2 relative rounded flex items-center justify-center">
                  {/* Real procedural QR Code mockup with dots and logo */}
                  <div className="relative w-full h-full bg-slate-50 flex items-center justify-center border-2 border-neutral-800 rounded-xs">
                    <div className="absolute top-2 left-2 w-10 h-10 border-4 border-black" />
                    <div className="absolute top-2 right-2 w-10 h-10 border-4 border-black" />
                    <div className="absolute bottom-2 left-2 w-10 h-10 border-4 border-black" />
                    {/* Simulated dot patterns */}
                    <div className="w-5/6 h-5/6 bg-[radial-gradient(circle_at_center,_#000_1px,_transparent_1.5px)] bg-[size:8px_8px] opacity-75" />
                    
                    {/* Floating Center Brand Badge */}
                    <div className="absolute bg-black text-white text-[9px] font-black tracking-widest px-1.5 py-0.5 border border-white uppercase">
                      VIBE
                    </div>
                  </div>
                </div>

                {/* Amount to transfer */}
                <div className="text-center">
                  <span className="text-[10px] text-neutral-400 font-mono uppercase">Số tiền chuyển khoản</span>
                  <p className="text-lg font-mono font-bold text-black mt-0.5">{formatVND(currentOrder.totalAmount)}</p>
                </div>
              </div>

              {/* Countdown Ticker */}
              <div className="flex justify-between items-center bg-neutral-50 border border-neutral-100 p-3.5 rounded text-xs">
                <span className="text-neutral-500">Tự động hoàn thành sau:</span>
                <span className="font-mono font-bold text-red-600 animate-pulse">{simulationCountdown} giây</span>
              </div>

              {/* Manual Approve simulation */}
              <div className="flex gap-2">
                <button
                  id="simulator-cancel"
                  onClick={() => {
                    setShowPaymentSimulation(false);
                    setCurrentOrder(null);
                    addToast('Đã hủy giao dịch quét mã thanh toán.', 'error');
                  }}
                  className="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 py-3 text-xs font-semibold tracking-wider uppercase rounded-sm transition"
                >
                  Hủy thanh toán
                </button>
                <button
                  id="simulator-approve"
                  onClick={forceApprovePayment}
                  className="flex-1 bg-black hover:bg-neutral-800 text-white py-3 text-xs font-semibold tracking-wider uppercase rounded-sm transition"
                >
                  Đã chuyển khoản
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* CUSTOM FLOAT TOAST FEEDBACK NOTIFICATIONS */}
      <div id="toast-wrapper" className="fixed bottom-6 right-6 z-50 flex flex-col gap-2 max-w-sm w-full">
        <AnimatePresence>
          {toasts.map((toast) => (
            <motion.div
              key={toast.id}
              initial={{ opacity: 0, y: 20, scale: 0.9 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              className={`flex items-start gap-2.5 p-4 rounded-md shadow-lg border text-xs text-white ${
                toast.type === 'success'
                  ? 'bg-neutral-900 border-neutral-800'
                  : toast.type === 'error'
                  ? 'bg-red-950 border-red-900 text-red-200'
                  : 'bg-neutral-800 border-neutral-700'
              }`}
            >
              {toast.type === 'success' ? (
                <CheckCircle2 className="h-4 w-4 text-green-400 mt-0.5 flex-shrink-0" />
              ) : toast.type === 'error' ? (
                <AlertCircle className="h-4 w-4 text-red-400 mt-0.5 flex-shrink-0" />
              ) : (
                <Info className="h-4 w-4 text-neutral-300 mt-0.5 flex-shrink-0" />
              )}
              <span className="font-medium tracking-wide leading-relaxed">{toast.message}</span>
            </motion.div>
          ))}
        </AnimatePresence>
      </div>
    </div>
  );
}
