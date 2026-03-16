import { useState } from 'react';
import { FileText, Download, Calendar, Filter, PieChart as PieChartIcon, BarChart3, TrendingUp } from 'lucide-react';
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip as RechartsTooltip, BarChart, Bar, XAxis, YAxis, CartesianGrid } from 'recharts';

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

const pieData = [
  { name: 'Văn học', value: 400 },
  { name: 'Kinh tế', value: 300 },
  { name: 'Thiếu nhi', value: 300 },
  { name: 'Kỹ năng sống', value: 200 },
  { name: 'Khác', value: 100 },
];

const barData = [
  { name: 'T2', sales: 40 },
  { name: 'T3', sales: 30 },
  { name: 'T4', sales: 20 },
  { name: 'T5', sales: 27 },
  { name: 'T6', sales: 18 },
  { name: 'T7', sales: 23 },
  { name: 'CN', sales: 34 },
];

export function Reports() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Báo cáo thống kê</h2>
          <p className="text-gray-500 text-sm mt-1">Tổng hợp dữ liệu và phân tích hiệu quả kinh doanh</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
            <Calendar className="w-4 h-4" />
            Tháng này
          </button>
          <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 font-medium text-sm">
            <Download className="w-4 h-4" />
            Xuất báo cáo
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {[
          { title: 'Báo cáo doanh thu', desc: 'Chi tiết doanh thu, lợi nhuận, chi phí', icon: TrendingUp, color: 'blue' },
          { title: 'Báo cáo bán hàng', desc: 'Số lượng đơn, sách bán chạy, nhân viên', icon: BarChart3, color: 'green' },
          { title: 'Báo cáo kho', desc: 'Tồn kho, nhập xuất, hàng lỗi', icon: PieChartIcon, color: 'orange' },
        ].map((report, idx) => {
          const Icon = report.icon;
          return (
            <div key={idx} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50 hover:border-blue-100 hover:shadow-md transition-all cursor-pointer group flex items-start gap-4">
              <div className={`p-3 rounded-xl bg-${report.color}-50 text-${report.color}-600 group-hover:bg-${report.color}-100 transition-colors`}>
                <Icon className="w-6 h-6" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{report.title}</h3>
                <p className="text-sm text-gray-500 mt-1">{report.desc}</p>
              </div>
            </div>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Tỷ trọng doanh thu theo danh mục</h3>
            <button className="p-2 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors">
              <Filter className="w-4 h-4" />
            </button>
          </div>
          <div className="h-64 w-full flex items-center justify-center relative">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={pieData}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={80}
                  paddingAngle={5}
                  dataKey="value"
                >
                  {pieData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <RechartsTooltip contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} />
              </PieChart>
            </ResponsiveContainer>
            <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span className="text-2xl font-bold text-gray-800">1.3T</span>
              <span className="text-xs text-gray-500">Tổng doanh thu</span>
            </div>
          </div>
          <div className="mt-4 grid grid-cols-2 gap-2">
            {pieData.map((entry, index) => (
              <div key={index} className="flex items-center gap-2 text-sm">
                <div className="w-3 h-3 rounded-full" style={{ backgroundColor: COLORS[index % COLORS.length] }}></div>
                <span className="text-gray-600">{entry.name}</span>
                <span className="font-medium text-gray-800 ml-auto">{entry.value}M</span>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Số lượng đơn hàng theo ngày</h3>
            <button className="p-2 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors">
              <Filter className="w-4 h-4" />
            </button>
          </div>
          <div className="h-64 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={barData} margin={{ top: 5, right: 5, bottom: 5, left: -20 }}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f0f0f0" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#9ca3af', fontSize: 12 }} dy={10} />
                <YAxis axisLine={false} tickLine={false} tick={{ fill: '#9ca3af', fontSize: 12 }} />
                <RechartsTooltip cursor={{ fill: '#f9fafb' }} contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} />
                <Bar dataKey="sales" fill="#3b82f6" radius={[4, 4, 0, 0]} barSize={30} />
              </BarChart>
            </ResponsiveContainer>
          </div>
          <div className="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
            <div>
              <p className="text-sm text-gray-500">Tổng đơn hàng tuần này</p>
              <p className="text-xl font-bold text-gray-800 mt-1">192 <span className="text-sm font-medium text-green-500">+12%</span></p>
            </div>
            <button className="text-sm font-medium text-blue-600 hover:underline">Xem chi tiết</button>
          </div>
        </div>
      </div>
    </div>
  );
}
