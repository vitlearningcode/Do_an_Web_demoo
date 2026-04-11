<?php
/**
 * formThemDiaChi.php — HTML form thêm địa chỉ giao hàng mới
 * Yêu cầu: $danhSachDiaChi (array) — dùng để tự động tick "mặc định" khi chưa có địa chỉ nào
 */
?>
<div class="dc-them-khung">
    <form method="POST" action="">
        <input type="hidden" name="hanh_dong_dia_chi" value="them_moi">
        <label for="dia-chi-moi">Thêm địa chỉ mới</label>
        <input type="text" id="dia-chi-moi" name="dia_chi_moi"
               placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP" required>
        <label class="dc-tuy-chon-mac-dinh">
            <input type="checkbox" name="la_mac_dinh" value="1"
                   <?= empty($danhSachDiaChi) ? 'checked' : '' ?>>
            Đặt làm địa chỉ mặc định
        </label>
        <button type="submit" class="dc-nut-them">
            <i class="fas fa-plus"></i> Thêm địa chỉ
        </button>
    </form>
</div>
