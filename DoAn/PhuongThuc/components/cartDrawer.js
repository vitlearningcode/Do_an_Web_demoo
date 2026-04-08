// ==================== CART DRAWER COMPONENT ====================

class CartDrawer {
  constructor(options = {}) {
    this.isOpen = false;
    this.items = options.items || [];
    this.onUpdate = options.onUpdate || (() => {});
    this.onCheckout = options.onCheckout || (() => {});
    
    this.elements = {
      drawer: null,
      overlay: null,
      itemsContainer: null,
      totalElement: null,
      countElement: null
    };
    
    this.init();
  }

  init() {
    this.elements.drawer = document.getElementById('cart-drawer');
    this.elements.overlay = document.getElementById('cart-overlay');
    this.elements.itemsContainer = document.getElementById('cart-items');
    this.elements.totalElement = document.getElementById('cart-total');
    this.elements.countElement = document.getElementById('cart-count');
    
    this.bindEvents();
  }

  bindEvents() {
    const closeBtn = document.getElementById('cart-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
    
    if (this.elements.overlay) {
      this.elements.overlay.addEventListener('click', () => this.close());
    }

    // Checkout button
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => this.handleCheckout());
    }
  }

  open() {
    this.isOpen = true;
    this.elements.drawer?.classList.add('active');
    this.elements.overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.isOpen = false;
    this.elements.drawer?.classList.remove('active');
    this.elements.overlay?.classList.remove('active');
    document.body.style.overflow = '';
  }

  toggle() {
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }

  addItem(book, quantity = 1) {
    const existing = this.items.find(item => item.id === book.id);
    if (existing) {
      existing.quantity += quantity;
    } else {
      this.items.push({ ...book, quantity });
    }
    this.updateUI();
    this.onUpdate(this.items);
  }

  removeItem(id) {
    this.items = this.items.filter(item => item.id !== id);
    this.updateUI();
    this.onUpdate(this.items);
  }

  updateQuantity(id, delta) {
    const item = this.items.find(item => item.id === id);
    if (item) {
      item.quantity += delta;
      if (item.quantity < 1) {
        this.removeItem(id);
      } else {
        this.updateUI();
        this.onUpdate(this.items);
      }
    }
  }

  clear() {
    this.items = [];
    this.updateUI();
    this.onUpdate(this.items);
  }

  getTotalItems() {
    return this.items.reduce((sum, item) => sum + item.quantity, 0);
  }

  getTotalPrice() {
    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  }

  updateUI() {
    // Update count badge in header
    if (this.elements.countElement) {
      const count = this.getTotalItems();
      this.elements.countElement.textContent = count;
      this.elements.countElement.classList.toggle('hidden', count === 0);
    }

    // Update totals in footer
    if (this.elements.totalElement) {
      this.elements.totalElement.textContent = formatPrice(this.getTotalPrice());
    }

    // Update items list
    if (this.elements.itemsContainer) {
      if (this.items.length === 0) {
        this.elements.itemsContainer.innerHTML = `
          <div class="empty-cart">
            <i class="fas fa-shopping-bag" style="font-size: 64px; opacity: 0.2;"></i>
            <p style="font-size: 18px; font-weight: 500; color: var(--gray-400);">Giỏ hàng trống</p>
            <button onclick="cartDrawer.close()" class="btn-secondary" style="margin-top: 16px;">Tiếp tục mua sắm</button>
          </div>
        `;
      } else {
        this.elements.itemsContainer.innerHTML = this.items.map(item => `
          <div class="cart-item" data-id="${item.id}">
            <div class="cart-item-image">
              <img src="${item.image}" alt="${item.name}" loading="lazy">
            </div>
            <div class="cart-item-info">
              <h4>${item.name}</h4>
              <p class="book-author">${item.author}</p>
              <div class="book-price">
                <span>${formatPrice(item.price)}</span>
                ${item.originalPrice ? `<span class="original-price">${formatPrice(item.originalPrice)}</span>` : ''}
              </div>
              <div class="cart-qty">
                <button class="qty-btn" onclick="cartDrawer.updateQuantity('${item.id}', -1)">-</button>
                <span class="qty-display">${item.quantity}</span>
                <button class="qty-btn" onclick="cartDrawer.updateQuantity('${item.id}', 1)">+</button>
              </div>
            </div>
            <button class="cart-item-remove" onclick="cartDrawer.removeItem('${item.id}')" title="Xóa">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `).join('');
      }
    }
  }

  handleCheckout() {
    if (this.items.length === 0) {
      toast.warning('Giỏ hàng trống');
      return;
    }
    this.onCheckout(this.items);
    toast.success('Tiến hành thanh toán...');
    // In a real app, redirect to checkout page
  }
}

// Create global instance
const cartDrawer = new CartDrawer();

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { CartDrawer, cartDrawer };
}

