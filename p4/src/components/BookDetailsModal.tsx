import { X, Star, ShoppingCart, Heart, Share2, ShieldCheck, Truck } from 'lucide-react';
import { Book } from '../types';
import { useState } from 'react';

interface BookDetailsModalProps {
  book: Book | null;
  isOpen: boolean;
  onClose: () => void;
  onAddToCart: (book: Book, quantity: number) => void;
}

export function BookDetailsModal({ book, isOpen, onClose, onAddToCart }: BookDetailsModalProps) {
  const [quantity, setQuantity] = useState(1);

  if (!isOpen || !book) return null;

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col relative">
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-2 bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-800 rounded-full transition-colors z-10"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="flex flex-col md:flex-row overflow-y-auto">
          {/* Image Section */}
          <div className="w-full md:w-2/5 bg-gray-50 p-8 flex flex-col items-center justify-center border-r border-gray-100">
            <div className="relative w-full max-w-[280px] aspect-[3/4] rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-white">
              <img src={book.image} alt={book.name} className="w-full h-full object-cover" referrerPolicy="no-referrer" />
              {book.badge && (
                <span className="absolute top-3 left-3 px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded uppercase tracking-wider">
                  {book.badge}
                </span>
              )}
            </div>
            <div className="flex gap-4 mt-8 w-full max-w-[280px]">
              <button className="flex-1 py-2.5 border border-gray-200 rounded-xl flex items-center justify-center gap-2 text-gray-600 hover:bg-gray-50 transition-colors font-medium text-sm">
                <Heart className="w-4 h-4" />
                Yêu thích
              </button>
              <button className="flex-1 py-2.5 border border-gray-200 rounded-xl flex items-center justify-center gap-2 text-gray-600 hover:bg-gray-50 transition-colors font-medium text-sm">
                <Share2 className="w-4 h-4" />
                Chia sẻ
              </button>
            </div>
          </div>

          {/* Details Section */}
          <div className="w-full md:w-3/5 p-8 flex flex-col">
            <div className="mb-6">
              <span className="text-sm font-semibold text-blue-600 uppercase tracking-wider">{book.category}</span>
              <h2 className="text-3xl font-bold text-gray-800 mt-2 mb-2 leading-tight">{book.name}</h2>
              <p className="text-lg text-gray-600 mb-4">Tác giả: <span className="font-medium text-gray-800">{book.author}</span></p>
              
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-1">
                  <div className="flex text-yellow-400">
                    <Star className="w-4 h-4 fill-current" />
                    <Star className="w-4 h-4 fill-current" />
                    <Star className="w-4 h-4 fill-current" />
                    <Star className="w-4 h-4 fill-current" />
                    <Star className="w-4 h-4 fill-current opacity-50" />
                  </div>
                  <span className="font-medium text-gray-800 ml-1">{book.rating}</span>
                </div>
                <span className="w-1 h-1 rounded-full bg-gray-300"></span>
                <span className="text-sm text-gray-500">{book.reviews} đánh giá</span>
                <span className="w-1 h-1 rounded-full bg-gray-300"></span>
                <span className="text-sm text-gray-500">Đã bán 1.2k+</span>
              </div>
            </div>

            <div className="p-6 bg-gray-50 rounded-2xl mb-8">
              <div className="flex items-end gap-4 mb-2">
                <span className="text-4xl font-bold text-orange-600">{book.price.toLocaleString('vi-VN')} ₫</span>
                {book.originalPrice && (
                  <span className="text-lg text-gray-400 line-through mb-1">{book.originalPrice.toLocaleString('vi-VN')} ₫</span>
                )}
                {book.originalPrice && (
                  <span className="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded mb-1.5 ml-auto">
                    -{Math.round((1 - book.price / book.originalPrice) * 100)}%
                  </span>
                )}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-y-4 gap-x-8 mb-8 text-sm">
              <div className="flex justify-between border-b border-gray-100 pb-2">
                <span className="text-gray-500">Nhà xuất bản</span>
                <span className="font-medium text-gray-800">{book.publisher || 'NXB Trẻ'}</span>
              </div>
              <div className="flex justify-between border-b border-gray-100 pb-2">
                <span className="text-gray-500">Số trang</span>
                <span className="font-medium text-gray-800">{book.pages || 320}</span>
              </div>
              <div className="flex justify-between border-b border-gray-100 pb-2">
                <span className="text-gray-500">Hình thức bìa</span>
                <span className="font-medium text-gray-800">Bìa mềm</span>
              </div>
              <div className="flex justify-between border-b border-gray-100 pb-2">
                <span className="text-gray-500">Kích thước</span>
                <span className="font-medium text-gray-800">14 x 20.5 cm</span>
              </div>
            </div>

            <div className="mb-8">
              <h3 className="font-bold text-gray-800 mb-2">Mô tả sách</h3>
              <p className="text-gray-600 text-sm leading-relaxed line-clamp-4">
                {book.description || 'Một cuốn sách tuyệt vời mang đến những góc nhìn mới mẻ và sâu sắc. Tác phẩm đã được dịch ra nhiều ngôn ngữ và nhận được sự đón nhận nồng nhiệt từ độc giả trên toàn thế giới. Nội dung sách không chỉ cung cấp kiến thức mà còn truyền cảm hứng mạnh mẽ, giúp người đọc khám phá tiềm năng bản thân và hướng tới một cuộc sống ý nghĩa hơn.'}
              </p>
              <button className="text-blue-600 text-sm font-medium mt-2 hover:underline">Xem thêm</button>
            </div>

            <div className="mt-auto flex flex-col gap-4">
              <div className="flex gap-4">
                <div className="flex items-center border border-gray-200 rounded-xl bg-white">
                  <button 
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="w-12 h-12 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors"
                  >
                    -
                  </button>
                  <input 
                    type="number" 
                    value={quantity}
                    onChange={(e) => setQuantity(Math.max(1, parseInt(e.target.value) || 1))}
                    className="w-12 h-12 text-center font-medium text-gray-800 focus:outline-none border-x border-gray-100"
                  />
                  <button 
                    onClick={() => setQuantity(quantity + 1)}
                    className="w-12 h-12 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors"
                  >
                    +
                  </button>
                </div>
                <button 
                  onClick={() => {
                    onAddToCart(book, quantity);
                    onClose();
                  }}
                  className="flex-1 bg-blue-600 text-white font-bold rounded-xl flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200"
                >
                  <ShoppingCart className="w-5 h-5" />
                  Thêm vào giỏ hàng
                </button>
              </div>
              
              <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                <div className="flex items-center gap-2 text-sm text-gray-600">
                  <ShieldCheck className="w-5 h-5 text-green-500" />
                  <span>100% Sách chính hãng</span>
                </div>
                <div className="flex items-center gap-2 text-sm text-gray-600">
                  <Truck className="w-5 h-5 text-blue-500" />
                  <span>Giao hàng toàn quốc</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
