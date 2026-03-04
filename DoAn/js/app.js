// ==================== DATA ====================
const featuredBooks = [
  { id: '1', name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: 86000, originalPrice: 120000, rating: 4.8, reviews: 1240, image: 'https://picsum.photos/seed/book1/300/400', badge: 'Bán chạy' },
  { id: '2', name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: 79000, originalPrice: 95000, rating: 4.9, reviews: 850, image: 'https://picsum.photos/seed/book2/300/400' },
  { id: '3', name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: 110000, rating: 4.7, reviews: 620, image: 'https://picsum.photos/seed/book3/300/400', badge: 'Mới' },
  { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: 80000, rating: 4.6, reviews: 2100, image: 'https://picsum.photos/seed/book4/300/400' },
  { id: '5', name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: 250000, originalPrice: 300000, rating: 4.9, reviews: 3200, image: 'https://picsum.photos/seed/book5/300/400', badge: '-15%' },
  { id: '6', name: 'Cây Cam Ngọt Của Tôi', author: 'José Mauro de Vasconcelos', category: 'Văn học', price: 95000, rating: 4.8, reviews: 1500, image: 'https://picsum.photos/seed/book6/300/400' },
  { id: '7', name: 'Tâm Lý Học Tội Phạm', author: 'Tôn Thất', category: 'Tâm lý học', price: 135000, originalPrice: 150000, rating: 4.5, reviews: 430, image: 'https://picsum.photos/seed/book7/300/400' },
  { id: '8', name: 'Muôn Kiếp Nhân Sinh', author: 'Nguyên Phong', category: 'Tôn giáo - Tâm linh', price: 168000, rating: 4.7, reviews: 980, image: 'https://picsum.photos/seed/book8/300/400' },
];

const newReleases = [
  { id: '9', name: 'Dune - Xứ Cát', author: 'Frank Herbert', category: 'Khoa học viễn tưởng', price: 210000, originalPrice: 250000, rating: 4.9, reviews: 150, image: 'https://picsum.photos/seed/book9/300/400', badge: 'Mới' },
  { id: '10', name: 'Atomic Habits', author: 'James Clear', category: 'Kỹ năng sống', price: 145000, rating: 4.8, reviews: 5200, image: 'https://picsum.photos/seed/book10/300/400' },
  { id: '11', name: 'Kẻ Trộm Sách', author: 'Markus Zusak', category: 'Văn học', price: 125000, originalPrice: 140000, rating: 4.7, reviews: 890, image: 'https://picsum.photos/seed/book11/300/400' },
  { id: '12', name: 'Sức Mạnh Của Thói Quen', author: 'Charles Duhigg', category: 'Tâm lý học', price: 115000, rating: 4.6, reviews: 1200, image: 'https://picsum.photos/seed/book12/300/400' },
  { id: '13', name: 'Tư Duy Nhanh Và Chậm', author: 'Daniel Kahneman', category: 'Kinh tế', price: 185000, originalPrice: 210000, rating: 4.8, reviews: 3400, image: 'https://picsum.photos/seed/book13/300/400', badge: 'Bán chạy' },
];

// ==================== STATE ====================
let cartItems = [];
let currentRole = 'customer';
let currentAdminView = 'overview';
let selectedBook = null;
let currentQty = 1;
let currentHeroSlide = 0;

// ==================== UTILITIES ====================
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

function showToast(message) {
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toast-message');
  toastMessage.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}

