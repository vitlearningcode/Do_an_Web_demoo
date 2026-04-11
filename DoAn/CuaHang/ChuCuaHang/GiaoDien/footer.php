<?php
/**
 * footer.php — Phần cuối trang Admin (ChuCuaHang)
 * File này chỉ gọi require_once các thành phần con.
 */
require_once __DIR__ . '/thanhPhan/toastAdminThongBao.php';
require_once __DIR__ . '/thanhPhan/scriptAdminLayout.php';
    </main><!-- /.adm-content -->
</div><!-- /.adm-main -->

<!-- ===========================
     TOAST NOTIFICATION
<div class="adm-toast" id="adm-toast">
    <i class="fas fa-check-circle"></i>
    <span id="adm-toast-msg"></span>
</div>

<!-- Sidebar overlay (mobile) -->
<div id="adm-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:150;"
     onclick="document.getElementById('adm-sidebar').classList.remove('open'); this.style.display='none';"></div>

<script>
// ── Hamburger toggle ──────────────────────────────────────────
// Desktop (> 1024px): thu nhỏ sidebar → icon-only (collapsed)
// Mobile (≤ 1024px): slide sidebar vào từ trái
const _menuBtn = document.getElementById('adm-menu-toggle');
const _sidebar = document.getElementById('adm-sidebar');
const _main    = document.querySelector('.adm-main');
const _overlay = document.getElementById('adm-overlay');
const LS_KEY   = 'adm_sidebar_collapsed';

// Phục hồi trạng thái collapsed từ localStorage (chỉ desktop)
if (window.innerWidth > 1024 && localStorage.getItem(LS_KEY) === '1') {
    _sidebar.classList.add('collapsed');
    if (_main) _main.classList.add('sidebar-collapsed');
}

if (_menuBtn) {
    _menuBtn.addEventListener('click', () => {
        const isDesktop = window.innerWidth > 1024;

        if (isDesktop) {
            // Desktop: toggle collapsed
            const isCollapsed = _sidebar.classList.toggle('collapsed');
            if (_main) _main.classList.toggle('sidebar-collapsed', isCollapsed);
            localStorage.setItem(LS_KEY, isCollapsed ? '1' : '0');
        } else {
            // Mobile: slide in/out
            const isOpen = _sidebar.classList.toggle('open');
            _overlay.style.display = isOpen ? 'block' : 'none';
        }
    });
}

// Đóng sidebar khi click overlay (mobile)
if (_overlay) {
    _overlay.addEventListener('click', () => {
        _sidebar.classList.remove('open');
        _overlay.style.display = 'none';
    });
}

// ── Auto-hide URL flash message (nếu có) ────────────────────
(function () {
    const params = new URLSearchParams(window.location.search);
    const msg    = params.get('thongbao');
    const loai   = params.get('loai') || 'success';
    if (!msg) return;

    const toast    = document.getElementById('adm-toast');
    const toastMsg = document.getElementById('adm-toast-msg');
    if (!toast || !toastMsg) return;

    toastMsg.textContent = decodeURIComponent(msg);
    toast.className = `adm-toast ${loai} show`;

    const icon = toast.querySelector('i');
    if (loai === 'error')        icon.className = 'fas fa-times-circle';
    else if (loai === 'warning') icon.className = 'fas fa-exclamation-triangle';
    else if (loai === 'info')    icon.className = 'fas fa-info-circle';
    else                         icon.className = 'fas fa-check-circle';

    setTimeout(() => toast.classList.remove('show'), 3500);

    const clean = new URL(window.location.href);
    clean.searchParams.delete('thongbao');
    clean.searchParams.delete('loai');
    window.history.replaceState({}, '', clean.toString());
})();
</script>
</body>
</html>
