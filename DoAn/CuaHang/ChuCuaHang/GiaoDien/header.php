<?php
/**
 * header.php — Phần đầu trang Admin (ChuCuaHang)
 * File này chỉ khai báo biến + helper và gọi require_once các thành phần con.
 * Yêu cầu: $trangHienTai, $tenAdmin, $pdo đã khai báo từ index.php
 */

$trangHienTai = $trangHienTai ?? 'tongQuan';
$tenAdmin     = $tenAdmin     ?? 'Chủ cửa hàng';

// Bản đồ trang → tiêu đề topbar
$banDoTieuDe = [
    'tongQuan'      => 'Tổng quan',
    'donHang'       => 'Quản lý đơn hàng',
    'sachVaTonKho'  => 'Sách & Tồn kho',
    'nhapHang'      => 'Nhập hàng & Công nợ',
    'nhaCungCap'    => 'Nhà cung cấp',
    'khuyenMai'     => 'Khuyến mãi',
    'taiKhoan'      => 'Tài khoản',
];
$tieuDeHienTai = $banDoTieuDe[$trangHienTai] ?? 'Quản trị';

// Helper: trả về class 'active' nếu đang ở trang tương ứng
function navActive(string $trang, string $hienTai): string {
    return $trang === $hienTai ? ' active' : '';
}

// URL gốc admin
$adminUrl = 'index.php';

// Đếm đơn chờ duyệt
require_once __DIR__ . '/thanhPhan/demDonChoDuyet.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tieuDeHienTai) ?> — Admin | Book Sales</title>
    <!-- Design tokens dùng chung với TrangBanHang -->
    <link rel="stylesheet" href="../../GiaoDien/base.css">
    <!-- Style riêng admin panel -->
    <link rel="stylesheet" href="GiaoDien/admin_panel.css">
    <!-- Font Awesome + Google Font Inter -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . '/thanhPhan/khuVucSidebar.php'; ?>
<?php require_once __DIR__ . '/thanhPhan/khuVucTopbar.php'; ?>
