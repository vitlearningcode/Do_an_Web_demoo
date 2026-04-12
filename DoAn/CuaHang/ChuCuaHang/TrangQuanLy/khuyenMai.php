<?php
// ══════════════════════════════════════════════════════
//  khuyenMai.php — Quản lý chiến dịch khuyến mãi & Quảng cáo
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';
$tab = $_GET['tab'] ?? 'khuyenmai';

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

    // Quảng cáo / banner
    $dsQC = $pdo->query("
        SELECT * FROM QuangCao ORDER BY maQC DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $dsKM = $dsSach = $dsQC = [];
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

// Sửa banner?
$suaQC = null;
$suaQCMa = (int)($_GET['sua_qc'] ?? 0);
if ($suaQCMa > 0) {
    try {
        $stmtQC = $pdo->prepare("SELECT * FROM QuangCao WHERE maQC = ?");
        $stmtQC->execute([$suaQCMa]);
        $suaQC = $stmtQC->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $suaQC = null; }
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
        <div class="adm-section-title">Khuyến mãi & Quảng cáo</div>
        <div class="adm-section-subtitle">Quản lý chiến dịch giảm giá và banner quảng cáo</div>
    </div>
    <?php if ($tab === 'khuyenmai'): ?>
        <a href="<?= $baseUrl ?>&tab=khuyenmai&tao=1" class="adm-btn adm-btn-primary">
            <i class="fas fa-plus"></i> Tạo chiến dịch
        </a>
    <?php else: ?>
        <a href="<?= $baseUrl ?>&tab=quangcao&them_qc=1" class="adm-btn adm-btn-primary">
            <i class="fas fa-plus"></i> Thêm banner
        </a>
    <?php endif; ?>
</div>

<!-- TABS -->
<div class="adm-card" style="margin-bottom:16px;padding:0">
    <div class="adm-tabs" style="padding:0 16px">
        <a href="<?= $baseUrl ?>&tab=khuyenmai" class="adm-tab <?= $tab === 'khuyenmai' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i> Khuyến mãi / Flash Sale
        </a>
        <a href="<?= $baseUrl ?>&tab=quangcao" class="adm-tab <?= $tab === 'quangcao' ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i> Quảng cáo / Banner
        </a>
    </div>
</div>

<?php if ($tab === 'quangcao'): ?>
<!-- ═══ TAB QUẢNG CÁO ═══ -->
<div class="adm-card">
    <?php if (empty($dsQC)): ?>
        <div class="adm-empty"><i class="fas fa-image"></i><p>Chưa có banner quảng cáo nào.</p></div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
        <?php foreach ($dsQC as $qc): ?>
        <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.06)">
            <!-- Preview ảnh -->
            <div style="height:140px;background:#f1f5f9;overflow:hidden;position:relative">
                <img src="<?= anhBanner($qc['hinhAnh'] ?? null) ?>" alt="Banner"
                     style="width:100%;height:100%;object-fit:cover">
                <span style="position:absolute;top:8px;left:8px;background:<?= $qc['trangThai'] ? '#16a34a' : '#9ca3af' ?>;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:99px">
                    <?= $qc['trangThai'] ? '● Đang hiển thị' : '○ Đang ẩn' ?>
                </span>
            </div>
            <!-- Thông tin -->
            <div style="padding:12px 14px">
                <div style="font-size:11px;color:#2563eb;font-weight:600;text-transform:uppercase;margin-bottom:4px">
                    <?= htmlspecialchars($qc['nhan']) ?>
                </div>
                <div style="font-weight:700;font-size:14px;margin-bottom:4px"><?= htmlspecialchars($qc['tieuDe']) ?></div>
                <div style="font-size:12px;color:#64748b;margin-bottom:8px;line-clamp:2;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                    <?= htmlspecialchars($qc['moTa'] ?? '') ?>
                </div>
                <div style="font-size:12px;color:#94a3b8">
                    Nút: <strong><?= htmlspecialchars($qc['chuNut']) ?></strong> · Màu: <span style="color:<?= htmlspecialchars($qc['mauNen']) ?>"><?= htmlspecialchars($qc['mauNen']) ?></span>
                </div>
            </div>
            <!-- Actions -->
            <div style="border-top:1px solid #f1f5f9;padding:8px 14px;display:flex;gap:8px">
                <a href="<?= $baseUrl ?>&tab=quangcao&sua_qc=<?= $qc['maQC'] ?>"
                   class="adm-btn adm-btn-outline adm-btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                <form method="POST" action="XuLy/quangCao.php" style="display:inline">
                    <input type="hidden" name="maQC" value="<?= $qc['maQC'] ?>">
                    <input type="hidden" name="hanh_dong" value="doi_trang_thai">
                    <button type="submit" class="adm-btn adm-btn-sm <?= $qc['trangThai'] ? 'adm-btn-warning' : 'adm-btn-success' ?>">
                        <i class="fas <?= $qc['trangThai'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                        <?= $qc['trangThai'] ? 'Ẩn' : 'Hiện' ?>
                    </button>
                </form>
                <form method="POST" action="XuLy/quangCao.php" style="display:inline"
                      onsubmit="return confirm('Xóa banner này?')">
                    <input type="hidden" name="maQC" value="<?= $qc['maQC'] ?>">
                    <input type="hidden" name="hanh_dong" value="xoa">
                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- POPUP: THÊM BANNER -->
<?php if (isset($_GET['them_qc'])): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff">
        <h3 style="font-size:16px;font-weight:700">Thêm banner quảng cáo</h3>
        <a href="<?= $baseUrl ?>&tab=quangcao" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/quangCao.php" style="padding:24px" enctype="multipart/form-data">
        <input type="hidden" name="hanh_dong" value="them">
        <?php include __DIR__ . '/_formQuangCao.php'; ?>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
            <a href="<?= $baseUrl ?>&tab=quangcao" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> Lưu banner</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>

<!-- POPUP: SỬA BANNER -->
<?php if ($suaQC): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff">
        <h3 style="font-size:16px;font-weight:700">Sửa banner #<?= $suaQC['maQC'] ?></h3>
        <a href="<?= $baseUrl ?>&tab=quangcao" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/quangCao.php" style="padding:24px" enctype="multipart/form-data">
        <input type="hidden" name="hanh_dong" value="sua">
        <input type="hidden" name="maQC" value="<?= $suaQC['maQC'] ?>">
        <?php include __DIR__ . '/_formQuangCao.php'; ?>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
            <a href="<?= $baseUrl ?>&tab=quangcao" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>

<?php else: /* TAB KHUYẾN MÃI */ ?>
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
<?php endif; /* endif tab khuyenmai vs quangcao */ ?>
