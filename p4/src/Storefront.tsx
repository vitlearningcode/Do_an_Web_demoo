import { useState, useEffect } from 'react';
import { Search, ShoppingCart, User, Menu, Star, ChevronRight, Heart, BookOpen, Clock, Flame, Sparkles } from 'lucide-react';
import { Book } from './types';
import { BookDetailsModal } from './components/BookDetailsModal';
import { CartDrawer } from './components/CartDrawer';
import { AuthModal } from './components/AuthModal';
import { HeroCarousel } from './components/HeroCarousel';
import { BookCard } from './components/BookCard';
import { Toast } from './components/Toast';

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

const newReleases: Book[] = [
  { id: '9', name: 'Dune - Xứ Cát', author: 'Frank Herbert', category: 'Khoa học viễn tưởng', price: 210000, originalPrice: 250000, rating: 4.9, reviews: 150, image: 'https://picsum.photos/seed/book9/300/400', badge: 'Mới' },
  { id: '10', name: 'Atomic Habits', author: 'James Clear', category: 'Kỹ năng sống', price: 145000, rating: 4.8, reviews: 5200, image: 'https://picsum.photos/seed/book10/300/400' },
  { id: '11', name: 'Kẻ Trộm Sách', author: 'Markus Zusak', category: 'Văn học', price: 125000, originalPrice: 140000, rating: 4.7, reviews: 890, image: 'https://picsum.photos/seed/book11/300/400' },
  { id: '12', name: 'Sức Mạnh Của Thói Quen', author: 'Charles Duhigg', category: 'Tâm lý học', price: 115000, rating: 4.6, reviews: 1200, image: 'https://picsum.photos/seed/book12/300/400' },
  { id: '13', name: 'Tư Duy Nhanh Và Chậm', author: 'Daniel Kahneman', category: 'Kinh tế', price: 185000, originalPrice: 210000, rating: 4.8, reviews: 3400, image: 'https://picsum.photos/seed/book13/300/400', badge: 'Bán chạy' },
];

