import { ShoppingBag, X, Trash2, ShieldCheck, ArrowRight, ArrowLeft } from 'lucide-react';
import { CartItem } from '../types';
import { motion, AnimatePresence } from 'motion/react';

interface CartDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  cartItems: CartItem[];
  onUpdateQuantity: (index: number, quantity: number) => void;
  onRemoveItem: (index: number) => void;
  onCheckout: () => void;
  onNavigateToShop: () => void;
}

export default function CartDrawer({
  isOpen,
  onClose,
  cartItems,
  onUpdateQuantity,
  onRemoveItem,
  onCheckout,
  onNavigateToShop,
}: CartDrawerProps) {
  const subtotal = cartItems.reduce((acc, item) => acc + item.product.price * item.quantity, 0);
  
  // Free shipping threshold: 1.000.000 VND
  const FREE_SHIPPING_LIMIT = 1000000;
  const isFreeShipping = subtotal >= FREE_SHIPPING_LIMIT;
  const remainingForFreeShipping = FREE_SHIPPING_LIMIT - subtotal;

  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
  };

  const handleCheckoutClick = () => {
    onClose();
    onCheckout();
  };

  const handleShopClick = () => {
    onClose();
    onNavigateToShop();
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 overflow-hidden">
          {/* Backdrop overlay */}
          <motion.div
            id="cart-backdrop"
            initial={{ opacity: 0 }}
            animate={{ opacity: 0.5 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black/70 backdrop-blur-xs"
          />

          {/* Drawer Panel */}
          <div className="fixed inset-y-0 right-0 z-50 flex max-w-full pl-10">
            <motion.div
              id="cart-drawer-panel"
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'tween', duration: 0.3 }}
              className="flex w-screen max-w-md flex-col bg-white shadow-2xl"
            >
              {/* Header */}
              <div className="flex items-center justify-between border-b border-neutral-100 px-4 py-5 sm:px-6">
                <div className="flex items-center gap-2">
                  <ShoppingBag className="h-5 w-5 text-neutral-800" />
                  <h2 className="font-display text-lg font-bold text-neutral-900 tracking-wide uppercase">
                    Giỏ hàng của bạn ({cartItems.length})
                  </h2>
                </div>
                <button
                  id="close-cart-btn"
                  onClick={onClose}
                  className="flex h-8 w-8 items-center justify-center rounded-full hover:bg-neutral-100 text-neutral-500 hover:text-black transition"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>

              {/* Free shipping threshold banner */}
              {cartItems.length > 0 && (
                <div className="bg-neutral-50 border-b border-neutral-100 px-4 py-3 sm:px-6">
                  {isFreeShipping ? (
                    <p className="text-xs text-neutral-800 font-sans font-medium flex items-center gap-1.5 justify-center">
                      <span className="inline-flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                      Chúc mừng! Đơn hàng của bạn đã đủ điều kiện <b>Miễn phí vận chuyển</b>.
                    </p>
                  ) : (
                    <div className="space-y-1.5">
                      <p className="text-[11px] text-neutral-600 font-sans text-center">
                        Mua thêm <b className="font-mono text-black">{formatVND(remainingForFreeShipping)}</b> để được miễn phí giao hàng toàn quốc.
                      </p>
                      <div className="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                        <div
                          className="bg-black h-full transition-all duration-500"
                          style={{ width: `${Math.min(100, (subtotal / FREE_SHIPPING_LIMIT) * 100)}%` }}
                        />
                      </div>
                    </div>
                  )}
                </div>
              )}

              {/* Cart Items list */}
              <div className="flex-1 overflow-y-auto px-4 py-4 sm:px-6 divide-y divide-neutral-100">
                {cartItems.length === 0 ? (
                  <div className="flex h-full flex-col items-center justify-center text-center py-20">
                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-50 border border-neutral-100 mb-4">
                      <ShoppingBag className="h-6 w-6 text-neutral-400" />
                    </div>
                    <p className="text-sm font-semibold text-neutral-900 font-display">Giỏ hàng trống</p>
                    <p className="mt-1 text-xs text-neutral-500 max-w-[240px] leading-relaxed">
                      Hãy khám phá các sản phẩm áo thun, hoodie và jacket cực chất từ thương hiệu Whenever.
                    </p>
                    <button
                      id="cart-empty-shop-now"
                      onClick={handleShopClick}
                      className="mt-6 flex items-center justify-center gap-2 bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-3 px-6 transition rounded-sm"
                    >
                      <ArrowLeft className="h-4 w-4" /> Tiếp tục mua sắm
                    </button>
                  </div>
                ) : (
                  cartItems.map((item, idx) => (
                    <div id={`cart-drawer-item-${idx}`} key={`${item.product.id}-${item.selectedSize}-${idx}`} className="flex py-5 gap-4">
                      {/* Product Thumbnail */}
                      <div className="h-24 w-18 flex-shrink-0 overflow-hidden rounded-sm bg-neutral-50 border border-neutral-100">
                        <img
                          src={item.product.images[0]}
                          alt={item.product.name}
                          className="h-full w-full object-cover object-center"
                          referrerPolicy="no-referrer"
                        />
                      </div>

                      {/* Info & controls */}
                      <div className="flex flex-1 flex-col justify-between">
                        <div>
                          <div className="flex justify-between text-sm">
                            <h3 className="font-display font-bold text-neutral-900 tracking-wide line-clamp-1 pr-4">
                              {item.product.name}
                            </h3>
                            <span className="font-mono text-xs font-semibold text-neutral-900">
                              {formatVND(item.product.price * item.quantity)}
                            </span>
                          </div>
                          <p className="mt-1 text-xs font-mono font-medium text-neutral-400 uppercase">
                            Size: <span className="text-neutral-800 font-bold">{item.selectedSize}</span>
                          </p>
                        </div>

                        <div className="flex items-center justify-between text-xs mt-3">
                          {/* Quantity selector */}
                          <div className="flex items-center rounded-sm border border-neutral-200 bg-white">
                            <button
                              id={`cart-qty-minus-${idx}`}
                              onClick={() => onUpdateQuantity(idx, Math.max(1, item.quantity - 1))}
                              className="h-7 w-7 text-neutral-500 hover:text-black hover:bg-neutral-50 flex items-center justify-center font-bold"
                            >
                              -
                            </button>
                            <span className="w-8 text-center text-xs font-mono font-bold text-neutral-800">{item.quantity}</span>
                            <button
                              id={`cart-qty-plus-${idx}`}
                              onClick={() => onUpdateQuantity(idx, item.quantity + 1)}
                              className="h-7 w-7 text-neutral-500 hover:text-black hover:bg-neutral-50 flex items-center justify-center font-bold"
                            >
                              +
                            </button>
                          </div>

                          {/* Delete Action */}
                          <button
                            id={`cart-remove-${idx}`}
                            onClick={() => onRemoveItem(idx)}
                            className="flex h-8 w-8 items-center justify-center rounded-full text-neutral-400 hover:bg-red-50 hover:text-red-500 transition"
                            aria-label="Remove item"
                          >
                            <Trash2 className="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>

              {/* Footer Checkout tools */}
              {cartItems.length > 0 && (
                <div className="border-t border-neutral-100 bg-neutral-50 px-4 py-5 sm:px-6">
                  <div className="space-y-1.5">
                    <div className="flex justify-between text-xs text-neutral-500">
                      <span>Tạm tính</span>
                      <span className="font-mono">{formatVND(subtotal)}</span>
                    </div>
                    <div className="flex justify-between text-xs text-neutral-500">
                      <span>Phí vận chuyển</span>
                      <span className="font-mono">
                        {isFreeShipping ? 'Miễn phí' : formatVND(30000)}
                      </span>
                    </div>
                    <div className="flex justify-between border-t border-neutral-200 pt-3 text-sm font-bold">
                      <span className="font-display">Tổng cộng</span>
                      <span className="font-mono text-black">
                        {formatVND(subtotal + (isFreeShipping ? 0 : 30000))}
                      </span>
                    </div>
                  </div>

                  <div className="mt-6 space-y-3">
                    <button
                      id="cart-proceed-checkout"
                      onClick={handleCheckoutClick}
                      className="w-full flex items-center justify-center gap-2 bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-4 transition rounded-sm shadow-md"
                    >
                      Tiến hành thanh toán
                      <ArrowRight className="h-4 w-4" />
                    </button>
                    <button
                      id="cart-continue-shopping"
                      onClick={onClose}
                      className="w-full flex items-center justify-center gap-1.5 bg-white border border-neutral-200 text-neutral-700 hover:text-black hover:border-black font-semibold text-xs tracking-widest uppercase py-3.5 transition rounded-sm"
                    >
                      Tiếp tục mua sắm
                    </button>
                  </div>

                  <div className="mt-4 flex items-center justify-center gap-1.5 text-[10px] text-neutral-500">
                    <ShieldCheck className="h-4 w-4 text-neutral-800" />
                    <span>Hệ thống bảo mật đơn hàng cao cấp Whenever.</span>
                  </div>
                </div>
              )}
            </motion.div>
          </div>
        </div>
      )}
    </AnimatePresence>
  );
}
