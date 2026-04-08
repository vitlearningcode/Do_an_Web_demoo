// ==================== TOAST COMPONENT ====================

class Toast {
  constructor() {
    this.toastElement = null;
    this.messageElement = null;
    this.iconElement = null;
    this.currentTimeout = null;
    this.init();
  }

  init() {
    // Create toast element if not exists
    if (!document.getElementById('toast')) {
      this.createToastElement();
    }
    this.toastElement = document.getElementById('toast');
    this.messageElement = document.getElementById('toast-message');
    this.iconElement = this.toastElement.querySelector('i');
  }

  createToastElement() {
    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = 'toast';
    toast.innerHTML = `
      <i class="fas fa-check-circle"></i>
      <span id="toast-message"></span>
    `;
    document.body.appendChild(toast);
  }

  show(message, type = 'success', duration = 3000) {
    if (!this.toastElement) this.init();
    
    this.messageElement.textContent = message;
    
    // Update icon based on type
    this.iconElement.className = 'fas';
    switch (type) {
      case 'success':
        this.iconElement.classList.add('fa-check-circle');
        this.iconElement.style.color = '#10b981';
        break;
      case 'error':
        this.iconElement.classList.add('fa-exclamation-circle');
        this.iconElement.style.color = '#ef4444';
        break;
      case 'warning':
        this.iconElement.classList.add('fa-exclamation-triangle');
        this.iconElement.style.color = '#f59e0b';
        break;
      case 'info':
        this.iconElement.classList.add('fa-info-circle');
        this.iconElement.style.color = '#3b82f6';
        break;
      default:
        this.iconElement.classList.add('fa-check-circle');
        this.iconElement.style.color = '#10b981';
    }
    
    this.toastElement.classList.add('show');
    
    // Clear previous timeout
    if (this.currentTimeout) {
      clearTimeout(this.currentTimeout);
    }
    
    this.currentTimeout = setTimeout(() => {
      this.hide();
    }, duration);
  }

  hide() {
    if (this.toastElement) {
      this.toastElement.classList.remove('show');
    }
  }

  success(message, duration = 3000) {
    this.show(message, 'success', duration);
  }

  error(message, duration = 3000) {
    this.show(message, 'error', duration);
  }

  warning(message, duration = 3000) {
    this.show(message, 'warning', duration);
  }

  info(message, duration = 3000) {
    this.show(message, 'info', duration);
  }
}

// Create global instance
const toast = new Toast();

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { toast, Toast };
}

