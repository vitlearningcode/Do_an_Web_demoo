<?php
// ── header.php — Sidebar + Topbar cho trang Admin (ChuCuaHang)
// Yêu cầu: $trangHienTai (string) được truyền từ index.php
//           $tenAdmin      (string) tên người dùng admin
// ──────────────────────────────────────────────────────────────
$trangHienTai = $trangHienTai ?? 'tongQuan';
$tenAdmin     = $tenAdmin     ?? 'Chủ cửa hàng';

// Map trang → tiêu đề topbar
$tieuDeBanDo = [
    'tongQuan'      => 'Tổng quan',
    'donHang'       => 'Quản lý đơn hàng',
    'sachVaTonKho'  => 'Sách & Tồn kho',
    'nhapHang'      => 'Nhập hàng & Công nợ',
    'khuyenMai'     => 'Khuyến mãi',
    'taiKhoan'      => 'Tài khoản',
];
$tieuDeHienTai = $tieuDeBanDo[$trangHienTai] ?? 'Quản trị';

// Helper: class active cho nav item
function navActive(string $trang, string $hienTai): string {
    return $trang === $hienTai ? ' active' : '';
}

// URL gốc admin
$adminUrl = 'index.php';

// Đếm đơn ChoDuyet để hiện badge
$soDonChoDuyet = 0;
if (isset($pdo)) {
    try {
        $stmtBadge = $pdo->query("SELECT COUNT(*) FROM DonHang WHERE trangThai = 'ChoDuyet'");
        $soDonChoDuyet = (int)$stmtBadge->fetchColumn();
    } catch (Throwable $e) {
        $soDonChoDuyet = 0;
    }
}
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

<!-- ===========================
     SIDEBAR
=========================== -->
<aside class="adm-sidebar" id="adm-sidebar">

    <!-- Logo -->
    <div class="adm-sidebar-logo">
        <div class="logo-icon"><i class="fas fa-book-open"></i></div>
        <div class="logo-text">
            <h2>BOOK SALES</h2>
            <p>Admin Panel</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="adm-sidebar-nav">

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Tổng quan</p>
            <a href="<?= $adminUrl ?>?trang=tongQuan"
               class="adm-nav-item<?= navActive('tongQuan', $trangHienTai) ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Tổng quan</span>
            </a>
        </div>

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Kinh doanh</p>
            <a href="<?= $adminUrl ?>?trang=donHang"
               class="adm-nav-item<?= navActive('donHang', $trangHienTai) ?>">
                <i class="fas fa-shopping-bag"></i>
                <span>Đơn hàng</span>
                <?php if ($soDonChoDuyet > 0): ?>
                    <span class="adm-nav-badge"><?= $soDonChoDuyet ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $adminUrl ?>?trang=sachVaTonKho"
               class="adm-nav-item<?= navActive('sachVaTonKho', $trangHienTai) ?>">
                <i class="fas fa-book"></i>
                <span>Sách & Tồn kho</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=nhapHang"
               class="adm-nav-item<?= navActive('nhapHang', $trangHienTai) ?>">
                <i class="fas fa-truck"></i>
                <span>Nhập hàng</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=khuyenMai"
               class="adm-nav-item<?= navActive('khuyenMai', $trangHienTai) ?>">
                <i class="fas fa-tags"></i>
                <span>Khuyến mãi</span>
            </a>
        </div>

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Hệ thống</p>
            <a href="<?= $adminUrl ?>?trang=taiKhoan"
               class="adm-nav-item<?= navActive('taiKhoan', $trangHienTai) ?>">
                <i class="fas fa-users"></i>
                <span>Tài khoản</span>
            </a>
        </div>

    </nav>

    <!-- Footer -->
    <div class="adm-sidebar-footer">
        <a href="../../index.php" class="adm-nav-item" title="Xem trang bán hàng">
            <i class="fas fa-store"></i>
            <span>Trang bán hàng</span>
        </a>
        <a href="../../xuly_dangxuat.php" class="adm-nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>

<!-- ===========================
     MAIN WRAPPER
=========================== -->
<div class="adm-main">

    <!-- TOPBAR -->
    <header class="adm-topbar">
        <div class="adm-topbar-left">
            <!-- Hamburger (responsive) -->
            <button class="adm-topbar-btn" id="adm-menu-toggle" title="Menu">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="adm-page-title"><?= htmlspecialchars($tieuDeHienTai) ?></h1>
            </div>
        </div>
        <div class="adm-topbar-right">
            <!-- Nút xem trang bán hàng -->
            <a href="../../index.php" class="adm-topbar-btn" title="Xem trang bán hàng" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <!-- User chip -->
            <div class="adm-user-chip">
                <div class="adm-user-avatar"><i class="fas fa-user-shield"></i></div>
                <div>
                    <div class="adm-user-name"><?= htmlspecialchars($tenAdmin) ?></div>
                    <div class="adm-user-role">Chủ cửa hàng</div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT: được inject từ TrangQuanLy/*.php -->
    <main class="adm-content">
