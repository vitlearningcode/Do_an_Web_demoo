<?php
// ══════════════════════════════════════════════════════
//  nhaCungCap.php — Quản lý đối tác và nhà cung cấp
// ══════════════════════════════════════════════════════

require_once __DIR__ . '/../_kiemTraQuyen.php';

try {
    $dsNCC = $pdo->query("
        SELECT * FROM NhaCungCap ORDER BY maNCC DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $dsNCC = [];
}

// Lấy thông tin nhà cung cấp cần sửa
$suaNCC = null;
$suaMaNCC = (int)($_GET['sua'] ?? 0);
if ($suaMaNCC > 0) {
    try {
        $stmtSua = $pdo->prepare("SELECT * FROM NhaCungCap WHERE maNCC = ?");
        $stmtSua->execute([$suaMaNCC]);
        $suaNCC = $stmtSua->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $suaNCC = null; }
}

$baseUrl = 'index.php?trang=nhaCungCap';
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Quản lý Nhà Cung Cấp</div>
        <div class="adm-section-subtitle">Danh sách các đối tác cung cấp sách và chiết khấu mặc định</div>
    </div>
    <a href="<?= $baseUrl ?>&them=1" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Thêm nhà cung cấp
    </a>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã NCC</th>
                    <th>Tên đối tác</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Chiết khấu MĐ</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsNCC)): ?>
                <tr><td colspan="6"><div class="adm-empty"><i class="fas fa-handshake"></i><p>Chưa có nhà cung cấp nào.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($dsNCC as $ncc): ?>
                <tr>
                    <td><strong>#<?= htmlspecialchars($ncc['maNCC']) ?></strong></td>
                    <td style="font-weight: 500; color: #1e293b;"><i class="far fa-building" style="margin-right:6px; color:#94a3b8"></i><?= htmlspecialchars($ncc['tenNCC']) ?></td>
                    <td><?= htmlspecialchars($ncc['sdt'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($ncc['email'] ?? '—') ?></td>
                    <td>
                        <span class="adm-badge adm-badge-info"><?= (float)($ncc['chietKhauMacDinh'] ?? 0) ?>%</span>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        <a href="<?= $baseUrl ?>&sua=<?= $ncc['maNCC'] ?>" class="adm-btn adm-btn-outline adm-btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="XuLy/nhaCungCap.php" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này không? (Nếu NCC đã có phiếu nhập, sẽ không thể xóa)')">
                            <input type="hidden" name="maNCC" value="<?= $ncc['maNCC'] ?>">
                            <input type="hidden" name="hanh_dong" value="xoa">
                            <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= POPUP THÊM / SỬA ================= -->
<?php if (isset($_GET['them']) || $suaNCC): 
    $laSua = $suaNCC !== null;
    $tieude = $laSua ? "Sửa Nhà Cung Cấp #{$suaNCC['maNCC']}" : "Thêm Nhà Cung Cấp Mới";
    $hanh_dong = $laSua ? 'sua' : 'them';
?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-size:16px;font-weight:700"><?= $tieude ?></h3>
        <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/nhaCungCap.php" style="padding:24px">
        <input type="hidden" name="hanh_dong" value="<?= $hanh_dong ?>">
        <?php if ($laSua): ?>
            <input type="hidden" name="maNCC" value="<?= $suaNCC['maNCC'] ?>">
        <?php endif; ?>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Tên Nhà Cung Cấp <span style="color:#ef4444">*</span></label>
            <input class="adm-input" type="text" name="tenNCC" value="<?= $laSua ? htmlspecialchars($suaNCC['tenNCC']) : '' ?>" placeholder="VD: Công ty TNHH Sách..." required>
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Số Điện Thoại</label>
            <input class="adm-input" type="text" name="sdt" value="<?= $laSua ? htmlspecialchars($suaNCC['sdt'] ?? '') : '' ?>" placeholder="VD: 0912345678">
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Email</label>
            <input class="adm-input" type="email" name="email" value="<?= $laSua ? htmlspecialchars($suaNCC['email'] ?? '') : '' ?>" placeholder="VD: contact@company.com">
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Chiết Khấu Mặc Định (%)</label>
            <input class="adm-input" type="number" step="0.1" name="chietKhauMacDinh" value="<?= $laSua ? (float)($suaNCC['chietKhauMacDinh'] ?? 0) : '0' ?>" required>
            <small style="color:#64748b;font-size:11px;margin-top:4px;display:block;">Chiết khấu mặc định để tính giá nhập sách. 0-100%.</small>
        </div>
        
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px">
            <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary">
                <i class="fas fa-save"></i> <?= $laSua ? 'Lưu thay đổi' : 'Thêm mới' ?>
            </button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
