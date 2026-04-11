<?php
/**
 * danhSachTheDonHang.php — HTML danh sách thẻ card từng đơn hàng
 * Hiển thị: trạng thái, danh sách sản phẩm, nút đánh giá, tổng tiền, ngày đặt
 * Yêu cầu: $dsDonHang, $chiTietDH, $daDanhGia, hàm thongTinBadge()
 */
?>
<div class="dh-content">

    <?php if (!empty($thongBaoSauDanhGia) && $thongBaoSauDanhGia === 'ok'): ?>
    <div style="background:#dcfce7;color:#15803d;padding:10px 18px;border-radius:8px;margin-bottom:14px;font-weight:500;">
        <i class="fas fa-check-circle"></i> Đánh giá của bạn đã được ghi nhận. Cảm ơn!
    </div>
    <?php endif; ?>

    <?php if (empty($dsDonHang)): ?>
    <div class="dh-empty">
        <i class="fas fa-box-open"></i>
        Không có đơn hàng nào.
    </div>
    <?php else: ?>

    <?php foreach ($dsDonHang as $donHang):
        [$classBadge, $nhanBadge] = thongTinBadge($donHang['trangThai']);
        $danhSachSanPham = $chiTietDH[$donHang['maDH']] ?? [];
    ?>
    <div class="dh-order-card">
        <!-- Header đơn -->
        <div class="dh-order-header">
            <div class="dh-order-id">
                Mã đơn: <span><?= htmlspecialchars($donHang['maDH']) ?></span>
                &nbsp;·&nbsp;
                <span><?= htmlspecialchars($donHang['tenPT']) ?></span>
            </div>
            <span class="dh-status-badge <?= $classBadge ?>"><?= $nhanBadge ?></span>
        </div>

        <!-- Danh sách sách trong đơn -->
        <div class="dh-order-items">
        <?php foreach ($danhSachSanPham as $sanPham):
            $daXemXet = !empty($daDanhGia[$sanPham['maSach']]);
            $duongDanAnh = htmlspecialchars($sanPham['urlAnh'] ?? 'https://placehold.co/70x90?text=📚');
        ?>
        <div class="dh-order-item">
            <img class="dh-item-img"
                 src="<?= $duongDanAnh ?>"
                 alt="<?= htmlspecialchars($sanPham['tenSach']) ?>"
                 onerror="this.src='https://placehold.co/70x90?text=📚'">
            <div class="dh-item-info">
                <h4><?= htmlspecialchars($sanPham['tenSach']) ?></h4>
                <p>x<?= (int)$sanPham['soLuong'] ?></p>

                <?php if ($donHang['trangThai'] === 'HoanThanh'): ?>
                    <?php if ($daXemXet): ?>
                    <div class="dh-reviewed-tag"><i class="fas fa-check-circle"></i> Đã đánh giá</div>
                    <?php else: ?>
                    <!-- Nút đánh giá → mở modal (PHP render sẵn, JS chỉ toggle) -->
                    <button class="dh-btn-review"
                            type="button"
                            data-madh="<?= htmlspecialchars($donHang['maDH']) ?>"
                            data-masach="<?= htmlspecialchars($sanPham['maSach']) ?>"
                            data-ten="<?= htmlspecialchars($sanPham['tenSach']) ?>"
                            onclick="moModalDanhGia(this)">
                        <i class="fas fa-star"></i> Đánh giá
                    </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="dh-item-price-col">
                <div class="dh-item-price"><?= number_format($sanPham['thanhTien'], 0, ',', '.') ?>đ</div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>

        <!-- Footer đơn -->
        <div class="dh-order-footer">
            <div class="dh-order-date">
                <i class="far fa-clock"></i>
                <?= date('d/m/Y H:i', strtotime($donHang['ngayDat'])) ?>
                &nbsp;·&nbsp;
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars(mb_strimwidth($donHang['diaChiChiTiet'], 0, 40, '…')) ?>
            </div>
            <div class="dh-order-total">
                Tổng cộng: <strong><?= number_format($donHang['tongTien'], 0, ',', '.') ?>đ</strong>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>

</div><!-- /dh-content -->
