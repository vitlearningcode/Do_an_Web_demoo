<?php
/**
 * scriptAdminLayout.php — Script JS cho layout admin
 * Bao gồm: hamburger toggle + tự động hiện toast từ URL params + đóng HTML
 * Không có AJAX — chỉ đọc URLSearchParams và thao tác DOM
 */
?>
<script>
// ── Hamburger toggle (responsive) ───────────────────────────
const _nutMenu   = document.getElementById('adm-menu-toggle');
const _sidebar   = document.getElementById('adm-sidebar');
const _overlay   = document.getElementById('adm-overlay');

if (_nutMenu) {
    _nutMenu.addEventListener('click', () => {
        const dangMo = _sidebar.classList.toggle('open');
        _overlay.style.display = dangMo ? 'block' : 'none';
    });
}

// ── Tự động hiện toast từ URL flash message ──────────────────
(function () {
    const thamSo  = new URLSearchParams(window.location.search);
    const thongBao = thamSo.get('thongbao');
    const loai     = thamSo.get('loai') || 'success'; // success | error | warning
    if (!thongBao) return;

    const toastEl    = document.getElementById('adm-toast');
    const noiDungEl  = document.getElementById('adm-toast-msg');
    if (!toastEl || !noiDungEl) return;

    noiDungEl.textContent = decodeURIComponent(thongBao);
    toastEl.className = `adm-toast ${loai} show`;

    const bieu_tuong = toastEl.querySelector('i');
    if (loai === 'error')        bieu_tuong.className = 'fas fa-times-circle';
    else if (loai === 'warning') bieu_tuong.className = 'fas fa-exclamation-triangle';
    else                         bieu_tuong.className = 'fas fa-check-circle';

    setTimeout(() => toastEl.classList.remove('show'), 3500);

    // Xóa query params khỏi URL (không reload)
    const duongDanSach = new URL(window.location.href);
    duongDanSach.searchParams.delete('thongbao');
    duongDanSach.searchParams.delete('loai');
    window.history.replaceState({}, '', duongDanSach.toString());
})();
</script>
</body>
</html>
