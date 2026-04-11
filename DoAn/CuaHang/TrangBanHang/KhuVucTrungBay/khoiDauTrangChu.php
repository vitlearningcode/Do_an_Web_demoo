<?php
/**
 * khoiDauTrangChu.php — Khởi tạo trang chủ: session, DB, kiểm tra đăng nhập, xóa cart cookie
 * Output: $isLoggedIn (bool), $phai_xoa_cart (bool)
 * Yêu cầu: session_start() và require db đã gọi ở index.php
 * (File này được gọi SAU session_start và require db)
 */

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Xóa cờ session từ thanh toán thành công (để JS xóa localStorage)
$phai_xoa_cart = !empty($_SESSION['xoa_cart_local']) || !empty($_COOKIE['xoa_cart_local']);
if (!empty($_SESSION['xoa_cart_local']))  unset($_SESSION['xoa_cart_local']);
if (!empty($_COOKIE['xoa_cart_local']))   setcookie('xoa_cart_local', '', time() - 1, '/');
