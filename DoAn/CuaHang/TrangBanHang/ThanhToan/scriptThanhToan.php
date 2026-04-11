<?php
/**
 * scriptThanhToan.php — Script JS trang thanh toán
 * Hàm: choiLoaiDiaChi(loai) — toggle hiển thị địa chỉ đã lưu / nhập mới
 * Không có AJAX — chỉ thao tác DOM (show/hide)
 */
?>
<script>
/* Chuyển đổi hiển thị giữa địa chỉ đã lưu và địa chỉ nhập mới */
function choiLoaiDiaChi(loai) {
    var khungDaLuu  = document.getElementById('khung-da-luu');
    var khungMoi    = document.getElementById('khung-dc-moi');
    var selectDiaChi = document.getElementById('select-dia-chi');
    var oNhapMoi    = document.getElementById('nhap-dia-chi-moi');

    if (loai === 'da_luu') {
        if (khungDaLuu)   khungDaLuu.style.display = '';
        if (khungMoi)     khungMoi.classList.remove('hien');
        if (selectDiaChi) selectDiaChi.required = true;
        if (oNhapMoi)     oNhapMoi.required = false;
    } else {
        if (khungDaLuu)   khungDaLuu.style.display = 'none';
        if (khungMoi)     khungMoi.classList.add('hien');
        if (selectDiaChi) selectDiaChi.required = false;
        if (oNhapMoi)     oNhapMoi.required = true;
    }
}
</script>
