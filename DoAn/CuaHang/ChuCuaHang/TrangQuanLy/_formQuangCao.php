<?php
/**
 * _formQuangCao.php — Form fields chung cho Thêm / Sửa banner
 * Biến $suaQC được truyền vào từ context gọi (null = thêm mới)
 */
$qcData = $suaQC ?? [];
$mauNens = [
    'blue'    => 'Xanh dương',
    'emerald' => 'Xanh lá',
    'purple'  => 'Tím',
    'red'     => 'Đỏ',
    'orange'  => 'Cam',
    'gray'    => 'Xám',
];
?>
<div style="display:flex;flex-direction:column;gap:14px">

    <!-- Ảnh bìa -->
    <div class="adm-form-group">
        <label style="font-size:13px;font-weight:600">Hình ảnh banner <span style="color:#ef4444">*</span></label>
        <?php if (!empty($qcData['hinhAnh'])): ?>
        <div style="margin-bottom:8px">
            <img src="<?= anhBanner($qcData['hinhAnh'] ?? null) ?>" alt="Ảnh hiện tại"
                 style="width:100%;max-height:120px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0">
            <div style="font-size:12px;color:#64748b;margin-top:4px">Ảnh hiện tại — upload mới để thay thế</div>
        </div>
        <?php endif; ?>
        <input class="adm-input" type="text" name="hinhAnh_url"
               value="<?= htmlspecialchars($qcData['hinhAnh'] ?? '') ?>"
               placeholder="https://... (URL ảnh)">
        <div style="font-size:12px;color:#94a3b8;text-align:center;padding:4px 0">— HOẶC —</div>
        <input class="adm-input" type="file" name="hinhAnh_file"
               accept="image/jpeg,image/png,image/webp,image/gif" style="padding:6px">
    </div>

    <!-- Nhãn -->
    <div class="adm-form-group">
        <label style="font-size:13px;font-weight:600">Nhãn <span style="color:#ef4444">*</span></label>
        <input class="adm-input" type="text" name="nhan"
               value="<?= htmlspecialchars($qcData['nhan'] ?? '') ?>"
               placeholder="VD: Khuyến mãi tháng 10" required>
    </div>

    <!-- Tiêu đề -->
    <div class="adm-form-group">
        <label style="font-size:13px;font-weight:600">Tiêu đề <span style="color:#ef4444">*</span></label>
        <input class="adm-input" type="text" name="tieuDe"
               value="<?= htmlspecialchars($qcData['tieuDe'] ?? '') ?>"
               placeholder="VD: Hội Sách Mùa Thu<br>Giảm Giá 50%" required>
        <div style="font-size:11px;color:#94a3b8;margin-top:3px">Có thể dùng &lt;br&gt; để xuống dòng trong tiêu đề</div>
    </div>

    <!-- Mô tả -->
    <div class="adm-form-group">
        <label style="font-size:13px;font-weight:600">Mô tả</label>
        <textarea class="adm-textarea" name="moTa" rows="2"
                  placeholder="Mô tả ngắn về chương trình..."><?= htmlspecialchars($qcData['moTa'] ?? '') ?></textarea>
    </div>

    <!-- Chữ nút & Màu nền (2 cột) -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="adm-form-group">
            <label style="font-size:13px;font-weight:600">Chữ nút CTA</label>
            <input class="adm-input" type="text" name="chuNut"
                   value="<?= htmlspecialchars($qcData['chuNut'] ?? 'Xem thêm') ?>"
                   placeholder="Mua Ngay">
        </div>
        <div class="adm-form-group">
            <label style="font-size:13px;font-weight:600">Màu nền</label>
            <select class="adm-input" name="mauNen">
                <?php foreach ($mauNens as $val => $tenMau): ?>
                    <option value="<?= $val ?>" <?= ($qcData['mauNen'] ?? 'blue') === $val ? 'selected' : '' ?>>
                        <?= $tenMau ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

</div>
