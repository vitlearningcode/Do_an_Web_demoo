<div id="app">
    <div class="top-bar">
        <div class="container">
            <p>Miễn phí vận chuyển cho đơn hàng từ 250.000đ</p>
            <div class="top-bar-links">
                <a href="#">Theo dõi đơn hàng</a>
                <a href="#">Hỗ trợ khách hàng</a>
            </div>
        </div>
    </div>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="logo-text">
                        <h1>BOOK SALES</h1>
                        <p>STOREFRONT</p>
                    </div>
                </div>

                <div class="search-box">
                    <input type="text" placeholder="Tìm kiếm tựa sách, tác giả, nhà xuất bản...">
                    <button>
                        <i class="fas fa-search"></i>
                    </button>
                </div>
<!-- =====================================================================nút đăng nhập =========================================== -->
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
                <li><a href="#" onclick="moCapNhatThongTin(event)"><i class="fas fa-user-edit"></i> Cập nhật thông tin</a></li>
                <li><a href="CuaHang/TrangBanHang/donHang/index.php"><i class="fas fa-box"></i> Theo dõi đơn hàng</a></li>
                <li class="divider"></li>
                <li><a href="javascript:void(0)" onclick="openLogout()" class="text-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </div>
    </div>
<?php else: ?>
                        <button class="action-btn" onclick="openLogin()">
                            <i class="fas fa-user"></i>
                            <span>Đăng nhập</span>
                        </button>
                    <?php endif; ?>
<!-- =============================================================================================== -->
                    <button class="action-btn" id="btn-cart">
                        <div class="cart-icon-wrapper">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count hidden" id="cart-count">0</span>
                        </div>
                        <span>Giỏ hàng</span>
                    </button>
                </div>
            </div>

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
<!-- =====================================================================from đăng nhập ===========================================-->
    <div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>

    <div id="login-modal" class="auth-modal login-size">
        <div class="modal-header">
            <h2>Chào mừng trở lại</h2>
            <p>Đăng nhập để tiếp tục mua sắm và theo dõi đơn hàng</p>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form action="xuly_dangnhap.php" method="POST">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="tendangnhap" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="matkhau" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" name="btn_dangnhap" class="btn-primary">Đăng nhập &rarr;</button>
            </form>
        </div>
        <div class="modal-footer">
            <p>Chưa có tài khoản? <a href="javascript:void(0)" onclick="openRegister()">Đăng ký ngay</a></p>
        </div>
    </div>

    <div id="register-modal" class="auth-modal register-size">
        <div class="modal-header">
            <h2>Tạo tài khoản mới</h2>
            <p>Đăng ký để nhận nhiều ưu đãi hấp dẫn từ Book Sales</p>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form action="xuly_dangky.php" method="POST">
                <div class="input-group">
                    <label>Họ và tên</label>
                    <input type="text" name="hoten" placeholder="Nhập họ và tên" required>
                </div>
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="tendangnhap" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Nhập địa chỉ email" required>
                </div>
                <div class="input-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="sodienthoai" placeholder="Nhập số điện thoại" required>
                </div>
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="matkhau" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" name="btn_dangky" class="btn-primary">Đăng ký &rarr;</button>
            </form>
        </div>
        <div class="modal-footer">
            <p>Đã có tài khoản? <a href="javascript:void(0)" onclick="openLogin()">Đăng nhập</a></p>
        </div>
    </div>

    <div id="logout-modal" class="auth-modal logout-size">
        <div class="modal-header">
            <h2>Xác nhận đăng xuất</h2>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center; margin-top: 15px;">
            <p style="font-size: 15px; color: #333; margin-bottom: 30px;">Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?</p>
            
            <div class="input-group" style="display: flex; justify-content: space-between; width: 343px; margin: 0 auto;">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy bỏ</button>
                <a href="xuly_dangxuat.php" class="btn-confirm">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>