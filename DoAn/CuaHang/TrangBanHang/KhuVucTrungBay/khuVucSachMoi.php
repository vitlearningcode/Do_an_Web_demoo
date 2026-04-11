<?php
/**
 * khuVucSachMoi.php — HTML khu vực Sách mới phát hành
 * Yêu cầu: $ds_sachmoi (array), hàm hienThiTheSach()
 */
?>
<section class="new-releases">
    <div class="section-header">
        <div>
            <h3><i class="fas fa-sparkles"></i> Sách Mới Phát Hành</h3>
            <p>Cập nhật những tựa sách mới nhất</p>
        </div>
        <a href="#" class="view-all-btn light">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="books-grid">
        <?php foreach ($ds_sachmoi as $sach):
            /* Badge cam: "Mới" */
            echo hienThiTheSach($sach, [
                ['class' => 'label-type', 'label' => 'Mới'],
            ]);
        endforeach; ?>
    </div>
</section>
