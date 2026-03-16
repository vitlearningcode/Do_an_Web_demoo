import { useState, useEffect } from 'react';
import { ShoppingCart, Heart, Star, Eye } from 'lucide-react';
import { Book } from '../types';

interface BookCardProps {
  book: Book;
  onAddToCart: (book: Book) => void;
  onQuickView: (book: Book) => void;
}

export function BookCard({ book, onAddToCart, onQuickView }: BookCardProps) {
  const [isHovered, setIsHovered] = useState(false);
  const [isWishlisted, setIsWishlisted] = useState(false);

  const discount = book.originalPrice 
    ? Math.round((1 - book.price / book.originalPrice) * 100) 
    : 0;

  return (
    <div 
      className="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group flex flex-col h-full cursor-pointer relative overflow-hidden"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      onClick={() => onQuickView(book)}
    >
      <div className="relative aspect-[3/4] mb-4 overflow-hidden rounded-xl bg-gray-50 flex items-center justify-center">
        <img 
          src={book.image} 
          alt={book.name} 
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
          referrerPolicy="no-referrer" 
        />
        
        {/* Badges */}
        <div className="absolute top-2 left-2 flex flex-col gap-1 z-10">
          {book.badge && (
            <span className="px-2 py-1 bg-orange-500 text-white text-[10px] font-bold rounded uppercase tracking-wider shadow-sm">
              {book.badge}
            </span>
          )}
          {discount > 0 && (
            <span className="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded uppercase tracking-wider shadow-sm">
              -{discount}%
            </span>
          )}
        </div>

        {/* Quick Actions */}
        <div className={`absolute top-2 right-2 flex flex-col gap-2 z-10 transition-all duration-300 ${isHovered ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-4'}`}>
          <button 
            onClick={(e) => {
              e.stopPropagation();
              setIsWishlisted(!isWishlisted);
            }}
            className="p-2 bg-white/90 backdrop-blur-sm text-gray-500 hover:text-red-500 rounded-full shadow-sm transition-colors"
          >
            <Heart className={`w-4 h-4 ${isWishlisted ? 'fill-red-500 text-red-500' : ''}`} />
          </button>
          <button 
            onClick={(e) => {
              e.stopPropagation();
              onQuickView(book);
            }}
            className="p-2 bg-white/90 backdrop-blur-sm text-gray-500 hover:text-blue-600 rounded-full shadow-sm transition-colors"
          >
            <Eye className="w-4 h-4" />
          </button>
        </div>

        {/* Quick Add to Cart Overlay */}
        <div className={`absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent transition-all duration-300 ${isHovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
          <button 
            onClick={(e) => {
              e.stopPropagation();
              onAddToCart(book);
            }}
            className="w-full py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-lg"
          >
            <ShoppingCart className="w-4 h-4" />
            Thêm Nhanh
          </button>
        </div>
      </div>
      
      <div className="flex-1 flex flex-col">
        <span className="text-[11px] text-blue-600 font-semibold uppercase tracking-wider mb-1.5">{book.category}</span>
        <h4 className="font-bold text-gray-800 line-clamp-2 mb-1.5 group-hover:text-blue-600 transition-colors leading-snug">
          {book.name}
        </h4>
        <p className="text-xs text-gray-500 mb-2.5 line-clamp-1">{book.author}</p>
        
        <div className="flex items-center gap-1.5 mb-3.5">
          <div className="flex text-yellow-400">
            <Star className="w-3.5 h-3.5 fill-current" />
            <span className="text-xs font-bold text-gray-700 ml-1">{book.rating}</span>
          </div>
          <span className="w-1 h-1 rounded-full bg-gray-300"></span>
          <span className="text-[11px] text-gray-400">({book.reviews})</span>
        </div>

        <div className="mt-auto pt-3 flex items-end justify-between border-t border-gray-50">
          <div>
            <span className="font-bold text-orange-600 text-lg block leading-none mb-1">{book.price.toLocaleString('vi-VN')} ₫</span>
            {book.originalPrice && (
              <span className="text-xs text-gray-400 line-through">{book.originalPrice.toLocaleString('vi-VN')} ₫</span>
            )}
          </div>
          <button 
            onClick={(e) => {
              e.stopPropagation();
              onAddToCart(book);
            }}
            className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all duration-300 hover:shadow-md hover:shadow-blue-200"
          >
            <ShoppingCart className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  );
}
