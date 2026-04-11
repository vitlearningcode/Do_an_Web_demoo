<?php
/**
 * formThongTinCaNhan.php — HTML form cập nhật thông tin cá nhân + đổi mật khẩu
 * Yêu cầu: $nguoiDung (array), $taiKhoan (array)
 * Action POST → capNhat.php với hanh_dong_thong_tin = 1
 */
?>
<div class="cn-the">
    <div class="cn-avatar"><?= mb_strtoupper(mb_substr($nguoiDung['tenND'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') ?></div>
    <p class="cn-cap">Tài khoản: <strong><?= htmlspecialchars($taiKhoan['tenDN'] ?? '') ?></strong></p>

    <form action="" method="POST">
        <input type="hidden" name="hanh_dong_thong_tin" value="1">
        <p class="cn-tieu-muc"><i class="fas fa-user"></i> Thông Tin Cá Nhân</p>
        <div class="cn-nhom">
            <label for="hoten">Họ và tên</label>
            <input type="text" id="hoten" name="hoten" value="<?= htmlspecialchars($nguoiDung['tenND'] ?? '') ?>" required>
        </div>
        <div class="cn-nhom">
            <label for="sdt">Số điện thoại</label>
            <input type="tel" id="sdt" name="sdt" value="<?= htmlspecialchars($nguoiDung['sdt'] ?? '') ?>" placeholder="0912345678">
        </div>
        <div class="cn-nhom">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($nguoiDung['email'] ?? '') ?>" placeholder="example@email.com">
        </div>

        <hr class="cn-sep">
        <p class="cn-tieu-muc"><i class="fas fa-lock"></i> Đổi Mật Khẩu <small style="font-weight:400;color:#9ca3af;font-size:.82rem;">(để trống nếu không đổi)</small></p>
        <div class="cn-nhom">
            <label for="mk_cu">Mật khẩu cũ</label>
            <input type="password" id="mk_cu" name="mk_cu" placeholder="Nhập mật khẩu hiện tại">
        </div>
        <div class="cn-nhom">
            <label for="mk_moi">Mật khẩu mới</label>
            <input type="password" id="mk_moi" name="mk_moi" placeholder="Nhập mật khẩu mới">
        </div>
        <div class="cn-nhom">
            <label for="mk_xn">Xác nhận mật khẩu mới</label>
            <input type="password" id="mk_xn" name="mk_xn" placeholder="Nhập lại mật khẩu mới">
        </div>
        <button type="submit" class="cn-nut-luu"><i class="fas fa-save"></i> Lưu thông tin</button>
    </form>
</div>
