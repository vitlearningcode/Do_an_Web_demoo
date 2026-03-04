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
    // Update count
    if (this.elements.countElement) {
      const count = this.getTotalItems();
      this.elements.countElement.textContent = count;
      this.elements.countElement.classList.toggle('hidden', count === 0);
    }

    // Update total
    if (this.elements.totalElement) {
      this.elements.totalElement.textContent = formatPrice(this.getTotalPrice());
    }

    // Update items
    if (this.elements.itemsContainer) {
      if (this.items.length === 0) {
        this.elements.itemsContainer.innerHTML = '<p class="cart-empty">Giỏ hàng trống</p>';
      } else {
        this.elements.itemsContainer.innerHTML = this.items.map(item => `
          <div class="cart-item">
            <img src="${item.image}" alt="${item.name}">
            <div class="cart-item-info">
              <h4 class="cart-item-name">${item.name}</h4>
              <p class="cart-item-price">${formatPrice(item.price)}</p>
              <div class="cart-item-qty">
                <button onclick="cartDrawer.updateQuantity('${item.id}', -1)">-</button>
                <span>${item.quantity}</span>
                <button onclick="cartDrawer.updateQuantity('${item.id}', 1)">+</button>
              </div>
            </div>
            <button class="cart-item-remove" onclick="cartDrawer.removeItem('${item.id}')">
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

