import { X, Trash2, ShoppingBag, ArrowRight } from 'lucide-react';
import { Book } from '../types';

interface CartItem extends Book {
  quantity: number;
}

interface CartDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  items: CartItem[];
  onUpdateQuantity: (id: string, quantity: number) => void;
  onRemoveItem: (id: string) => void;
}

export function CartDrawer({ isOpen, onClose, items, onUpdateQuantity, onRemoveItem }: CartDrawerProps) {
  const total = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

  return (
    <>
      {/* Overlay */}
      <div 
        className={`fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity duration-300 ${
          isOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'
        }`}
        onClick={onClose}
      />

      {/* Drawer */}
      <div 
        className={`fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 transform transition-transform duration-300 ease-in-out flex flex-col ${
          isOpen ? 'translate-x-0' : 'translate-x-full'
        }`}
      >
        {/* Header */}
        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-white">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-50 text-blue-600 rounded-xl">
              <ShoppingBag className="w-6 h-6" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-800">Giỏ hàng</h2>
              <p className="text-sm text-gray-500">{items.length} sản phẩm</p>
            </div>
          </div>
          <button 
            onClick={onClose}
            className="p-2 text-gray-400 hover:bg-gray-100 rounded-full transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Items List */}
        <div className="flex-1 overflow-y-auto p-6 space-y-6">
          {items.length === 0 ? (
            <div className="h-full flex flex-col items-center justify-center text-gray-400 gap-4">
              <ShoppingBag className="w-16 h-16 opacity-20" />
              <p className="text-lg font-medium">Giỏ hàng trống</p>
              <button 
                onClick={onClose}
                className="mt-4 px-6 py-2 bg-blue-50 text-blue-600 font-medium rounded-full hover:bg-blue-100 transition-colors"
              >
                Tiếp tục mua sắm
              </button>
            </div>
          ) : (
            items.map((item) => (
              <div key={item.id} className="flex gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative group">
                <div className="w-20 h-28 rounded-lg overflow-hidden flex-shrink-0 bg-gray-50 border border-gray-200">
                  <img src={item.image} alt={item.name} className="w-full h-full object-cover" referrerPolicy="no-referrer" />
                </div>
                <div className="flex-1 flex flex-col">
                  <h4 className="font-bold text-gray-800 line-clamp-2 mb-1 pr-6">{item.name}</h4>
                  <p className="text-sm text-gray-500 mb-2">{item.author}</p>
                  
                  <div className="mt-auto flex items-end justify-between">
                    <div>
                      <span className="font-bold text-orange-600 block">{item.price.toLocaleString('vi-VN')} ₫</span>
                      {item.originalPrice && (
                        <span className="text-xs text-gray-400 line-through">{item.originalPrice.toLocaleString('vi-VN')} ₫</span>
                      )}
                    </div>
                    
                    <div className="flex items-center border border-gray-200 rounded-lg bg-white">
                      <button 
                        onClick={() => onUpdateQuantity(item.id, Math.max(1, item.quantity - 1))}
                        className="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors"
                      >
                        -
                      </button>
                      <span className="w-8 text-center text-sm font-medium text-gray-800">
                        {item.quantity}
                      </span>
                      <button 
                        onClick={() => onUpdateQuantity(item.id, item.quantity + 1)}
                        className="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors"
                      >
                        +
                      </button>
                    </div>
                  </div>
                </div>
                
                <button 
                  onClick={() => onRemoveItem(item.id)}
                  className="absolute top-4 right-4 p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-md transition-all opacity-0 group-hover:opacity-100"
                  title="Xóa sản phẩm"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            ))
          )}
        </div>

        {/* Footer */}
        {items.length > 0 && (
          <div className="p-6 bg-gray-50 border-t border-gray-100">
            <div className="space-y-3 mb-6">
              <div className="flex justify-between text-gray-600 text-sm">
                <span>Tạm tính</span>
                <span className="font-medium">{total.toLocaleString('vi-VN')} ₫</span>
              </div>
              <div className="flex justify-between text-gray-600 text-sm">
                <span>Phí vận chuyển</span>
                <span className="font-medium text-green-600">Miễn phí</span>
              </div>
              <div className="flex justify-between text-gray-800 text-lg font-bold pt-3 border-t border-gray-200">
                <span>Tổng cộng</span>
                <span className="text-orange-600">{total.toLocaleString('vi-VN')} ₫</span>
              </div>
            </div>
            <button className="w-full py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
              Tiến hành thanh toán
              <ArrowRight className="w-5 h-5" />
            </button>
          </div>
        )}
      </div>
    </>
  );
}
