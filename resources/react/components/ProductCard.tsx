import React, { useState, MouseEvent } from 'react';
import { Heart, Eye, ShoppingBag, Star } from 'lucide-react';
import { Product } from '../types';
import { motion } from 'motion/react';

interface ProductCardProps {
  product: Product;
  onViewDetails: (product: Product) => void;
  onAddToCart: (product: Product, size: string) => void;
  onToggleWishlist: (product: Product) => void;
  isWishlisted: boolean;
  key?: string | number;
}

export default function ProductCard({
  product,
  onViewDetails,
  onAddToCart,
  onToggleWishlist,
  isWishlisted,
}: ProductCardProps) {
  const [hovered, setHovered] = useState(false);
  const [selectedSize, setSelectedSize] = useState<string>('');
  const [showSizes, setShowSizes] = useState(false);

  // Format price in VND (e.g. 1.250.000 đ)
  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
  };

  const handleQuickAdd = (e: React.MouseEvent) => {
    e.stopPropagation();
    if (product.sizes.length === 1 || product.sizes[0] === 'Free Size') {
      onAddToCart(product, product.sizes[0]);
    } else {
      setShowSizes(true);
    }
  };

  const selectSizeAndAdd = (e: React.MouseEvent, size: string) => {
    e.stopPropagation();
    onAddToCart(product, size);
    setSelectedSize(size);
    setShowSizes(false);
  };

  return (
    <div
      id={`product-card-${product.id}`}
      className="group relative flex flex-col overflow-hidden bg-white"
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => {
        setHovered(false);
        setShowSizes(false);
      }}
    >
      {/* Product Image Stage */}
      <div className="relative aspect-3/4 w-full overflow-hidden bg-neutral-100">
        {/* Badges */}
        <div className="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
          {product.isNewArrival && (
            <span className="bg-black text-[9px] font-bold font-mono tracking-widest text-white px-2 py-1 uppercase rounded-sm">
              New
            </span>
          )}
          {product.isBestSeller && (
            <span className="bg-neutral-200 text-black text-[9px] font-bold font-mono tracking-widest px-2 py-1 uppercase rounded-sm border border-neutral-300">
              Best
            </span>
          )}
          {product.originalPrice && (
            <span className="bg-red-600 text-[9px] font-bold font-mono tracking-widest text-white px-2 py-1 uppercase rounded-sm">
              -{Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)}%
            </span>
          )}
        </div>

        {/* Wishlist button */}
        <button
          id={`wishlist-btn-${product.id}`}
          onClick={(e) => {
            e.stopPropagation();
            onToggleWishlist(product);
          }}
          className={`absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-sm transition hover:scale-110 ${
            isWishlisted ? 'text-red-500' : 'text-neutral-400 hover:text-black'
          }`}
          aria-label="Add to wishlist"
        >
          <Heart className={`h-4.5 w-4.5 ${isWishlisted ? 'fill-current' : ''}`} />
        </button>

        {/* Dynamic Image hover flip */}
        <div
          onClick={() => onViewDetails(product)}
          className="cursor-pointer h-full w-full relative"
        >
          <img
            src={product.images[0]}
            alt={product.name}
            className={`h-full w-full object-cover object-center transition-all duration-700 ${
              hovered && product.images[1] ? 'opacity-0 scale-105' : 'opacity-100 scale-100'
            }`}
            loading="lazy"
            referrerPolicy="no-referrer"
          />
          {product.images[1] && (
            <img
              src={product.images[1]}
              alt={`${product.name} alternate view`}
              className={`absolute inset-0 h-full w-full object-cover object-center transition-all duration-700 ${
                hovered ? 'opacity-100 scale-100' : 'opacity-0 scale-95'
              }`}
              loading="lazy"
              referrerPolicy="no-referrer"
            />
          )}
        </div>

        {/* Desktop Quick Action Overlays */}
        <div className="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/20 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-300 hidden md:flex items-center gap-2">
          <button
            id={`quick-view-${product.id}`}
            onClick={() => onViewDetails(product)}
            className="flex-1 flex items-center justify-center gap-1 bg-white hover:bg-neutral-100 text-black font-semibold text-xs py-2 px-3 transition rounded-sm shadow-md uppercase tracking-wider"
          >
            <Eye className="h-3.5 w-3.5" />
            Quick View
          </button>
        </div>

        {/* Mobile Quick triggers */}
        <button
          id={`mobile-quick-view-${product.id}`}
          onClick={() => onViewDetails(product)}
          className="absolute bottom-3 right-3 md:hidden flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-md text-black"
          aria-label="Quick view"
        >
          <Eye className="h-4 w-4" />
        </button>

        {/* Sliding Size selector for Quick Add to Cart */}
        {showSizes && (
          <div className="absolute inset-0 z-20 bg-black/75 backdrop-blur-xs flex flex-col justify-center items-center p-4 transition-all duration-300">
            <p className="text-white text-xs font-semibold uppercase tracking-widest mb-3">Chọn kích cỡ</p>
            <div className="flex flex-wrap justify-center gap-2 max-w-xs">
              {product.sizes.map((size) => (
                <button
                  id={`quick-size-${product.id}-${size}`}
                  key={size}
                  onClick={(e) => selectSizeAndAdd(e, size)}
                  className="h-10 min-w-[2.5rem] px-2 text-xs font-mono font-bold bg-white text-black hover:bg-black hover:text-white hover:border-white border border-transparent transition flex items-center justify-center rounded-sm"
                >
                  {size}
                </button>
              ))}
            </div>
            <button
              id={`cancel-quick-add-${product.id}`}
              onClick={(e) => {
                e.stopPropagation();
                setShowSizes(false);
              }}
              className="mt-4 text-[10px] text-neutral-300 hover:text-white uppercase tracking-widest underline font-mono"
            >
              Hủy
            </button>
          </div>
        )}
      </div>

      {/* Product Information */}
      <div className="flex flex-col py-4 px-1 flex-1">
        <span className="text-[10px] uppercase font-mono tracking-widest text-neutral-400 font-semibold">
          {product.category === 'tshirt' ? 'T-Shirt' : product.category}
        </span>
        
        <h3 className="mt-1 font-display text-xs font-semibold text-neutral-900 tracking-wide line-clamp-1 group-hover:text-black transition">
          <button onClick={() => onViewDetails(product)} className="text-left focus:outline-none hover:underline">
            {product.name}
          </button>
        </h3>

        {/* Rating and size tag */}
        <div className="mt-2 flex items-center gap-1.5 text-xs text-neutral-500">
          <div className="flex items-center text-amber-500">
            <Star className="h-3 w-3 fill-current" />
            <span className="ml-1 text-[11px] font-mono font-semibold text-neutral-700">{product.rating.toFixed(1)}</span>
          </div>
          <span className="text-neutral-300">•</span>
          <span className="text-[10px] font-mono text-neutral-500">
            {product.sizes.join(', ')}
          </span>
        </div>

        {/* Price & Cart Trigger */}
        <div className="mt-3.5 flex items-center justify-between gap-1">
          <div className="flex flex-wrap items-baseline gap-1.5">
            <span className="text-xs font-semibold font-mono text-neutral-900">
              {formatVND(product.price)}
            </span>
            {product.originalPrice && (
              <span className="text-[11px] font-mono text-neutral-400 line-through">
                {formatVND(product.originalPrice)}
              </span>
            )}
          </div>

          <button
            id={`add-to-cart-quick-${product.id}`}
            onClick={handleQuickAdd}
            className="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-100 hover:bg-black hover:text-white text-neutral-800 transition"
            aria-label="Add to cart"
          >
            <ShoppingBag className="h-3.5 w-3.5" />
          </button>
        </div>
      </div>
    </div>
  );
}
