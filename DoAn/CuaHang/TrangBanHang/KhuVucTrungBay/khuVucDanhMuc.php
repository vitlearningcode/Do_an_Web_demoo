<?php
/**
 * khuVucDanhMuc.php — HTML khu vực Danh mục sách
 * Yêu cầu: $ds_danhmuc (array), $bieu_tuong (array) từ taiDanhSach_DanhMuc.php
 */
?>
<section class="categories-section">
    <div class="section-header">
        <h3>Khám Phá Theo Danh Mục</h3>
        <a href="#">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="categories-grid">
        <?php
        $viTri = 0;
        foreach ($ds_danhmuc as $theLoai): ?>
        <a href="#" class="category-card">
            <div class="category-icon"><?= $bieu_tuong[$viTri++ % 6] ?></div>
            <span><?= htmlspecialchars($theLoai['tenTL']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>
