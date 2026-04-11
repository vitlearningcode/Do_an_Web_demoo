<?php
// Khai báo đường dẫn gốc của dự án để dùng cho mọi link trong header
// Đảm bảo không bị lỗi đường dẫn khi ở các trang con
$duong_dan_goc = '/DoAn-Web/DoAn/'; 
?>
<script>
    // Khai báo biến toàn cục cho JavaScript để sửa lỗi sai đường dẫn fetch
    const DUONG_DAN_GOC_JS = '<?= $duong_dan_goc ?>';
</script>
<link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/gioHang.css">
<link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/xemNhanhSach.css">


<div id="dau-trang-co-dinh">
<div id="app">
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

    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="logo-text">
                        <a href="<?= $duong_dan_goc ?>index.php" style="text-decoration:none; color:inherit;"><h1>BOOK SALES</h1></a>
                        <p>STOREFRONT</p>
                    </div>
                </div>
                <!-- ==================================================================gợi ý tìm kiếm==================================================================== -->

                <div class="search-box" id="khung-tim-kiem" style="position: relative;">
                    <input type="text" id="o-nhap-tu-khoa" placeholder="Tìm kiếm tựa sách, tác giả..." onkeyup="timKiemNhanh(this.value)" autocomplete="off">
                    <button>
                        <i class="fas fa-search"></i>
                    </button>

                    <div id="danh-sach-ket-qua" class="khung-goi-y-tim-kiem" style="display: none;">
                    </div>
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
</div><!-- /app -->
</div><!-- /dau-trang-co-dinh -->

<!-- ===================================================== Modal tra cứu đơn hàng (không cần đăng nhập) ===================================================== -->
<div id="overlay-tra-cuu" class="overlay-tra-cuu" onclick="dongTraCuuDonHang()"></div>
<div id="panel-tra-cuu-don-hang" class="panel-tra-cuu">
    <button class="panel-tra-cuu__dong" onclick="dongTraCuuDonHang()" aria-label="Đóng">&times;</button>
    <div class="panel-tra-cuu__dau">
        <i class="fas fa-box-open"></i>
        <h3>Theo Dõi Đơn Hàng</h3>
        <p>Nhập mã đơn hàng hoặc số điện thoại để tra cứu.</p>
    </div>
    <form class="panel-tra-cuu__form" action="CuaHang/TrangBanHang/donHang/traDoc.php" method="POST">
        <div class="panel-tra-cuu__nhom">
            <label for="tra-cuu-ma-don">Mã đơn hàng</label>
            <input type="text" id="tra-cuu-ma-don" name="ma_don_hang" placeholder="VD: DH1745000012" autocomplete="off">
        </div>
        <div class="panel-tra-cuu__phan-cach">hoặc</div>
        <div class="panel-tra-cuu__nhom">
            <label for="tra-cuu-sdt">Số điện thoại</label>
            <input type="tel" id="tra-cuu-sdt" name="so_dien_thoai" placeholder="VD: 0901234567" autocomplete="off">
        </div>
        <button type="submit" class="panel-tra-cuu__nut-tim">
            <i class="fas fa-search"></i> Tra cứu ngay
        </button>
    </form>
</div>

<!-- ===================================================== Panel hỗ trợ khách hàng ===================================================== -->
<div id="overlay-ho-tro" class="overlay-tra-cuu" onclick="dongHoTro()"></div>
<div id="panel-ho-tro-khach-hang" class="panel-ho-tro">
    <button class="panel-tra-cuu__dong" onclick="dongHoTro()" aria-label="Đóng">&times;</button>
    <div class="panel-tra-cuu__dau" style="background:linear-gradient(135deg,#0f766e,#0d9488);">
        <i class="fas fa-headset"></i>
        <h3>Hỗ Trợ Khách Hàng</h3>
        <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn!</p>
    </div>
    <div class="panel-ho-tro__noi-dung">
        <!-- Liên hệ nhanh -->
        <div class="ho-tro__lien-he">
            <a href="tel:1900636467" class="ho-tro__the">
                <span class="ho-tro__bieu-tuong" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-phone-alt"></i></span>
                <div>
                    <strong>Hotline miễn phí</strong>
                    <span>1900 636 467 &mdash; 8h–21h mỗi ngày</span>
                </div>
            </a>
            <a href="mailto:hotro@booksales.vn" class="ho-tro__the">
                <span class="ho-tro__bieu-tuong" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-envelope"></i></span>
                <div>
                    <strong>Email hỗ trợ</strong>
                    <span>hotro@booksales.vn</span>
                </div>
            </a>
            <a href="https://zalo.me/0901234567" target="_blank" rel="noopener" class="ho-tro__the">
                <span class="ho-tro__bieu-tuong" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-comment-dots"></i></span>
                <div>
                    <strong>Chat Zalo</strong>
                    <span>Phản hồi trong vòng 5 phút</span>
                </div>
            </a>
        </div>
        <!-- FAQ -->
        <p class="ho-tro__tieu-de-faq"><i class="fas fa-question-circle"></i> Câu hỏi thường gặp</p>
        <details class="ho-tro__faq">
            <summary>Thời gian giao hàng bao lâu?</summary>
            <p>Nội thành: 1–2 ngày. Ngoại tỉnh: 3–5 ngày làm việc.</p>
        </details>
        <details class="ho-tro__faq">
            <summary>Tôi có thể đổi/trả sách không?</summary>
            <p>Chấp nhận đổi trả trong 7 ngày nếu sách lỗi từ nhà xuất bản hoặc giao nhầm.</p>
        </details>
        <details class="ho-tro__faq">
            <summary>Phí vận chuyển tính thế nào?</summary>
            <p>Miễn phí vận chuyển cho đơn hàng từ 150.000đ. Dưới mức này phí 25.000đ.</p>
        </details>
        <details class="ho-tro__faq">
            <summary>Tôi quên mật khẩu thì làm thế nào?</summary>
            <p>Liên hệ Hotline hoặc email để được hỗ trợ đặt lại mật khẩu thủ công.</p>
        </details>
        <details class="ho-tro__faq">
            <summary>Thanh toán chuyển khoản có an toàn không?</summary>
            <p>Hoàn toàn an toàn. Bạn sẽ nhận mã QR của tài khoản chính thức và giữ kho trong 7 phút.</p>
        </details>
    </div>
</div>

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
<script>
/* ── Panel tra cứu đơn hàng ─────────────────────────────────────── */
function moTraCuuDonHang() {
    document.getElementById('panel-tra-cuu-don-hang').classList.add('hien');
    document.getElementById('overlay-tra-cuu').classList.add('hien');
    document.body.style.overflow = 'hidden';
}
function dongTraCuuDonHang() {
    document.getElementById('panel-tra-cuu-don-hang').classList.remove('hien');
    document.getElementById('overlay-tra-cuu').classList.remove('hien');
    document.body.style.overflow = '';
}
/* ── Panel hỗ trợ khách hàng ─────────────────────────────────────── */
function moHoTro() {
    document.getElementById('panel-ho-tro-khach-hang').classList.add('hien');
    document.getElementById('overlay-ho-tro').classList.add('hien');
    document.body.style.overflow = 'hidden';
}
function dongHoTro() {
    document.getElementById('panel-ho-tro-khach-hang').classList.remove('hien');
    document.getElementById('overlay-ho-tro').classList.remove('hien');
    document.body.style.overflow = '';
}
</script>