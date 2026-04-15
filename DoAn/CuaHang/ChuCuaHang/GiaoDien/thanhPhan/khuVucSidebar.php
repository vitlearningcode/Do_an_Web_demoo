<?php
/**
 * khuVucSidebar.php — HTML Sidebar admin: logo + nav items + link thoát
 * Yêu cầu: $trangHienTai, $soDonChoDuyet, $adminUrl đã khai báo
 */
?>
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
            <a href="<?= $adminUrl ?>?trang=nhaCungCap"
               class="adm-nav-item<?= navActive('nhaCungCap', $trangHienTai) ?>">
                <i class="fas fa-handshake"></i>
                <span>Nhà cung cấp</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=khuyenMai"
               class="adm-nav-item<?= navActive('khuyenMai', $trangHienTai) ?>">
                <i class="fas fa-tags"></i>
                <span>Khuyến mãi</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=baoCaoDoanhThu"
               class="adm-nav-item<?= navActive('baoCaoDoanhThu', $trangHienTai) ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Báo cáo DT</span>
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

    <!-- Footer sidebar -->
    <div class="adm-sidebar-footer">
        <a href="../../index.php" class="adm-nav-item" title="Xem trang bán hàng">
            <i class="fas fa-store"></i>
            <span>Trang bán hàng</span>
        </a>
        <a href="../../CuaHang/PhienDangNhap/xuly_dangxuat.php" class="adm-nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>
