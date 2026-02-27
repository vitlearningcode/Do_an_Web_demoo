import { 
  BookOpen, 
  TrendingUp, 
  ShoppingCart, 
  PackageSearch, 
  FileText, 
  Settings, 
  Phone, 
  LogOut,
  BookPlus,
  LayoutDashboard
} from 'lucide-react';
import { ViewType } from '../types';

interface SidebarProps {
  currentView: ViewType;
  setCurrentView: (view: ViewType) => void;
  onLogoutClick: () => void;
}

export function Sidebar({ currentView, setCurrentView, onLogoutClick }: SidebarProps) {
  const menuItems = [
    { id: 'overview', label: 'Tổng quan', icon: LayoutDashboard },
    { id: 'import', label: 'Quản lý nhập sách', icon: BookPlus },
    { id: 'book-info', label: 'Quản lý thông tin sách', icon: BookOpen },
    { id: 'revenue', label: 'Quản lý doanh thu', icon: TrendingUp },
    { id: 'sales', label: 'Quản lý bán hàng', icon: ShoppingCart },
    { id: 'inventory', label: 'Quản lý kho', icon: PackageSearch },
    { id: 'reports', label: 'Báo cáo', icon: FileText },
  ] as const;

  return (
    <aside className="w-64 bg-white shadow-xl shadow-gray-200/50 flex flex-col h-full z-20 border-r border-gray-100">
      <div className="p-6 flex items-center gap-3 border-b border-gray-100 bg-white sticky top-0">
        <div className="bg-blue-600 p-2 rounded-lg">
          <TrendingUp className="text-white w-6 h-6" />
        </div>
        <div>
          <h1 className="font-bold text-blue-600 leading-tight">BOOK SALES</h1>
          <p className="text-xs font-semibold text-orange-500 tracking-wider">MANAGEMENT</p>
        </div>
      </div>

      <nav className="flex-1 py-6 flex flex-col gap-1.5 px-4 overflow-y-auto">
        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = currentView === item.id;
          return (
            <button
              key={item.id}
              onClick={() => setCurrentView(item.id as ViewType)}
              className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden ${
                isActive 
                  ? 'bg-blue-600 text-white shadow-md shadow-blue-200/50' 
                  : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600'
              }`}
            >
              {isActive && <div className="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-full"></div>}
              <Icon className={`w-5 h-5 transition-transform duration-300 ${isActive ? 'text-white scale-110' : 'text-gray-400 group-hover:text-blue-600 group-hover:scale-110'}`} />
              <span className={`font-medium text-sm transition-colors ${isActive ? 'text-white' : 'group-hover:text-blue-600'}`}>{item.label}</span>
            </button>
          );
        })}
      </nav>

      <div className="p-4 border-t border-gray-100 flex flex-col gap-1.5 bg-gray-50/50">
        <button 
          onClick={() => setCurrentView('settings')}
          className={`flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group ${currentView === 'settings' ? 'bg-white text-blue-600 shadow-sm shadow-gray-200/50' : 'text-gray-600 hover:bg-white hover:text-blue-600 hover:shadow-sm hover:shadow-gray-200/50'}`}
        >
          <Settings className={`w-5 h-5 transition-colors ${currentView === 'settings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'}`} />
          <span className="text-sm font-medium">Cài đặt hệ thống</span>
        </button>
        <button 
          onClick={() => setCurrentView('contact')}
          className={`flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group ${currentView === 'contact' ? 'bg-white text-blue-600 shadow-sm shadow-gray-200/50' : 'text-gray-600 hover:bg-white hover:text-blue-600 hover:shadow-sm hover:shadow-gray-200/50'}`}
        >
          <Phone className={`w-5 h-5 transition-colors ${currentView === 'contact' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'}`} />
          <span className="text-sm font-medium">Hỗ trợ & Liên hệ</span>
        </button>
        <div className="h-px bg-gray-200 my-1 mx-4"></div>
        <button 
          onClick={onLogoutClick}
          className="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all group"
        >
          <LogOut className="w-5 h-5 text-gray-400 group-hover:text-red-500 group-hover:scale-110 transition-all" />
          <span className="text-sm font-medium">Đăng xuất</span>
        </button>
      </div>
    </aside>
  );
}
