<?php
/**
 * ============================================================
 * LUỒNG ĐĂNG XUẤT
 *
 * GọI BỚI: Link/button "Đăng xuất" trong header hoặc modal xác nhận
 *   (xacNhanDangXuat.js → submit form action="xuly_dangxuat.php")
 *
 * QUY TRÌNH:
 *   1. Đặt cookie 'xoa_cart_local' → JS phía client sẽ xóa localStorage
 *   2. Xóa toàn bộ session (session_unset + session_destroy)
 *   3. Redirect về index.php
 *   4. khoiDauTrangChu.php phát hiện cookie → PHP render <script>localStorage.removeItem(...)</script>
 *
 * TẠI SAO CẦN COOKIE thay vì chỉ session?
 *   Vì session bị destroy trước khi redirect,
 *   sau redirect session đã mất nên không đọc được flag từ session nữa.
 *   Cookie tồn tại độc lập với session nên vẫn đọc được sau redirect.
 * ============================================================
 */

// Bắc buộc gọi trước output — cần để truy cập session hiện tại
session_start();

// BƯỚC 1: Đặt cookie thông báo cho trang tiếp theo (khoiDauTrangChu.php)
// Max-age = 60 giây: đủ để index.php đọc và xử lý sau redirect
// '/' → cookie áp dụng cho toàn bộ domain
setcookie('xoa_cart_local', '1', time() + 60, '/');

// BƯỚC 2: Xóa toàn bộ dữ liệu session trên server và đủ để cookie PHPSESSID
session_unset();   // Xóa tất cả biến trong $_SESSION
session_destroy(); // Huỷ phiên trên server, kất thúc SESSION cookie

// BƯỚC 3: Redirect về trang chủ
// Dòng tiếp theo trong khoiDauTrangChu.php:
//   $phai_xoa_cart = !empty($_COOKIE['xoa_cart_local'])
//   → index.php render: <script>localStorage.removeItem('book_cart')</script>
//   → Cookie 'xoa_cart_local' bị xóa (setcookie với time()-1)
header("Location: ../../index.php");
exit(); // Dừng script, không chạy gì thêm
?>