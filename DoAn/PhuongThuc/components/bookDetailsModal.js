// ==================== BOOK DETAILS MODAL COMPONENT ====================

class BookDetailsModal {
  constructor(options = {}) {
    this.isOpen = false;
    this.book = null;
    this.quantity = 1;
    this.onAddToCart = options.onAddToCart || (() => {});
    this.onBuyNow = options.onBuyNow || (() => {});
    
    this.elements = {
      modal: null,
      image: null,
      name: null,
      author: null,
      rating: null,
      reviews: null,
      price: null,
      originalPrice: null,
      description: null,
      badge: null,
      discount: null,
      qtyInput: null
    };
    
    this.init();
  }

  init() {
    this.elements.modal = document.getElementById('book-modal');
    this.elements.image = document.getElementById('book-detail-image');
    this.elements.name = document.getElementById('book-detail-name');
    this.elements.author = document.getElementById('book-detail-author');
    this.elements.rating = document.getElementById('book-detail-rating');
    this.elements.reviews = document.getElementById('book-detail-reviews');
    this.elements.price = document.getElementById('book-detail-price');
    this.elements.originalPrice = document.getElementById('book-detail-original');
    this.elements.description = document.getElementById('book-detail-description');
    this.elements.badge = document.getElementById('book-detail-badge');
    this.elements.discount = document.getElementById('book-detail-discount');
    this.elements.qtyInput = document.getElementById('qty-input');
    
    this.bindEvents();
  }

  bindEvents() {
    const closeBtn = document.getElementById('book-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
    
    if (this.elements.modal) {
      this.elements.modal.addEventListener('click', (e) => {
        if (e.target === this.elements.modal) this.close();
      });
    }

    // Quantity controls
    const minusBtn = document.getElementById('qty-minus');
    const plusBtn = document.getElementById('qty-plus');
    const addToCartBtn = document.getElementById('btn-add-to-cart');
    
    if (minusBtn) {
      minusBtn.addEventListener('click', () => this.updateQuantity(-1));
    }
    if (plusBtn) {
      plusBtn.addEventListener('click', () => this.updateQuantity(1));
    }
    if (addToCartBtn) {
      addToCartBtn.addEventListener('click', () => this.handleAddToCart());
    }

    // ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.close();
      }
    });
  }

  open(book) {
    this.book = book;
    this.quantity = 1;
    this.updateUI();
    this.isOpen = true;
    this.elements.modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.isOpen = false;
    this.elements.modal?.classList.remove('active');
    document.body.style.overflow = '';
    this.book = null;
  }

  updateUI() {
    if (!this.book) return;

    // Image
    if (this.elements.image) {
      this.elements.image.src = this.book.image;
      this.elements.image.alt = this.book.name;
    }

    // Badges
    if (this.elements.badge) {
      if (this.book.badge) {
        this.elements.badge.textContent = this.book.badge;
        this.elements.badge.style.display = 'inline-block';
      } else {
        this.elements.badge.style.display = 'none';
      }
    }

    // Discount badge
    if (this.elements.discount) {
      const discount = this.book.originalPrice 
        ? Math.round((1 - this.book.price / this.book.originalPrice) * 100) 
        : 0;
      if (discount > 0) {
        this.elements.discount.textContent = `-${discount}%`;
        this.elements.discount.style.display = 'inline-block';
      } else {
        this.elements.discount.style.display = 'none';
      }
    }

    // Info
    if (this.elements.name) {
      this.elements.name.textContent = this.book.name;
    }
    if (this.elements.author) {
      this.elements.author.textContent = `Tác giả: ${this.book.author}`;
    }
    if (this.elements.rating) {
      this.elements.rating.textContent = this.book.rating;
    }
    if (this.elements.reviews) {
      this.elements.reviews.textContent = this.book.reviews;
    }
    if (this.elements.price) {
      this.elements.price.textContent = formatPrice(this.book.price);
    }
    if (this.elements.originalPrice) {
      if (this.book.originalPrice) {
        this.elements.originalPrice.textContent = formatPrice(this.book.originalPrice);
        this.elements.originalPrice.style.display = 'inline';
      } else {
        this.elements.originalPrice.style.display = 'none';
      }
    }
    if (this.elements.description) {
      this.elements.description.textContent = `Cuốn sách "${this.book.name}" của tác giả ${this.book.author}, thuộc thể loại ${this.book.category}. Đây là một cuốn sách rất đáng đọc với nội dung hấp dẫn và bổ ích.`;
    }
    if (this.elements.qtyInput) {
      this.elements.qtyInput.value = this.quantity;
    }
  }

  updateQuantity(delta) {
    this.quantity += delta;
    if (this.quantity < 1) this.quantity = 1;
    if (this.quantity > 99) this.quantity = 99;
    if (this.elements.qtyInput) {
      this.elements.qtyInput.value = this.quantity;
    }
  }

  handleAddToCart() {
    if (!this.book) return;
    this.onAddToCart(this.book, this.quantity);
    this.close();
  }

  handleBuyNow() {
    if (!this.book) return;
    this.onBuyNow(this.book, this.quantity);
  }
}

// Create global instance
const bookDetailsModal = new BookDetailsModal({
  onAddToCart: (book, quantity) => {
    cartDrawer.addItem(book, quantity);
    toast.success(`Đã thêm "${book.name}" vào giỏ hàng`);
  }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { BookDetailsModal, bookDetailsModal };
}

