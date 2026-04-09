<?php
// ══════════════════════════════════════════════════════
//  khuyenMai.php — Quản lý chiến dịch khuyến mãi
// ══════════════════════════════════════════════════════
try {
    $dsKM = $pdo->query("
        SELECT km.maKM, km.tenKM, km.ngayBatDau, km.ngayKetThuc,
               COUNT(ckm.maSach) AS soSach,
               (NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc) AS dangDienRa
        FROM KhuyenMai km
        LEFT JOIN ChiTietKhuyenMai ckm ON km.maKM = ckm.maKM
        GROUP BY km.maKM
        ORDER BY km.ngayBatDau DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dsSach = $pdo->query("
        SELECT maSach, tenSach FROM Sach WHERE trangThai = 'DangKD' ORDER BY tenSach
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $dsKM = $dsSach = [];
}

// Xem chi tiết sách trong chiến dịch
$xemKM = $_GET['xem'] ?? null;
$chiTietKM = [];
if ($xemKM) {
    try {
        $stmtCT = $pdo->prepare("
            SELECT ckm.phanTramGiam, ckm.soLuongKhuyenMai, s.maSach, s.tenSach, s.giaBan
            FROM ChiTietKhuyenMai ckm
            JOIN Sach s ON ckm.maSach = s.maSach
            WHERE ckm.maKM = ?
        ");
        $stmtCT->execute([$xemKM]);
        $chiTietKM = $stmtCT->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $chiTietKM = []; }
}

if (!function_exists('fmtTien')) {
    function fmtTien(float $so): string {
        return number_format($so, 0, ',', '.') . '₫';
    }
}

$baseUrl = 'index.php?trang=khuyenMai';
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Khuyến mãi & Flash Sale</div>
        <div class="adm-section-subtitle">Tạo và quản lý các chiến dịch giảm giá</div>
    </div>
    <a href="<?= $baseUrl ?>&tao=1" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Tạo chiến dịch
    </a>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã chiến dịch</th>
                    <th>Tên</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Số sách</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsKM)): ?>
                <tr><td colspan="7"><div class="adm-empty"><i class="fas fa-tags"></i><p>Chưa có chiến dịch nào.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($dsKM as $km): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($km['maKM']) ?></strong></td>
                    <td><?= htmlspecialchars($km['tenKM']) ?></td>
                    <td style="font-size:13px"><?= date('d/m/Y H:i', strtotime($km['ngayBatDau'])) ?></td>
                    <td style="font-size:13px"><?= date('d/m/Y H:i', strtotime($km['ngayKetThuc'])) ?></td>
                    <td><?= $km['soSach'] ?> sách</td>
                    <td>
                        <?php if ($km['dangDienRa']): ?>
                            <span class="adm-badge adm-badge-success"><i class="fas fa-fire"></i> Đang diễn ra</span>
                        <?php elseif (strtotime($km['ngayKetThuc']) < time()): ?>
                            <span class="adm-badge adm-badge-gray">Đã kết thúc</span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-info"><i class="fas fa-clock"></i> Sắp diễn ra</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <a href="<?= $baseUrl ?>&xem=<?= urlencode($km['maKM']) ?>"
                           class="adm-btn adm-btn-outline adm-btn-sm">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ POPUP: XEM CHI TIẾT CHIẾN DỊCH ═══ -->
<?php if ($xemKM && !empty($chiTietKM)): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px">
<div style="background:#fff;border-radius:16px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
        <h3 style="font-size:16px;font-weight:700">Chi tiết: <?= htmlspecialchars($xemKM) ?></h3>
        <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <div class="adm-table-wrap" style="max-height:400px;overflow-y:auto">
        <table class="adm-table">
            <thead><tr><th>Tên sách</th><th>Giá gốc</th><th>Giảm</th><th>Giá KM</th><th>SL giới hạn</th></tr></thead>
            <tbody>
            <?php foreach ($chiTietKM as $ct): ?>
            <tr>
                <td><?= htmlspecialchars($ct['tenSach']) ?></td>
                <td><?= fmtTien((float)$ct['giaBan']) ?></td>
                <td><span class="adm-badge adm-badge-danger">-<?= $ct['phanTramGiam'] ?>%</span></td>
                <td><strong><?= fmtTien((float)$ct['giaBan'] * (1 - $ct['phanTramGiam']/100)) ?></strong></td>
                <td><?= $ct['soLuongKhuyenMai'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:14px 24px;border-top:1px solid #f1f5f9;text-align:right">
        <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Đóng</a>
    </div>
</div>
</div>
<?php endif; ?>

<!-- ═══ POPUP: TẠO CHIẾN DỊCH MỚI ═══ -->
<?php if (isset($_GET['tao'])): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:640px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff">
        <h3 style="font-size:16px;font-weight:700">Tạo chiến dịch khuyến mãi</h3>
        <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/taoKhuyenMai.php" style="padding:24px">
        <div class="adm-form-group" style="margin-bottom:12px">
            <label style="font-size:13px;font-weight:600">Mã chiến dịch <span style="color:#ef4444">*</span></label>
            <input class="adm-input" type="text" name="maKM" placeholder="VD: SALE_HE_2026" required>
        </div>
        <div class="adm-form-group" style="margin-bottom:12px">
            <label style="font-size:13px;font-weight:600">Tên chiến dịch <span style="color:#ef4444">*</span></label>
            <input class="adm-input" type="text" name="tenKM" placeholder="Flash Sale Mùa Hè..." required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
            <div class="adm-form-group">
                <label style="font-size:13px;font-weight:600">Ngày bắt đầu <span style="color:#ef4444">*</span></label>
                <input class="adm-input" type="datetime-local" name="ngayBatDau" required>
            </div>
            <div class="adm-form-group">
                <label style="font-size:13px;font-weight:600">Ngày kết thúc <span style="color:#ef4444">*</span></label>
                <input class="adm-input" type="datetime-local" name="ngayKetThuc" required>
            </div>
        </div>

        <p style="font-weight:600;font-size:14px;margin-bottom:10px">Sách áp dụng:</p>
        <div style="background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:16px">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:8px;margin-bottom:8px;font-size:12px;font-weight:600;color:#64748b">
                <span>Sách</span><span>Giảm (%)</span><span>Số lượng KM</span>
            </div>
            <?php
            // Các mức giảm hợp lệ (theo CHECK constraint trong DB)
            $mucGiam = [10, 22, 33];
            for ($i = 0; $i < 5; $i++): ?>
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:8px;margin-bottom:8px">
                <select class="adm-input" name="maSach[]" style="padding:8px">
                    <option value="">-- Chọn sách --</option>
                    <?php foreach ($dsSach as $s): ?>
                        <option value="<?= $s['maSach'] ?>"><?= htmlspecialchars($s['tenSach']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="adm-input" name="phanTramGiam[]" style="padding:8px">
                    <?php foreach ($mucGiam as $m): ?>
                        <option value="<?= $m ?>"><?= $m ?>%</option>
                    <?php endforeach; ?>
                </select>
                <input class="adm-input" type="number" name="soLuongKM[]" min="1" placeholder="SL" style="padding:8px" value="50">
            </div>
            <?php endfor; ?>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> Tạo chiến dịch</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
