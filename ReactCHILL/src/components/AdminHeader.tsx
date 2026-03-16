import { Bell, Search, User, Menu } from 'lucide-react';

export function AdminHeader() {
  return (
    <header className="bg-white border-b border-gray-100 h-16 px-6 flex items-center justify-between sticky top-0 z-30">
      <div className="flex items-center gap-4 flex-1">
        <button className="p-2 text-gray-500 hover:bg-gray-100 rounded-lg lg:hidden transition-colors">
          <Menu className="w-5 h-5" />
        </button>
        <div className="relative max-w-md w-full hidden sm:block">
          <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input 
            type="text" 
            placeholder="Tìm kiếm nhanh (Ctrl+K)..." 
            className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all"
          />
        </div>
      </div>

      <div className="flex items-center gap-4">
        <button className="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
          <Bell className="w-5 h-5" />
          <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        </button>
        <div className="h-8 w-px bg-gray-200 mx-2"></div>
        <button className="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded-xl transition-colors">
          <div className="text-right hidden sm:block">
            <p className="text-sm font-bold text-gray-800 leading-none">Admin User</p>
            <p className="text-xs text-gray-500 mt-1">Quản lý cửa hàng</p>
          </div>
          <div className="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center border border-blue-200">
            <User className="w-5 h-5" />
          </div>
        </button>
      </div>
    </header>
  );
}
