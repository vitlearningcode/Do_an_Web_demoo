<?php
/**
 * khuVucFlashSale.php — HTML khu vực Flash Sale (header + đồng hồ + grid sách)
 * Yêu cầu: $ds_flashsale (array), hàm hienThiTheSach()
 * Chỉ render nếu $ds_flashsale không rỗng
 */
?>
<?php if (!empty($ds_flashsale)): ?>
<section class="flash-sale">
    <div class="flash-sale-inner">
        <div class="flash-sale-header">
            <div class="flash-sale-title">
                <div class="flash-icon"><i class="fas fa-fire"></i></div>
                <div>
                    <h3>Flash Sale <span>Giá Sốc</span></h3>
                    <p>Kết thúc trong</p>
                </div>
            </div>
            <div class="flash-timer">
                <div class="timer-block"><div class="timer-value" id="hours">00</div><span>Giờ</span></div>
                <span class="timer-sep">:</span>
                <div class="timer-block"><div class="timer-value" id="minutes">00</div><span>Phút</span></div>
                <span class="timer-sep">:</span>
                <div class="timer-block"><div class="timer-value timer-seconds" id="seconds">00</div><span>Giây</span></div>
            </div>
        </div>
        <div class="books-grid">
            <?php foreach ($ds_flashsale as $sach):
                /*
                 * Tính phần trăm đã bán cho thanh tiến trình
                 * tongBan      = tổng đơn (chưa bị hủy) → phản ánh chính xác số đã được đặt
                 * soLuongKM    = giới hạn khuyến mãi từ ChiTietKhuyenMai
                 * conLai       = soLuongTon (kho đã trừ ngay khi đặt hàng)
                 */
                $daBan       = (int)($sach['tongBan'] ?? 0);
                $conLai      = max(0, (int)($sach['soLuongTon'] ?? 0));
                $giuiHanKM   = (int)($sach['soLuongKhuyenMai'] ?? 0);
                // Nếu không có giới hạn → dùng tồn kho + đã bán làm mốc 100%
                $tongSoLuong = $giuiHanKM > 0 ? $giuiHanKM : max(1, $daBan + $conLai);
                $phanTram    = $tongSoLuong > 0 ? min(100, round($daBan / $tongSoLuong * 100)) : 0;

                // HTML thanh tiến trình — truyền vào customHtmlBottom
                $thanhTienTrinh = '
                <div class="flash-sale-progress">
                    <div class="flash-progress-label">
                        <span class="da-ban-label">Đã bán: ' . $daBan . '</span>
                        <span>Còn: ' . $conLai . '</span>
                    </div>
                    <div class="flash-progress-bar-bg">
                        <div class="flash-progress-bar-fill" style="width:' . $phanTram . '%"></div>
                    </div>
                </div>';

                echo hienThiTheSach($sach, [
                    ['class' => 'label-type',    'label' => 'Flash Sale'],
                    ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'],
                ], $thanhTienTrinh);
            endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
