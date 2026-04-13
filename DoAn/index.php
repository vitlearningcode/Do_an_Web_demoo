<?php
/**
 * index.php — Trang chủ cửa hàng (Entry Point)
 * File chỉ còn: session_start, require DB, load dữ liệu, render layout.
 * Mọi chức năng được tách thành file riêng trong KhuVucTrungBay/.
 */
session_start();
require_once "KetNoi/config/db.php";

// ── Tải các thành phần dữ liệu ─────────────────────────────────────────────
require_once "CuaHang/TrangBanHang/GiaoDien/thanhPhan/bookCard.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiFlashSale.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiSachBanChay.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiSachMoi.php";
require_once "CuaHang/TrangBanHang/LoadDanhMuc/taiDanhSach_DanhMuc.php";
require_once "CuaHang/TrangBanHang/LoadDuLieu/taiQuangCao.php";

// ── Khởi tạo: kiểm tra đăng nhập + xử lý cờ xóa cart ─────────────────────
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khoiDauTrangChu.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sales Management - Cửa hàng sách trực tuyến</title>
    <link rel="stylesheet" href="GiaoDien/style.css">
    <link rel="stylesheet" href="GiaoDien/xemNhanhSach.css">
    <link rel="stylesheet" href="GiaoDien/gioHang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <?php if ($isLoggedIn): ?>
    <script>
        // PHP render giỏ hàng từ Session vào biến JS (không AJAX, không fetch)
        var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <?php else: ?>
    <script>var cartServerData = null;</script>
    <?php endif; ?>
    <?php if ($phai_xoa_cart): ?>
    <script>localStorage.removeItem('book_cart');</script>
    <?php endif; ?>
</head>
<body>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/header.php"; ?>

<main class="main-content container">
<div id="home-content">

    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucHeroBanner.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucFlashSale.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucDanhMuc.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachBanChay.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachMoi.php"; ?>

</div>
</main>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/footer.php"; ?>

<?php include_once "CuaHang/TrangBanHang/ChiTietSach/formXemNhanhSach.php"; ?>
<?php include_once "CuaHang/TrangBanHang/GioHang/formGioHang.php"; ?>

<script src="PhuongThuc/components/thongBao.js"></script>
<script src="PhuongThuc/trinhChieuBanner.js"></script>
<script src="PhuongThuc/components/bookCard.js"></script>
<script src="PhuongThuc/cart.js"></script>
<script src="PhuongThuc/components/xacThuc.js"></script>
<script src="PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="PhuongThuc/components/chatbot.js"></script>
<script src="PhuongThuc/btnDanhMuc.js"></script>
<script src="PhuongThuc/btnThemGioHang.js"></script>
<script src="PhuongThuc/app.js"></script>
<script src="PhuongThuc/xemNhanhSach.js"></script>

<?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/scriptTrangChu.php"; ?>

</body>
</html>
