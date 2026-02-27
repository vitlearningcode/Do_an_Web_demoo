import { useState } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, BarChart, Bar, Legend } from 'recharts';
import { DollarSign, TrendingUp, TrendingDown, CreditCard, Percent } from 'lucide-react';

const data = [
  { name: 'T2', revenue: 4000, profit: 2400, amt: 2400 },
  { name: 'T3', revenue: 3000, profit: 1398, amt: 2210 },
  { name: 'T4', revenue: 2000, profit: 9800, amt: 2290 },
  { name: 'T5', revenue: 2780, profit: 3908, amt: 2000 },
  { name: 'T6', revenue: 1890, profit: 4800, amt: 2181 },
  { name: 'T7', revenue: 2390, profit: 3800, amt: 2500 },
  { name: 'CN', revenue: 3490, profit: 4300, amt: 2100 },
];

export function RevenueManagement() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Quản lý doanh thu</h2>
          <p className="text-gray-500 text-sm mt-1">Theo dõi biểu đồ, chiết khấu và nợ công</p>
        </div>
        <div className="flex gap-3">
          <select className="border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-sm">
            <option>Hôm nay</option>
            <option>Tuần này</option>
            <option>Tháng này</option>
            <option>Năm nay</option>
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {[
          { title: 'Tổng Doanh Thu', value: '124,500,000 ₫', change: '+12.5%', isUp: true, icon: DollarSign, color: 'blue' },
          { title: 'Lợi Nhuận', value: '45,200,000 ₫', change: '+8.2%', isUp: true, icon: TrendingUp, color: 'green' },
          { title: 'Nợ Công', value: '12,000,000 ₫', change: '-2.4%', isUp: false, icon: CreditCard, color: 'red' },
          { title: 'Chiết Khấu Đã Cấp', value: '5,400,000 ₫', change: '+1.5%', isUp: true, icon: Percent, color: 'orange' },
        ].map((stat, idx) => {
          const Icon = stat.icon;
          return (
            <div key={idx} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50 flex flex-col justify-between">
              <div className="flex justify-between items-start mb-4">
                <div className={`p-3 rounded-xl bg-${stat.color}-50 text-${stat.color}-600`}>
                  <Icon className="w-6 h-6" />
                </div>
                <span className={`flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full ${stat.isUp ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50'}`}>
                  {stat.isUp ? <TrendingUp className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
                  {stat.change}
                </span>
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500 mb-1">{stat.title}</p>
                <h3 className="text-2xl font-bold text-gray-800">{stat.value}</h3>
              </div>
            </div>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Biểu đồ doanh thu & lợi nhuận</h3>
            <div className="flex gap-2">
              <div className="flex items-center gap-2 text-sm text-gray-500">
                <div className="w-3 h-3 rounded-full bg-blue-500"></div> Doanh thu
              </div>
              <div className="flex items-center gap-2 text-sm text-gray-500 ml-4">
                <div className="w-3 h-3 rounded-full bg-green-500"></div> Lợi nhuận
              </div>
            </div>
          </div>
          <div className="h-80 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={data} margin={{ top: 5, right: 20, bottom: 5, left: 0 }}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f0f0f0" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#9ca3af', fontSize: 12 }} dy={10} />
                <YAxis axisLine={false} tickLine={false} tick={{ fill: '#9ca3af', fontSize: 12 }} dx={-10} />
                <Tooltip 
                  contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)' }}
                  cursor={{ stroke: '#f3f4f6', strokeWidth: 2 }}
                />
                <Line type="monotone" dataKey="revenue" stroke="#3b82f6" strokeWidth={3} dot={{ r: 4, strokeWidth: 2 }} activeDot={{ r: 6, strokeWidth: 0 }} />
                <Line type="monotone" dataKey="profit" stroke="#10b981" strokeWidth={3} dot={{ r: 4, strokeWidth: 2 }} activeDot={{ r: 6, strokeWidth: 0 }} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-50">
          <h3 className="text-lg font-bold text-gray-800 mb-6">Chiết khấu theo danh mục</h3>
          <div className="h-64 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={[
                { name: 'Văn học', value: 4000 },
                { name: 'Kinh tế', value: 3000 },
                { name: 'Thiếu nhi', value: 2000 },
                { name: 'Kỹ năng', value: 2780 },
              ]} layout="vertical" margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f0f0f0" />
                <XAxis type="number" hide />
                <YAxis dataKey="name" type="category" axisLine={false} tickLine={false} tick={{ fill: '#4b5563', fontSize: 12 }} width={80} />
                <Tooltip cursor={{ fill: '#f9fafb' }} contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} />
                <Bar dataKey="value" fill="#f97316" radius={[0, 4, 4, 0]} barSize={20} />
              </BarChart>
            </ResponsiveContainer>
          </div>
          
          <div className="mt-6 pt-6 border-t border-gray-100">
            <h4 className="text-sm font-semibold text-gray-700 mb-3">Nợ công cần thu</h4>
            <div className="space-y-3">
              {[
                { name: 'Đại lý A', amount: '5,000,000 ₫', status: 'Quá hạn' },
                { name: 'Nhà sách B', amount: '3,200,000 ₫', status: 'Sắp đến hạn' },
                { name: 'Công ty C', amount: '1,500,000 ₫', status: 'Trong hạn' },
              ].map((debt, idx) => (
                <div key={idx} className="flex justify-between items-center text-sm">
                  <span className="text-gray-600">{debt.name}</span>
                  <div className="text-right">
                    <span className="font-medium text-gray-800 block">{debt.amount}</span>
                    <span className={`text-[10px] uppercase font-bold tracking-wider ${
                      debt.status === 'Quá hạn' ? 'text-red-500' : 
                      debt.status === 'Sắp đến hạn' ? 'text-orange-500' : 'text-green-500'
                    }`}>{debt.status}</span>
                  </div>
                </div>
              ))}
            </div>
            <button className="w-full mt-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
              Xem chi tiết nợ công
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
