// ==================== CART COMPONENT ====================

// Cart state
let cartItems = [];

// Initialize cart functionality
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

// Add item to cart
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

// Remove item from cart
function removeFromCart(id) {
  cartItems = cartItems.filter(item => item.id !== id);
  updateCartUI();
}

// Update cart item quantity
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

// Update cart UI
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
window.addToCart = addToCart;

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initCart, addToCart, removeFromCart, updateCartQuantity, updateCartUI, cartItems };
}

