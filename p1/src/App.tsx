import { useState } from 'react';
import { Sidebar } from './components/Sidebar';
import { ImportManagement } from './views/ImportManagement';
import { BookInfoManagement } from './views/BookInfoManagement';
import { RevenueManagement } from './views/RevenueManagement';
import { SalesManagement } from './views/SalesManagement';
import { InventoryManagement } from './views/InventoryManagement';
import { Reports } from './views/Reports';
import { Chatbot } from './components/Chatbot';

export type ViewType = 
  | 'import' 
  | 'book-info' 
  | 'revenue' 
  | 'sales' 
  | 'inventory' 
  | 'reports';

export default function App() {
  const [currentView, setCurrentView] = useState<ViewType>('reports');

  const renderView = () => {
    switch (currentView) {
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
      <main className="flex-1 overflow-y-auto p-6">
        {renderView()}
      </main>
      <Chatbot />
    </div>
  );
}