// ==================== BOOK CARD HTML ====================
function createBookCard(book, isFlashSale = false) {
  const displayPrice = isFlashSale ? Math.floor(book.price * 0.7) : book.price;
  const displayOriginal = isFlashSale ? book.price : book.originalPrice;
  
  return `
    <div class="book-card" data-book-id="${book.id}">
      <div class="book-image">
        <img src="${book.image}" alt="${book.name}">
        <span class="book-badge ${isFlashSale ? 'flash' : ''}">${isFlashSale ? 'Flash Sale' : (book.badge || '')}</span>
      </div>
      <div class="book-info">
        <h3 class="book-name">${book.name}</h3>
        <p class="book-author">${book.author}</p>
        <div class="book-card-bottom">
          <div class="book-price">
            <span class="current-price">${formatPrice(displayPrice)}</span>
            ${displayOriginal ? `<span class="original-price">${formatPrice(displayOriginal)}</span>` : ''}
          </div>
          <div class="book-rating">
            <i class="fas fa-star"></i>
            <span>${book.rating}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

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
  const slides = document.querySelectorAll('.hero-slide');
  const dotsContainer = document.getElementById('hero-dots');
  
  // Create dots
  slides.forEach((_, index) => {
    const dot = document.createElement('div');
    dot.className = `hero-dot ${index === 0 ? 'active' : ''}`;
    dot.addEventListener('click', () => goToSlide(index));
    dotsContainer.appendChild(dot);
  });

  // Auto slide
  setInterval(() => {
    currentHeroSlide = (currentHeroSlide + 1) % slides.length;
    updateHeroSlide();
  }, 5000);
}

function goToSlide(index) {
  currentHeroSlide = index;
  updateHeroSlide();
}

function updateHeroSlide() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  
  slides.forEach((slide, i) => {
    slide.classList.toggle('active', i === currentHeroSlide);
  });
  
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentHeroSlide);
  });
}

// ==================== FLASH SALE TIMER ====================
function initFlashSaleTimer() {
  let hours = 12;
  let minutes = 45;
  let seconds = 30;

  setInterval(() => {
    seconds--;
    if (seconds < 0) {
      seconds = 59;
      minutes--;
      if (minutes < 0) {
        minutes = 59;
        hours--;
        if (hours < 0) {
          hours = 24;
        }
      }
    }
    
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
  }, 1000);
}

// ==================== BOOKS RENDERING ====================
function renderBooks() {
  const flashSaleContainer = document.getElementById('flash-sale-books');
  const featuredContainer = document.getElementById('featured-books');
  const newReleasesContainer = document.getElementById('new-releases');

  // Flash Sale - first 5 books
  flashSaleContainer.innerHTML = featuredBooks.slice(0, 5).map(book => createBookCard(book, true)).join('');
  
  // Featured Books
  featuredContainer.innerHTML = featuredBooks.map(book => createBookCard(book)).join('');
  
  // New Releases
  newReleasesContainer.innerHTML = newReleases.map(book => createBookCard(book)).join('');

  // Add click handlers
  document.querySelectorAll('.book-card').forEach(card => {
    card.addEventListener('click', () => {
      const bookId = card.dataset.bookId;
      const book = [...featuredBooks, ...newReleases].find(b => b.id === bookId);
      if (book) {
        openBookModal(book);
      }
    });
  });
}

// ==================== CART FUNCTIONALITY ====================
function initCart() {
  const cartBtn = document.getElementById('btn-cart');
  const cartDrawer = document.getElementById('cart-drawer');
  const cartOverlay = document.getElementById('cart-overlay');
  const cartClose = document.getElementById('cart-close');

  cartBtn.addEventListener('click', () => {
    cartDrawer.classList.add('active');
    cartOverlay.classList.add('active');
  });

  const closeCart = () => {
    cartDrawer.classList.remove('active');
    cartOverlay.classList.remove('active');
  };

  cartClose.addEventListener('click', closeCart);
  cartOverlay.addEventListener('click', closeCart);
}

function addToCart(book, quantity = 1) {
  const existing = cartItems.find(item => item.id === book.id);
  if (existing) {
    existing.quantity += quantity;
  } else {
    cartItems.push({ ...book, quantity });
  }
  updateCartUI();
  showToast(`Đã thêm "${book.name}" vào giỏ hàng`);
}

function removeFromCart(id) {
  cartItems = cartItems.filter(item => item.id !== id);
  updateCartUI();
}

function updateCartQuantity(id, delta) {
  const item = cartItems.find(item => item.id === id);
  if (item) {
    item.quantity += delta;
    if (item.quantity < 1) {
      removeFromCart(id);
    } else {
      updateCartUI();
    }
  }
}

function updateCartUI() {
  const cartCount = document.getElementById('cart-count');
  const cartItemsContainer = document.getElementById('cart-items');
  const cartTotal = document.getElementById('cart-total');

  const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  cartCount.textContent = totalItems;
  cartCount.classList.toggle('hidden', totalItems === 0);
  cartTotal.textContent = formatPrice(totalPrice);

  if (cartItems.length === 0) {
    cartItemsContainer.innerHTML = '<p class="cart-empty">Giỏ hàng trống</p>';
  } else {
    cartItemsContainer.innerHTML = cartItems.map(item => `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.name}">
        <div class="cart-item-info">
          <h4 class="cart-item-name">${item.name}</h4>
          <p class="cart-item-price">${formatPrice(item.price)}</p>
          <div class="cart-item-qty">
            <button onclick="updateCartQuantity('${item.id}', -1)">-</button>
            <span>${item.quantity}</span>
            <button onclick="updateCartQuantity('${item.id}', 1)">+</button>
          </div>
        </div>
        <button class="cart-item-remove" onclick="removeFromCart('${item.id}')">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    `).join('');
  }
}

// Make functions global for inline handlers
window.updateCartQuantity = updateCartQuantity;
window.removeFromCart = removeFromCart;

// ==================== BOOK MODAL ====================
function openBookModal(book) {
  selectedBook = book;
  currentQty = 1;
  
  document.getElementById('book-detail-image').src = book.image;
  document.getElementById('book-detail-badge').textContent = book.badge || '';
  document.getElementById('book-detail-badge').style.display = book.badge ? 'inline-block' : 'none';
  document.getElementById('book-detail-name').textContent = book.name;
  document.getElementById('book-detail-author').textContent = `Tác giả: ${book.author}`;
  document.getElementById('book-detail-rating').textContent = book.rating;
  document.getElementById('book-detail-reviews').textContent = book.reviews;
  document.getElementById('book-detail-price').textContent = formatPrice(book.price);
  document.getElementById('book-detail-original').textContent = book.originalPrice ? formatPrice(book.originalPrice) : '';
  document.getElementById('book-detail-original').style.display = book.originalPrice ? 'inline' : 'none';
  document.getElementById('book-detail-description').textContent = book.description || `Cuốn sách "${book.name}" của tác giả ${book.author}, thuộc thể loại ${book.category}. Đây là một cuốn sách rất đáng đọc với nội dung hấp dẫn và bổ ích.`;
  document.getElementById('qty-input').value = 1;

  document.getElementById('book-modal').classList.add('active');
}

function closeBookModal() {
  document.getElementById('book-modal').classList.remove('active');
  selectedBook = null;
}

function initBookModal() {
  const bookModal = document.getElementById('book-modal');
  const bookClose = document.getElementById('book-close');
  const qtyMinus = document.getElementById('qty-minus');
  const qtyPlus = document.getElementById('qty-plus');
  const qtyInput = document.getElementById('qty-input');
  const addToCartBtn = document.getElementById('btn-add-to-cart');

  bookClose.addEventListener('click', closeBookModal);
  bookModal.addEventListener('click', (e) => {
    if (e.target === bookModal) closeBookModal();
  });

  qtyMinus.addEventListener('click', () => {
    if (currentQty > 1) {
      currentQty--;
      qtyInput.value = currentQty;
    }
  });

  qtyPlus.addEventListener('click', () => {
    currentQty++;
    qtyInput.value = currentQty;
  });

  addToCartBtn.addEventListener('click', () => {
    if (selectedBook) {
      addToCart(selectedBook, currentQty);
      closeBookModal();
    }
  });
}

// ==================== AUTH MODAL ====================
function initAuthModal() {
  const authBtn = document.getElementById('btn-login');
  const authModal = document.getElementById('auth-modal');
  const authClose = document.getElementById('auth-close');
  const authForm = document.getElementById('auth-form');

  authBtn.addEventListener('click', () => {
    authModal.classList.add('active');
  });

  authClose.addEventListener('click', () => {
    authModal.classList.remove('active');
  });

  authModal.addEventListener('click', (e) => {
    if (e.target === authModal) authModal.classList.remove('active');
  });

  authForm.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Đăng nhập thành công!');
    authModal.classList.remove('active');
  });
}

// ==================== ADMIN VIEWS ====================
const adminViews = {
  overview: `
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info">
          <h4>1,234</h4>
          <p>Đơn hàng hôm nay</p>
          <span class="stat-change positive">+12.5%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
          <h4>125.5M</h4>
          <p>Doanh thu hôm nay</p>
          <span class="stat-change positive">+8.2%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div class="stat-info">
          <h4>856</h4>
          <p>Khách hàng mới</p>
          <span class="stat-change positive">+15.3%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-book"></i></div>
        <div class="stat-info">
          <h4>3,456</h4>
          <p>Sản phẩm tồn kho</p>
          <span class="stat-change negative">-2.1%</span>
        </div>
      </div>
    </div>
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Đơn hàng gần đây</h3>
        <a href="#" class="view-all-btn">Xem tất cả</a>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#DH001</td>
            <td>Nguyễn Văn A</td>
            <td>Đắc Nhân Tâm</td>
            <td>86,000đ</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#DH002</td>
            <td>Trần Thị B</td>
            <td>Nhà Giả Kim</td>
            <td>79,000đ</td>
            <td><span class="status-badge warning">Đang xử lý</span></td>
          </tr>
          <tr>
            <td>#DH003</td>
            <td>Lê Văn C</td>
            <td>Atomic Habits</td>
            <td>145,000đ</td>
            <td><span class="status-badge danger">Chờ thanh toán</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  import: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý nhập hàng</h3>
        <button class="submit-btn" style="width: auto; padding: 10px 20px;">+ Thêm đơn nhập</button>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã phiếu</th>
            <th>Ngày nhập</th>
            <th>Nhà cung cấp</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#PN001</td>
            <td>20/01/2024</td>
            <td>Công ty ABC</td>
            <td>500</td>
            <td>50,000,000đ</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#PN002</td>
            <td>22/01/2024</td>
            <td>Công ty XYZ</td>
            <td>300</td>
            <td>30,000,000đ</td>
            <td><span class="status-badge warning">Đang chờ</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  'book-info': `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý thông tin sách</h3>
        <button class="submit-btn" style="width: auto; padding: 10px 20px;">+ Thêm sách mới</button>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã sách</th>
            <th>Tên sách</th>
            <th>Tác giả</th>
            <th>Thể loại</th>
            <th>Giá bán</th>
            <th>Tồn kho</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          ${featuredBooks.map(book => `
            <tr>
              <td>${book.id}</td>
              <td>${book.name}</td>
              <td>${book.author}</td>
              <td>${book.category}</td>
              <td>${formatPrice(book.price)}</td>
              <td>${Math.floor(Math.random() * 100) + 10}</td>
              <td>
                <button style="color: var(--primary); background: none; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                <button style="color: var(--danger); background: none;"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `,
  
  revenue: `
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
          <h4>890.5M</h4>
          <p>Doanh thu tháng này</p>
          <span class="stat-change positive">+18.2%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
          <h4>1.2M</h4>
          <p>Trung bình/ngày</p>
          <span class="stat-change positive">+5.4%</span>
        </div>
      </div>
    </div>
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Doanh thu theo ngày</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Ngày</th>
            <th>Số đơn</th>
            <th>Doanh thu</th>
            <th>Lợi nhuận</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>20/01/2024</td><td>45</td><td>4,500,000đ</td><td>1,350,000đ</td></tr>
          <tr><td>19/01/2024</td><td>52</td><td>5,200,000đ</td><td>1,560,000đ</td></tr>
          <tr><td>18/01/2024</td><td>38</td><td>3,800,000đ</td><td>1,140,000đ</td></tr>
        </tbody>
      </table>
    </div>
  `,
  
  sales: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý bán hàng</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#DH001</td>
            <td>Nguyễn Văn A</td>
            <td>Đắc Nhân Tâm</td>
            <td>2</td>
            <td>172,000đ</td>
            <td>20/01/2024</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#DH002</td>
            <td>Trần Thị B</td>
            <td>Nhà Giả Kim</td>
            <td>1</td>
            <td>79,000đ</td>
            <td>20/01/2024</td>
            <td><span class="status-badge warning">Đang xử lý</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  inventory: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý tồn kho</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã sách</th>
            <th>Tên sách</th>
            <th>Tồn kho</th>
            <th>Đã bán</th>
            <th>Còn lại</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          ${featuredBooks.slice(0, 5).map(book => {
            const sold = Math.floor(Math.random() * 50);
            const stock = Math.floor(Math.random() * 100) + 20;
            return `
              <tr>
                <td>${book.id}</td>
                <td>${book.name}</td>
                <td>${stock}</td>
                <td>${sold}</td>
                <td>${stock - sold}</td>
                <td><span class="status-badge ${stock - sold < 20 ? 'danger' : 'success'}">${stock - sold < 20 ? 'Sắp hết' : 'Còn hàng'}</span></td>
              </tr>
            `;
          }).join('')}
        </tbody>
      </table>
    </div>
  `,
  
  reports: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Báo cáo tổng hợp</h3>
      </div>
      <div style="padding: 40px; text-align: center; color: var(--gray-500);">
        <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Biểu đồ và báo cáo chi tiết sẽ được hiển thị tại đây</p>
      </div>
    </div>
  `,
  
  settings: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Cài đặt hệ thống</h3>
      </div>
      <div class="form-group">
        <label>Tên cửa hàng</label>
        <input type="text" value="Book Sales Store">
      </div>
      <div class="form-group">
        <label>Email liên hệ</label>
        <input type="email" value="contact@booksales.vn">
      </div>
      <div class="form-group">
        <label>Điện thoại</label>
        <input type="tel" value="1900 xxxx">
      </div>
      <div class="form-group">
        <label>Địa chỉ</label>
        <input type="text" value="123 Đường ABC, Quận 1, TP.HCM">
      </div>
      <button class="submit-btn">Lưu thay đổi</button>
    </div>
  `,
  
  contact: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý liên hệ</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Nguyễn Văn A</td>
            <td>a@gmail.com</td>
            <td>Hỏi về sách</td>
            <td>Tôi muốn hỏi về...</td>
            <td>20/01/2024</td>
            <td><span class="status-badge warning">Chưa đọc</span></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Trần Thị B</td>
            <td>b@gmail.com</td>
            <td>Góp ý</td>
            <td>Cửa hàng nên...</td>
            <td>19/01/2024</td>
            <td><span class="status-badge success">Đã trả lời</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `
};

