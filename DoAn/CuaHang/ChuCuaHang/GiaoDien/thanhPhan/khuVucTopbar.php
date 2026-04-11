<?php
/**
 * khuVucTopbar.php — HTML Topbar khu vực admin
 * Bao gồm: nút hamburger + tiêu đề trang + nút xem storefront + chip user
 * Yêu cầu: $tieuDeHienTai, $tenAdmin đã khai báo
 */
?>
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
