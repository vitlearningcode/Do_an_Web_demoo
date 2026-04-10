<?php
// ══════════════════════════════════════════════════════
//  nhapHang.php — Quản lý phiếu nhập & công nợ NCC
// ══════════════════════════════════════════════════════
$tab = $_GET['tab'] ?? 'phieu';

try {
    // ── Tab Phiếu nhập ──
    $dsPhieuNhap = $pdo->query("
        SELECT pn.maPN, pn.ngayLap, pn.tongLuongNhap, pn.tongTien,
               pn.soTienDaThanhToan, pn.trangThai,
               ncc.tenNCC,
               (pn.tongTien - pn.soTienDaThanhToan) AS conNo
        FROM PhieuNhap pn
        JOIN NhaCungCap ncc ON pn.maNCC = ncc.maNCC
        ORDER BY pn.ngayLap DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── Tab Công nợ ──
    $dsCongNo = $pdo->query("
        SELECT cn.tongNo, cn.capNhatCuoi,
               ncc.maNCC, ncc.tenNCC, ncc.sdt, ncc.email
        FROM CongNo cn
        JOIN NhaCungCap ncc ON cn.maNCC = ncc.maNCC
        ORDER BY cn.tongNo DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ── Form tạo phiếu: lấy NCC + Sách ──
    $dsNCC  = $pdo->query("SELECT maNCC, tenNCC FROM NhaCungCap ORDER BY tenNCC")->fetchAll(PDO::FETCH_ASSOC);
    $dsSach = $pdo->query("SELECT maSach, tenSach, giaBan FROM Sach WHERE trangThai='DangKD' ORDER BY tenSach")->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $dsPhieuNhap = $dsCongNo = $dsNCC = $dsSach = [];
}

if (!function_exists('fmtTien')) {
    function fmtTien(float $so): string {
        return number_format($so, 0, ',', '.') . '₫';
    }
}

$baseUrl = 'index.php?trang=nhapHang';
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Nhập hàng & Công nợ</div>
        <div class="adm-section-subtitle">Quản lý phiếu nhập và theo dõi công nợ nhà cung cấp</div>
    </div>
    <a href="<?= $baseUrl ?>&tab=phieu&tao=1" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Tạo phiếu nhập
    </a>
</div>

<!-- TABS -->
<div class="adm-card" style="margin-bottom:0">
<div class="adm-tabs">
    <a href="<?= $baseUrl ?>&tab=phieu" class="adm-tab <?= $tab === 'phieu' ? 'active' : '' ?>">
        <i class="fas fa-file-invoice"></i> Phiếu nhập hàng
    </a>
    <a href="<?= $baseUrl ?>&tab=congno" class="adm-tab <?= $tab === 'congno' ? 'active' : '' ?>">
        <i class="fas fa-hand-holding-usd"></i> Công nợ NCC
    </a>
</div>

<?php if ($tab === 'phieu'): ?>
<!-- ── BẢNG PHIẾU NHẬP ── -->
<div class="adm-table-wrap">
<table class="adm-table">
    <thead>
        <tr>
            <th>Mã phiếu</th>
            <th>Ngày lập</th>
            <th>Nhà cung cấp</th>
            <th>SL nhập</th>
            <th>Tổng tiền</th>
            <th>Đã thanh toán</th>
            <th>Còn nợ</th>
            <th>Trạng thái</th>
            <th style="text-align:center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($dsPhieuNhap)): ?>
        <tr><td colspan="9"><div class="adm-empty"><i class="fas fa-box-open"></i><p>Chưa có phiếu nhập nào.</p></div></td></tr>
    <?php else: ?>
        <?php foreach ($dsPhieuNhap as $pn): ?>
        <tr>
            <td><strong><?= htmlspecialchars($pn['maPN']) ?></strong></td>
            <td style="font-size:13px"><?= date('d/m/Y', strtotime($pn['ngayLap'])) ?></td>
            <td><?= htmlspecialchars($pn['tenNCC']) ?></td>
            <td><?= number_format($pn['tongLuongNhap']) ?></td>
            <td><?= fmtTien((float)$pn['tongTien']) ?></td>
            <td><?= fmtTien((float)$pn['soTienDaThanhToan']) ?></td>
            <td>
                <?php if ((float)$pn['conNo'] > 0): ?>
                    <strong style="color:#dc2626"><?= fmtTien((float)$pn['conNo']) ?></strong>
                <?php else: ?>
                    <span style="color:#16a34a">Đã trả đủ</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($pn['trangThai'] === 'Completed'): ?>
                    <span class="adm-badge adm-badge-success">Hoàn thành</span>
                <?php elseif ($pn['trangThai'] === 'Returned'): ?>
                    <span class="adm-badge adm-badge-danger">Đã trả</span>
                <?php else: ?>
                    <span class="adm-badge adm-badge-warning">Đang chờ</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center">
                <?php if ($pn['trangThai'] === 'Waiting' && (float)$pn['conNo'] > 0): ?>
                <a href="<?= $baseUrl ?>&tab=phieu&thanh_toan=<?= urlencode($pn['maPN']) ?>"
                   class="adm-btn adm-btn-success adm-btn-sm">
                    <i class="fas fa-money-bill-wave"></i> Thanh toán
                </a>
                <?php else: ?>
                    <span style="color:#94a3b8;font-size:13px">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php elseif ($tab === 'congno'): ?>
<!-- ── BẢNG CÔNG NỢ ── -->
<div class="adm-table-wrap">
<table class="adm-table">
    <thead>
        <tr>
            <th>Nhà cung cấp</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Tổng nợ hiện tại</th>
            <th>Cập nhật lần cuối</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($dsCongNo)): ?>
        <tr><td colspan="5"><div class="adm-empty"><i class="fas fa-check-double"></i><p>Không có công nợ nào.</p></div></td></tr>
    <?php else: ?>
        <?php foreach ($dsCongNo as $cn): ?>
        <tr>
            <td><strong><?= htmlspecialchars($cn['tenNCC']) ?></strong></td>
            <td><?= htmlspecialchars($cn['sdt'] ?? '—') ?></td>
            <td style="font-size:13px"><?= htmlspecialchars($cn['email'] ?? '—') ?></td>
            <td>
                <?php if ((float)$cn['tongNo'] > 0): ?>
                    <strong style="color:#dc2626;font-size:15px"><?= fmtTien((float)$cn['tongNo']) ?></strong>
                <?php else: ?>
                    <span class="adm-badge adm-badge-success">Không nợ</span>
                <?php endif; ?>
            </td>
            <td style="font-size:13px;color:#64748b"><?= $cn['capNhatCuoi'] ? date('d/m/Y H:i', strtotime($cn['capNhatCuoi'])) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
</div><!-- /.adm-card -->

<!-- ═══ POPUP: THANH TOÁN PHIẾU NHẬP ═══ -->
<?php if (isset($_GET['thanh_toan'])): ?>
<?php
$maPNThanhToan = $_GET['thanh_toan'];
try {
    $stmtPN = $pdo->prepare("
        SELECT pn.*, ncc.tenNCC
        FROM PhieuNhap pn JOIN NhaCungCap ncc ON pn.maNCC = ncc.maNCC
        WHERE pn.maPN = ?
    ");
    $stmtPN->execute([$maPNThanhToan]);
    $pnInfo = $stmtPN->fetch(PDO::FETCH_ASSOC);
} catch(Throwable $e) { $pnInfo = null; }
?>
<?php if ($pnInfo): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px">
<div style="background:#fff;border-radius:16px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
        <h3 style="font-size:16px;font-weight:700">Thanh toán phiếu <?= htmlspecialchars($maPNThanhToan) ?></h3>
        <a href="<?= $baseUrl ?>&tab=phieu" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <div style="padding:20px 24px">
        <div style="background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:16px;font-size:14px">
            <p>NCC: <strong><?= htmlspecialchars($pnInfo['tenNCC']) ?></strong></p>
            <p>Tổng tiền: <strong><?= fmtTien((float)$pnInfo['tongTien']) ?></strong></p>
            <p>Đã trả: <?= fmtTien((float)$pnInfo['soTienDaThanhToan']) ?></p>
            <p>Còn nợ: <strong style="color:#dc2626"><?= fmtTien((float)$pnInfo['tongTien'] - (float)$pnInfo['soTienDaThanhToan']) ?></strong></p>
        </div>
        <form method="POST" action="XuLy/thanhToanPhieuNhap.php">
            <input type="hidden" name="maPN" value="<?= htmlspecialchars($maPNThanhToan) ?>">
            <div class="adm-form-group" style="margin-bottom:12px">
                <label style="font-size:13px;font-weight:600">Số tiền trả (₫) <span style="color:#ef4444">*</span></label>
                <input class="adm-input" type="number" name="soTienTra" min="1"
                       max="<?= (float)$pnInfo['tongTien'] - (float)$pnInfo['soTienDaThanhToan'] ?>"
                       placeholder="Nhập số tiền..." required>
            </div>
            <div class="adm-form-group" style="margin-bottom:12px">
                <label style="font-size:13px;font-weight:600">Hình thức thanh toán</label>
                <select class="adm-input" name="hinhThucTra">
                    <option>Chuyển khoản</option>
                    <option>Tiền mặt</option>
                    <option>Chuyển khoản VCB</option>
                </select>
            </div>
            <div class="adm-form-group" style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600">Ghi chú</label>
                <input class="adm-input" type="text" name="ghiChu" placeholder="Ghi chú (tùy chọn)">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="<?= $baseUrl ?>&tab=phieu" class="adm-btn adm-btn-outline">Hủy</a>
                <button type="submit" class="adm-btn adm-btn-success"><i class="fas fa-check"></i> Xác nhận thanh toán</button>
            </div>
        </form>
    </div>
</div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- ═══ POPUP: TẠO PHIẾU NHẬP MỚI ═══ -->
<?php if (isset($_GET['tao'])): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:640px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff">
        <h3 style="font-size:16px;font-weight:700">Tạo phiếu nhập hàng mới</h3>
        <a href="<?= $baseUrl ?>&tab=phieu" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/taoPhieuNhap.php" style="padding:24px">
        <div class="adm-form-group" style="margin-bottom:14px">
            <label style="font-size:13px;font-weight:600">Mã phiếu nhập <span style="color:#ef4444">*</span></label>
            <input class="adm-input" type="text" name="maPN" placeholder="VD: PN003" required>
        </div>
        <div class="adm-form-group" style="margin-bottom:14px">
            <label style="font-size:13px;font-weight:600">Nhà cung cấp <span style="color:#ef4444">*</span></label>
            <select class="adm-input" name="maNCC" required>
                <option value="">-- Chọn NCC --</option>
                <?php foreach ($dsNCC as $ncc): ?>
                    <option value="<?= $ncc['maNCC'] ?>"><?= htmlspecialchars($ncc['tenNCC']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <p style="font-weight:600;font-size:14px;margin-bottom:10px;margin-top:4px">Chi tiết sách nhập:</p>
        <div style="background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:16px">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:8px;margin-bottom:8px;font-size:12px;font-weight:600;color:#64748b">
                <span>Sách</span><span>Số lượng</span><span>Giá nhập (₫)</span><span>Chiết khấu (%)</span>
            </div>
            <?php for ($i = 0; $i < 5; $i++): ?>
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:8px;margin-bottom:8px">
                <select class="adm-input" name="maSach[]" style="padding:8px">
                    <option value="">-- Chọn sách --</option>
                    <?php foreach ($dsSach as $s): ?>
                        <option value="<?= $s['maSach'] ?>"><?= htmlspecialchars($s['tenSach']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="adm-input" type="number" name="soLuong[]" min="1" placeholder="SL" style="padding:8px">
                <input class="adm-input" type="number" name="giaNhap[]" min="0" placeholder="Giá" style="padding:8px">
                <input class="adm-input" type="number" name="chietKhau[]" min="0" max="100" placeholder="%" style="padding:8px" value="0">
            </div>
            <?php endfor; ?>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="<?= $baseUrl ?>&tab=phieu" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> Lưu phiếu nhập</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
