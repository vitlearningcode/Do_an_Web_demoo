import { LogOut, X } from 'lucide-react';

interface LogoutModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
}

export function LogoutModal({ isOpen, onClose, onConfirm }: LogoutModalProps) {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden relative animate-in zoom-in-95 duration-200">
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-2 text-gray-400 hover:bg-gray-100 rounded-full transition-colors z-10"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="p-8 flex flex-col items-center text-center">
          <div className="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-6">
            <LogOut className="w-8 h-8 ml-1" />
          </div>
          
          <h3 className="text-xl font-bold text-gray-800 mb-2">Đăng xuất hệ thống?</h3>
          <p className="text-sm text-gray-500 mb-8">
            Bạn có chắc chắn muốn đăng xuất khỏi phiên làm việc hiện tại? Bạn sẽ cần đăng nhập lại để truy cập Admin Dashboard.
          </p>

          <div className="flex w-full gap-3">
            <button 
              onClick={onClose}
              className="flex-1 py-2.5 px-4 bg-gray-50 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors border border-gray-200"
            >
              Hủy bỏ
            </button>
            <button 
              onClick={onConfirm}
              className="flex-1 py-2.5 px-4 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-red-200"
            >
              Đăng xuất
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
