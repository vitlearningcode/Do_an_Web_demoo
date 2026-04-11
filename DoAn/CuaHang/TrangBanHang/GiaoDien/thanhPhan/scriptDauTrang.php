<?php
/**
 * scriptDauTrang.php — Script JS cho phần đầu trang
 * Chứa: moTraCuuDonHang, dongTraCuuDonHang, moHoTro, dongHoTro
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
