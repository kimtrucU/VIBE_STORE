import { Heart, X, Trash2, ShoppingBag } from 'lucide-react';
import { Product } from '../types';
import { motion, AnimatePresence } from 'motion/react';

interface WishlistDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  wishlistItems: Product[];
  onRemoveFromWishlist: (product: Product) => void;
  onAddToCart: (product: Product, size: string) => void;
  onViewProduct: (product: Product) => void;
}

export default function WishlistDrawer({
  isOpen,
  onClose,
  wishlistItems,
  onRemoveFromWishlist,
  onAddToCart,
  onViewProduct,
}: WishlistDrawerProps) {
  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
  };

  const handleQuickAdd = (product: Product) => {
    // default select first size or S/Free size
    const defaultSize = product.sizes.includes('Free Size') ? 'Free Size' : product.sizes[0] || 'M';
    onAddToCart(product, defaultSize);
  };

  const handleItemClick = (product: Product) => {
    onClose();
    onViewProduct(product);
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 overflow-hidden">
          {/* Backdrop overlay */}
          <motion.div
            id="wishlist-backdrop"
            initial={{ opacity: 0 }}
            animate={{ opacity: 0.5 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black/70 backdrop-blur-xs"
          />

          {/* Drawer Panel */}
          <div className="fixed inset-y-0 right-0 z-50 flex max-w-full pl-10">
            <motion.div
              id="wishlist-drawer-panel"
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'tween', duration: 0.3 }}
              className="flex w-screen max-w-md flex-col bg-white shadow-2xl"
            >
              {/* Header */}
              <div className="flex items-center justify-between border-b border-neutral-100 px-4 py-5 sm:px-6">
                <div className="flex items-center gap-2">
                  <Heart className="h-5 w-5 text-red-500 fill-current" />
                  <h2 className="font-display text-lg font-bold text-neutral-900 tracking-wide uppercase">
                    Danh sách yêu thích ({wishlistItems.length})
                  </h2>
                </div>
                <button
                  id="close-wishlist-btn"
                  onClick={onClose}
                  className="flex h-8 w-8 items-center justify-center rounded-full hover:bg-neutral-100 text-neutral-500 hover:text-black transition"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>

              {/* Items list */}
              <div className="flex-1 overflow-y-auto px-4 py-4 sm:px-6 divide-y divide-neutral-100">
                {wishlistItems.length === 0 ? (
                  <div className="flex h-full flex-col items-center justify-center text-center py-20">
                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-50 border border-neutral-100 mb-4">
                      <Heart className="h-6 w-6 text-neutral-300" />
                    </div>
                    <p className="text-sm font-semibold text-neutral-900 font-display">Chưa có sản phẩm yêu thích</p>
                    <p className="mt-1 text-xs text-neutral-500 max-w-[240px] leading-relaxed">
                      Lưu lại những thiết kế thời thượng của Whenever để dễ dàng mua sắm khi cần thiết.
                    </p>
                  </div>
                ) : (
                  wishlistItems.map((product) => (
                    <div id={`wishlist-drawer-item-${product.id}`} key={product.id} className="flex py-5 gap-4">
                      {/* Product Thumbnail */}
                      <div
                        onClick={() => handleItemClick(product)}
                        className="h-24 w-18 flex-shrink-0 overflow-hidden rounded-sm bg-neutral-50 border border-neutral-100 cursor-pointer hover:opacity-80 transition"
                      >
                        <img
                          src={product.images[0]}
                          alt={product.name}
                          className="h-full w-full object-cover object-center"
                          referrerPolicy="no-referrer"
                        />
                      </div>

                      {/* Info & controls */}
                      <div className="flex flex-1 flex-col justify-between">
                        <div>
                          <div className="flex justify-between text-sm">
                            <h3
                              onClick={() => handleItemClick(product)}
                              className="font-display font-bold text-neutral-900 tracking-wide line-clamp-1 pr-4 cursor-pointer hover:underline"
                            >
                              {product.name}
                            </h3>
                            <span className="font-mono text-xs font-semibold text-neutral-900">
                              {formatVND(product.price)}
                            </span>
                          </div>
                          <p className="mt-1 text-xs text-neutral-400 font-mono">
                            {product.category.toUpperCase()}
                          </p>
                        </div>

                        <div className="flex items-center justify-between text-xs mt-3">
                          {/* Quick add trigger */}
                          <button
                            id={`wishlist-quick-add-${product.id}`}
                            onClick={() => handleQuickAdd(product)}
                            className="flex items-center gap-1.5 bg-neutral-900 hover:bg-black text-white text-[10px] font-bold font-sans tracking-wider uppercase py-1.5 px-3 rounded-sm transition"
                          >
                            <ShoppingBag className="h-3 w-3" />
                            Chọn Size & Thêm
                          </button>

                          {/* Delete Action */}
                          <button
                            id={`wishlist-remove-${product.id}`}
                            onClick={() => onRemoveFromWishlist(product)}
                            className="flex h-8 w-8 items-center justify-center rounded-full text-neutral-400 hover:bg-neutral-50 hover:text-red-500 transition"
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

              {/* Close Button at bottom */}
              <div className="border-t border-neutral-100 bg-neutral-50 px-4 py-4 sm:px-6">
                <button
                  id="wishlist-drawer-close"
                  onClick={onClose}
                  className="w-full text-center bg-black hover:bg-neutral-800 text-white font-semibold text-xs tracking-widest uppercase py-3.5 transition rounded-sm"
                >
                  Đóng panel
                </button>
              </div>
            </motion.div>
          </div>
        </div>
      )}
    </AnimatePresence>
  );
}
