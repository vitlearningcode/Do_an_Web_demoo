<?php
/**
 * toastAdminThongBao.php — Toast thông báo + Overlay sidebar (mobile) cho admin
 */
?>
    </main><!-- /.adm-content -->
</div><!-- /.adm-main -->

<!-- Toast thông báo -->
<div class="adm-toast" id="adm-toast">
    <i class="fas fa-check-circle"></i>
    <span id="adm-toast-msg"></span>
</div>

<!-- Overlay sidebar (mobile) -->
<div id="adm-overlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:150;"
     onclick="document.getElementById('adm-sidebar').classList.remove('open'); this.style.display='none';">
</div>
