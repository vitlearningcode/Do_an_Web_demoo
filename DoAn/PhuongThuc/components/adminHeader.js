// ==================== ADMIN HEADER COMPONENT ====================

class AdminHeader {
  constructor(options = {}) {
    this.title = options.title || 'Tổng quan';
    this.user = options.user || { name: 'Admin User', role: 'Quản lý cửa hàng' };
    this.onSearch = options.onSearch || (() => {});
    this.onLogout = options.onLogout || (() => {});
    
    this.elements = {
      title: null,
      searchInput: null,
      userName: null,
      userRole: null
    };
    
    this.init();
  }

  init() {
    this.elements.title = document.getElementById('admin-view-title');
    this.elements.searchInput = document.querySelector('.admin-search input');
    this.elements.userName = document.querySelector('.admin-user-name');
    this.elements.userRole = document.querySelector('.admin-user-role');
    
    this.bindEvents();
    this.updateUserInfo();
  }

  bindEvents() {
    // Search functionality
    if (this.elements.searchInput) {
      this.elements.searchInput.addEventListener('input', debounce((e) => {
        this.onSearch(e.target.value);
      }, 300));
      
      // Keyboard shortcut for search
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          this.elements.searchInput?.focus();
        }
      });
    }
    
    // Notification button
    const notificationBtn = document.querySelector('.admin-notification-btn');
    if (notificationBtn) {
      notificationBtn.addEventListener('click', () => {
        toast.info('Bạn có 3 thông báo mới');
      });
    }
  }

  setTitle(title) {
    this.title = title;
    if (this.elements.title) {
      this.elements.title.textContent = title;
    }
  }

  updateUserInfo(user) {
    if (user) {
      this.user = user;
    }
    
    if (this.elements.userName) {
      this.elements.userName.textContent = this.user.name;
    }
    if (this.elements.userRole) {
      this.elements.userRole.textContent = this.user.role;
    }
  }

  setViewTitle(title) {
    this.setTitle(title);
  }
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AdminHeader };
}

