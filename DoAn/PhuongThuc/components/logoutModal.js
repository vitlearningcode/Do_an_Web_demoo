// ==================== LOGOUT MODAL COMPONENT ====================

class LogoutModal {
  constructor(options = {}) {
    this.isOpen = false;
    this.onConfirm = options.onConfirm || (() => {});
    this.onCancel = options.onCancel || (() => {});
    
    this.elements = {
      modal: null,
      confirmBtn: null,
      cancelBtn: null
    };
    
    this.init();
  }

  init() {
    // Create modal HTML if not exists
    if (!document.getElementById('logout-modal')) {
      this.createModalHTML();
    }
    
    this.elements.modal = document.getElementById('logout-modal');
    this.elements.confirmBtn = document.getElementById('logout-confirm');
    this.elements.cancelBtn = document.getElementById('logout-cancel');
    
    this.bindEvents();
  }

  createModalHTML() {
    const modalHTML = `
      <div class="modal-overlay" id="logout-modal">
        <div class="modal logout-modal">
          <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
          </div>
          <h2>Đăng xuất</h2>
          <p>Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?</p>
          <div class="logout-actions">
            <button class="cancel-btn" id="logout-cancel">Hủy</button>
            <button class="confirm-btn" id="logout-confirm">Đăng xuất</button>
          </div>
        </div>
      </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add CSS for this modal
    const style = document.createElement('style');
    style.textContent = `
      .logout-modal {
        max-width: 400px;
        text-align: center;
      }
      .logout-icon {
        width: 60px;
        height: 60px;
        background: #fee2e2;
        color: #ef4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 16px;
      }
      .logout-modal h2 {
        margin-bottom: 8px;
      }
      .logout-modal p {
        color: var(--gray-500);
        margin-bottom: 24px;
      }
      .logout-actions {
        display: flex;
        gap: 12px;
      }
      .logout-actions button {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
      }
      .logout-actions .cancel-btn {
        background: var(--gray-100);
        color: var(--gray-700);
        border: none;
      }
      .logout-actions .cancel-btn:hover {
        background: var(--gray-200);
      }
      .logout-actions .confirm-btn {
        background: #ef4444;
        color: white;
        border: none;
      }
      .logout-actions .confirm-btn:hover {
        background: #dc2626;
      }
    `;
    document.head.appendChild(style);
  }

  bindEvents() {
    if (this.elements.confirmBtn) {
      this.elements.confirmBtn.addEventListener('click', () => this.handleConfirm());
    }
    
    if (this.elements.cancelBtn) {
      this.elements.cancelBtn.addEventListener('click', () => this.handleCancel());
    }
    
    if (this.elements.modal) {
      this.elements.modal.addEventListener('click', (e) => {
        if (e.target === this.elements.modal) this.handleCancel();
      });
    }
    
    // ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.handleCancel();
      }
    });
  }

  open() {
    this.isOpen = true;
    this.elements.modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.isOpen = false;
    this.elements.modal?.classList.remove('active');
    document.body.style.overflow = '';
  }

  handleConfirm() {
    this.close();
    this.onConfirm();
    toast.success('Đăng xuất thành công');
  }

  handleCancel() {
    this.close();
    this.onCancel();
  }
}

// Create global instance
const logoutModal = new LogoutModal({
  onConfirm: () => {
    // Handle logout logic here
    window.dispatchEvent(new CustomEvent('user-logout'));
  }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LogoutModal, logoutModal };
}

