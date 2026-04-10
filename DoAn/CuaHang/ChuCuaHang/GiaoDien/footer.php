    </main><!-- /.adm-content -->
</div><!-- /.adm-main -->

<!-- ===========================
     TOAST NOTIFICATION
=========================== -->
<div class="adm-toast" id="adm-toast">
    <i class="fas fa-check-circle"></i>
    <span id="adm-toast-msg"></span>
</div>

<!-- Sidebar overlay (mobile) -->
<div id="adm-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:150;"
     onclick="document.getElementById('adm-sidebar').classList.remove('open'); this.style.display='none';"></div>

<script>
// ── Hamburger toggle (responsive) ───────────────────────────
const _menuBtn  = document.getElementById('adm-menu-toggle');
const _sidebar  = document.getElementById('adm-sidebar');
const _overlay  = document.getElementById('adm-overlay');

if (_menuBtn) {
    _menuBtn.addEventListener('click', () => {
        const isOpen = _sidebar.classList.toggle('open');
        _overlay.style.display = isOpen ? 'block' : 'none';
    });
}

// ── Auto-hide URL flash message (nếu có) ────────────────────
(function () {
    const params = new URLSearchParams(window.location.search);
    const msg    = params.get('thongbao');
    const loai   = params.get('loai') || 'success'; // success | error | warning
    if (!msg) return;

    const toast    = document.getElementById('adm-toast');
    const toastMsg = document.getElementById('adm-toast-msg');
    if (!toast || !toastMsg) return;

    toastMsg.textContent = decodeURIComponent(msg);
    toast.className = `adm-toast ${loai} show`;

    const icon = toast.querySelector('i');
    if (loai === 'error')   icon.className = 'fas fa-times-circle';
    else if (loai === 'warning') icon.className = 'fas fa-exclamation-triangle';
    else icon.className = 'fas fa-check-circle';

    setTimeout(() => toast.classList.remove('show'), 3500);

    // Xóa query params khỏi URL (không reload)
    const clean = new URL(window.location.href);
    clean.searchParams.delete('thongbao');
    clean.searchParams.delete('loai');
    window.history.replaceState({}, '', clean.toString());
})();
</script>
</body>
</html>
