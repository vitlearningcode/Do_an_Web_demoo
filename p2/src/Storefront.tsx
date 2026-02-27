import { useState } from 'react';
import { Search, ShoppingCart, User, Menu, Star, ChevronRight, Heart, BookOpen } from 'lucide-react';
import { Book } from './types';
import { BookDetailsModal } from './components/BookDetailsModal';
import { CartDrawer } from './components/CartDrawer';
import { AuthModal } from './components/AuthModal';

// Mock data for books
const featuredBooks: Book[] = [
  { id: '1', name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: 86000, originalPrice: 120000, rating: 4.8, reviews: 1240, image: 'https://picsum.photos/seed/book1/300/400', badge: 'Bán chạy' },
  { id: '2', name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: 79000, originalPrice: 95000, rating: 4.9, reviews: 850, image: 'https://picsum.photos/seed/book2/300/400' },
  { id: '3', name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: 110000, rating: 4.7, reviews: 620, image: 'https://picsum.photos/seed/book3/300/400', badge: 'Mới' },
  { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: 80000, rating: 4.6, reviews: 2100, image: 'https://picsum.photos/seed/book4/300/400' },
  { id: '5', name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: 250000, originalPrice: 300000, rating: 4.9, reviews: 3200, image: 'https://picsum.photos/seed/book5/300/400', badge: '-15%' },
  { id: '6', name: 'Cây Cam Ngọt Của Tôi', author: 'José Mauro de Vasconcelos', category: 'Văn học', price: 95000, rating: 4.8, reviews: 1500, image: 'https://picsum.photos/seed/book6/300/400' },
  { id: '7', name: 'Tâm Lý Học Tội Phạm', author: 'Tôn Thất', category: 'Tâm lý học', price: 135000, originalPrice: 150000, rating: 4.5, reviews: 430, image: 'https://picsum.photos/seed/book7/300/400' },
  { id: '8', name: 'Muôn Kiếp Nhân Sinh', author: 'Nguyên Phong', category: 'Tôn giáo - Tâm linh', price: 168000, rating: 4.7, reviews: 980, image: 'https://picsum.photos/seed/book8/300/400' },
];

