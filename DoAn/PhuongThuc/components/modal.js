// ==================== MODAL COMPONENTS ====================

let selectedBook = null;
let currentQty = 1;

// Initialize book modal
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

// Open book modal
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

// Close book modal
function closeBookModal() {
  document.getElementById('book-modal').classList.remove('active');
  selectedBook = null;
}

// Initialize auth modal
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

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initBookModal, openBookModal, closeBookModal, initAuthModal, selectedBook, currentQty };
}

