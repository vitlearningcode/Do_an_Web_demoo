<?php
/**
 * donHang/index.php — Trang theo dõi đơn hàng (Entry Point)
 * Tabs: Tất cả | Chờ Duyệt | Đang Giao | Đã Giao | Đã Hủy | Đánh Giá
 * Thuần PHP: không AJAX, không chèn HTML từ JS.
 * Mỗi chức năng được tách thành file riêng và gọi qua require_once.
 */
session_start();
require_once '../../../KetNoi/config/db.php';
require_once 'hamHoTroDonHang.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Bắt buộc phải đăng nhập
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND = (int)$_SESSION['nguoi_dung_id'];

// Bản đồ tab → trạng thái DB
$trangThaiMap = [
    'tat-ca'     => null,
    'cho-duyet'  => 'ChoDuyet',
    'dang-giao'  => 'DangGiao',
    'da-giao'    => 'HoanThanh',
    'da-huy'     => 'DaHuy',
    'danh-gia'   => 'HoanThanh', // Chỉ đơn HoanThanh mới có thể đánh giá
];

$tabHienTai = $_GET['tab'] ?? 'tat-ca';
if (!array_key_exists($tabHienTai, $trangThaiMap)) {
    $tabHienTai = 'tat-ca';
}

// Tải dữ liệu đơn hàng từ DB
require_once 'layDuLieuDonHang.php';

// Lọc riêng cho tab "Đánh giá"
require_once 'locTabDanhGia.php';

// Thông báo sau khi đánh giá thành công
$thongBaoSauDanhGia = $_GET['tb'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo Dõi Đơn Hàng - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="../../../GiaoDien/donHang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = true;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
</head>
<body>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="dh-page">
<div class="dh-container">

    <a href="../../../index.php" class="dh-back-link">
        <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
    </a>

    <h1 class="dh-title"><i class="fas fa-box"></i> Đơn Hàng Của Tôi</h1>

    <?php require_once 'khuVucTabDonHang.php'; ?>
    <?php require_once 'danhSachTheDonHang.php'; ?>

</div><!-- /dh-container -->
</div><!-- /dh-page -->


<?php require_once 'modalDanhGiaSanPham.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>

<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>

<?php require_once 'scriptDonHang.php'; ?>

</body>
</html>