export function Storefront() {
  const [cartItems, setCartItems] = useState<(Book & { quantity: number })[]>([]);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [isAuthOpen, setIsAuthOpen] = useState(false);
  const [selectedBook, setSelectedBook] = useState<Book | null>(null);

  const cartCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);

  const handleAddToCart = (book: Book, quantity: number = 1) => {
    setCartItems(prev => {
      const existing = prev.find(item => item.id === book.id);
      if (existing) {
        return prev.map(item => 
          item.id === book.id ? { ...item, quantity: item.quantity + quantity } : item
        );
      }
      return [...prev, { ...book, quantity }];
    });
    setIsCartOpen(true);
  };

  const handleUpdateQuantity = (id: string, quantity: number) => {
    if (quantity < 1) return;
    setCartItems(prev => prev.map(item => 
      item.id === id ? { ...item, quantity } : item
    ));
  };

  const handleRemoveItem = (id: string) => {
    setCartItems(prev => prev.filter(item => item.id !== id));
  };

  return (
    <div className="min-h-screen bg-gray-50 font-sans text-gray-800">
      {/* Top Bar */}
      <div className="bg-blue-700 text-white text-xs py-2 px-4 flex justify-between items-center">
        <div className="container mx-auto flex justify-between items-center">
          <p>Miễn phí vận chuyển cho đơn hàng từ 250.000đ</p>
          <div className="flex gap-4">
            <a href="#" className="hover:text-blue-200 transition-colors">Theo dõi đơn hàng</a>
            <a href="#" className="hover:text-blue-200 transition-colors">Hỗ trợ khách hàng</a>
          </div>
        </div>
      </div>

      {/* Header */}
      <header className="bg-white shadow-sm sticky top-0 z-40">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between gap-8">
            {/* Logo */}
            <div className="flex items-center gap-2">
              <div className="bg-blue-600 p-2 rounded-lg">
                <BookOpen className="text-white w-6 h-6" />
              </div>
              <div>
                <h1 className="font-bold text-blue-600 leading-tight text-xl">BOOK SALES</h1>
                <p className="text-[10px] font-semibold text-orange-500 tracking-wider">STOREFRONT</p>
              </div>
            </div>

            {/* Search */}
            <div className="flex-1 max-w-2xl relative hidden md:block">
              <input 
                type="text" 
                placeholder="Tìm kiếm tựa sách, tác giả, nhà xuất bản..." 
                className="w-full pl-4 pr-12 py-3 bg-gray-100 border-transparent rounded-full focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
              />
              <button className="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                <Search className="w-4 h-4" />
              </button>
            </div>

            {/* Actions */}
            <div className="flex items-center gap-6">
              <button 
                onClick={() => setIsAuthOpen(true)}
                className="flex flex-col items-center gap-1 text-gray-600 hover:text-blue-600 transition-colors"
              >
                <User className="w-6 h-6" />
                <span className="text-xs font-medium">Đăng nhập</span>
              </button>
              <button 
                onClick={() => setIsCartOpen(true)}
                className="flex flex-col items-center gap-1 text-gray-600 hover:text-blue-600 transition-colors relative"
              >
                <div className="relative">
                  <ShoppingCart className="w-6 h-6" />
                  {cartCount > 0 && (
                    <span className="absolute -top-2 -right-2 bg-orange-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">
                      {cartCount}
                    </span>
                  )}
                </div>
                <span className="text-xs font-medium">Giỏ hàng</span>
              </button>
            </div>
          </div>

          {/* Categories Nav */}
          <nav className="mt-4 hidden md:flex items-center gap-6 text-sm font-medium text-gray-700">
            <button className="flex items-center gap-2 text-blue-600">
              <Menu className="w-5 h-5" />
              Danh mục sách
            </button>
            <a href="#" className="hover:text-blue-600 transition-colors">Sách Mới</a>
            <a href="#" className="hover:text-blue-600 transition-colors">Bán Chạy</a>
            <a href="#" className="hover:text-blue-600 transition-colors">Văn Học</a>
            <a href="#" className="hover:text-blue-600 transition-colors">Kinh Tế</a>
            <a href="#" className="hover:text-blue-600 transition-colors">Tâm Lý - Kỹ Năng</a>
            <a href="#" className="hover:text-blue-600 transition-colors">Thiếu Nhi</a>
            <a href="#" className="text-orange-500 hover:text-orange-600 transition-colors ml-auto">Khuyến Mãi</a>
          </nav>
        </div>
      </header>

      <main className="container mx-auto px-4 py-8 space-y-12">
        {/* Hero Banner */}
        <section className="relative rounded-2xl overflow-hidden bg-blue-900 text-white h-[400px] flex items-center">
          <div className="absolute inset-0 z-0">
            <img src="https://picsum.photos/seed/banner/1200/400" alt="Banner" className="w-full h-full object-cover opacity-40" referrerPolicy="no-referrer" />
            <div className="absolute inset-0 bg-gradient-to-r from-blue-900 via-blue-900/80 to-transparent"></div>
          </div>
          <div className="relative z-10 p-12 max-w-2xl">
            <span className="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full mb-4 uppercase tracking-wider">
              Khuyến mãi tháng 10
            </span>
            <h2 className="text-4xl md:text-5xl font-bold leading-tight mb-4">
              Hội Sách Mùa Thu <br/> Giảm Giá Lên Đến 50%
            </h2>
            <p className="text-blue-100 text-lg mb-8">
              Khám phá hàng ngàn tựa sách hấp dẫn với mức giá ưu đãi nhất trong năm. Miễn phí giao hàng toàn quốc.
            </p>
            <button className="px-8 py-3 bg-white text-blue-900 font-bold rounded-full hover:bg-gray-100 transition-colors shadow-lg">
              Mua Ngay
            </button>
          </div>
        </section>

        {/* Featured Books */}
        <section>
          <div className="flex justify-between items-end mb-6">
            <div>
              <h3 className="text-2xl font-bold text-gray-800">Sách Bán Chạy Nhất</h3>
              <p className="text-gray-500 mt-1">Những tựa sách được độc giả yêu thích nhất tuần qua</p>
            </div>
            <a href="#" className="text-blue-600 font-medium hover:underline flex items-center gap-1">
              Xem tất cả <ChevronRight className="w-4 h-4" />
            </a>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            {featuredBooks.map((book) => (
              <div 
                key={book.id} 
                className="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-100 transition-all group flex flex-col h-full cursor-pointer"
                onClick={() => setSelectedBook(book)}
              >
                <div className="relative aspect-[3/4] mb-4 overflow-hidden rounded-xl bg-gray-100">
                  <img src={book.image} alt={book.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" referrerPolicy="no-referrer" />
                  {book.badge && (
                    <span className="absolute top-2 left-2 px-2 py-1 bg-orange-500 text-white text-[10px] font-bold rounded uppercase tracking-wider z-10">
                      {book.badge}
                    </span>
                  )}
                  <button className="absolute top-2 right-2 p-2 bg-white/80 backdrop-blur text-gray-400 hover:text-red-500 rounded-full opacity-0 group-hover:opacity-100 transition-all z-10">
                    <Heart className="w-4 h-4" />
                  </button>
                </div>
                
                <div className="flex-1 flex flex-col">
                  <span className="text-xs text-blue-600 font-medium mb-1">{book.category}</span>
                  <h4 className="font-bold text-gray-800 line-clamp-2 mb-1 group-hover:text-blue-600 transition-colors">
                    {book.name}
                  </h4>
                  <p className="text-sm text-gray-500 mb-2">{book.author}</p>
                  
                  <div className="flex items-center gap-1 mb-3">
                    <div className="flex text-yellow-400">
                      <Star className="w-3.5 h-3.5 fill-current" />
                      <Star className="w-3.5 h-3.5 fill-current" />
                      <Star className="w-3.5 h-3.5 fill-current" />
                      <Star className="w-3.5 h-3.5 fill-current" />
                      <Star className="w-3.5 h-3.5 fill-current opacity-50" />
                    </div>
                    <span className="text-xs text-gray-400">({book.reviews})</span>
                  </div>

                  <div className="mt-auto pt-3 flex items-center justify-between border-t border-gray-50">
                    <div>
                      <span className="font-bold text-orange-600 block">{book.price.toLocaleString('vi-VN')} ₫</span>
                      {book.originalPrice && (
                        <span className="text-xs text-gray-400 line-through">{book.originalPrice.toLocaleString('vi-VN')} ₫</span>
                      )}
                    </div>
                    <button 
                      onClick={(e) => {
                        e.stopPropagation();
                        handleAddToCart(book);
                      }}
                      className="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"
                    >
                      <ShoppingCart className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* Categories Grid */}
        <section className="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <h3 className="text-2xl font-bold text-gray-800 mb-8 text-center">Khám Phá Theo Danh Mục</h3>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            {[
              { name: 'Văn học', icon: '📚', color: 'bg-purple-50 text-purple-600' },
              { name: 'Kinh tế', icon: '📈', color: 'bg-blue-50 text-blue-600' },
              { name: 'Tâm lý', icon: '🧠', color: 'bg-green-50 text-green-600' },
              { name: 'Thiếu nhi', icon: '🧸', color: 'bg-yellow-50 text-yellow-600' },
              { name: 'Khoa học', icon: '🔬', color: 'bg-cyan-50 text-cyan-600' },
              { name: 'Ngoại ngữ', icon: '🌍', color: 'bg-red-50 text-red-600' },
            ].map((cat, idx) => (
              <a key={idx} href="#" className="flex flex-col items-center justify-center p-6 rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all group">
                <div className={`w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-3 ${cat.color} group-hover:scale-110 transition-transform`}>
                  {cat.icon}
                </div>
                <span className="font-medium text-gray-800 text-sm text-center">{cat.name}</span>
              </a>
            ))}
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="bg-gray-900 text-gray-300 py-12 mt-12">
        <div className="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div className="flex items-center gap-2 mb-6">
              <div className="bg-blue-600 p-2 rounded-lg">
                <BookOpen className="text-white w-5 h-5" />
              </div>
              <div>
                <h1 className="font-bold text-white leading-tight">BOOK SALES</h1>
                <p className="text-[10px] font-semibold text-orange-500 tracking-wider">MANAGEMENT</p>
              </div>
            </div>
            <p className="text-sm text-gray-400 leading-relaxed">
              Hệ thống bán sách trực tuyến hàng đầu với hàng ngàn tựa sách đa dạng, cam kết chất lượng và dịch vụ tốt nhất.
            </p>
          </div>
          <div>
            <h4 className="text-white font-bold mb-4">Hỗ Trợ Khách Hàng</h4>
            <ul className="space-y-2 text-sm">
              <li><a href="#" className="hover:text-white transition-colors">Chính sách đổi trả</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Phương thức vận chuyển</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Phương thức thanh toán</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Câu hỏi thường gặp</a></li>
            </ul>
          </div>
          <div>
            <h4 className="text-white font-bold mb-4">Về Chúng Tôi</h4>
            <ul className="space-y-2 text-sm">
              <li><a href="#" className="hover:text-white transition-colors">Giới thiệu</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Tuyển dụng</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Chính sách bảo mật</a></li>
            </ul>
          </div>
          <div>
            <h4 className="text-white font-bold mb-4">Đăng Ký Nhận Tin</h4>
            <p className="text-sm text-gray-400 mb-4">Nhận thông tin về các chương trình khuyến mãi mới nhất.</p>
            <div className="flex">
              <input type="email" placeholder="Email của bạn" className="px-4 py-2 bg-gray-800 border border-gray-700 rounded-l-lg focus:outline-none focus:border-blue-500 w-full text-sm" />
              <button className="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                Gửi
              </button>
            </div>
          </div>
        </div>
      </footer>

      <BookDetailsModal 
        book={selectedBook}
        isOpen={!!selectedBook}
        onClose={() => setSelectedBook(null)}
        onAddToCart={handleAddToCart}
      />

      <CartDrawer 
        isOpen={isCartOpen}
        onClose={() => setIsCartOpen(false)}
        items={cartItems}
        onUpdateQuantity={handleUpdateQuantity}
        onRemoveItem={handleRemoveItem}
      />

      <AuthModal 
        isOpen={isAuthOpen}
        onClose={() => setIsAuthOpen(false)}
      />
    </div>
  );
}
