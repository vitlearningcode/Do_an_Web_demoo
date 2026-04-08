// ==================== BOOK CARD COMPONENT ====================

class BookCard {
  constructor(options = {}) {
    this.onAddToCart = options.onAddToCart || (() => {});
    this.onQuickView = options.onQuickView || (() => {});
    this.onWishlist = options.onWishlist || (() => {});
    this.wishlists = new Set(options.wishlists || []);
  }

  render(book, options = {}) {
    const displayPrice = options.isFlashSale ? Math.floor(book.price * 0.7) : book.price;
    const displayOriginal = options.isFlashSale ? book.price : book.originalPrice;
    const discount = displayOriginal ? Math.round((1 - displayPrice / displayOriginal) * 100) : 0;
    const isWishlisted = this.wishlists.has(book.id);
    
    return `
      <div class="book-card" data-book-id="${book.id}">
        <div class="book-image">
          <img src="${book.image}" alt="${book.name}" referrerPolicy="no-referrer">
          
          <!-- Badges -->
          <div class="book-badges">
            ${book.badge ? `<span class="book-badge">${book.badge}</span>` : ''}
            ${discount > 0 ? `<span class="book-badge discount">-${discount}%</span>` : ''}
          </div>

          <!-- Quick Actions -->
          <div class="book-quick-actions">
            <button class="book-quick-btn wishlist ${isWishlisted ? 'active' : ''}" data-id="${book.id}" title="Yêu thích">
              <i class="fas fa-heart ${isWishlisted ? 'fas' : 'far'}"></i>
            </button>
            <button class="book-quick-btn" onclick="event.stopPropagation(); bookCard.onQuickView('${book.id}')" title="Xem nhanh">
              <i class="fas fa-eye"></i>
            </button>
          </div>

          <!-- Quick Add -->
          <div class="book-quick-add">
            <button onclick="event.stopPropagation(); bookCard.onQuickAdd('${book.id}')">
              <i class="fas fa-shopping-cart"></i> Thêm Nhanh
            </button>
          </div>
        </div>
        
        <div class="book-info">
          <span class="book-category">${book.category}</span>
          <h3 class="book-name">${book.name}</h3>
          <p class="book-author">${book.author}</p>
          <div class="book-rating">
            <i class="fas fa-star"></i>
            <span>${book.rating}</span>
            <span class="reviews-count">(${book.reviews})</span>
          </div>
          <div class="book-card-bottom">
            <div class="book-price">
              <span class="current-price">${formatPrice(displayPrice)}</span>
              ${displayOriginal ? `<span class="original-price">${formatPrice(displayOriginal)}</span>` : ''}
            </div>
            <button class="add-cart-btn" onclick="event.stopPropagation(); bookCard.onAdd('${book.id}')">
              <i class="fas fa-shopping-cart"></i>
            </button>
          </div>
        </div>
      </div>
    `;
  }

  renderAll(books, containerId, options = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = books.map(book => this.render(book, options)).join('');
    this.bindEvents(books);
  }

  bindEvents(books) {
    // Card click for quick view
    document.querySelectorAll('.book-card').forEach(card => {
      card.addEventListener('click', () => {
        const bookId = card.dataset.bookId;
        const book = books.find(b => b.id === bookId);
        if (book) this.onQuickView(book);
      });
    });

    // Wishlist button
    document.querySelectorAll('.book-quick-btn.wishlist').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const id = btn.dataset.id;
        this.toggleWishlist(id, btn);
      });
    });
  }

  toggleWishlist(id, btn) {
    if (this.wishlists.has(id)) {
      this.wishlists.delete(id);
      btn.classList.remove('active');
      btn.innerHTML = '<i class="far fa-heart"></i>';
      toast.warning('Đã xóa khỏi yêu thích');
    } else {
      this.wishlists.add(id);
      btn.classList.add('active');
      btn.innerHTML = '<i class="fas fa-heart"></i>';
      toast.success('Đã thêm vào yêu thích');
    }
    this.onWishlist(Array.from(this.wishlists));
  }

  onQuickAdd(id) {
    const book = window.featuredBooks?.find(b => b.id === id) || window.newReleases?.find(b => b.id === id) || window.booksData?.find(b => b.id === id);
    if (book) {
      this.onAddToCart(book);
    }
  }

  onAdd(id) {
    const book = window.featuredBooks?.find(b => b.id === id) || window.newReleases?.find(b => b.id === id) || window.booksData?.find(b => b.id === id);
    if (book) {
      this.onAddToCart(book);
    }
  }

  onQuickView(id) {
    const book = window.featuredBooks?.find(b => b.id === id) || window.newReleases?.find(b => b.id === id) || window.booksData?.find(b => b.id === id);
    if (book) {
      this.onQuickView(book);
    }
  }
}

// Create global instance
const bookCard = new BookCard();

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { BookCard, bookCard };
}