function loadAdminView(view) {
  currentAdminView = view;
  const adminContent = document.getElementById('admin-content');
  const viewTitle = document.getElementById('admin-view-title');
  
  const titles = {
    overview: 'Tổng quan',
    import: 'Nhập hàng',
    'book-info': 'Thông tin sách',
    revenue: 'Doanh thu',
    sales: 'Bán hàng',
    inventory: 'Tồn kho',
    reports: 'Báo cáo',
    settings: 'Cài đặt',
    contact: 'Liên hệ'
  };
  
  viewTitle.textContent = titles[view] || 'Tổng quan';
  adminContent.innerHTML = adminViews[view] || adminViews.overview;
  
  // Update active nav item
  document.querySelectorAll('.nav-item').forEach(item => {
    item.classList.toggle('active', item.dataset.view === view);
  });
}

function initAdminSidebar() {
  document.querySelectorAll('.nav-item[data-view]').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      loadAdminView(item.dataset.view);
    });
  });

  document.getElementById('btn-logout').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('btn-customer').click();
  });
}

// ==================== CHATBOT ====================
function initChatbot() {
  const chatbotToggle = document.getElementById('chatbot-toggle');
  const chatbot = document.getElementById('chatbot');
  const chatbotClose = document.getElementById('chatbot-close');
  const chatbotSend = document.getElementById('chatbot-send');
  const chatbotInput = document.getElementById('chatbot-input');
  const messagesContainer = document.getElementById('chatbot-messages');

  chatbotToggle.addEventListener('click', () => {
    chatbot.classList.toggle('active');
  });

  chatbotClose.addEventListener('click', () => {
    chatbot.classList.remove('active');
  });

  function sendMessage() {
    const message = chatbotInput.value.trim();
    if (!message) return;

    // Add user message
    const userMsg = document.createElement('div');
    userMsg.className = 'chatbot-message user';
    userMsg.innerHTML = `<p>${message}</p>`;
    messagesContainer.appendChild(userMsg);
    chatbotInput.value = '';

    // Simulate bot response
    setTimeout(() => {
      const botMsg = document.createElement('div');
      botMsg.className = 'chatbot-message bot';
      
      const responses = [
        'Cảm ơn bạn đã liên hệ! Tôi có thể giúp gì cho bạn?',
        'Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại.',
        'Để xem giỏ hàng, hãy nhấn vào biểu tượng giỏ hàng ở góc trên.',
        'Chúng tôi có chương trình khuyến mãi đặc biệt vào cuối tuần này!',
        'Bạn cần hỗ trợ về đơn hàng nào? Tôi sẽ giúp bạn kiểm tra.'
      ];
      
      botMsg.innerHTML = `<p>${responses[Math.floor(Math.random() * responses.length)]}</p>`;
      messagesContainer.appendChild(botMsg);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 1000);
  }

  chatbotSend.addEventListener('click', sendMessage);
  chatbotInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
  });
}

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
  initRoleToggle();
  initHeroCarousel();
  initFlashSaleTimer();
  renderBooks();
  initCart();
  initBookModal();
  initAuthModal();
  initAdminSidebar();
  initChatbot();
});

