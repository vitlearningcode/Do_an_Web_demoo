<?php
/**
 * formPhuongThucThanhToan.php — HTML phần chọn phương thức thanh toán
 * Gồm: COD (value=1) và QR VietQR (value=2)
 */
?>
<div class="section-box">
    <h3><i class="fas fa-wallet"></i> Phương thức thanh toán</h3>

    <label class="payment-option">
        <input type="radio" name="phuong_thuc" value="1" checked>
        <div class="option-content">
            <div class="icon text-green"><i class="fas fa-money-bill-wave"></i></div>
            <div class="details">
                <strong>Thanh toán tiền mặt khi nhận hàng (COD)</strong>
                <p>Trả tiền mặt trực tiếp cho người giao hàng.</p>
            </div>
        </div>
    </label>

    <label class="payment-option">
        <input type="radio" name="phuong_thuc" value="2">
        <div class="option-content">
            <div class="icon text-qr"><i class="fas fa-qrcode"></i></div>
            <div class="details">
                <strong>Chuyển khoản qua mã VietQR (Tự Động)</strong>
                <p>TPBANK - LÊ MINH ĐỨC (Giữ kho 7 phút).</p>
            </div>
        </div>
    </label>
</div>

<!-- Nút xác nhận đặt hàng -->
<div class="action-footer">
    <p class="terms">Bằng việc đặt hàng, bạn đồng ý với Điều khoản Sử dụng và Chính sách của chúng tôi.</p>
    <button type="submit" class="btn-submit-order">
        Hoàn Tất Đặt Mua <i class="fas fa-arrow-right"></i>
    </button>
</div>
