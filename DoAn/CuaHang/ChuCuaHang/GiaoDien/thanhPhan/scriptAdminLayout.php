<?php
/**
 * scriptAdminLayout.php — Script JS cho layout admin
 * Bao gồm:
 *   - Hamburger toggle: Desktop → thu nhỏ sidebar (collapsed), Mobile → slide in/out
 *   - Phục hồi trạng thái sidebar từ localStorage
 *   - Tự động hiện toast từ URL flash message (?thongbao=...&loai=...)
 *   - Đóng HTML </body></html>
 * Không có AJAX — chỉ thao tác DOM và URLSearchParams
 */
?>
<script>
// ── Hamburger toggle (responsive) ───────────────────────────────────────────
// Desktop (> 1024px): thu nhỏ sidebar thành icon-only (class 'collapsed')
// Mobile  (≤ 1024px): kéo sidebar vào từ trái (class 'open') + overlay mờ
const _nutMenu  = document.getElementById('adm-menu-toggle');
const _sidebar  = document.getElementById('adm-sidebar');
const _khuChinh = document.querySelector('.adm-main');
const _overlay  = document.getElementById('adm-overlay');
const KHOA_LS   = 'adm_sidebar_collapsed'; // key localStorage

// Phục hồi trạng thái thu nhỏ từ phiên trước (chỉ desktop)
if (window.innerWidth > 1024 && localStorage.getItem(KHOA_LS) === '1') {
    if (_sidebar)  _sidebar.classList.add('collapsed');
    if (_khuChinh) _khuChinh.classList.add('sidebar-collapsed');
}

if (_nutMenu) {
    _nutMenu.addEventListener('click', function () {
        const laDesktop = window.innerWidth > 1024;

        if (laDesktop) {
            // Desktop: toggle thu nhỏ sidebar
            const daCollapsed = _sidebar.classList.toggle('collapsed');
            if (_khuChinh) _khuChinh.classList.toggle('sidebar-collapsed', daCollapsed);
            localStorage.setItem(KHOA_LS, daCollapsed ? '1' : '0');
        } else {
            // Mobile: kéo sidebar vào / ra
            const dangMo = _sidebar.classList.toggle('open');
            if (_overlay) _overlay.style.display = dangMo ? 'block' : 'none';
        }
    });
}

// Đóng sidebar khi bấm overlay (mobile)
if (_overlay) {
    _overlay.addEventListener('click', function () {
        if (_sidebar) _sidebar.classList.remove('open');
        _overlay.style.display = 'none';
    });
}

// ── Tự động hiện toast từ URL flash message ──────────────────────────────────
(function () {
    const thamSo   = new URLSearchParams(window.location.search);
    const thongBao = thamSo.get('thongbao');
    const loai     = thamSo.get('loai') || 'success'; // success | error | warning | info
    if (!thongBao) return;

    const toastEl   = document.getElementById('adm-toast');
    const noiDungEl = document.getElementById('adm-toast-msg');
    if (!toastEl || !noiDungEl) return;

    noiDungEl.textContent = decodeURIComponent(thongBao);
    toastEl.className = 'adm-toast ' + loai + ' show';

    const bieuTuong = toastEl.querySelector('i');
    if (bieuTuong) {
        if (loai === 'error')        bieuTuong.className = 'fas fa-times-circle';
        else if (loai === 'warning') bieuTuong.className = 'fas fa-exclamation-triangle';
        else if (loai === 'info')    bieuTuong.className = 'fas fa-info-circle';
        else                         bieuTuong.className = 'fas fa-check-circle';
    }

    setTimeout(function () { toastEl.classList.remove('show'); }, 3500);

    // Xóa query params khỏi URL mà không reload trang
    const urlSach = new URL(window.location.href);
    urlSach.searchParams.delete('thongbao');
    urlSach.searchParams.delete('loai');
    window.history.replaceState({}, '', urlSach.toString());
})();
</script>
</body>
</html>
