import { 
  BookOpen, 
  TrendingUp, 
  ShoppingCart, 
  PackageSearch, 
  FileText, 
  Settings, 
  Phone, 
  LogOut,
  BookPlus
} from 'lucide-react';
import { ViewType } from '../App';

interface SidebarProps {
  currentView: ViewType;
  setCurrentView: (view: ViewType) => void;
}

export function Sidebar({ currentView, setCurrentView }: SidebarProps) {
  const menuItems = [
    { id: 'import', label: 'Quản lý nhập sách', icon: BookPlus },
    { id: 'book-info', label: 'Quản lý thông tin sách', icon: BookOpen },
    { id: 'revenue', label: 'Quản lý doanh thu', icon: TrendingUp },
    { id: 'sales', label: 'Quản lý bán hàng', icon: ShoppingCart },
    { id: 'inventory', label: 'Quản lý kho', icon: PackageSearch },
    { id: 'reports', label: 'Báo cáo', icon: FileText },
  ] as const;

  return (
    <aside className="w-64 bg-white shadow-lg flex flex-col h-full z-10">
      <div className="p-6 flex items-center gap-3 border-b border-gray-100">
        <div className="bg-blue-600 p-2 rounded-lg">
          <TrendingUp className="text-white w-6 h-6" />
        </div>
        <div>
          <h1 className="font-bold text-blue-600 leading-tight">BOOK SALES</h1>
          <p className="text-xs font-semibold text-orange-500 tracking-wider">MANAGEMENT</p>
        </div>
      </div>

      <nav className="flex-1 py-6 flex flex-col gap-2 px-4">
        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = currentView === item.id;
          return (
            <button
              key={item.id}
              onClick={() => setCurrentView(item.id as ViewType)}
              className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 ${
                isActive 
                  ? 'bg-blue-600 text-white shadow-md shadow-blue-200' 
                  : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600'
              }`}
            >
              <Icon className={`w-5 h-5 ${isActive ? 'text-white' : 'text-blue-600'}`} />
              <span className="font-medium text-sm">{item.label}</span>
            </button>
          );
        })}
      </nav>

      <div className="p-4 border-t border-gray-100 flex flex-col gap-2">
        <button className="flex items-center gap-3 px-4 py-2 text-gray-500 hover:text-gray-800 transition-colors">
          <Settings className="w-5 h-5" />
          <span className="text-sm font-medium">Cài Đặt</span>
        </button>
        <button className="flex items-center gap-3 px-4 py-2 text-gray-500 hover:text-gray-800 transition-colors">
          <Phone className="w-5 h-5" />
          <span className="text-sm font-medium">Liên Hệ</span>
        </button>
        <button className="flex items-center gap-3 px-4 py-2 text-gray-500 hover:text-gray-800 transition-colors mt-2">
          <LogOut className="w-5 h-5" />
          <span className="text-sm font-medium">Thoát</span>
        </button>
      </div>
    </aside>
  );
}
