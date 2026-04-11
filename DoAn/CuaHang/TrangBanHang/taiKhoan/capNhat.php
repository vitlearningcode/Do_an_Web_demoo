<?php
/**
 * taiKhoan/capNhat.php — Trang cập nhật thông tin tài khoản (Entry Point)
 * Thuần PHP form POST — không AJAX.
 * Mỗi chức năng tách thành file riêng, gọi qua require_once.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND         = (int)$_SESSION['nguoi_dung_id'];
$thongBao     = '';
$loaiThongBao = ''; // success | error

// Xử lý POST thông tin cá nhân + đổi mật khẩu
require_once 'xuLyCapNhatThongTin.php';

// Xử lý POST địa chỉ giao hàng
require_once 'xuLyDiaChi.php';

// Lấy dữ liệu hiện tại từ DB
require_once 'layThongTinTaiKhoan.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Thông Tin - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = true;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <?php require_once 'cssDanhSachTaiKhoan.php'; ?>
</head>
<body>
<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="cn-trang">
    <a href="../../../index.php" class="cn-quay-lai"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>

    <?php if ($thongBao): ?>
    <div class="cn-thong-bao <?= $loaiThongBao ?>">
        <i class="fas fa-<?= $loaiThongBao === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($thongBao) ?>
    </div>
    <?php endif; ?>

    <!-- Thông tin cá nhân + đổi mật khẩu -->
    <?php require_once 'formThongTinCaNhan.php'; ?>

    <!-- Địa chỉ giao hàng -->
    <div class="cn-the">
        <p class="cn-tieu-muc"><i class="fas fa-map-marker-alt"></i> Địa Chỉ Giao Hàng</p>
        <p style="font-size:.85rem;color:#6b7280;margin:-10px 0 18px;">Địa chỉ mặc định sẽ được điền sẵn khi thanh toán.</p>
        <?php require_once 'danhSachDiaChi.php'; ?>
        <?php require_once 'formThemDiaChi.php'; ?>
    </div>
</div>

<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>
<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>
<script>
function toggleUserMenu(e) {
    e.stopPropagation();
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.toggle('show');
}
document.addEventListener('click', function() {
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.remove('show');
});
</script>

<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>
</body>
</html>
