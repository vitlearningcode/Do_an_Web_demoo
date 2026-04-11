<?php
/**
 * scriptTrangChu.php — Script JS trang chủ
 * Bao gồm: khởi tạo Banner Slider + đồng hồ đếm ngược Flash Sale
 * Không có AJAX — chỉ thao tác DOM
 * Yêu cầu: $flashSale_ThoiGianKT (string|null) từ taiFlashSale.php
 */
?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    // Khởi tạo Banner Slider
    if (document.getElementById('hero-slider') && typeof TrinhChieuBanner !== 'undefined') {
        new TrinhChieuBanner('hero-slider');
    }

    <?php if ($flashSale_ThoiGianKT): ?>
    // Đồng hồ đếm ngược Flash Sale — dùng ngayKetThuc thực từ DB
    (function () {
        const thoiGianKetThuc = new Date("<?= $flashSale_ThoiGianKT ?>").getTime();
        function demNguoc() {
            const hieuSo = thoiGianKetThuc - Date.now();
            if (hieuSo <= 0) {
                ['hours', 'minutes', 'seconds'].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '00';
                });
                return;
            }
            const them2So = function(v) { return String(v).padStart(2, '0'); };
            const datGiaTri = function(id, v) {
                const el = document.getElementById(id);
                if (el) el.textContent = them2So(v);
            };
            datGiaTri('hours',   Math.floor(hieuSo / 3600000));
            datGiaTri('minutes', Math.floor((hieuSo % 3600000) / 60000));
            datGiaTri('seconds', Math.floor((hieuSo % 60000) / 1000));
        }
        demNguoc();
        setInterval(demNguoc, 1000);
    })();
    <?php endif; ?>

});
</script>
