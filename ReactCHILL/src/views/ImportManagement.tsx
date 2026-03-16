import { useState } from 'react';
import { Search, Plus, FileDown, Filter } from 'lucide-react';

export function ImportManagement() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Quản lý nhập sách & Nhà cung cấp</h2>
          <p className="text-gray-500 text-sm mt-1">Quản lý các đơn nhập hàng và thông tin nhà cung cấp</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
            <FileDown className="w-4 h-4" />
            Xuất file
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm">
            <Plus className="w-4 h-4" />
            Tạo đơn nhập
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Danh sách đơn nhập</h3>
            <div className="flex gap-2">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input 
                  type="text" 
                  placeholder="Tìm kiếm mã đơn..." 
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
                <tr className="border-b border-gray-100">
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã Đơn</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nhà Cung Cấp</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày Nhập</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng Tiền</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {[1, 2, 3, 4, 5].map((item) => (
                  <tr key={item} className="hover:bg-gray-50/50 transition-colors cursor-pointer">
                    <td className="py-3 px-4 text-sm font-medium text-blue-600">#IMP-{1000 + item}</td>
                    <td className="py-3 px-4 text-sm text-gray-700">NXB Kim Đồng</td>
                    <td className="py-3 px-4 text-sm text-gray-500">12/10/2023</td>
                    <td className="py-3 px-4 text-sm font-medium text-gray-800">12,500,000 ₫</td>
                    <td className="py-3 px-4">
                      <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                        Hoàn thành
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        <div className="bg-white rounded-2xl shadow-sm p-6">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Nhà cung cấp</h3>
            <button className="text-blue-600 text-sm font-medium hover:underline">Xem tất cả</button>
          </div>
          
          <div className="space-y-4">
            {[
              { name: 'NXB Kim Đồng', books: 1240, debt: '0 ₫' },
              { name: 'NXB Trẻ', books: 850, debt: '5,000,000 ₫' },
              { name: 'Nhã Nam', books: 2100, debt: '12,000,000 ₫' },
              { name: 'Alphabooks', books: 430, debt: '0 ₫' },
            ].map((supplier, idx) => (
              <div key={idx} className="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-blue-100 hover:bg-blue-50/30 transition-colors cursor-pointer">
                <div>
                  <h4 className="font-medium text-gray-800 text-sm">{supplier.name}</h4>
                  <p className="text-xs text-gray-500 mt-0.5">{supplier.books} đầu sách</p>
                </div>
                <div className="text-right">
                  <p className="text-xs text-gray-500 mb-0.5">Công nợ</p>
                  <p className={`text-sm font-medium ${supplier.debt === '0 ₫' ? 'text-green-600' : 'text-red-600'}`}>
                    {supplier.debt}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
