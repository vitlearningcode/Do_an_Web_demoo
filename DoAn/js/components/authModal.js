// ==================== AUTH MODAL COMPONENT ====================

class AuthModal {
  constructor(options = {}) {
    this.isOpen = false;
    this.mode = options.mode || 'login'; // 'login' or 'register'
    this.onLogin = options.onLogin || (() => {});
    this.onRegister = options.onRegister || (() => {});
    
    this.elements = {
      modal: null,
      form: null,
      title: null,
      switchText: null,
      switchLink: null,
      submitBtn: null
    };
    
    this.init();
  }

  init() {
    this.elements.modal = document.getElementById('auth-modal');
    this.elements.form = document.getElementById('auth-form');
    this.elements.title = this.elements.modal?.querySelector('h2');
    this.elements.switchText = document.querySelector('.modal-footer-text');
    this.elements.submitBtn = this.elements.form?.querySelector('button[type="submit"]');
    
    this.bindEvents();
  }

  bindEvents() {
    const closeBtn = document.getElementById('auth-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
    
    if (this.elements.modal) {
      this.elements.modal.addEventListener('click', (e) => {
        if (e.target === this.elements.modal) this.close();
      });
    }

    // Form submission
    if (this.elements.form) {
      this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    // Switch between login/register
    const switchLink = this.elements.switchText?.querySelector('a');
    if (switchLink) {
      switchLink.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleMode();
      });
    }

    // ESC key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.close();
      }
    });
  }

  open(mode = 'login') {
    this.mode = mode;
    this.updateUI();
    this.isOpen = true;
    this.elements.modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus first input
    setTimeout(() => {
      const firstInput = this.elements.form?.querySelector('input');
      firstInput?.focus();
    }, 100);
  }

  close() {
    this.isOpen = false;
    this.elements.modal?.classList.remove('active');
    document.body.style.overflow = '';
    this.elements.form?.reset();
  }

  toggleMode() {
    this.mode = this.mode === 'login' ? 'register' : 'login';
    this.updateUI();
  }

  updateUI() {
    if (this.elements.title) {
      this.elements.title.textContent = this.mode === 'login' ? 'Đăng nhập' : 'Đăng ký';
    }
    
    if (this.elements.submitBtn) {
      this.elements.submitBtn.textContent = this.mode === 'login' ? 'Đăng nhập' : 'Đăng ký';
    }
    
    if (this.elements.switchText) {
      if (this.mode === 'login') {
        this.elements.switchText.innerHTML = 'Chưa có tài khoản? <a href="#" onclick="authModal.toggleMode(); return false;">Đăng ký ngay</a>';
      } else {
        this.elements.switchText.innerHTML = 'Đã có tài khoản? <a href="#" onclick="authModal.toggleMode(); return false;">Đăng nhập ngay</a>';
      }
    }
  }

  handleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this.elements.form);
    const data = {
      email: formData.get('email'),
      password: formData.get('password'),
      name: this.mode === 'register' ? formData.get('name') : null
    };
    
    if (this.mode === 'login') {
      this.onLogin(data);
      toast.success('Đăng nhập thành công!');
    } else {
      this.onRegister(data);
      toast.success('Đăng ký thành công!');
    }
    
    this.close();
  }
}

// Create global instance
const authModal = new AuthModal({
  onLogin: (data) => {
    console.log('Login:', data);
    // Handle login logic here
  },
  onRegister: (data) => {
    console.log('Register:', data);
    // Handle register logic here
  }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AuthModal, authModal };
}

