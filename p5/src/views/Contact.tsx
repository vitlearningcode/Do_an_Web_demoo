import { Mail, Phone, MapPin, MessageSquare, Send } from 'lucide-react';

export function Contact() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Hỗ trợ & Liên hệ</h2>
          <p className="text-gray-500 text-sm mt-1">Gửi yêu cầu hỗ trợ kỹ thuật hoặc liên hệ với đội ngũ phát triển</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Contact Info */}
        <div className="lg:col-span-1 space-y-6">
          <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 className="text-lg font-bold text-gray-800 mb-6">Thông tin liên hệ</h3>
            <div className="space-y-6">
              <div className="flex items-start gap-4">
                <div className="p-3 bg-blue-50 text-blue-600 rounded-xl">
                  <Phone className="w-6 h-6" />
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500 mb-1">Hotline hỗ trợ kỹ thuật</p>
                  <p className="text-lg font-bold text-gray-800">1900 1234</p>
                  <p className="text-xs text-gray-400 mt-1">Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                </div>
              </div>
              
              <div className="flex items-start gap-4">
                <div className="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                  <Mail className="w-6 h-6" />
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500 mb-1">Email hỗ trợ</p>
                  <p className="text-base font-bold text-gray-800">support@booksales.com</p>
                </div>
              </div>

              <div className="flex items-start gap-4">
                <div className="p-3 bg-purple-50 text-purple-600 rounded-xl">
                  <MapPin className="w-6 h-6" />
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500 mb-1">Văn phòng chính</p>
                  <p className="text-sm font-medium text-gray-800 leading-relaxed">
                    Tầng 12, Tòa nhà Tech Tower,<br />
                    Quận 1, TP. Hồ Chí Minh
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-gradient-to-br from-blue-600 to-blue-800 p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
            <div className="absolute top-0 right-0 w-32 h-32 bg-white rounded-full opacity-10 -mr-16 -mt-16"></div>
            <MessageSquare className="w-8 h-8 mb-4 opacity-80" />
            <h3 className="text-xl font-bold mb-2">Cần hỗ trợ khẩn cấp?</h3>
            <p className="text-blue-100 text-sm mb-6">Chat trực tiếp với đội ngũ hỗ trợ kỹ thuật của chúng tôi.</p>
            <button className="w-full py-2.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition-colors shadow-sm">
              Bắt đầu Chat
            </button>
          </div>
        </div>

        {/* Contact Form */}
        <div className="lg:col-span-2 bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100">
          <h3 className="text-xl font-bold text-gray-800 mb-6">Gửi yêu cầu hỗ trợ</h3>
          
          <form className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                <input type="text" className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Nhập họ và tên" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Email liên hệ</label>
                <input type="email" className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Nhập email" />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Chủ đề</label>
              <select className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option>Báo lỗi hệ thống</option>
                <option>Yêu cầu tính năng mới</option>
                <option>Hỗ trợ sử dụng</option>
                <option>Khác</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Nội dung chi tiết</label>
              <textarea rows={6} className="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..."></textarea>
            </div>

            <div className="flex justify-end">
              <button type="button" className="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center gap-2">
                <Send className="w-4 h-4" />
                Gửi yêu cầu
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
