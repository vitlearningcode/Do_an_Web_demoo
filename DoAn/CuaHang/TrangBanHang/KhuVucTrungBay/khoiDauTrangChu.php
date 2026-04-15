<?php
/**
 * ============================================================
 * LUỒNG: KHỚI ĐẦU TRANG CHỦ (Chạy ngay sau session_start và require db)
 *
 * Gọi bởi: index.php (require_once sau require db.php)
 *
 * XÁC ĐỊNH:
 *   $isLoggedIn  (bool) — dùng để quyết định có load layGioHangCoGia.php không
 *   $phai_xoa_cart (bool) — nếu true, index.php sẽ hiển thị và dỏ hàng localStorage
 *
 * CUỐI TRANG: index.php dùng $isLoggedIn và $phai_xoa_cart để:
 *   - Load layGioHangCoGia.php cho user đăng nhập
 *   - Inject <script>localStorage.removeItem('book_cart')</script> nếu cần
 *
 * LUỒNG COOKIE XÓA CART (Sau thanh toán hoặc đăng xuất):
 *   xuLyThanhToan.php / xuly_dangxuat.php
 *     → $_SESSION['xoa_cart_local'] = true
 *     → setcookie('xoa_cart_local', '1', ...)
 *   index.php → khoiDauTrangChu.php phát hiện
 *     → $phai_xoa_cart = true
 *     → unset session flag + xóa cookie
 *   index.php render (dòng 50-52):
 *     → <script>localStorage.removeItem('book_cart')</script>
 * ============================================================
 */

// Kiểm tra đăng nhập: chỉ cần SESSION['nguoi_dung_id'] tồn tại
// (không query lại DB — tạm tin session, giá và kho sẽ xác thực ở các bước sau)
$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Kiểm tra cờ xóa cart: tồn tại trong session (set bởi xuẤt logout)
// hoặc trong cookie (set bởi logout/thanh toán xong — vì sau redirect session bị destroy)
$phai_xoa_cart = !empty($_SESSION['xoa_cart_local']) || !empty($_COOKIE['xoa_cart_local']);

// Xóa flag sau khi đã xác định (tránh xóa cart nhiều lần)
if (!empty($_SESSION['xoa_cart_local']))  unset($_SESSION['xoa_cart_local']);
if (!empty($_COOKIE['xoa_cart_local']))   setcookie('xoa_cart_local', '', time() - 1, '/'); // Xóa cookie: đặt thời gian hết hạn trong quá khứ
