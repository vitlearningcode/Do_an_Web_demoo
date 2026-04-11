<?php
/**
 * modalXacNhanDangXuat.php — Modal xác nhận đăng xuất (dạng cũ, dùng ở footer)
 * PHP render sẵn HTML, JS chỉ toggle class — không innerHTML
 * Yêu cầu: $duong_dan_goc (string) đã được khai báo từ header.php
 */
?>
<div class="modal-overlay" id="logout-modal">
    <div class="modal logout-modal">
        <div class="logout-icon"><i class="fas fa-sign-out-alt"></i></div>
        <h2>Đăng xuất</h2>
        <p>Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?</p>
        <div class="logout-actions">
            <button class="cancel-btn" id="logout-cancel">Hủy</button>
            <a href="<?= $duong_dan_goc ?>xuly_dangxuat.php" class="confirm-btn" id="logout-confirm">Đăng xuất</a>
        </div>
    </div>
</div>
