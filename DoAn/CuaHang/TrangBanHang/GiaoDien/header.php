<?php
/**
 * header.php — Đầu trang cửa hàng (TrangBanHang)
 * File này chỉ khai báo biến và gọi require_once các thành phần con.
 * Yêu cầu: $isLoggedIn (bool) đã khai báo trước khi include file này.
 */

// Đường dẫn gốc dùng cho mọi link trong header
$duong_dan_goc = '/DoAn-Web/DoAn/';
?>
<script>
    // Khai báo biến toàn cục cho JavaScript
    const DUONG_DAN_GOC_JS = '<?= $duong_dan_goc ?>';
</script>
<link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/gioHang.css">
<link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/xemNhanhSach.css">

<?php require_once __DIR__ . '/thanhPhan/dauTrang.php'; ?>
<?php require_once __DIR__ . '/thanhPhan/panelTraCuuDonHang.php'; ?>
<?php require_once __DIR__ . '/thanhPhan/panelHoTroKhachHang.php'; ?>
<?php require_once __DIR__ . '/thanhPhan/modalDangNhap.php'; ?>
<?php require_once __DIR__ . '/thanhPhan/scriptDauTrang.php'; ?>