export function Storefront() {
  const [cartItems, setCartItems] = useState<(Book & { quantity: number })[]>([]);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [isAuthOpen, setIsAuthOpen] = useState(false);
  const [selectedBook, setSelectedBook] = useState<Book | null>(null);
  const [toastMessage, setToastMessage] = useState('');
  const [isToastVisible, setIsToastVisible] = useState(false);
  const [timeLeft, setTimeLeft] = useState({ hours: 12, minutes: 45, seconds: 30 });

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft(prev => {
        if (prev.seconds > 0) return { ...prev, seconds: prev.seconds - 1 };
        if (prev.minutes > 0) return { ...prev, minutes: prev.minutes - 1, seconds: 59 };
        if (prev.hours > 0) return { ...prev, hours: prev.hours - 1, minutes: 59, seconds: 59 };
        return { hours: 24, minutes: 0, seconds: 0 };
      });
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const cartCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);

  const showToast = (message: string) => {
    setToastMessage(message);
    setIsToastVisible(true);
  };

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
    showToast(`Đã thêm "${book.name}" vào giỏ hàng`);
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
      <header className="bg-white shadow-sm sticky top-0 z-40 transition-all duration-300">
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

      <main className="container mx-auto px-4 py-8 space-y-16">
        {/* Hero Banner */}
        <HeroCarousel />

        {/* Flash Sale */}
        <section className="bg-gradient-to-r from-orange-500 to-red-500 rounded-3xl p-1 shadow-lg">
          <div className="bg-white rounded-[22px] p-6 md:p-8">
            <div className="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-red-100 text-red-600 rounded-2xl">
                  <Flame className="w-8 h-8" />
                </div>
                <div>
                  <h3 className="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-2">
                    Flash Sale
                    <span className="text-red-500">Giá Sốc</span>
                  </h3>
                  <p className="text-gray-500 mt-1 font-medium">Kết thúc trong</p>
                </div>
              </div>
              
              <div className="flex items-center gap-3">
                <div className="flex flex-col items-center">
                  <div className="bg-gray-900 text-white text-xl font-bold w-12 h-12 flex items-center justify-center rounded-xl shadow-inner">
                    {timeLeft.hours.toString().padStart(2, '0')}
                  </div>
                  <span className="text-[10px] font-bold text-gray-500 uppercase mt-1 tracking-wider">Giờ</span>
                </div>
                <span className="text-2xl font-bold text-gray-400 mb-5">:</span>
                <div className="flex flex-col items-center">
                  <div className="bg-gray-900 text-white text-xl font-bold w-12 h-12 flex items-center justify-center rounded-xl shadow-inner">
                    {timeLeft.minutes.toString().padStart(2, '0')}
                  </div>
                  <span className="text-[10px] font-bold text-gray-500 uppercase mt-1 tracking-wider">Phút</span>
                </div>
                <span className="text-2xl font-bold text-gray-400 mb-5">:</span>
                <div className="flex flex-col items-center">
                  <div className="bg-red-500 text-white text-xl font-bold w-12 h-12 flex items-center justify-center rounded-xl shadow-inner animate-pulse">
                    {timeLeft.seconds.toString().padStart(2, '0')}
                  </div>
                  <span className="text-[10px] font-bold text-red-500 uppercase mt-1 tracking-wider">Giây</span>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
              {featuredBooks.slice(0, 5).map((book) => (
                <BookCard 
                  key={book.id} 
                  book={{...book, badge: 'Flash Sale', originalPrice: book.price * 1.5}} 
                  onAddToCart={() => handleAddToCart(book)}
                  onQuickView={() => setSelectedBook(book)}
                />
              ))}
            </div>
          </div>
        </section>

        {/* Categories Grid */}
        <section className="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <div className="flex items-center justify-between mb-8">
            <h3 className="text-2xl font-bold text-gray-800">Khám Phá Theo Danh Mục</h3>
            <a href="#" className="text-blue-600 font-medium hover:underline flex items-center gap-1">
              Xem tất cả <ChevronRight className="w-4 h-4" />
            </a>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            {[
              { name: 'Văn học', icon: '📚', color: 'bg-purple-50 text-purple-600 border-purple-100 hover:border-purple-300 hover:shadow-purple-100' },
              { name: 'Kinh tế', icon: '📈', color: 'bg-blue-50 text-blue-600 border-blue-100 hover:border-blue-300 hover:shadow-blue-100' },
              { name: 'Tâm lý', icon: '🧠', color: 'bg-green-50 text-green-600 border-green-100 hover:border-green-300 hover:shadow-green-100' },
              { name: 'Thiếu nhi', icon: '🧸', color: 'bg-yellow-50 text-yellow-600 border-yellow-100 hover:border-yellow-300 hover:shadow-yellow-100' },
              { name: 'Khoa học', icon: '🔬', color: 'bg-cyan-50 text-cyan-600 border-cyan-100 hover:border-cyan-300 hover:shadow-cyan-100' },
              { name: 'Ngoại ngữ', icon: '🌍', color: 'bg-red-50 text-red-600 border-red-100 hover:border-red-300 hover:shadow-red-100' },
            ].map((cat, idx) => (
              <a key={idx} href="#" className={`flex flex-col items-center justify-center p-6 rounded-2xl border transition-all duration-300 group hover:shadow-lg hover:-translate-y-1 ${cat.color}`}>
                <div className="w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-3 bg-white shadow-sm group-hover:scale-110 transition-transform duration-300">
                  {cat.icon}
                </div>
                <span className="font-bold text-gray-800 text-sm text-center">{cat.name}</span>
              </a>
            ))}
          </div>
        </section>

        {/* Featured Books */}
        <section>
          <div className="flex justify-between items-end mb-8">
            <div>
              <h3 className="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <Star className="w-6 h-6 text-yellow-500 fill-yellow-500" />
                Sách Bán Chạy Nhất
              </h3>
              <p className="text-gray-500 mt-2 font-medium">Những tựa sách được độc giả yêu thích nhất tuần qua</p>
            </div>
            <a href="#" className="text-blue-600 font-medium hover:underline flex items-center gap-1 bg-blue-50 px-4 py-2 rounded-full transition-colors hover:bg-blue-100">
              Xem tất cả <ChevronRight className="w-4 h-4" />
            </a>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            {featuredBooks.map((book) => (
              <BookCard 
                key={book.id} 
                book={book} 
                onAddToCart={() => handleAddToCart(book)}
                onQuickView={() => setSelectedBook(book)}
              />
            ))}
          </div>
        </section>

        {/* New Releases */}
        <section className="bg-gray-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-blue-600 rounded-full blur-[100px] opacity-20"></div>
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-600 rounded-full blur-[100px] opacity-20"></div>
          
          <div className="relative z-10">
            <div className="flex justify-between items-end mb-8">
              <div>
                <h3 className="text-2xl md:text-3xl font-bold text-white flex items-center gap-2">
                  <Sparkles className="w-6 h-6 text-yellow-400" />
                  Sách Mới Phát Hành
                </h3>
                <p className="text-gray-400 mt-2 font-medium">Cập nhật những tựa sách mới nhất từ các nhà xuất bản</p>
              </div>
              <a href="#" className="text-white font-medium hover:text-blue-400 transition-colors flex items-center gap-1 bg-white/10 px-4 py-2 rounded-full hover:bg-white/20 backdrop-blur-sm border border-white/10">
                Xem tất cả <ChevronRight className="w-4 h-4" />
              </a>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
              {newReleases.map((book) => (
                <BookCard 
                  key={book.id} 
                  book={book} 
                  onAddToCart={() => handleAddToCart(book)}
                  onQuickView={() => setSelectedBook(book)}
                />
              ))}
            </div>
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

      <Toast 
        message={toastMessage}
        isVisible={isToastVisible}
        onClose={() => setIsToastVisible(false)}
      />
    </div>
  );
}
