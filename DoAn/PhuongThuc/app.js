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
// function renderBooks() {
//   // Update bookCard callbacks FIRST (before renderAll)
//   bookCard.onAddToCart = (book) => {
//     cartDrawer.addItem(book, 1);
//     toast.success(`Đã thêm "${book.name}" vào giỏ hàng`);
//   };
  
//   bookCard.onQuickView = (book) => {
//     // TODO: bookDetailsModal.open(book) if implemented
//     console.log('Quick view:', book.name);
//   };
  
//   bookCard.onWishlist = (ids) => {
//     wishlists = new Set(ids);
//   };

//   bookCard.wishlists = wishlists;

//   const flashBooks = window.featuredBooks.slice(0, 5).map(book => ({...book, badge: 'Flash Sale', originalPrice: book.price * 1.5 }));
//   const featuredBooks = window.featuredBooks;
//   const newReleasesBooks = window.newReleases;

//   bookCard.renderAll(flashBooks, 'flash-sale-books', { isFlashSale: true });
//   bookCard.renderAll(featuredBooks, 'featured-books', {});
//   bookCard.renderAll(newReleasesBooks, 'new-releases', {});
  
//   console.log('✅ Books rendered:', flashBooks.length, 'flash,', featuredBooks.length, 'featured,', newReleasesBooks.length, 'new');
// }

// ==================== BOOKS RENDERING (TỪ DATABASE) ====================
async function renderBooks() {
  try {
    // 1. Gọi API để lấy danh sách sách
    const response = await fetch('api/get_sach_filter.php');
    const result = await response.json();

    console.log("Dữ liệu từ Database:", result);

    if (result.status === 200 && result.data.length > 0) {
      const booksFromDB = result.data;

      // 2. Chuyển đổi dữ liệu chuẩn xác theo SQL Schema của bạn
      const mappedBooks = booksFromDB.map(sach => ({
        id: sach.maSach,              // Từ bảng Sach
        name: sach.tenSach,           // Từ bảng Sach
        price: parseFloat(sach.giaBan), // Từ bảng Sach
        originalPrice: parseFloat(sach.giaBan) * 1.2, // Giả lập giá gốc cao hơn 20% cho đẹp giao diện
        // Lưu ý: urlAnh và tenTG cần được Backend JOIN từ bảng HinhAnhSach và TacGia
        image: sach.urlAnh || 'https://picsum.photos/seed/book1/200/300', 
        author: sach.tenTG || 'Đang cập nhật', 
        badge: 'Sách Hot'
      }));

      // 3. Gán sự kiện cho các nút trên giao diện
      bookCard.onAddToCart = (book) => {
        cartDrawer.addItem(book, 1);
        toast.success(`Đã thêm "${book.name}" vào giỏ hàng`);
      };
      
      bookCard.onQuickView = (book) => {
        console.log('Xem nhanh sách:', book.name);
      };
      
      bookCard.onWishlist = (ids) => {
        wishlists = new Set(ids);
      };
      bookCard.wishlists = wishlists;

      // 4. Phân bổ sách lên các khu vực (Lấy tạm vài cuốn demo)
      const flashBooks = mappedBooks.slice(0, 5).map(b => ({...b, badge: 'Flash Sale'}));
      const featuredBooks = mappedBooks.slice(0, 8);
      const newReleasesBooks = mappedBooks.slice(0, 10);

      // Render ra giao diện
      bookCard.renderAll(flashBooks, 'flash-sale-books', { isFlashSale: true });
      bookCard.renderAll(featuredBooks, 'featured-books', {});
      bookCard.renderAll(newReleasesBooks, 'new-releases', {});
      
      console.log('✅ Đã đổ dữ liệu từ DB lên giao diện thành công!');
      
    } else {
      console.error("Không có dữ liệu hoặc lỗi API:", result.message);
    }
  } catch (error) {
    console.error("Lỗi khi kết nối với API:", error);
  }
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

