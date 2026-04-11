<?php
/**
 * ThanhToan/thanhToan.php — Trang thanh toán (Entry Point)
 * Thuần PHP — không AJAX, không JSON.
 * Mỗi chức năng tách thành file riêng, gọi qua require_once.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Kiểm tra giỏ hàng + tính tổng tiền
require_once 'kiemTraGioHang.php';

// Lấy thông tin KH + địa chỉ (nếu đã đăng nhập)
require_once 'layThongTinKhachHang.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến Hành Thanh Toán - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="thanhToan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php require_once 'cssThanhToanInline.php'; ?>
</head>
<body class="bg-gray-50 checkout-body">

<div class="checkout-container">
    <header class="checkout-header">
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>
        <h2>Bảo Mật Thanh Toán Cửa Hàng Sách</h2>
    </header>

    <div class="checkout-content">
        <!-- Cột form thông tin + phương thức thanh toán -->
        <div class="checkout-info">
            <form action="xuLyThanhToan.php" method="POST" id="form-thanh-toan">
                <?php require_once 'formThongTinNhanHang.php'; ?>
                <?php require_once 'formPhuongThucThanhToan.php'; ?>
            </form>
        </div>

        <!-- Cột tổng kết đơn hàng -->
        <?php require_once 'tomTatGioHang.php'; ?>
    </div>
</div>

<?php require_once 'scriptThanhToan.php'; ?>

</body>
</html>
