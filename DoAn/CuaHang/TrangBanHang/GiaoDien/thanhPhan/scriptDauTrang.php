<?php
/**
 * scriptDauTrang.php — Script JS cho phần đầu trang
 * Chứa: moTraCuuDonHang, dongTraCuuDonHang, moHoTro, dongHoTro
 * + Patch xacNhanDangXuat để dùng URL tuyệt đối từ PHP ($duong_dan_goc)
 * Không có AJAX, không innerHTML — chỉ toggle class/style
 */
?>
<script>
/* ── Panel tra cứu đơn hàng ── */
function moTraCuuDonHang() {
    document.getElementById('panel-tra-cuu-don-hang').classList.add('hien');
    document.getElementById('overlay-tra-cuu').classList.add('hien');
    document.body.style.overflow = 'hidden';
}
function dongTraCuuDonHang() {
    document.getElementById('panel-tra-cuu-don-hang').classList.remove('hien');
    document.getElementById('overlay-tra-cuu').classList.remove('hien');
    document.body.style.overflow = '';
}

/* ── Panel hỗ trợ khách hàng ── */
function moHoTro() {
    document.getElementById('panel-ho-tro-khach-hang').classList.add('hien');
    document.getElementById('overlay-ho-tro').classList.add('hien');
    document.body.style.overflow = 'hidden';
}
function dongHoTro() {
    document.getElementById('panel-ho-tro-khach-hang').classList.remove('hien');
    document.getElementById('overlay-ho-tro').classList.remove('hien');
    document.body.style.overflow = '';
}
</script>

<?php /* ── Patch xacNhanDangXuat.js: override đường dẫn tuyệt đối (PHP render, không AJAX) ── */ ?>
<script>
/*
 * xacNhanDangXuat.js hardcode "xuly_dangxuat.php" (tương đối) → sai khi gọi từ trang con.
 * Patch: ghi đè xuLyXacNhan() sau khi file JS đã load, dùng đường dẫn
 * tuyệt đối do PHP render ở đây.
 * Chạy sau DOMContentLoaded (defer-safe) để đảm bảo xacNhanDangXuat đã khởi tạo.
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof xacNhanDangXuat !== 'undefined') {
        xacNhanDangXuat.xuLyXacNhan = function () {
            this.dong();
            window.location.href = '<?= $duong_dan_goc ?>xuly_dangxuat.php';
        };
    }
});
</script>
