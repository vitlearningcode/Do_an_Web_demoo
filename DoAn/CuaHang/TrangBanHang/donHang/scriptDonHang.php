<?php
/**
 * scriptDonHang.php — Script JS trang đơn hàng
 * Chứa: moModalDanhGia, dongModalDanhGia, chonSao, validate form, toggleUserMenu
 * Không có AJAX, không innerHTML — chỉ toggle class + điền textContent/value
 */
?>
<script>
// ── Mở modal đánh giá ────────────────────────────────────────────────────────
function moModalDanhGia(nutBam) {
    var overlay   = document.getElementById('review-overlay');
    var tenSach   = document.getElementById('review-ten-sach');
    var oMaDH     = document.getElementById('review-maDH');
    var oMaSach   = document.getElementById('review-maSach');
    var oDiem     = document.getElementById('review-diem');
    var oNhanXet  = document.getElementById('review-nhanXet');

    // Điền dữ liệu từ data-* (PHP đã render) — không innerHTML
    tenSach.textContent = nutBam.dataset.ten    || '';
    oMaDH.value         = nutBam.dataset.madh   || '';
    oMaSach.value       = nutBam.dataset.masach || '';
    oDiem.value         = '0';
    oNhanXet.value      = '';

    // Reset sao về 0
    chonSao(0);

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ── Đóng modal đánh giá ──────────────────────────────────────────────────────
function dongModalDanhGia() {
    document.getElementById('review-overlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Đóng khi click ngoài modal
document.getElementById('review-overlay').addEventListener('click', function(e) {
    if (e.target === this) dongModalDanhGia();
});

// ── Chọn sao đánh giá ────────────────────────────────────────────────────────
function chonSao(soSao) {
    var tatCaSao = document.querySelectorAll('#review-stars .dh-star');
    tatCaSao.forEach(function(sao, viTri) {
        if (viTri < soSao) {
            sao.classList.add('selected');
        } else {
            sao.classList.remove('selected');
        }
    });
    var oDiem = document.getElementById('review-diem');
    if (oDiem) oDiem.value = soSao;
}

// ── Validate trước khi submit ─────────────────────────────────────────────────
document.getElementById('form-danh-gia').addEventListener('submit', function(e) {
    var diem = parseInt(document.getElementById('review-diem').value) || 0;
    if (diem < 1) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất 1 sao!');
    }
});

// ── Toggle menu người dùng ────────────────────────────────────────────────────
function toggleUserMenu(e) {
    e.stopPropagation();
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.toggle('show');
}
document.addEventListener('click', function() {
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.remove('show');
});
</script>
