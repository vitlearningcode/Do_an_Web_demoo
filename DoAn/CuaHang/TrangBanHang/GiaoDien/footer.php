<footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="logo">
                        <div class="logo-icon"><i class="fas fa-book"></i></div>
                        <div class="logo-text">
                            <h1>BOOK SALES</h1>
                            <p>MANAGEMENT</p>
                        </div>
                    </div>
                    <p>Hệ thống bán sách trực tuyến hàng đầu với hàng ngàn tựa sách đa dạng, cam kết chất lượng và dịch vụ tốt nhất.</p>
                </div>
                <div class="footer-links">
                    <h4>Hỗ Trợ Khách Hàng</h4>
                    <ul>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Phương thức vận chuyển</a></li>
                        <li><a href="#">Phương thức thanh toán</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Về Chúng Tôi</h4>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Điều khoản sử dụng</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h4>Đăng Ký Nhận Tin</h4>
                    <p>Nhận thông tin về các chương trình khuyến mãi mới nhất.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Email của bạn">
                        <button>Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Đã tách Giỏ Hàng sang file formGioHang.php -->

    <div class="modal-overlay" id="auth-modal">
        <div class="modal">
            <button class="modal-close" id="auth-close"><i class="fas fa-times"></i></button>
            <h2>Đăng nhập</h2>
            <form id="auth-form" action="ChucNang/CuaHang/XacThuc/xu_ly.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Nhập email của bạn" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="submit-btn">Xác nhận</button>
            </form>
            <p class="modal-footer-text">Chưa có tài khoản? <a href="#" onclick="authModal.chuyenCheDo(); return false;">Đăng ký ngay</a></p>
        </div>
    </div>

    <div id="chatbot-toggle" class="chatbot-toggle">
        <i class="fas fa-comments"></i>
    </div>
    <div id="chatbot" class="chatbot">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <i class="fas fa-robot"></i>
                <div>
                    <h4>Trợ lý AI</h4>
                    <p>Luôn sẵn sàng hỗ trợ</p>
                </div>
            </div>
            <button id="chatbot-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot">
                <div class="message-avatar bot"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p>Xin chào! Tôi là trợ lý AI của Cửa Hàng. Tôi có thể giúp gì cho bạn hôm nay?</p>
                </div>
            </div>
        </div>
        <div class="typing-indicator" id="typing-indicator" style="display: none;">
            <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-input" placeholder="Nhập câu hỏi của bạn...">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <div class="modal-overlay" id="logout-modal">
        <div class="modal logout-modal">
            <div class="logout-icon"><i class="fas fa-sign-out-alt"></i></div>
            <h2>Đăng xuất</h2>
            <p>Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?</p>
            <div class="logout-actions">
                <button class="cancel-btn" id="logout-cancel">Hủy</button>
                <button class="confirm-btn" id="logout-confirm">Đăng xuất</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-message"></span>
    </div>

</div>