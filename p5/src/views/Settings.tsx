import { useState } from 'react';
import { Save, User, Lock, Bell, Globe, Shield, Settings as SettingsIcon } from 'lucide-react';

export function Settings() {
  const [activeTab, setActiveTab] = useState('profile');

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Cài đặt hệ thống</h2>
          <p className="text-gray-500 text-sm mt-1">Quản lý thông tin tài khoản và cấu hình hệ thống</p>
        </div>
        <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm">
          <Save className="w-4 h-4" />
          Lưu thay đổi
        </button>
      </div>

      <div className="flex flex-col md:flex-row gap-6">
        {/* Sidebar Tabs */}
        <div className="w-full md:w-64 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 h-fit">
          <nav className="flex flex-col gap-2">
            {[
              { id: 'profile', label: 'Hồ sơ cá nhân', icon: User },
              { id: 'security', label: 'Bảo mật', icon: Lock },
              { id: 'notifications', label: 'Thông báo', icon: Bell },
              { id: 'general', label: 'Cài đặt chung', icon: Globe },
              { id: 'permissions', label: 'Phân quyền', icon: Shield },
            ].map((tab) => {
              const Icon = tab.icon;
              const isActive = activeTab === tab.id;
              return (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-medium ${
                    isActive 
                      ? 'bg-blue-50 text-blue-600' 
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                  }`}
                >
                  <Icon className={`w-5 h-5 ${isActive ? 'text-blue-600' : 'text-gray-400'}`} />
                  {tab.label}
                </button>
              );
            })}
          </nav>
        </div>

        {/* Content Area */}
        <div className="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
          {activeTab === 'profile' && (
            <div className="space-y-6 max-w-2xl">
              <h3 className="text-lg font-bold text-gray-800 mb-4">Thông tin cá nhân</h3>
              
              <div className="flex items-center gap-6 mb-8">
                <div className="w-24 h-24 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center border-4 border-white shadow-lg text-3xl font-bold">
                  A
                </div>
                <div>
                  <button className="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm mb-2">
                    Thay đổi ảnh đại diện
                  </button>
                  <p className="text-xs text-gray-500">JPG, GIF hoặc PNG tối đa 3MB</p>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                  <input type="text" defaultValue="Admin User" className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                  <input type="text" defaultValue="Quản lý cửa hàng" disabled className="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 cursor-not-allowed" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input type="email" defaultValue="admin@booksales.com" className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                  <input type="tel" defaultValue="0987654321" className="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                </div>
              </div>
            </div>
          )}

          {activeTab !== 'profile' && (
            <div className="flex flex-col items-center justify-center h-64 text-gray-400">
              <SettingsIcon className="w-12 h-12 mb-4 opacity-20" />
              <p>Nội dung đang được cập nhật...</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
