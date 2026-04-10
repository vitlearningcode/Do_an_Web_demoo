// ==================== CẤU TRÚC CHÍNH CỦA TRANG CỬA HÀNG ====================
// Ghi chú: Trang Admin đã có trang riêng (CuaHang/ChuCuaHang/).
//          File này CHỈ phục vụ trang cửa hàng (storefront) — index.php.
// ==================== TRẠNG THÁI HỆ THỐNG (STATE) ====================
let danhSachYeuThich = new Set();

// ==================== KHỞI TẠO ỨNG DỤNG ====================
document.addEventListener("DOMContentLoaded", () => {
  khoiTaoDemNguocKhuyenMai();
  khoiTaoNutGioHang();
  khoiTaoNutDangNhap();

  console.log("✅ Hệ thống JavaScript trang cửa hàng đã khởi tạo (Chế độ PHP SSR)");
});

// ==================== ĐẾM NGƯỢC FLASH SALE ====================
// Ghi chú: Đồng hồ đếm ngược thực từ thời gian KhuyenMai trong DB được
//          PHP gán vào biến ketThucFlashSale rồi JS đếm ngược phía client.
function khoiTaoDemNguocKhuyenMai() {
  // Chỉ chạy nếu có biến thời gian kết thúc từ PHP (xem index.php)
  // Flash sale timer đã được xử lý inline trong index.php
}

// ==================== NÚT GIỎ HÀNG (MENU TRÊN) ====================
function khoiTaoNutGioHang() {
  // cart.js xử lý giỏ hàng (gắn sự kiện cho #btn-cart)
  // Hàm này dự phòng nếu cần logic bổ sung
}

// ==================== NÚT ĐĂNG NHẬP ====================
function khoiTaoNutDangNhap() {
  const nutDangNhap = document.getElementById("btn-login");
  if (nutDangNhap) {
    nutDangNhap.addEventListener("click", () => {
      if (typeof authModal !== "undefined") {
        authModal.mo("dang_nhap");
      }
    });
  }
}

// ============================================
// Đăng nhập / Đăng ký / Đăng xuất — Modal
// ============================================

// Hàm mở form Đăng nhập
function openLogin() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'block';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// Hàm mở form Đăng ký
function openRegister() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'block';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// Hàm mở form Đăng xuất
function openLogout() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'block';
}

// Hàm đóng tất cả các Modal
function closeModal() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'none';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// ===================================================
// Menu dropdown nút đăng nhập
// ===================================================

// Bật/tắt menu dropdown người dùng
function toggleUserMenu(event) {
    event.stopPropagation();
    let menu = document.getElementById('userDropdown');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Đóng menu khi click ra ngoài
document.addEventListener('click', function(event) {
    let menu = document.getElementById('userDropdown');
    let btn  = document.getElementById('btn-user-profile');

    if (menu && menu.classList.contains('show')) {
        if (btn && !btn.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove('show');
        }
    }
});

// ==================== CẬP NHẬT THÔNG TIN ====================
function moCapNhatThongTin(event) {
    if (event) event.preventDefault();
    window.location.href = 'CuaHang/TrangBanHang/taiKhoan/capNhat.php';
}
