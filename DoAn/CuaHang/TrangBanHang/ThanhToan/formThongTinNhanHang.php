<?php
/**
 * formThongTinNhanHang.php — HTML form thông tin giao hàng
 * Bao gồm: họ tên, sdt, email, chọn địa chỉ đã lưu hoặc nhập mới
 * Yêu cầu: $isLoggedIn, $thongTinKH, $danhSachDiaChi, $diaChiMacDinh
 */
?>
<div class="section-box">
    <h3><i class="fas fa-map-marker-alt"></i> Thông tin nhận hàng</h3>

    <?php if ($isLoggedIn && $thongTinKH): ?>
    <!-- Khách đã đăng nhập: tự điền thông tin -->
    <div class="form-group">
        <label>Họ và tên</label>
        <input type="text" name="hoten" required
               value="<?= htmlspecialchars($thongTinKH['tenND'] ?? '') ?>"
               placeholder="Nhập đầy đủ họ tên...">
    </div>
    <div class="form-group-row">
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="tel" name="sdt" pattern="[0-9]{10,11}" required
                   value="<?= htmlspecialchars($thongTinKH['sdt'] ?? '') ?>"
                   placeholder="Số điện thoại...">
        </div>
        <div class="form-group">
            <label>Email (Để nhận hóa đơn)</label>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($thongTinKH['email'] ?? '') ?>"
                   placeholder="Email liên lạc...">
        </div>
    </div>

    <!-- Chọn địa chỉ giao hàng -->
    <div class="form-group">
        <label>Địa chỉ giao hàng</label>

        <?php if (!empty($danhSachDiaChi)): ?>
        <!-- Chọn: địa chỉ đã lưu hoặc nhập mới -->
        <div class="chon-loai-dc">
            <label>
                <input type="radio" name="loai_dia_chi" value="da_luu"
                       id="radio-da-luu" checked
                       onchange="choiLoaiDiaChi('da_luu')">
                <i class="fas fa-bookmark"></i> Địa chỉ đã lưu
            </label>
            <label>
                <input type="radio" name="loai_dia_chi" value="moi"
                       id="radio-moi"
                       onchange="choiLoaiDiaChi('moi')">
                <i class="fas fa-plus"></i> Nhập địa chỉ mới
            </label>
        </div>

        <!-- Dropdown địa chỉ đã lưu -->
        <div class="khung-dc-da-luu" id="khung-da-luu">
            <select name="ma_dia_chi" id="select-dia-chi">
            <?php foreach ($danhSachDiaChi as $diaChi): ?>
                <option value="<?= (int)$diaChi['maDC'] ?>"
                        <?= $diaChi['laMacDinh'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($diaChi['diaChiChiTiet']) ?>
                    <?= $diaChi['laMacDinh'] ? ' ★ Mặc định' : '' ?>
                </option>
            <?php endforeach; ?>
            </select>
            <a href="../taiKhoan/capNhat.php" class="link-them-dc" style="margin-top:6px;display:inline-block;">
                <i class="fas fa-plus-circle"></i> Thêm địa chỉ mới trong hồ sơ
            </a>
        </div>

        <!-- Ô nhập địa chỉ mới (ẩn mặc định) -->
        <div class="khung-dc-moi" id="khung-dc-moi">
            <input type="text" name="dia_chi_moi"
                   id="nhap-dia-chi-moi"
                   placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
        </div>

        <?php else: ?>
        <!-- Chưa có địa chỉ nào → chỉ hiện ô nhập -->
        <input type="hidden" name="loai_dia_chi" value="moi">
        <input type="text" name="dia_chi_moi"
               value="<?= htmlspecialchars($diaChiMacDinh) ?>"
               required
               placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
        <a href="../taiKhoan/capNhat.php" class="link-them-dc" style="margin-top:6px;display:inline-block;">
            <i class="fas fa-save"></i> Lưu địa chỉ vào hồ sơ để dùng lần sau
        </a>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- Khách vãng lai: tất cả để trống -->
    <div class="form-group">
        <label>Họ và tên</label>
        <input type="text" name="hoten" required placeholder="Nhập đầy đủ họ tên...">
    </div>
    <div class="form-group-row">
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="tel" name="sdt" pattern="[0-9]{10,11}" required placeholder="Số điện thoại...">
        </div>
        <div class="form-group">
            <label>Email (Để nhận hóa đơn)</label>
            <input type="email" name="email" required placeholder="Email liên lạc...">
        </div>
    </div>
    <div class="form-group">
        <label>Địa chỉ giao hàng</label>
        <input type="hidden" name="loai_dia_chi" value="moi">
        <input type="text" name="dia_chi_moi" required
               placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
    </div>
    <?php endif; ?>
</div>
