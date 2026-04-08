// ==================== UTILITY FUNCTIONS ====================

// Format price to Vietnamese Dong
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

// Show toast notification
function showToast(message) {
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toast-message');
  toastMessage.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}

// Generate book card HTML
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

// Export utilities
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { formatPrice, showToast, createBookCard };
}

