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
  
      // Ưu tiên lấy giỏ hàng từ PHP Session (cartServerData do PHP render sẵn)
      // cartServerData = null nếu chưa đăng nhập, [] nếu đầy đủ là mảng
      if (typeof cartServerData !== 'undefined' && cartServerData !== null && Array.isArray(cartServerData)) {
        cartArr = cartServerData; // Dùng dữ liệu từ server (PHP session)
      } else {
        // Fallback: localStorage khi chưa đăng nhập
        var stored = localStorage.getItem('book_cart');
        if (stored) {
          try {
            cartArr = JSON.parse(stored);
            if (!Array.isArray(cartArr)) cartArr = [];
          } catch (e) {
            cartArr = [];
          }
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
      // 1. Luôn lưu localStorage (fallback + dùng cho form checkout)
      localStorage.setItem('book_cart', JSON.stringify(cartArr));
      updateHeaderIcon();
      // Ghi mảng vào hidden input để PHP serialize khi checkout
      if (dataInput) dataInput.value = JSON.stringify(cartArr);

      // 2. Nếu đã đăng nhập → đồng bộ lên PHP Session qua hidden form + iframe
      // (thuần PHP, không AJAX, không fetch)
      if (typeof dangDangNhap !== 'undefined' && dangDangNhap) {
        var syncForm = document.getElementById('cart-sync-form');
        var syncJson = document.getElementById('cart-sync-json');
        if (syncForm && syncJson) {
          syncJson.value = JSON.stringify(cartArr);
          syncForm.submit();
        }
      }
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
  
        // Lấy tồn kho từ __tonKhoMap
        var tonKho = (window.__tonKhoMap && window.__tonKhoMap[item.maSach] !== undefined)
                     ? window.__tonKhoMap[item.maSach]
                     : (item.soLuongTon !== undefined ? item.soLuongTon : Infinity);
  
        // 1. Clone template
        var node = template.content.cloneNode(true);
        
        // 2. Map data
        node.querySelector('.cart-item-img').src = item.hinhAnh || '';
        node.querySelector('.cart-item-title').textContent = item.tenSach || '';
        node.querySelector('.cart-item-author').textContent = item.tacGia || '';
        node.querySelector('.cart-item-price').textContent = formatMoney(item.giaBan) + ' ₫';
        node.querySelector('.qty-value').textContent = item.soLuong;
        
        // 3. Cảnh báo tồn kho + disable nút +
        var warnEl  = node.querySelector('.cart-stock-warn');
        var plusBtn = node.querySelector('.qty-plus');
  
        if (tonKho !== Infinity) {
          if (item.soLuong >= tonKho) {
            // Đạt giới hạn
            if (warnEl) {
              warnEl.textContent = '⚠️ Tối đa ' + tonKho + ' cuốn';
              warnEl.className = 'cart-stock-warn at-limit';
              warnEl.style.display = 'inline';
            }
            if (plusBtn) plusBtn.disabled = true;
          } else if (tonKho <= 5) {
            // Sắp hết
            if (warnEl) {
              warnEl.textContent = '⚠️ Còn ' + tonKho + ' cuốn';
              warnEl.className = 'cart-stock-warn';
              warnEl.style.display = 'inline';
            }
          }
        }
        
        // 4. Event Listeners
        node.querySelector('.qty-minus').addEventListener('click', function() {
            changeQty(index, -1);
        });
        
        node.querySelector('.qty-plus').addEventListener('click', function() {
            changeQty(index, 1);
        });
        
        node.querySelector('.cart-item-remove').addEventListener('click', function() {
            removeItem(index);
        });
  
        // 5. Append
        cartItemsContainer.appendChild(node);
      });
  
      countText.textContent = totalQty + ' sản phẩm';
      var txtMoney = formatMoney(totalMoney) + ' ₫';
      subtotalEl.textContent = txtMoney;
      totalEl.textContent = txtMoney;
      
      saveCart();
    }
  
    function addItem(thongTin, soLuongThem) {
      // Lấy tồn kho từ __tonKhoMap (PHP inject từ DB)
      var tonKho = (window.__tonKhoMap && window.__tonKhoMap[thongTin.maSach] !== undefined)
                    ? window.__tonKhoMap[thongTin.maSach]
                    : Infinity;

      // Chặn hết hàng — alert đã được btnThemGioHang.js xử lý trước, chỉ cần return
      if (tonKho !== Infinity && tonKho <= 0) {
        return;
      }

      // Check trùng
      var foundIndex = -1;
      for (var i = 0; i < cartArr.length; i++) {
          if (cartArr[i].maSach === thongTin.maSach) {
              foundIndex = i;
              break;
          }
      }
  
      if (foundIndex > -1) {
        var newQty = cartArr[foundIndex].soLuong + soLuongThem;
        if (newQty > tonKho) {
          cartArr[foundIndex].soLuong = tonKho;
          showToast('⚠️ Chỉ còn ' + tonKho + ' cuốn — đã giới hạn số lượng!');
        } else {
          cartArr[foundIndex].soLuong = newQty;
          showToast('Đã thêm "' + thongTin.tenSach + '" vào giỏ hàng');
        }
      } else {
        thongTin.soLuong = (tonKho !== Infinity) ? Math.min(soLuongThem, tonKho) : soLuongThem;
        cartArr.push(thongTin);
        showToast('Đã thêm "' + thongTin.tenSach + '" vào giỏ hàng');
      }
  
      saveCart();
      renderCart();

      updateHeaderIcon();
    }
  
    function changeQty(index, delta) {
      if (!cartArr[index]) return;
      // Lấy tồn kho
      var ms = cartArr[index].maSach;
      var tonKho = (window.__tonKhoMap && window.__tonKhoMap[ms] !== undefined)
                    ? window.__tonKhoMap[ms]
                    : Infinity;
  
      var newQty = cartArr[index].soLuong + delta;
      if (newQty <= 0) {
          removeItem(index);
      } else if (newQty > tonKho) {
          // Không cho vượt tồn kho
          cartArr[index].soLuong = tonKho;
          showToast('⚠️ Chỉ còn ' + tonKho + ' cuốn trong kho!');
          saveCart();
          renderCart();
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
