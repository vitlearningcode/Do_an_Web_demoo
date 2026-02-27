import { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Sidebar } from './components/Sidebar';
import { AdminHeader } from './components/AdminHeader';
import { Overview } from './views/Overview';
import { ImportManagement } from './views/ImportManagement';
import { BookInfoManagement } from './views/BookInfoManagement';
import { RevenueManagement } from './views/RevenueManagement';
import { SalesManagement } from './views/SalesManagement';
import { InventoryManagement } from './views/InventoryManagement';
import { Reports } from './views/Reports';
import { Chatbot } from './components/Chatbot';
import { ViewType } from './types';

export function AdminDashboard() {
  const [currentView, setCurrentView] = useState<ViewType>('overview');

  const renderView = () => {
    switch (currentView) {
      case 'overview': return <Overview />;
      case 'import': return <ImportManagement />;
      case 'book-info': return <BookInfoManagement />;
      case 'revenue': return <RevenueManagement />;
      case 'sales': return <SalesManagement />;
      case 'inventory': return <InventoryManagement />;
      case 'reports': return <Reports />;
      default: return <Reports />;
    }
  };

  return (
    <div className="flex h-screen bg-[#E5E7EB] font-sans text-gray-800 overflow-hidden">
      <Sidebar currentView={currentView} setCurrentView={setCurrentView} />
      
      <div className="flex-1 flex flex-col h-screen overflow-hidden relative">
        <AdminHeader />
        
        <main className="flex-1 overflow-y-auto p-6">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentView}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.2 }}
              className="h-full"
            >
              <div className="max-w-7xl mx-auto h-full">
                {renderView()}
              </div>
            </motion.div>
          </AnimatePresence>
        </main>
      </div>
      
      <Chatbot />
    </div>
  );
}
