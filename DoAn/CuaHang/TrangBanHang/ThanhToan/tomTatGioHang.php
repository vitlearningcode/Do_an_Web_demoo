<?php
/**
 * tomTatGioHang.php — HTML cột tổng kết đơn hàng (danh sách sản phẩm + tổng tiền)
 * Yêu cầu: $gioHang (array), $tongTien (int)
 */
?>
<div class="checkout-summary">
    <div class="section-box summary-box">
        <div class="summary-header">
            <h3>Đơn đặt hàng (<?= count($gioHang) ?>)</h3>
            <a href="../../../index.php">Sửa giỏ hàng</a>
        </div>

        <div class="cart-scroll-list">
            <?php foreach ($gioHang as $sanPham): ?>
            <div class="summary-item">
                <div class="s-img-wrapper">
                    <img src="<?= anhSach($sanPham['hinhAnh'] ?? null) ?>" alt="Cover">
                    <span class="s-qty-badge"><?= $sanPham['soLuong'] ?></span>
                </div>
                <div class="s-info">
                    <h4 title="<?= htmlspecialchars($sanPham['tenSach']) ?>"><?= htmlspecialchars($sanPham['tenSach']) ?></h4>
                </div>
                <div class="s-price"><?= number_format($sanPham['giaBan'] * $sanPham['soLuong'], 0, ',', '.') ?>đ</div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="summary-calc">
            <div class="summary-row">
                <span>Tạm tính</span>
                <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
            </div>
            <div class="summary-row">
                <span>Phí vận chuyển</span>
                <span class="text-green font-medium">Miễn phí</span>
            </div>
        </div>

        <div class="summary-total">
            <div class="total-label">
                <span>Tổng cộng</span>
                <small>Đã bao gồm VAT</small>
            </div>
            <span class="total-amount"><?= number_format($tongTien, 0, ',', '.') ?> đ</span>
        </div>
    </div>
</div>
