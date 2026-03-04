// ==================== MAIN APPLICATION - MODULAR STRUCTURE ====================

// ==================== GLOBAL DATA ====================
const booksData = [
  { id: '1', name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: 86000, originalPrice: 120000, rating: 4.8, reviews: 1240, image: 'https://picsum.photos/seed/book1/300/400', badge: 'Bán chạy' },
  { id: '2', name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: 79000, originalPrice: 95000, rating: 4.9, reviews: 850, image: 'https://picsum.photos/seed/book2/300/400' },
  { id: '3', name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: 110000, rating: 4.7, reviews: 620, image: 'https://picsum.photos/seed/book3/300/400', badge: 'Mới' },
  { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: 80000, rating: 4.6, reviews: 2100, image: 'https://picsum.photos/seed/book4/300/400' },
  { id: '5', name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: 250000, originalPrice: 300000, rating: 4.9, reviews: 3200, image: 'https://picsum.photos/seed/book5/300/400', badge: '-15%' },
  { id: '6', name: 'Cây Cam Ngọt Của Tôi', author: 'José Mauro de Vasconcelos', category: 'Văn học', price: 95000, rating: 4.8, reviews: 1500, image: 'https://picsum.photos/seed/book6/300/400' },
  { id: '7', name: 'Tâm Lý Học Tội Phạm', author: 'Tôn Thất', category: 'Tâm lý học', price: 135000, originalPrice: 150000, rating: 4.5, reviews: 430, image: 'https://picsum.photos/seed/book7/300/400' },
  { id: '8', name: 'Muôn Kiếp Nhân Sinh', author: 'Nguyên Phong', category: 'Tôn giáo - Tâm linh', price: 168000, rating: 4.7, reviews: 980, image: 'https://picsum.photos/seed/book8/300/400' },
];

const newReleasesData = [
  { id: '9', name: 'Dune - Xứ Cát', author: 'Frank Herbert', category: 'Khoa học viễn tưởng', price: 210000, originalPrice: 250000, rating: 4.9, reviews: 150, image: 'https://picsum.photos/seed/book9/300/400', badge: 'Mới' },
  { id: '10', name: 'Atomic Habits', author: 'James Clear', category: 'Kỹ năng sống', price: 145000, rating: 4.8, reviews: 5200, image: 'https://picsum.photos/seed/book10/300/400' },
  { id: '11', name: 'Kẻ Trộm Sách', author: 'Markus Zusak', category: 'Văn học', price: 125000, originalPrice: 140000, rating: 4.7, reviews: 890, image: 'https://picsum.photos/seed/book11/300/400' },
  { id: '12', name: 'Sức Mạnh Của Thói Quen', author: 'Charles Duhigg', category: 'Tâm lý học', price: 115000, rating: 4.6, reviews: 1200, image: 'https://picsum.photos/seed/book12/300/400' },
  { id: '13', name: 'Tư Duy Nhanh Và Chậm', author: 'Daniel Kahneman', category: 'Kinh tế', price: 185000, originalPrice: 210000, rating: 4.8, reviews: 3400, image: 'https://picsum.photos/seed/book13/300/400', badge: 'Bán chạy' },
];

// All books combined
window.booksData = [...booksData, ...newReleasesData];

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
function initHeroCarousel() {
  const heroCarousel = new HeroCarousel('hero-slider');
}

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
  const flashSaleContainer = document.getElementById('flash-sale-books');
  const featuredContainer = document.getElementById('featured-books');
  const newReleasesContainer = document.getElementById('new-releases');

  // Update bookCard instance callbacks
  bookCard.onAddToCart = (book) => {
    cartDrawer.addItem(book, 1);
    toast.success(`Đã thêm "${book.name}" vào giỏ hàng`);
  };
  
  bookCard.onQuickView = (book) => {
    bookDetailsModal.open(book);
  };
  
  bookCard.onWishlist = (ids) => {
    wishlists = new Set(ids);
  };

  // Initialize bookCard with wishlists
  bookCard.wishlists = wishlists;

  // Flash Sale - first 5 books with 30% discount
  bookCard.renderAll(booksData.slice(0, 5), 'flash-sale-books', { isFlashSale: true });
  
  // Featured Books
  bookCard.renderAll(booksData, 'featured-books', {});
  
  // New Releases
  bookCard.renderAll(newReleasesData, 'new-releases', {});
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

