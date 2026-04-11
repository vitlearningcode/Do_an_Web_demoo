<?php
/**
 * danhSachDiaChi.php — HTML danh sách địa chỉ đã lưu + form đặt mặc định + form xóa
 * Yêu cầu: $danhSachDiaChi (array)
 */
?>
<?php if (empty($danhSachDiaChi)): ?>
<div class="dc-trong">
    <i class="fas fa-map-marker-alt" style="font-size:2rem;color:#d1d5db;display:block;margin-bottom:8px;"></i>
    Bạn chưa lưu địa chỉ nào. Thêm ngay bên dưới!
</div>
<?php else: ?>
<div class="dc-danh-sach">
<?php foreach ($danhSachDiaChi as $diaChi): ?>
    <div class="dc-dong <?= $diaChi['laMacDinh'] ? 'mac-dinh' : '' ?>">
        <div class="dc-noi-dung">
            <div class="dc-van-ban"><?= htmlspecialchars($diaChi['diaChiChiTiet']) ?></div>
            <?php if ($diaChi['laMacDinh']): ?>
            <span class="dc-mac-dinh-badge"><i class="fas fa-check"></i> Mặc định</span>
            <?php endif; ?>
        </div>
        <div class="dc-hanh-dong">
            <?php if (!$diaChi['laMacDinh']): ?>
            <form method="POST" action="" style="margin:0;">
                <input type="hidden" name="hanh_dong_dia_chi" value="dat_mac_dinh">
                <input type="hidden" name="ma_dc" value="<?= (int)$diaChi['maDC'] ?>">
                <button type="submit" class="dc-nut mac-dinh">Đặt mặc định</button>
            </form>
            <?php endif; ?>
            <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Xóa địa chỉ này?')">
                <input type="hidden" name="hanh_dong_dia_chi" value="xoa_dia_chi">
                <input type="hidden" name="ma_dc" value="<?= (int)$diaChi['maDC'] ?>">
                <button type="submit" class="dc-nut xoa"><i class="fas fa-trash-alt"></i> Xóa</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
