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
                    <!-- fas fa-box-open: Class của thư viện FontAwesome (Giữ nguyên tiếng Anh) -->
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
                        <!-- fas fa-book-open: Class của thư viện FontAwesome -->
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
                        <!-- fas fa-search: Class của thư viện FontAwesome -->
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
                                <!-- fas fa-user: Class của thư viện FontAwesome -->
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
                                <li><a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/donHang/theoDoiDonHang.php"><i class="fas fa-box"></i> Theo dõi đơn hàng</a></li>
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
                            <!-- fas fa-shopping-cart: Class của thư viện FontAwesome -->
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count hidden" id="cart-count">0</span>
                        </div>
                        <span>Giỏ hàng</span>
                    </button>
                </div>
            </div>
<!-- ===========================================================================danh mục trượt============================================== -->
            <nav class="categories-nav">
                
                <div class="category-dropdown-wrapper" style="position: relative;">
                    <button class="category-btn" id="nut-danh-muc-bay" style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20" fill="currentColor">
                            <path d="M34.283,384c17.646,30.626,56.779,41.148,87.405,23.502c0.021-0.012,0.041-0.024,0.062-0.036l9.493-5.483 c17.92,15.332,38.518,27.222,60.757,35.072V448c0,35.346,28.654,64,64,64s64-28.654,64-64v-10.944 c22.242-7.863,42.841-19.767,60.757-35.115l9.536,5.504c30.633,17.673,69.794,7.167,87.467-23.467 c17.673-30.633,7.167-69.794-23.467-87.467l0,0l-9.472-5.461c4.264-23.201,4.264-46.985,0-70.187l9.472-5.461 c30.633-17.673,41.14-56.833,23.467-87.467c-17.673-30.633-56.833-41.14-87.467-23.467l-9.493,5.483 C362.862,94.638,342.25,82.77,320,74.944V64c0-35.346-28.654-64-64-64s-64,28.654-64,64v10.944 c-22.242,7.863-42.841,19.767-60.757,35.115l-9.536-5.525C91.073,86.86,51.913,97.367,34.24,128s-7.167,69.794,23.467,87.467l0,0 l9.472,5.461c-4.264,23.201-4.264,46.985,0,70.187l-9.472,5.461C27.158,314.296,16.686,353.38,34.283,384z M256,170.667 c47.128,0,85.333,38.205,85.333,85.333S303.128,341.333,256,341.333S170.667,303.128,170.667,256S208.872,170.667,256,170.667z"/>
                        </svg>
                        Danh mục sách |
                    </button>

                    <div id="menu-bay-the-loai" class="fly-menu-container">
                        <ul class="fly-menu-list">
                            <?php
                            try {
                                // Gọi DB lấy danh sách thể loại
                                $stmtTheLoai = $pdo->query("SELECT maTL, tenTL FROM TheLoai ORDER BY tenTL ASC");
                                $dsTheLoai = $stmtTheLoai->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($dsTheLoai as $index => $tl) {
                                    $delay = ($index * 0.05) . 's';
                                    // Gắn link trỏ về trang trangTheLoai.php nằm trong thư mục GiaoDien
                                    $linkLoc = $duong_dan_goc . "CuaHang/TrangBanHang/GiaoDien/trangTheLoai.php?theloai=" . urlencode($tl['tenTL']);
                                    
                                    echo "<li style='transition-delay: {$delay}'>
                                            <a href='{$linkLoc}'>" . htmlspecialchars($tl['tenTL']) . "</a>
                                          </li>";
                                }
                            } catch (Exception $e) {
                                echo "<li><a href='#'>Lỗi tải danh mục</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div> 
            </nav>
        </div>
    </header>
</div><!-- /app -->
</div><!-- /dau-trang-co-dinh -->
