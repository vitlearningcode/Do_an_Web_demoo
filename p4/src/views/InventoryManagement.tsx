import { useState } from 'react';
import { Search, Plus, Filter, PackageSearch, MapPin, ClipboardList, AlertTriangle } from 'lucide-react';

export function InventoryManagement() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Quản lý kho</h2>
          <p className="text-gray-500 text-sm mt-1">Vị trí lưu trữ, kiểm kê và điều chỉnh tồn kho</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
            <ClipboardList className="w-4 h-4" />
            Tạo phiếu kiểm kê
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm">
            <Plus className="w-4 h-4" />
            Điều chỉnh tồn kho
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {[
          { title: 'Tổng số lượng sách', value: '15,430', icon: PackageSearch, color: 'blue' },
          { title: 'Sắp hết hàng', value: '24', icon: AlertTriangle, color: 'orange' },
          { title: 'Vị trí trống', value: '12', icon: MapPin, color: 'green' },
        ].map((stat, idx) => {
          const Icon = stat.icon;
          return (
            <div key={idx} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50 flex items-center gap-4">
              <div className={`p-4 rounded-2xl bg-${stat.color}-50 text-${stat.color}-600`}>
                <Icon className="w-8 h-8" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500 mb-1">{stat.title}</p>
                <h3 className="text-2xl font-bold text-gray-800">{stat.value}</h3>
              </div>
            </div>
          );
        })}
      </div>

      <div className="bg-white rounded-2xl shadow-sm p-6">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-lg font-bold text-gray-800">Danh sách tồn kho & Vị trí</h3>
          <div className="flex gap-2">
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input 
                type="text" 
                placeholder="Tìm kiếm sách, vị trí..." 
                className="pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all w-64"
              />
            </div>
            <button className="p-2 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors">
              <Filter className="w-4 h-4" />
            </button>
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-gray-100 bg-gray-50/50">
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider rounded-tl-xl">Tên Sách</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã Sách</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vị Trí Lưu Trữ</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Tồn Kho</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Định Mức</th>
                <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider rounded-tr-xl">Trạng Thái</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-50">
              {[
                { name: 'Đắc Nhân Tâm', code: 'BK-1001', location: 'Kệ A - Tầng 2 - Ô 3', stock: 120, min: 20, status: 'Bình thường' },
                { name: 'Nhà Giả Kim', code: 'BK-1002', location: 'Kệ B - Tầng 1 - Ô 1', stock: 45, min: 50, status: 'Cần nhập thêm' },
                { name: 'Nghĩ Giàu Làm Giàu', code: 'BK-1003', location: 'Kệ A - Tầng 3 - Ô 2', stock: 8, min: 10, status: 'Sắp hết hàng' },
                { name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', code: 'BK-1004', location: 'Kệ C - Tầng 2 - Ô 5', stock: 210, min: 30, status: 'Bình thường' },
                { name: 'Sapiens: Lược Sử Loài Người', code: 'BK-1005', location: 'Kệ D - Tầng 1 - Ô 4', stock: 32, min: 20, status: 'Bình thường' },
              ].map((item, idx) => (
                <tr key={idx} className="hover:bg-gray-50/50 transition-colors cursor-pointer">
                  <td className="py-3 px-4 text-sm font-medium text-gray-800">{item.name}</td>
                  <td className="py-3 px-4 text-sm text-gray-500">{item.code}</td>
                  <td className="py-3 px-4 text-sm text-gray-600">
                    <span className="flex items-center gap-1">
                      <MapPin className="w-3 h-3 text-gray-400" />
                      {item.location}
                    </span>
                  </td>
                  <td className="py-3 px-4 text-sm font-medium text-center text-gray-800">{item.stock}</td>
                  <td className="py-3 px-4 text-sm text-center text-gray-500">{item.min}</td>
                  <td className="py-3 px-4">
                    <span className={`px-2.5 py-1 rounded-full text-xs font-medium border ${
                      item.status === 'Bình thường' ? 'bg-green-50 text-green-700 border-green-200' :
                      item.status === 'Cần nhập thêm' ? 'bg-orange-50 text-orange-700 border-orange-200' :
                      'bg-red-50 text-red-700 border-red-200'
                    }`}>
                      {item.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
