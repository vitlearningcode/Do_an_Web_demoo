<?php
/**
 * khuVucHeroBanner.php — HTML khu vực hero banner slider
 * Yêu cầu: $ds_quangCao (array) từ taiQuangCao.php
 */
?>
<section class="hero-banner">
    <div class="hero-slider" id="hero-slider">
        <?php
        foreach ($ds_quangCao as $viTri => $banner):
            $mauNen = !empty($banner['mauNen']) ? $banner['mauNen'] : 'blue';
        ?>
        <div class="hero-slide <?= htmlspecialchars($mauNen) ?> <?= $viTri === 0 ? 'active' : '' ?>">
            <div class="hero-slide-bg">
                <img src="<?= anhBanner($banner['hinhAnh'] ?? null) ?>" alt="Banner">
                <div class="gradient-overlay"></div>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><?= htmlspecialchars($banner['nhan']) ?></span>
                <h2><?= $banner['tieuDe'] ?></h2>
                <p><?= htmlspecialchars($banner['moTa']) ?></p>
                <button class="hero-btn"><?= htmlspecialchars($banner['chuNut']) ?> <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="hero-nav prev" id="hero-prev"><i class="fas fa-chevron-left"></i></button>
    <button class="hero-nav next" id="hero-next"><i class="fas fa-chevron-right"></i></button>
    <div class="hero-indicators" id="hero-indicators"></div>
</section>
