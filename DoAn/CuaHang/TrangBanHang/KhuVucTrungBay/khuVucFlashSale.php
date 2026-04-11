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
                 * Badge 1 (cam): nhãn "Flash Sale"
                 * Badge 2 (đỏ): "-XX%" — phanTramGiam thực từ ChiTietKhuyenMai
                 */
                echo hienThiTheSach($sach, [
                    ['class' => 'label-type',    'label' => 'Flash Sale'],
                    ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'],
                ]);
            endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
