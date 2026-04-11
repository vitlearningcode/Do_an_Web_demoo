<?php
/**
 * khuVucSachBanChay.php — HTML khu vực Sách bán chạy nhất
 * Yêu cầu: $ds_banchay (array), hàm hienThiTheSach()
 */
?>
<section class="featured-books">
    <div class="section-header">
        <div>
            <h3><i class="fas fa-fire-alt" style="color:#f97316"></i> Sách Bán Chạy Nhất</h3>
            <p>Top 10 bán chạy nhất tháng <?= date('n/Y') ?></p>
        </div>
        <a href="CuaHang/TrangBanHang/danhSachSach.php" class="view-all-btn">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="books-grid" id="banchay-grid">
        <?php foreach ($ds_banchay as $sach):
            /*
             * Badge 1 (cam): "🔥 Hot"
             * Badge 2 (đỏ): "-XX%" nếu đang trong Flash Sale
             */
            $nhanHieu = [['class' => 'label-type', 'label' => '🔥 Hot']];
            if (!empty($sach['phanTramGiam'])) {
                $nhanHieu[] = ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'];
                $sach['giaSau'] = round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
            }
            echo hienThiTheSach($sach, $nhanHieu);
        endforeach; ?>
    </div>
</section>
