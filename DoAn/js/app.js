// ==================== MAIN APPLICATION - MODULAR STRUCTURE ====================

// ==================== GLOBAL DATA ====================
// Data now in js/data.js - remove duplicates

// ==================== STATE ====================
let currentRole = 'customer';
let currentAdminView = 'overview';
let wishlists = new Set();

// ==================== ADMIN VIEWS REGISTRY ====================
const adminViews = {
  overview: overviewView,
  import: importManagementView,
  'book-info': bookInfoManagementView,
  revenue: revenueManagementView,
  sales: salesManagementView,
  inventory: inventoryManagementView,
  reports: reportsView,
  settings: settingsView,
  contact: contactView
};

// View titles mapping
const viewTitles = {
  overview: 'Tổng quan',
  import: 'Quản lý nhập sách',
  'book-info': 'Quản lý thông tin sách',
  revenue: 'Quản lý doanh thu',
  sales: 'Quản lý bán hàng',
  inventory: 'Quản lý kho',
  reports: 'Báo cáo',
  settings: 'Cài đặt hệ thống',
  contact: 'Hỗ trợ & Liên hệ'
};

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
  // Initialize components
  initRoleToggle();
  initHeroCarousel();
  initFlashSaleTimer();
  renderBooks();
  initCartButton();
  initAuthButton();
  initAdminSidebar();
  initChatbot();
  loadTheLoaiFilter();
  
  console.log('✅ Application initialized successfully');
});

// ==================== ROLE TOGGLE ====================
function initRoleToggle() {
  const btnCustomer = document.getElementById('btn-customer');
  const btnAdmin = document.getElementById('btn-admin');
  const storefrontView = document.getElementById('storefront-view');
  const adminView = document.getElementById('admin-view');

  btnCustomer.addEventListener('click', () => {
    currentRole = 'customer';
    btnCustomer.classList.add('active');
    btnAdmin.classList.remove('active');
    storefrontView.style.display = 'block';
    adminView.style.display = 'none';
  });

  btnAdmin.addEventListener('click', () => {
    currentRole = 'admin';
    btnAdmin.classList.add('active');
    btnCustomer.classList.remove('active');
    storefrontView.style.display = 'none';
    adminView.style.display = 'flex';
    loadAdminView(currentAdminView);
  });
}

// ==================== HERO CAROUSEL ====================
// function initHeroCarousel() {
//   window.initHeroCarousel();
// }

// ==================== FLASH SALE TIMER ====================
function initFlashSaleTimer() {
  let hours = 12;
  let minutes = 45;
  let seconds = 30;

  setInterval(() => {
    seconds--;
    if (seconds < 0) { seconds = 59; minutes--; }
    if (minutes < 0) { minutes = 59; hours--; }
    if (hours < 0) { hours = 24; }
    
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    
    if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
    if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
    if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
  }, 1000);
}

// ==================== BOOKS RENDERING ====================
function renderBooks() {
  // Update bookCard callbacks FIRST (before renderAll)
  bookCard.onAddToCart = (book) => {
    cartDrawer.addItem(book, 1);
    toast.success(`Đã thêm "${book.name}" vào giỏ hàng`);
  };
  
  bookCard.onQuickView = (book) => {
    // TODO: bookDetailsModal.open(book) if implemented
    console.log('Quick view:', book.name);
  };
  
  bookCard.onWishlist = (ids) => {
    wishlists = new Set(ids);
  };

  bookCard.wishlists = wishlists;

  const flashBooks = window.featuredBooks.slice(0, 5).map(book => ({...book, badge: 'Flash Sale', originalPrice: book.price * 1.5 }));
  const featuredBooks = window.featuredBooks;
  const newReleasesBooks = window.newReleases;

  bookCard.renderAll(flashBooks, 'flash-sale-books', { isFlashSale: true });
  bookCard.renderAll(featuredBooks, 'featured-books', {});
  bookCard.renderAll(newReleasesBooks, 'new-releases', {});
  
  console.log('✅ Books rendered:', flashBooks.length, 'flash,', featuredBooks.length, 'featured,', newReleasesBooks.length, 'new');
}

// ==================== CART ====================
function initCartButton() {
  const cartBtn = document.getElementById('btn-cart');
  if (cartBtn) {
    cartBtn.addEventListener('click', () => {
      cartDrawer.toggle();
    });
  }
}

// ==================== AUTH ====================
function initAuthButton() {
  const authBtn = document.getElementById('btn-login');
  if (authBtn) {
    authBtn.addEventListener('click', () => {
      authModal.open('login');
    });
  }
}

// ==================== ADMIN SIDEBAR ====================
function initAdminSidebar() {
  document.querySelectorAll('.nav-item[data-view]').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      loadAdminView(item.dataset.view);
    });
  });

  const logoutBtn = document.getElementById('btn-logout');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', (e) => {
      e.preventDefault();
      logoutModal.open();
    });
  }

  // Handle logout
  logoutModal.onConfirm = () => {
    document.getElementById('btn-customer').click();
  };
}

function loadAdminView(view) {
  currentAdminView = view;
  
  // Update title
  const viewTitle = document.getElementById('admin-view-title');
  if (viewTitle) {
    viewTitle.textContent = viewTitles[view] || 'Tổng quan';
  }
  
  // Render view content
  const adminContent = document.getElementById('admin-content');
  if (adminContent && adminViews[view]) {
    adminContent.innerHTML = adminViews[view].render();
    
    // Initialize chart if available
    if (adminViews[view].initChart) {
      adminViews[view].initChart();
    }
  }
  
  // Update active nav item
  document.querySelectorAll('.nav-item').forEach(item => {
    if (item.dataset.view) {
      item.classList.toggle('active', item.dataset.view === view);
    }
  });
}

// ==================== CHATBOT ====================
function initChatbot() {
  // Chatbot is already initialized in chatbot.js
}

// ==================== EXPORTS ====================
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { booksData, newReleasesData };
}

