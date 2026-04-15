/**
 * ============================================================
 * LUỒNG: GIỎ HÀNG PHÍA CLIENT (cart.js)
 *
 * KIẾN TRÚC: IIFE Module Pattern (Immediately Invoked Function Expression)
 *   var cartDrawer = (function() { ... })();
 *   → Đóng gói hoàn toàn, chỉ lộ Public API qua return {}
 *   → Tránh xung đột biến toàn cục với các file JS khác
 *
 * NGUỒN DỮ LIỆU (theo thứ tự ưu tiên):
 *   1. cartServerData (PHP inject từ layGioHangCoGia.php) — ĐÃ ĐĂNG NHẬP
 *      Giá từ DB, tồn kho từ DB → đáng tin cậy hoàn toàn
 *   2. localStorage['book_cart'] — KHÁCH CHƯA ĐĂNG NHẬP
 *      Giá từ window.__giaSach (PHP inject), lưu tạm trên trình duyệt
 *
 * ĐỒNG BỘ LÊN SERVER (saveCart):
 *   localStorage (luôn) + iframe hidden form → luuGioHang.php (nếu đăng nhập)
 *   Cơ chế: KHÔNG dùng AJAX/fetch — dùng form.submit() vào iframe ẩn
 *
 * KIỂM SOÁT TỒN KHO (client-side):
 *   window.__tonKhoMap (PHP inject từ DB) → giới hạn soLuong ngay trên UI
 *   Server (xuLyThanhToan.php) vẫn kiểm tra lại bằng FOR UPDATE
 *
 * PUBLIC API (cartDrawer.*):
 *   cartDrawer.open()           → Mở drawer giỏ hàng
 *   cartDrawer.close()          → Đóng drawer
 *   cartDrawer.addItem(info, qty) → Thêm sản phẩm (gọi từ btnThemGioHang.js)
 *   cartDrawer.getCart()        → Lấy mảng cartArr hiện tại
 * ============================================================
 */

