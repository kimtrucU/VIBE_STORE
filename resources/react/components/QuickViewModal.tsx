import { useState, useEffect } from 'react';
import { X, Star, Heart, ShoppingBag, ArrowRight, Check, ShieldCheck, HelpCircle } from 'lucide-react';
import { Product } from '../types';
import { motion, AnimatePresence } from 'motion/react';

interface QuickViewModalProps {
  product: Product | null;
  onClose: () => void;
  onAddToCart: (product: Product, size: string, quantity: number) => void;
  onBuyNow: (product: Product, size: string, quantity: number) => void;
  onToggleWishlist: (product: Product) => void;
  isWishlisted: boolean;
}

export default function QuickViewModal({
  product,
  onClose,
  onAddToCart,
  onBuyNow,
  onToggleWishlist,
  isWishlisted,
}: QuickViewModalProps) {
  const [activeImageIdx, setActiveImageIdx] = useState(0);
  const [selectedSize, setSelectedSize] = useState('');
  const [quantity, setQuantity] = useState(1);
  const [sizeError, setSizeError] = useState(false);

  useEffect(() => {
    if (product) {
      setActiveImageIdx(0);
      setSelectedSize(product.sizes.length === 1 || product.sizes[0] === 'Free Size' ? product.sizes[0] : '');
      setQuantity(1);
      setSizeError(false);
    }
  }, [product]);

  if (!product) return null;

  const handleAddToCart = () => {
    if (!selectedSize) {
      setSizeError(true);
      return;
    }
    onAddToCart(product, selectedSize, quantity);
  };

  const handleBuyNow = () => {
    if (!selectedSize) {
      setSizeError(true);
      return;
    }
    onBuyNow(product, selectedSize, quantity);
  };

  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
  };

  return (
    <AnimatePresence>
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10">
        {/* Backdrop */}
        <motion.div
          id="modal-backdrop"
          initial={{ opacity: 0 }}
          animate={{ opacity: 0.6 }}
          exit={{ opacity: 0 }}
          onClick={onClose}
          className="fixed inset-0 bg-black/80 backdrop-blur-xs"
        />

        {/* Modal Window */}
        <motion.div
          id="modal-content"
          initial={{ opacity: 0, scale: 0.95, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: 20 }}
          transition={{ type: 'spring', duration: 0.4 }}
          className="relative z-10 flex h-full max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden bg-white rounded-md shadow-2xl md:flex-row"
        >
          {/* Close button */}
          <button
            id="close-modal-btn"
            onClick={onClose}
            className="absolute top-4 right-4 z-30 flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-neutral-800 shadow-md hover:bg-black hover:text-white transition"
            aria-label="Close modal"
          >
            <X className="h-5 w-5" />
          </button>

          {/* Left panel: Images gallery */}
          <div className="w-full md:w-1/2 flex flex-col bg-neutral-50 overflow-y-auto max-h-[45vh] md:max-h-[90vh] p-4 sm:p-6 border-r border-neutral-100">
            <div className="relative aspect-3/4 w-full overflow-hidden bg-neutral-100 rounded-sm">
              <img
                src={product.images[activeImageIdx]}
                alt={`${product.name} active display`}
                className="h-full w-full object-cover object-center transition"
                referrerPolicy="no-referrer"
              />
            </div>
            
            {/* Gallery Thumbnails */}
            {product.images.length > 1 && (
              <div className="mt-4 flex gap-3 overflow-x-auto pb-1 justify-center">
                {product.images.map((img, idx) => (
                  <button
                    id={`modal-thumb-${idx}`}
                    key={idx}
                    onClick={() => setActiveImageIdx(idx)}
                    className={`relative h-20 w-16 flex-shrink-0 overflow-hidden rounded-sm border-2 transition ${
                      activeImageIdx === idx ? 'border-black' : 'border-transparent hover:border-neutral-300'
                    }`}
                  >
                    <img src={img} alt="Thumbnail view" className="h-full w-full object-cover object-center" referrerPolicy="no-referrer" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Right panel: Details & Purchasing info */}
          <div className="w-full md:w-1/2 flex flex-col p-6 sm:p-8 overflow-y-auto max-h-[45vh] md:max-h-[90vh]">
            <div>
              {/* Category */}
              <span className="text-[10px] font-bold font-mono tracking-widest text-neutral-400 uppercase">
                {product.category}
              </span>

              {/* Title & Wishlist */}
              <div className="mt-1 flex items-start justify-between gap-4">
                <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-neutral-900">
                  {product.name}
                </h2>
                <button
                  id={`modal-wishlist-toggle-${product.id}`}
                  onClick={() => onToggleWishlist(product)}
                  className={`flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border border-neutral-200 transition ${
                    isWishlisted ? 'text-red-500 bg-red-50/55' : 'text-neutral-400 hover:text-black hover:bg-neutral-50'
                  }`}
                  aria-label="Wishlist"
                >
                  <Heart className={`h-4.5 w-4.5 ${isWishlisted ? 'fill-current' : ''}`} />
                </button>
              </div>

              {/* Rating and review counts */}
              <div className="mt-3 flex items-center gap-2">
                <div className="flex items-center text-amber-500">
                  {[...Array(5)].map((_, i) => (
                    <Star
                      key={i}
                      className={`h-4 w-4 ${
                        i < Math.floor(product.rating) ? 'fill-current' : 'text-neutral-200'
                      }`}
                    />
                  ))}
                </div>
                <span className="text-xs font-mono font-bold text-neutral-700">{product.rating.toFixed(1)}</span>
                <span className="text-neutral-300">•</span>
                <span className="text-xs text-neutral-500 hover:underline cursor-pointer">{product.reviewsCount} đánh giá khách hàng</span>
              </div>

              {/* Price section */}
              <div className="mt-4 flex items-baseline gap-3 border-b border-neutral-100 pb-4">
                <span className="font-display text-xl font-bold font-mono text-black">
                  {formatVND(product.price)}
                </span>
                {product.originalPrice && (
                  <>
                    <span className="text-sm font-mono text-neutral-400 line-through">
                      {formatVND(product.originalPrice)}
                    </span>
                    <span className="bg-red-50 text-red-600 font-mono text-xs font-bold px-1.5 py-0.5 rounded">
                      TIẾT KIỆM {formatVND(product.originalPrice - product.price)}
                    </span>
                  </>
                )}
              </div>
            </div>

            {/* Product description */}
            <div className="mt-4">
              <p className="text-sm text-neutral-600 leading-relaxed">
                {product.description}
              </p>
            </div>

            {/* Specifications Details list */}
            <div className="mt-5 space-y-1.5 rounded-md bg-neutral-50 p-4 border border-neutral-100">
              <span className="text-[10px] font-bold font-mono tracking-wider text-neutral-400 uppercase block mb-1">
                Chi tiết sản phẩm chính hãng:
              </span>
              {product.details.map((detail, idx) => (
                <div key={idx} className="flex items-start gap-2 text-xs text-neutral-700">
                  <Check className="h-3.5 w-3.5 text-neutral-800 mt-0.5 flex-shrink-0" />
                  <span>{detail}</span>
                </div>
              ))}
            </div>

            {/* Size selection */}
            <div className="mt-6">
              <div className="flex justify-between items-center">
                <span className="text-xs font-bold tracking-wider text-neutral-800 uppercase flex items-center gap-1">
                  Kích cỡ: <span className="font-mono text-neutral-500">{selectedSize || 'Chưa chọn'}</span>
                </span>
                <button className="text-[11px] text-neutral-500 hover:text-black flex items-center gap-0.5 hover:underline">
                  <HelpCircle className="h-3.5 w-3.5" /> Bảng size chuẩn
                </button>
              </div>

              {sizeError && (
                <span className="text-red-500 text-xs mt-1 block">
                  Vui lòng chọn kích cỡ phù hợp trước khi tiếp tục
                </span>
              )}

              <div className="mt-2.5 flex flex-wrap gap-2.5">
                {product.sizes.map((size) => (
                  <button
                    id={`modal-size-select-${size}`}
                    key={size}
                    onClick={() => {
                      setSelectedSize(size);
                      setSizeError(false);
                    }}
                    className={`h-11 min-w-[2.75rem] px-3 font-mono font-bold text-xs tracking-wide transition flex items-center justify-center rounded-sm border ${
                      selectedSize === size
                        ? 'border-black bg-black text-white'
                        : 'border-neutral-200 bg-white text-neutral-800 hover:border-black'
                    }`}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>

            {/* Quantity Selector */}
            <div className="mt-6 flex items-center gap-4">
              <span className="text-xs font-bold tracking-wider text-neutral-800 uppercase">Số lượng:</span>
              <div className="flex items-center rounded-sm border border-neutral-200">
                <button
                  id="modal-qty-minus"
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  className="h-9 w-9 text-neutral-500 hover:text-black hover:bg-neutral-50 flex items-center justify-center font-bold"
                >
                  -
                </button>
                <span className="w-10 text-center text-xs font-mono font-bold text-neutral-800">{quantity}</span>
                <button
                  id="modal-qty-plus"
                  onClick={() => setQuantity(quantity + 1)}
                  className="h-9 w-9 text-neutral-500 hover:text-black hover:bg-neutral-50 flex items-center justify-center font-bold"
                >
                  +
                </button>
              </div>
            </div>

            {/* Actions: Add to Cart and Buy Now */}
            <div className="mt-8 flex flex-col sm:flex-row gap-3">
              <button
                id="modal-add-to-cart-action"
                onClick={handleAddToCart}
                className="flex-1 flex items-center justify-center gap-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-900 border border-neutral-300 py-3.5 px-6 font-semibold text-xs tracking-widest uppercase transition rounded-sm"
              >
                <ShoppingBag className="h-4 w-4" />
                Thêm vào giỏ hàng
              </button>
              <button
                id="modal-buy-now-action"
                onClick={handleBuyNow}
                className="flex-1 flex items-center justify-center gap-2 bg-black hover:bg-neutral-800 text-white py-3.5 px-6 font-semibold text-xs tracking-widest uppercase transition rounded-sm"
              >
                Mua ngay
                <ArrowRight className="h-4 w-4" />
              </button>
            </div>

            {/* Authenticity banner */}
            <div className="mt-6 pt-5 border-t border-neutral-100 flex items-center gap-2.5 text-xs text-neutral-500 font-sans">
              <ShieldCheck className="h-5 w-5 text-neutral-800 flex-shrink-0" />
              <span>Cam kết 100% sản phẩm quần áo chính hãng <b>Whenever</b> dập tag khóa chuẩn.</span>
            </div>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
}
