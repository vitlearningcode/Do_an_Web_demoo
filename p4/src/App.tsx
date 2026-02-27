import { useState } from 'react';
import { AdminDashboard } from './AdminDashboard';
import { Storefront } from './Storefront';

export default function App() {
  const [role, setRole] = useState<'admin' | 'customer'>('customer');

  return (
    <>
      {/* Role Toggle for Demo Purposes */}
      <div className="fixed top-4 right-4 z-50 bg-white rounded-full shadow-lg border border-gray-200 p-1 flex">
        <button 
          onClick={() => setRole('customer')}
          className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${role === 'customer' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'}`}
        >
          Cửa hàng
        </button>
        <button 
          onClick={() => setRole('admin')}
          className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${role === 'admin' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'}`}
        >
          Admin
        </button>
      </div>

      {role === 'admin' ? <AdminDashboard /> : <Storefront />}
    </>
  );
}
