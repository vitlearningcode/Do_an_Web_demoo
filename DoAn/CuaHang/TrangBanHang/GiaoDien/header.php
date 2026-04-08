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

                <div class="header-actions">
                    <?php if ($isLoggedIn): ?>
                        <button class="action-btn" id="btn-user-profile">
                            <i class="fas fa-user-check" style="color: var(--primary);"></i>
                            <span>Tài khoản</span>
                        </button>
                        <button class="action-btn" onclick="xacNhanDangXuat.mo()" style="color: #ef4444;">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    <?php else: ?>
                        <button class="action-btn" onclick="authModal.mo('dang_nhap')">
                            <i class="fas fa-user"></i>
                            <span>Đăng nhập</span>
                        </button>
                    <?php endif; ?>

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