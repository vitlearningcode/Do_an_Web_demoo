<?php
/**
 * kiemTraGioHang.php — Kiểm tra giỏ hàng hợp lệ + tính tổng tiền
 * Output: $gioHang (array), $tongTien (int)
 * Nếu giỏ trống → redirect và thoát
 */

$gioHang = [];
if (!empty($_SESSION['cart'])) {
    $gioHang = $_SESSION['cart'];
} elseif (!empty($_SESSION['cart_temp'])) {
    $gioHang = $_SESSION['cart_temp'];
}

if (empty($gioHang)) {
    // Dùng đường dẫn tuyệt đối để JS redirect đúng từ mọi thư mục
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// Lưu tạm vào session để giữ qua trang QR
$_SESSION['cart_temp'] = $gioHang;

$tongTien = 0;
foreach ($gioHang as $sanPham) {
    if (isset($sanPham['soLuong'], $sanPham['giaBan'])) {
        $tongTien += $sanPham['giaBan'] * $sanPham['soLuong'];
    }
}
