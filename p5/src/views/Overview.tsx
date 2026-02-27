import { TrendingUp, Users, ShoppingBag, AlertCircle, ArrowUpRight, ArrowDownRight, Package, DollarSign } from 'lucide-react';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

const data = [
  { name: 'T2', revenue: 4000 },
  { name: 'T3', revenue: 3000 },
  { name: 'T4', revenue: 2000 },
  { name: 'T5', revenue: 2780 },
  { name: 'T6', revenue: 1890 },
  { name: 'T7', revenue: 2390 },
  { name: 'CN', revenue: 3490 },
];

export function Overview() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Tổng quan hệ thống</h2>
          <p className="text-gray-500 text-sm mt-1">Theo dõi các chỉ số quan trọng trong ngày</p>
        </div>
        <div className="flex gap-3">
          <select className="border border-gray-200 rounded-xl px-4 py-2 text-sm font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-sm hover:bg-gray-50 transition-colors">
            <option>Hôm nay</option>
            <option>7 ngày qua</option>
            <option>Tháng này</option>
          </select>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        {[
          { title: 'Doanh thu', value: '12,450,000 ₫', change: '+15.2%', isUp: true, icon: DollarSign, color: 'blue' },
          { title: 'Đơn hàng mới', value: '142', change: '+5.4%', isUp: true, icon: ShoppingBag, color: 'emerald' },
          { title: 'Khách hàng', value: '1,245', change: '-1.2%', isUp: false, icon: Users, color: 'purple' },
          { title: 'Cảnh báo tồn kho', value: '24', change: '+12', isUp: false, icon: AlertCircle, color: 'orange' },
        ].map((stat, idx) => {
          const Icon = stat.icon;
          return (
            <div key={idx} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
              <div className={`absolute top-0 right-0 w-32 h-32 bg-${stat.color}-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500`}></div>
              <div className="relative z-10">
                <div className="flex justify-between items-start mb-4">
                  <div className={`p-3 rounded-xl bg-${stat.color}-100 text-${stat.color}-600`}>
                    <Icon className="w-6 h-6" />
                  </div>
                  <span className={`flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full ${stat.isUp ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-red-700 bg-red-50 border border-red-100'}`}>
                    {stat.isUp ? <ArrowUpRight className="w-3 h-3" /> : <ArrowDownRight className="w-3 h-3" />}
                    {stat.change}
                  </span>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500 mb-1">{stat.title}</p>
                  <h3 className="text-2xl font-bold text-gray-800 tracking-tight">{stat.value}</h3>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Chart */}
        <div className="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div className="flex justify-between items-center mb-6">
            <div>
              <h3 className="text-lg font-bold text-gray-800">Biểu đồ doanh thu</h3>
              <p className="text-sm text-gray-500 mt-1">Doanh thu 7 ngày gần nhất</p>
            </div>
            <button className="text-sm font-medium text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
              Xem chi tiết
            </button>
          </div>
          <div className="h-72 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={data} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <defs>
                  <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#3b82f6" stopOpacity={0.3}/>
                    <stop offset="95%" stopColor="#3b82f6" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} dy={10} />
                <YAxis axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                <Tooltip 
                  contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)' }}
                  cursor={{ stroke: '#cbd5e1', strokeWidth: 1, strokeDasharray: '4 4' }}
                />
                <Area type="monotone" dataKey="revenue" stroke="#3b82f6" strokeWidth={3} fillOpacity={1} fill="url(#colorRevenue)" activeDot={{ r: 6, strokeWidth: 0, fill: '#3b82f6' }} />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Recent Activity / Low Stock */}
        <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
          <div className="flex justify-between items-center mb-6">
            <h3 className="text-lg font-bold text-gray-800">Sắp hết hàng</h3>
            <button className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
              <Package className="w-5 h-5" />
            </button>
          </div>
          
          <div className="flex-1 overflow-y-auto pr-2 space-y-4">
            {[
              { name: 'Nghĩ Giàu Làm Giàu', stock: 8, min: 10, category: 'Kinh tế' },
              { name: 'Nhà Giả Kim', stock: 12, min: 20, category: 'Văn học' },
              { name: 'Tâm Lý Học Tội Phạm', stock: 5, min: 15, category: 'Tâm lý học' },
              { name: 'Sapiens: Lược Sử Loài Người', stock: 15, min: 20, category: 'Khoa học' },
              { name: 'Muôn Kiếp Nhân Sinh', stock: 9, min: 15, category: 'Tôn giáo' },
            ].map((item, idx) => (
              <div key={idx} className="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-orange-200 hover:bg-orange-50/50 transition-colors group cursor-pointer">
                <div className="flex items-center gap-3">
                  <div className={`w-2 h-2 rounded-full ${item.stock <= 5 ? 'bg-red-500 animate-pulse' : 'bg-orange-500'}`}></div>
                  <div>
                    <h4 className="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-orange-700 transition-colors">{item.name}</h4>
                    <p className="text-xs text-gray-500 mt-0.5">{item.category}</p>
                  </div>
                </div>
                <div className="text-right">
                  <span className={`text-sm font-bold ${item.stock <= 5 ? 'text-red-600' : 'text-orange-600'}`}>{item.stock}</span>
                  <span className="text-xs text-gray-400 block">/ {item.min}</span>
                </div>
              </div>
            ))}
          </div>
          
          <button className="w-full mt-4 py-2.5 text-sm font-bold text-orange-600 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors border border-orange-100">
            Tạo đơn nhập hàng ngay
          </button>
        </div>
      </div>
    </div>
  );
}
