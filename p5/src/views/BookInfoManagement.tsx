import { useState } from 'react';
import { Search, Plus, Filter, Edit, Trash2, Image as ImageIcon, X, Upload, Save } from 'lucide-react';
import { ImageGeneratorModal } from '../components/ImageGeneratorModal';

export function BookInfoManagement() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [selectedBook, setSelectedBook] = useState<{name: string, index: number} | null>(null);
  const [books, setBooks] = useState([
    { name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: '86,000 ₫', stock: 120, img: 'https://picsum.photos/seed/book1/60/80' },
    { name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: '79,000 ₫', stock: 45, img: 'https://picsum.photos/seed/book2/60/80' },
    { name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: '110,000 ₫', stock: 8, img: 'https://picsum.photos/seed/book3/60/80' },
    { name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: '80,000 ₫', stock: 210, img: 'https://picsum.photos/seed/book4/60/80' },
    { name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: '250,000 ₫', stock: 32, img: 'https://picsum.photos/seed/book5/60/80' },
  ]);

  const handleImageSelect = (imageUrl: string) => {
    if (selectedBook !== null) {
      const newBooks = [...books];
      newBooks[selectedBook.index].img = imageUrl;
      setBooks(newBooks);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Quản lý thông tin sách chi tiết</h2>
          <p className="text-gray-500 text-sm mt-1">Thêm, sửa, xóa và cập nhật thông tin sách</p>
        </div>
        <div className="flex gap-3">
          <button 
            onClick={() => setIsAddModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm"
          >
            <Plus className="w-4 h-4" />
            Thêm sách mới
          </button>
        </div>
      </div>

      <div className="bg-white rounded-2xl shadow-sm p-6">
        <div className="flex justify-between items-center mb-6">
          <div className="flex gap-4">
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input 
                type="text" 
                placeholder="Tìm kiếm tên sách, tác giả, ISBN..." 
                className="pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all w-80"
              />
            </div>
            <select className="border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
              <option>Tất cả danh mục</option>
              <option>Văn học</option>
              <option>Kinh tế</option>
              <option>Kỹ năng sống</option>
            </select>
          </div>
          <button className="p-2 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors">
            <Filter className="w-4 h-4" />
          </button>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-gray-100 bg-gray-50/50">
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider rounded-tl-xl">Sách</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tác Giả</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Danh Mục</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Giá Bán</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tồn Kho</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right rounded-tr-xl">Thao Tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-50">
              {books.map((book, idx) => (
                <tr key={idx} className="hover:bg-gray-50/50 transition-colors group">
                  <td className="py-4 px-4">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-14 bg-gray-100 rounded overflow-hidden flex-shrink-0 border border-gray-200">
                        <img src={book.img} alt={book.name} className="w-full h-full object-cover" referrerPolicy="no-referrer" />
                      </div>
                      <div>
                        <p className="text-sm font-medium text-gray-800 line-clamp-1">{book.name}</p>
                        <p className="text-xs text-gray-500 mt-0.5">ISBN: 978-604-{1000 + idx}</p>
                      </div>
                    </div>
                  </td>
                  <td className="py-4 px-4 text-sm text-gray-600">{book.author}</td>
                  <td className="py-4 px-4">
                    <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                      {book.category}
                    </span>
                  </td>
                  <td className="py-4 px-4 text-sm font-medium text-gray-800">{book.price}</td>
                  <td className="py-4 px-4">
                    <div className="flex items-center gap-2">
                      <div className={`w-2 h-2 rounded-full ${book.stock < 10 ? 'bg-red-500' : book.stock < 50 ? 'bg-orange-500' : 'bg-green-500'}`}></div>
                      <span className={`text-sm font-medium ${book.stock < 10 ? 'text-red-600' : 'text-gray-700'}`}>{book.stock}</span>
                    </div>
                  </td>
                  <td className="py-4 px-4 text-right">
                    <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button 
                        onClick={() => {
                          setSelectedBook({ name: book.name, index: idx });
                          setIsModalOpen(true);
                        }}
                        className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" 
                        title="Tạo ảnh bìa AI"
                      >
                        <ImageIcon className="w-4 h-4" />
                      </button>
                      <button className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                        <Edit className="w-4 h-4" />
                      </button>
                      <button className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        
        <div className="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
          <p className="text-sm text-gray-500">Hiển thị <span className="font-medium text-gray-800">1-5</span> trong <span className="font-medium text-gray-800">124</span> kết quả</p>
          <div className="flex gap-1">
            <button className="px-3 py-1 border border-gray-200 rounded text-sm text-gray-500 hover:bg-gray-50 disabled:opacity-50">Trước</button>
            <button className="px-3 py-1 border border-blue-600 bg-blue-600 rounded text-sm text-white font-medium">1</button>
            <button className="px-3 py-1 border border-gray-200 rounded text-sm text-gray-600 hover:bg-gray-50">2</button>
            <button className="px-3 py-1 border border-gray-200 rounded text-sm text-gray-600 hover:bg-gray-50">3</button>
            <button className="px-3 py-1 border border-gray-200 rounded text-sm text-gray-500 hover:bg-gray-50">Sau</button>
          </div>
        </div>
      </div>

      {selectedBook && (
        <ImageGeneratorModal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          bookName={selectedBook.name}
          onImageSelect={handleImageSelect}
        />
      )}

      {/* Add/Edit Book Modal (Slide-over) */}
      {isAddModalOpen && (
        <div className="fixed inset-0 z-50 overflow-hidden">
          <div className="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onClick={() => setIsAddModalOpen(false)} />
          <div className="fixed inset-y-0 right-0 max-w-md w-full flex">
            <div className="w-full h-full bg-white shadow-2xl flex flex-col animate-in slide-in-from-right duration-300">
              <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 className="text-lg font-bold text-gray-800">Thêm sách mới</h3>
                <button 
                  onClick={() => setIsAddModalOpen(false)}
                  className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              <div className="flex-1 overflow-y-auto p-6 space-y-6">
                {/* Image Upload Area */}
                <div className="border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 hover:border-blue-300 transition-colors cursor-pointer group">
                  <div className="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <Upload className="w-6 h-6" />
                  </div>
                  <p className="text-sm font-medium text-gray-700">Kéo thả ảnh bìa hoặc click để tải lên</p>
                  <p className="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                </div>

                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tên sách <span className="text-red-500">*</span></label>
                    <input type="text" className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Nhập tên sách" />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tác giả <span className="text-red-500">*</span></label>
                    <input type="text" className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Nhập tên tác giả" />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                      <select className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option>Văn học</option>
                        <option>Kinh tế</option>
                        <option>Kỹ năng sống</option>
                        <option>Khoa học</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Giá bán (VNĐ) <span className="text-red-500">*</span></label>
                      <input type="number" className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0" />
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Số lượng nhập</label>
                      <input type="number" className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0" />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Mã ISBN</label>
                      <input type="text" className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="978-..." />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea rows={4} className="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" placeholder="Nhập mô tả nội dung sách..."></textarea>
                  </div>
                </div>
              </div>

              <div className="p-6 border-t border-gray-100 bg-gray-50/50 flex gap-3">
                <button 
                  onClick={() => setIsAddModalOpen(false)}
                  className="flex-1 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors"
                >
                  Hủy bỏ
                </button>
                <button 
                  onClick={() => setIsAddModalOpen(false)}
                  className="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 flex items-center justify-center gap-2"
                >
                  <Save className="w-4 h-4" />
                  Lưu thông tin
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
