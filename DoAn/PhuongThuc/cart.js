/**
 * cart.js — Vanilla JS Shopping Cart 
 * Sử dụng LocalStorage, KHÔNG tạo Element bằng String (innerHTML), chỉ dùng Template/CloneNode
 */

var cartDrawer = (function () {
    'use strict';
  
    // DOM Elements
    var overlay = null;
    var drawer = null;
    var btnClose = null;
    var cartItemsContainer = null;
    var template = null;
    var countText = null;
    var subtotalEl = null;
    var totalEl = null;
    var dataInput = null;
    var emptyMsg = null;
    var btnCheckout = null;
    
    // Toast Elements
    var toastEl = null;
    var toastMsg = null;
    var toastClose = null;
    var toastTimeout = null;
  
    // State
    var cartArr = [];
  
    function init() {
      overlay = document.getElementById('cart-overlay');
      drawer = document.getElementById('cart-drawer');
      btnClose = document.getElementById('cart-close');
      cartItemsContainer = document.getElementById('cart-items');
      template = document.getElementById('cart-item-template');
      countText = document.getElementById('cart-countText');
      subtotalEl = document.getElementById('cart-subtotal');
      totalEl = document.getElementById('cart-total');
      dataInput = document.getElementById('cart-data-input');
      emptyMsg = document.getElementById('cart-empty-msg');
      btnCheckout = document.getElementById('btn-checkout');
      
      toastEl = document.getElementById('cart-toast');
      toastMsg = document.getElementById('toast-message');
      toastClose = document.getElementById('toast-close');
  
      if (!overlay || !drawer) return; // Cart HTML chưa được load
  
      // Load from LocalStorage
      var stored = localStorage.getItem('book_cart');
      if (stored) {
        try {
          cartArr = JSON.parse(stored);
          if (!Array.isArray(cartArr)) cartArr = [];
        } catch (e) {
          cartArr = [];
        }
      }
  
      bindEvents();
      renderCart();
      updateHeaderIcon();
    }
  
    function bindEvents() {
      // Đóng drawer
      btnClose.addEventListener('click', closeCart);
      overlay.addEventListener('click', closeCart);
      
      // Đóng toast
      if (toastClose) {
        toastClose.addEventListener('click', function() {
            toastEl.classList.remove('show');
        });
      }
      
      // Header giỏ hàng nút (Nếu có id mn-gio-hang sẽ mở cart)
      var headerCartBtn = document.getElementById('btn-cart'); 
      if (headerCartBtn) {
          headerCartBtn.addEventListener('click', function(e) {
              e.preventDefault();
              openCart();
          });
      }
    }
  
    function openCart() {
      overlay.classList.add('active');
      drawer.classList.add('active');
      renderCart();
      
      // Cố định scroll giống xem Nhanh
      var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.paddingRight = scrollbarWidth + 'px';
      document.body.style.overflow = 'hidden';
    }
  
    function closeCart() {
      overlay.classList.remove('active');
      drawer.classList.remove('active');
      
      document.body.style.paddingRight = '';
      document.body.style.overflow = '';
    }
  
    function saveCart() {
      localStorage.setItem('book_cart', JSON.stringify(cartArr));
      updateHeaderIcon();
      // Ghi mảng vào hidden input để PHP serialize
      if (dataInput) dataInput.value = JSON.stringify(cartArr);
    }
  
    function updateHeaderIcon() {
        var qty = cartArr.reduce(function(sum, item) { return sum + item.soLuong; }, 0);
        // Header badge (Cập nhật sau ở bước gắn Header)
        var badge = document.getElementById('cart-count');
        if (badge) {
            badge.textContent = qty;
            badge.classList.toggle('hidden', qty === 0);
            badge.style.display = qty > 0 ? 'flex' : 'none';
        }
    }
  
    function showToast(message) {
        if (!toastEl) return;
        if (toastTimeout) clearTimeout(toastTimeout);
        
        toastMsg.textContent = message;
        toastEl.classList.add('show');
        
        toastTimeout = setTimeout(function() {
            toastEl.classList.remove('show');
        }, 3000);
    }
  
    function formatMoney(n) {
      return Number(n).toLocaleString('vi-VN');
    }
  
    function renderCart() {
      // Clear current items (cẩn thận với empty message)
      var items = cartItemsContainer.querySelectorAll('.cart-item');
      items.forEach(function(item) { item.remove(); });
  
      var totalMoney = 0;
      var totalQty = 0;
  
      if (cartArr.length === 0) {
          emptyMsg.style.display = 'block';
          btnCheckout.disabled = true;
          countText.textContent = '0 sản phẩm';
          subtotalEl.textContent = '0 ₫';
          totalEl.textContent = '0 ₫';
          saveCart();
          return;
      }
      
      emptyMsg.style.display = 'none';
      btnCheckout.disabled = false;
  
      cartArr.forEach(function(item, index) {
        totalMoney += item.giaBan * item.soLuong;
        totalQty += item.soLuong;
  
        // 1. Clone template
        var node = template.content.cloneNode(true);
        var itemDiv = node.querySelector('.cart-item');
        
        // 2. Map data
        node.querySelector('.cart-item-img').src = item.hinhAnh;
        node.querySelector('.cart-item-title').textContent = item.tenSach;
        node.querySelector('.cart-item-author').textContent = item.tacGia || '';
        node.querySelector('.cart-item-price').textContent = formatMoney(item.giaBan) + ' ₫';
        node.querySelector('.qty-value').textContent = item.soLuong;
        
        // 3. Event Listeners
        node.querySelector('.qty-minus').addEventListener('click', function() {
            changeQty(index, -1);
        });
        
        node.querySelector('.qty-plus').addEventListener('click', function() {
            changeQty(index, 1);
        });
        
        node.querySelector('.cart-item-remove').addEventListener('click', function() {
            removeItem(index);
        });
  
        // 4. Append
        cartItemsContainer.appendChild(node);
      });
  
      countText.textContent = totalQty + ' sản phẩm';
      var txtMoney = formatMoney(totalMoney) + ' ₫';
      subtotalEl.textContent = txtMoney;
      totalEl.textContent = txtMoney;
      
      saveCart();
    }
  
    function addItem(thongTin, soLuongThem) {
      // Check trùng
      var foundIndex = -1;
      for (var i = 0; i < cartArr.length; i++) {
          if (cartArr[i].maSach === thongTin.maSach) {
              foundIndex = i;
              break;
          }
      }
  
      if (foundIndex > -1) {
          cartArr[foundIndex].soLuong += soLuongThem;
      } else {
          thongTin.soLuong = soLuongThem;
          cartArr.push(thongTin);
      }
  
      saveCart();
      renderCart();
      showToast('Đã thêm "' + thongTin.tenSach + '" vào giỏ hàng');
      updateHeaderIcon();
    }
  
    function changeQty(index, delta) {
        if (!cartArr[index]) return;
        var newQty = cartArr[index].soLuong + delta;
        if (newQty <= 0) {
            removeItem(index);
        } else {
            cartArr[index].soLuong = newQty;
            saveCart();
            renderCart();
        }
    }
  
    function removeItem(index) {
        if (!cartArr[index]) return;
        cartArr.splice(index, 1);
        saveCart();
        renderCart();
    }
  
    // Listen for DOM load to init
    document.addEventListener('DOMContentLoaded', init);
  
    // Public API
    return {
      open: openCart,
      close: closeCart,
      addItem: addItem,
      getCart: function() { return cartArr; }
    };
  })();
