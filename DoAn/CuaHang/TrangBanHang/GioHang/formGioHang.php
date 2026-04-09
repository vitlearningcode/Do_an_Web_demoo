<div id="cart-overlay" class="cart-overlay"></div>

<div id="cart-drawer" class="cart-drawer">
    <!-- Header -->
    <div class="cart-header">
        <div class="cart-title">
            <div class="cart-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>
            </div>
            <div>
                <h2>Giỏ hàng</h2>
                <p id="cart-countText">0 sản phẩm</p>
            </div>
        </div>
        <button id="cart-close" class="cart-close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
        </button>
    </div>

    <!-- Body (Items List) bọc trong Form -->
    <form action="CuaHang/TrangBanHang/ThanhToan/thanhToan.php" method="POST" id="cart-form" class="cart-form">
        <!-- Dữ liệu ẩn chứa JSON giỏ hàng để mang sang thanh toán -->
        <input type="hidden" name="cart_data" id="cart-data-input" value="">
        
        <div id="cart-items" class="cart-items">
            <!-- JS cloneNode từ template xuống đây -->
            <div id="cart-empty-msg" class="cart-empty-msg" style="display: none;">
                Giỏ hàng của bạn đang trống!
            </div>
        </div>

        <!-- Footer -->
        <div class="cart-footer">
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span class="font-medium" id="cart-subtotal">0 ₫</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span class="text-green">Miễn phí</span>
                </div>
                <div class="summary-row total-row">
                    <span>Tổng cộng</span>
                    <span class="text-orange" id="cart-total">0 ₫</span>
                </div>
            </div>
            <button type="submit" class="checkout-btn" id="btn-checkout">
                Tiến hành thanh toán
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </button>
        </div>
    </form>
</div>

<!-- Template cho cart item (Không chèn HTML vào JS) -->
<template id="cart-item-template">
    <div class="cart-item group">
        <div class="cart-item-img-wrapper">
            <img class="cart-item-img" src="" alt="" referrerpolicy="no-referrer">
        </div>
        <div class="cart-item-info">
            <h4 class="cart-item-title"></h4>
            <p class="cart-item-author"></p>
            <div class="cart-item-bottom">
                <div>
                    <span class="cart-item-price"></span>
                </div>
                <div class="cart-item-qty">
                    <button type="button" class="qty-btn qty-minus">-</button>
                    <span class="qty-value">1</span>
                    <button type="button" class="qty-btn qty-plus">+</button>
                </div>
            </div>
        </div>
        <button type="button" class="cart-item-remove" title="Xóa sản phẩm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </button>
    </div>
</template>

<!-- Toast Notification -->
<div id="cart-toast" class="cart-toast">
    <div class="toast-content">
        <div class="toast-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335"></path><path d="m9 11 3 3L22 4"></path></svg>
        </div>
        <span id="toast-message" class="toast-message">Đã thêm vào giỏ hàng</span>
        <button id="toast-close" class="toast-close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
        </button>
    </div>
</div>
