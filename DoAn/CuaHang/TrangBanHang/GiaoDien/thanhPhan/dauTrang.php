<?php
/**
 * dauTrang.php — Phần đầu trang cố định
 * Bao gồm: TopBar + Header (logo, tìm kiếm, nút user/giỏ hàng) + Nav danh mục
 * Yêu cầu: $isLoggedIn (bool), $duong_dan_goc (string) đã được khai báo
 */
?>
<div id="dau-trang-co-dinh">
<div id="app">
    <!-- ── Thanh trên cùng ── -->
    <div class="top-bar">
        <div class="container">
            <marquee width="100%" behavior="alternate" scrollamount="2">
                <p style="font-size: 11pt;">Sách không tự mất đi, nó chỉ chuyển từ chỗ này sang chỗ khác (nếu chúng tôi có)</p>
            </marquee>
            <div class="top-bar-links">
                <a href="javascript:void(0)" onclick="moTraCuuDonHang()">
                    <i class="fas fa-box-open" style="margin-right:4px;"></i>Theo dõi đơn hàng
                </a>
                <a href="javascript:void(0)" onclick="moHoTro()">
                    <i class="fas fa-headset" style="margin-right:4px;"></i>Hỗ trợ khách hàng
                </a>
            </div>
        </div>
    </div>

    <!-- ── Header chính ── -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="logo-text">
                        <a href="<?= $duong_dan_goc ?>index.php" style="text-decoration:none; color:inherit;"><h1>BOOK SALES</h1></a>
                        <p>STOREFRONT</p>
                    </div>
                </div>

                <!-- Ô tìm kiếm nhanh -->
                <div class="search-box" id="khung-tim-kiem" style="position: relative;">
                    <input type="text" id="o-nhap-tu-khoa" placeholder="Tìm kiếm tựa sách, tác giả..." onkeyup="timKiemNhanh(this.value)" autocomplete="off">
                    <button>
                        <i class="fas fa-search"></i>
                    </button>
                    <div id="danh-sach-ket-qua" class="khung-goi-y-tim-kiem" style="display: none;"></div>
                </div>

                <!-- Nút hành động: đăng nhập / user / giỏ hàng -->
                <div class="header-actions">
                    <?php if ($isLoggedIn): ?>
                    <div class="user-dropdown-container">
                        <button class="action-btn profile-ring-btn" id="btn-user-profile" onclick="toggleUserMenu(event)">
                            <div class="profile-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['ten_nguoi_dung'] ?? 'Tài khoản') ?></span>
                        </button>

                        <div class="user-dropdown-menu" id="userDropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar"><i class="fas fa-user"></i></div>
                                <div class="dropdown-user-info">
                                    <strong><?= htmlspecialchars($_SESSION['ten_nguoi_dung'] ?? 'Khách hàng') ?></strong>
                                    <p><?= htmlspecialchars($_SESSION['vaitro'] ?? 'Khách hàng') ?></p>
                                </div>
                            </div>
                            <ul class="dropdown-list">
                                <li><a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/taiKhoan/capNhat.php"><i class="fas fa-user-edit"></i> Sửa thông tin</a></li>
                                <li><a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/donHang/index.php"><i class="fas fa-box"></i> Theo dõi đơn hàng</a></li>
                                <li>
                                    <a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/taiKhoan/sachYeuThich.php">
                                        <i class="fas fa-heart" style="color: #ef4444;"></i> Sách yêu thích
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onclick="openLogout()" class="text-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <button class="action-btn" onclick="openLogin()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Đăng nhập</span>
                    </button>
                    <?php endif; ?>

                    <!-- Nút giỏ hàng -->
                    <button class="action-btn" id="btn-cart">
                        <div class="cart-icon-wrapper">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count hidden" id="cart-count">0</span>
                        </div>
                        <span>Giỏ hàng</span>
                    </button>
                </div>
            </div>

            <!-- Nav danh mục -->
            <nav class="categories-nav">
                <button class="category-btn">
                    <i class="fas fa-bars"></i>
                    Danh mục sách
                </button>
                <a href="#">Sách Mới</a>
                <a href="#">Bán Chạy</a>
                <a href="#">Văn Học</a>
                <a href="#">Kinh Tế</a>
                <a href="#">Tâm Lý - Kỹ Năng</a>
                <a href="#">Thiếu Nhi</a>
                <a href="#" class="promo-link">Khuyến Mãi</a>
            </nav>
        </div>
    </header>
</div><!-- /app -->
</div><!-- /dau-trang-co-dinh -->
