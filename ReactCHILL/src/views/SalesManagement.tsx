import { useState } from 'react';
import { Search, Plus, Filter, FileText, Printer, Users, UserCheck } from 'lucide-react';

export function SalesManagement() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Quản lý bán hàng</h2>
          <p className="text-gray-500 text-sm mt-1">Tạo hóa đơn, in bill, quản lý nhân viên và khách hàng</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
            <Printer className="w-4 h-4" />
            In báo cáo
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm">
            <Plus className="w-4 h-4" />
            Tạo hóa đơn mới
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div className="lg:col-span-3 bg-white rounded-2xl shadow-sm p-6">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Danh sách hóa đơn gần đây</h3>
            <div className="flex gap-2">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input 
                  type="text" 
                  placeholder="Tìm kiếm mã HĐ, khách hàng..." 
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
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider rounded-tl-xl">Mã HĐ</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách Hàng</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nhân Viên</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày Tạo</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng Tiền</th>
                  <th className="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider rounded-tr-xl">Trạng Thái</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {[
                  { id: 'HD-20231012-01', customer: 'Nguyễn Văn A', staff: 'Trần Thị B', date: '12/10/2023 14:30', total: '450,000 ₫', status: 'Đã thanh toán' },
                  { id: 'HD-20231012-02', customer: 'Lê Hoàng C', staff: 'Phạm Văn D', date: '12/10/2023 15:15', total: '1,250,000 ₫', status: 'Chờ thanh toán' },
                  { id: 'HD-20231012-03', customer: 'Khách lẻ', staff: 'Trần Thị B', date: '12/10/2023 16:00', total: '85,000 ₫', status: 'Đã thanh toán' },
                  { id: 'HD-20231012-04', customer: 'Công ty ABC', staff: 'Nguyễn Văn E', date: '12/10/2023 16:45', total: '5,500,000 ₫', status: 'Đã thanh toán' },
                  { id: 'HD-20231012-05', customer: 'Đặng Thu Thảo', staff: 'Phạm Văn D', date: '12/10/2023 17:20', total: '320,000 ₫', status: 'Đã hủy' },
                ].map((invoice, idx) => (
                  <tr key={idx} className="hover:bg-gray-50/50 transition-colors cursor-pointer group">
                    <td className="py-3 px-4 text-sm font-medium text-blue-600 group-hover:underline">{invoice.id}</td>
                    <td className="py-3 px-4 text-sm text-gray-700">{invoice.customer}</td>
                    <td className="py-3 px-4 text-sm text-gray-500">{invoice.staff}</td>
                    <td className="py-3 px-4 text-sm text-gray-500">{invoice.date}</td>
                    <td className="py-3 px-4 text-sm font-medium text-gray-800">{invoice.total}</td>
                    <td className="py-3 px-4">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-medium border ${
                        invoice.status === 'Đã thanh toán' ? 'bg-green-50 text-green-700 border-green-200' :
                        invoice.status === 'Chờ thanh toán' ? 'bg-orange-50 text-orange-700 border-orange-200' :
                        'bg-red-50 text-red-700 border-red-200'
                      }`}>
                        {invoice.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-white rounded-2xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <Users className="w-5 h-5" />
              </div>
              <h3 className="text-lg font-bold text-gray-800">Khách hàng</h3>
            </div>
            <div className="space-y-4">
              <div className="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                <span className="text-gray-500">Tổng số khách hàng</span>
                <span className="font-bold text-gray-800">1,245</span>
              </div>
              <div className="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                <span className="text-gray-500">Khách hàng mới (tháng)</span>
                <span className="font-bold text-green-600">+124</span>
              </div>
              <div className="flex justify-between items-center text-sm">
                <span className="text-gray-500">Khách hàng VIP</span>
                <span className="font-bold text-orange-500">85</span>
              </div>
            </div>
            <button className="w-full mt-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
              Quản lý khách hàng
            </button>
          </div>

          <div className="bg-white rounded-2xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="p-2 bg-teal-50 text-teal-600 rounded-lg">
                <UserCheck className="w-5 h-5" />
              </div>
              <h3 className="text-lg font-bold text-gray-800">Nhân viên</h3>
            </div>
            <div className="space-y-3">
              {[
                { name: 'Trần Thị B', role: 'Thu ngân', sales: '45,000,000 ₫' },
                { name: 'Phạm Văn D', role: 'Bán hàng', sales: '38,500,000 ₫' },
                { name: 'Nguyễn Văn E', role: 'Bán hàng', sales: '32,000,000 ₫' },
              ].map((staff, idx) => (
                <div key={idx} className="flex justify-between items-center text-sm p-2 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                  <div>
                    <span className="font-medium text-gray-800 block">{staff.name}</span>
                    <span className="text-xs text-gray-500">{staff.role}</span>
                  </div>
                  <div className="text-right">
                    <span className="font-medium text-teal-600">{staff.sales}</span>
                  </div>
                </div>
              ))}
            </div>
            <button className="w-full mt-4 py-2 text-sm font-medium text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors">
              Quản lý nhân viên
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
