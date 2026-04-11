<?php
/**
 * panelHoTroKhachHang.php — Panel hỗ trợ khách hàng
 * Slide-in panel từ phải: hotline, email, zalo + FAQ
 * Phụ thuộc: scriptDauTrang.php (hàm moHoTro/dongHoTro)
 */
?>
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