// IIFE: wrap toàn bộ trong function tự gọi → private scope
var cartDrawer = (function () {
    'use strict'; // Strict mode: báo lỗi sớm, tránh bug ngầm

    // =========================================================
    // PHẦN 1: KHAI BÁO BIẾN PRIVATE
    // =========================================================

    // --- DOM references (gán trong init()) ---
    var overlay = null;           // Lớp phủ tối phía sau drawer
    var drawer = null;            // Panel giỏ hàng bên phải
    var btnClose = null;          // Nút X đóng drawer
    var cartItemsContainer = null;// <div> chứa các .cart-item
    var template = null;          // <template id="cart-item-template"> — clone thay vì innerHTML
    var countText = null;         // "3 sản phẩm"
    var subtotalEl = null;        // Tổng tiền hàng
    var totalEl = null;           // Tổng thanh toán
    var dataInput = null;         // Hidden input — chứa JSON cart để PHP đọc khi checkout
    var emptyMsg = null;          // Thông báo "Giỏ hàng trống"
    var btnCheckout = null;       // Nút "Thanh toán"

    // --- Toast notification ---
    var toastEl = null;
    var toastMsg = null;
    var toastClose = null;
    var toastTimeout = null;      // setTimeout ID — dùng để reset khi toast xuất hiện liên tiếp

    // --- State ---
    var cartArr = [];             // Mảng chính: [{maSach, tenSach, giaBan, soLuong, hinhAnh, tacGia}]

    // =========================================================
    // PHẦN 2: KHỞI TẠO
    // =========================================================

    function init() {
        // --- Tìm các phần tử DOM (do PHP đã render sẵn trong formGioHang.php) ---
        overlay            = document.getElementById('cart-overlay');
        drawer             = document.getElementById('cart-drawer');
        btnClose           = document.getElementById('cart-close');
        cartItemsContainer = document.getElementById('cart-items');
        template           = document.getElementById('cart-item-template');
        countText          = document.getElementById('cart-countText');
        subtotalEl         = document.getElementById('cart-subtotal');
        totalEl            = document.getElementById('cart-total');
        dataInput          = document.getElementById('cart-data-input');
        emptyMsg           = document.getElementById('cart-empty-msg');
        btnCheckout        = document.getElementById('btn-checkout');

        toastEl    = document.getElementById('cart-toast');
        toastMsg   = document.getElementById('toast-message');
        toastClose = document.getElementById('toast-close');

        // Guard: nếu trang không có HTML giỏ hàng thì dừng (trang admin, trang đơn hàng...)
        if (!overlay || !drawer) return;

        // -----------------------------------------------------------
        // CHỌN NGUỒN DỮ LIỆU GIỎ HÀNG
        //
        // Nguồn 1 (ưu tiên): cartServerData
        //   - PHP inject vào <head> của index.php:
        //       var cartServerData = <?= $cartServerDataJson ?>;
        //   - Được tạo bởi layGioHangCoGia.php: giá từ DB, tồn kho từ DB
        //   - Chỉ có khi đã đăng nhập ($isLoggedIn = true)
        //
        // Nguồn 2 (fallback): localStorage
        //   - Dùng khi cartServerData = null (chưa đăng nhập)
        //   - Giá lấy từ window.__giaSach (PHP inject từ DB) — không phải data-price
        //   - Dữ liệu tồn cục bộ, mất khi xóa trình duyệt
        // -----------------------------------------------------------
        if (typeof cartServerData !== 'undefined' && cartServerData !== null && Array.isArray(cartServerData)) {
            // ĐÃ ĐĂNG NHẬP: dùng dữ liệu server (đáng tin, giá từ DB)
            cartArr = cartServerData;
        } else {
            // KHÁCH / CHƯA ĐĂNG NHẬP: đọc từ localStorage
            var stored = localStorage.getItem('book_cart');
            if (stored) {
                try {
                    cartArr = JSON.parse(stored);
                    if (!Array.isArray(cartArr)) cartArr = []; // Validate: phải là mảng
                } catch (e) {
                    cartArr = []; // JSON lỗi → giỏ trống
                }
            }
        }

        bindEvents();
        renderCart();       // Vẽ giao diện giỏ hàng ngay
        updateHeaderIcon(); // Cập nhật số badge trên icon giỏ hàng header
    }

    // =========================================================
    // PHẦN 3: GẮN SỰ KIỆN
    // =========================================================

    function bindEvents() {
        // Đóng drawer khi click nút X hoặc click lớp overlay bên ngoài
        btnClose.addEventListener('click', closeCart);
        overlay.addEventListener('click', closeCart);

        // Đóng toast khi click X
        if (toastClose) {
            toastClose.addEventListener('click', function () {
                toastEl.classList.remove('show');
            });
        }

        // Mở drawer khi click icon giỏ hàng trên header
        // btn-cart được render bởi dauTrang.php trong header.php
        var headerCartBtn = document.getElementById('btn-cart');
        if (headerCartBtn) {
            headerCartBtn.addEventListener('click', function (e) {
                e.preventDefault(); // Tránh scroll lên đầu trang nếu là <a href="#">
                openCart();
            });
        }
    }

    // =========================================================
    // PHẦN 4: MỞ / ĐÓNG DRAWER
    // =========================================================

    function openCart() {
        overlay.classList.add('active'); // Hiện lớp tối
        drawer.classList.add('active');  // Trượt drawer vào từ phải

        renderCart(); // Render lại để cập nhật tồn kho mới nhất

        // Khoá scroll body (giống quick view modal)
        // Bù scrollbar width để tránh layout bị giật
        var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.paddingRight = scrollbarWidth + 'px';
        document.body.style.overflow = 'hidden';
    }

    function closeCart() {
        overlay.classList.remove('active');
        drawer.classList.remove('active');

        // Phục hồi scroll
        document.body.style.paddingRight = '';
        document.body.style.overflow = '';
    }

    // =========================================================
    // PHẦN 5: LƯU GIỎ HÀNG (CƠ CHẾ KÉP: localStorage + server sync)
    // =========================================================

    function saveCart() {
        // --- Lớp 1: localStorage (luôn lưu, ngay cả khách chưa đăng nhập) ---
        // Dùng cho: hiển thị khi reload trang, fallback khi session hết
        localStorage.setItem('book_cart', JSON.stringify(cartArr));

        // Cập nhật badge số lượng trên icon header
        updateHeaderIcon();

        // Ghi JSON vào hidden input → PHP đọc khi user submit form checkout
        // (cart.js không redirect, PHP mới redirect khi nhận form checkout)
        if (dataInput) dataInput.value = JSON.stringify(cartArr);

        // --- Lớp 2: Đồng bộ lên server (CHỈ KHI ĐÃ ĐĂNG NHẬP) ---
        // Cơ chế: form ẩn submit vào iframe ẩn → luuGioHang.php
        //   - KHÔng dùng AJAX/fetch (kiến trúc thuần PHP)
        //   - Cart sync chạy ngầm, user không thấy gì
        //   - luuGioHang.php: DELETE old + INSERT new vào bảng GioHang
        //   - Giá (giaBan) trong JSON bị bỏ qua ở server — bảo mật giá
        // dangDangNhap: PHP inject inline <script>const dangDangNhap = true/false</script>
        if (typeof dangDangNhap !== 'undefined' && dangDangNhap) {
            var syncForm = document.getElementById('cart-sync-form'); // <form> ẩn trong formGioHang.php
            var syncJson = document.getElementById('cart-sync-json'); // <input hidden> chứa JSON
            if (syncForm && syncJson) {
                syncJson.value = JSON.stringify(cartArr);
                syncForm.submit(); // Submit vào iframe → luuGioHang.php nhận POST
            }
        }
    }

    // =========================================================
    // PHẦN 6: CẬP NHẬT BADGE HEADER
    // =========================================================

    function updateHeaderIcon() {
        // Tổng số lượng tất cả sản phẩm (không phải số loại sản phẩm)
        var qty = cartArr.reduce(function (sum, item) { return sum + item.soLuong; }, 0);

        var badge = document.getElementById('cart-count'); // Badge số đỏ trên icon giỏ
        if (badge) {
            badge.textContent = qty;
            // Ẩn badge nếu giỏ trống
            badge.classList.toggle('hidden', qty === 0);
            badge.style.display = qty > 0 ? 'flex' : 'none';
        }
    }

    // =========================================================
    // PHẦN 7: TOAST THÔNG BÁO
    // =========================================================

    function showToast(message) {
        if (!toastEl) return;

        // Reset timer cũ nếu toast đang hiện (user thêm liên tục)
        if (toastTimeout) clearTimeout(toastTimeout);

        toastMsg.textContent = message;
        toastEl.classList.add('show'); // CSS transition hiện toast

        // Tự ẩn sau 3 giây
        toastTimeout = setTimeout(function () {
            toastEl.classList.remove('show');
        }, 3000);
    }

    // =========================================================
    // PHẦN 8: TIỆN ÍCH
    // =========================================================

    function formatMoney(n) {
        // Định dạng tiền Việt Nam: 150000 → "150.000"
        return Number(n).toLocaleString('vi-VN');
    }

    // =========================================================
    // PHẦN 9: RENDER GIAO DIỆN GIỎ HÀNG
    // =========================================================

    function renderCart() {
        // --- Xoá các item cũ (giữ lại empty message) ---
        var items = cartItemsContainer.querySelectorAll('.cart-item');
        items.forEach(function (item) { item.remove(); });

        var totalMoney = 0;
        var totalQty   = 0;

        // --- Giỏ trống ---
        if (cartArr.length === 0) {
            emptyMsg.style.display = 'block'; // Hiện thông báo "Giỏ hàng trống"
            btnCheckout.disabled = true;      // Disable nút thanh toán
            countText.textContent = '0 sản phẩm';
            subtotalEl.textContent = '0 ₫';
            totalEl.textContent = '0 ₫';
            saveCart(); // Đảm bảo localStorage và server được đồng bộ về trạng thái rỗng
            return;
        }

        // --- Giỏ có hàng ---
        emptyMsg.style.display = 'none';
        btnCheckout.disabled = false;

        cartArr.forEach(function (item, index) {
            totalMoney += item.giaBan * item.soLuong;
            totalQty   += item.soLuong;

            // -----------------------------------------------------------
            // LẤY TỒN KHO TỪ window.__tonKhoMap
            // __tonKhoMap được PHP inject từ layGioHangCoGia.php:
            //   var __tonKhoMap = <?= $tonKhoMapJson ?>;
            // Ưu tiên __tonKhoMap (realtime từ DB) hơn item.soLuongTon (có thể cũ)
            // Infinity: không biết tồn kho → không giới hạn phía client
            //   (server vẫn kiểm tra khi đặt hàng)
            // -----------------------------------------------------------
            var tonKho = (window.__tonKhoMap && window.__tonKhoMap[item.maSach] !== undefined)
                ? window.__tonKhoMap[item.maSach]
                : (item.soLuongTon !== undefined ? item.soLuongTon : Infinity);

            // --- Clone <template> thay vì innerHTML (an toàn hơn, tránh XSS) ---
            var node = template.content.cloneNode(true); // deep clone

            // --- Điền dữ liệu vào DOM node ---
            node.querySelector('.cart-item-img').src          = item.hinhAnh || '';
            node.querySelector('.cart-item-title').textContent = item.tenSach || '';
            node.querySelector('.cart-item-author').textContent = item.tacGia || '';
            node.querySelector('.cart-item-price').textContent  = formatMoney(item.giaBan) + ' ₫';
            node.querySelector('.qty-value').textContent        = item.soLuong;

            // --- Hiển thị cảnh báo tồn kho ---
            var warnEl  = node.querySelector('.cart-stock-warn');
            var plusBtn = node.querySelector('.qty-plus');

            if (tonKho !== Infinity) {
                if (item.soLuong >= tonKho) {
                    // ĐÃ ĐẠT GIỚI HẠN: disable nút + và hiện cảnh báo đỏ
                    if (warnEl) {
                        warnEl.textContent = '⚠️ Tối đa ' + tonKho + ' cuốn';
                        warnEl.className = 'cart-stock-warn at-limit'; // CSS: màu đỏ
                        warnEl.style.display = 'inline';
                    }
                    if (plusBtn) plusBtn.disabled = true;
                } else if (tonKho <= 5) {
                    // SẮP HẾT: hiện cảnh báo vàng (nhưng vẫn cho thêm)
                    if (warnEl) {
                        warnEl.textContent = '⚠️ Còn ' + tonKho + ' cuốn';
                        warnEl.className = 'cart-stock-warn'; // CSS: màu vàng
                        warnEl.style.display = 'inline';
                    }
                }
            }

            // --- Gắn event listeners cho nút +/- và xoá ---
            // Dùng closure với index để biết item nào đang bị tác động
            node.querySelector('.qty-minus').addEventListener('click', function () {
                changeQty(index, -1); // Giảm 1
            });
            node.querySelector('.qty-plus').addEventListener('click', function () {
                changeQty(index, +1); // Tăng 1 (bị chặn nếu >= tonKho)
            });
            node.querySelector('.cart-item-remove').addEventListener('click', function () {
                removeItem(index); // Xoá khỏi cartArr
            });

            // Thêm node vào DOM
            cartItemsContainer.appendChild(node);
        });

        // --- Cập nhật tổng cộng ---
        countText.textContent = totalQty + ' sản phẩm';
        var txtMoney = formatMoney(totalMoney) + ' ₫';
        subtotalEl.textContent = txtMoney;
        totalEl.textContent    = txtMoney;

        // Lưu sau khi render (đồng bộ localStorage + server)
        saveCart();
    }

    // =========================================================
    // PHẦN 10: THÊM SẢN PHẨM VÀO GIỎ
    // GỌI BỞI: btnThemGioHang.js → cartDrawer.addItem(thongTinSach, 1)
    // =========================================================

    function addItem(thongTin, soLuongThem) {
        // Đọc tồn kho từ __tonKhoMap (PHP inject từ DB)
        // Không tin soLuongTon trong thongTin (có thể cũ)
        var tonKho = (window.__tonKhoMap && window.__tonKhoMap[thongTin.maSach] !== undefined)
            ? window.__tonKhoMap[thongTin.maSach]
            : Infinity;

        // Guard: hết hàng hoàn toàn (btnThemGioHang.js đã alert trước, đây chỉ là bảo vệ thêm)
        if (tonKho !== Infinity && tonKho <= 0) return;

        // Tìm xem sản phẩm đã có trong giỏ chưa
        var foundIndex = -1;
        for (var i = 0; i < cartArr.length; i++) {
            if (cartArr[i].maSach === thongTin.maSach) {
                foundIndex = i;
                break;
            }
        }

        if (foundIndex > -1) {
            // --- SẢN PHẨM ĐÃ CÓ TRONG GIỎ: tăng soLuong ---
            var newQty = cartArr[foundIndex].soLuong + soLuongThem;
            if (newQty > tonKho) {
                // Vượt giới hạn kho: cap lại về tonKho
                cartArr[foundIndex].soLuong = tonKho;
                showToast('⚠️ Chỉ còn ' + tonKho + ' cuốn — đã giới hạn số lượng!');
            } else {
                cartArr[foundIndex].soLuong = newQty;
                showToast('Đã thêm "' + thongTin.tenSach + '" vào giỏ hàng');
            }
        } else {
            // --- SẢN PHẨM MỚI: thêm vào cuối mảng ---
            // Đảm bảo soLuong không vượt tonKho ngay từ đầu
            thongTin.soLuong = (tonKho !== Infinity) ? Math.min(soLuongThem, tonKho) : soLuongThem;
            cartArr.push(thongTin);
            showToast('Đã thêm "' + thongTin.tenSach + '" vào giỏ hàng');
        }

        // Lưu + render (saveCart() bên trong renderCart())
        saveCart();
        renderCart();
        updateHeaderIcon();
    }

    // =========================================================
    // PHẦN 11: THAY ĐỔI SỐ LƯỢNG (+/-)
    // =========================================================

    function changeQty(index, delta) {
        if (!cartArr[index]) return; // Guard: index có thể lỗi nếu render lại

        var ms = cartArr[index].maSach;

        // Đọc tồn kho realtime từ __tonKhoMap
        var tonKho = (window.__tonKhoMap && window.__tonKhoMap[ms] !== undefined)
            ? window.__tonKhoMap[ms]
            : Infinity;

        var newQty = cartArr[index].soLuong + delta;

        if (newQty <= 0) {
            // Nếu giảm về 0 → xoá hẳn khỏi giỏ
            removeItem(index);
        } else if (newQty > tonKho) {
            // Không cho vượt tồn kho (kiểm tra phía client)
            // Server (xuLyThanhToan.php) vẫn kiểm tra lại bằng FOR UPDATE khi đặt hàng
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

    // =========================================================
    // PHẦN 12: XOÁ SẢN PHẨM
    // =========================================================

    function removeItem(index) {
        if (!cartArr[index]) return;
        cartArr.splice(index, 1); // Xoá 1 phần tử tại index
        saveCart();   // Đồng bộ localStorage + server
        renderCart(); // Render lại (nếu cartArr rỗng sẽ hiện empty message)
    }

    // =========================================================
    // PHẦN 13: KHỞI ĐỘNG KHI DOM SẴN SÀNG
    // =========================================================

    // DOMContentLoaded: đợi HTML parse xong (không cần đợi ảnh/CSS load)
    // Lý do: cart.js load ở cuối <body> nên DOM đã sẵn sàng,
    // nhưng dùng event để đảm bảo an toàn
    document.addEventListener('DOMContentLoaded', init);

    // =========================================================
    // PHẦN 14: PUBLIC API — chỉ lộ những gì cần thiết
    // =========================================================

    return {
        open    : openCart,   // Mở drawer — gọi từ btn header hoặc trang khác
        close   : closeCart,  // Đóng drawer
        addItem : addItem,    // Thêm sản phẩm — gọi từ btnThemGioHang.js
        getCart : function () { return cartArr; } // Đọc giỏ hiện tại (readonly reference)
    };

})(); // ← Tự gọi ngay, gán kết quả (public API) vào window.cartDrawer
