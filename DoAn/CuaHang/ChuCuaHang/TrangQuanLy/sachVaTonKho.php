<?php
// ══════════════════════════════════════════════════════
//  sachVaTonKho.php — Quản lý sách & Tồn kho tích hợp
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';

$trang      = max(1, (int)($_GET['trang_so'] ?? 1));
$moiTrang   = 15;
$offset     = ($trang - 1) * $moiTrang;
$locTL      = $_GET['loc_tl']  ?? '';
$timKiem    = trim($_GET['tim'] ?? '');
$locTrang   = $_GET['loc_tt']  ?? ''; // DangKD | NgungKD | ''

// Xây điều kiện WHERE
$dieuKien   = "WHERE 1=1";
$params     = [];
if ($timKiem !== '') {
    $dieuKien .= " AND (s.tenSach LIKE ? OR s.maSach LIKE ?)";
    $params[] = "%$timKiem%";
    $params[] = "%$timKiem%";
}
if ($locTL !== '') {
    $dieuKien .= " AND tl.maTL = ?";
    $params[] = $locTL;
}
if ($locTrang !== '') {
    $dieuKien .= " AND s.trangThai = ?";
    $params[] = $locTrang;
}

try {
    // Danh mục cho filter
    $dsTheLoai = $pdo->query("SELECT maTL, tenTL FROM TheLoai ORDER BY tenTL")->fetchAll(PDO::FETCH_ASSOC);
    $dsNXB     = $pdo->query("SELECT maNXB, tenNXB FROM NhaXuatBan ORDER BY tenNXB")->fetchAll(PDO::FETCH_ASSOC);
    $dsTacGia  = $pdo->query("SELECT maTG, tenTG FROM TacGia ORDER BY tenTG")->fetchAll(PDO::FETCH_ASSOC);

    // Đếm tổng (cần JOIN TheLoai để filter)
    $sqlDem = "SELECT COUNT(DISTINCT s.maSach) FROM Sach s
               LEFT JOIN Sach_TheLoai st ON s.maSach = st.maSach
               LEFT JOIN TheLoai tl ON st.maTL = tl.maTL
               $dieuKien";
    $stmtDem = $pdo->prepare($sqlDem);
    $stmtDem->execute($params);
    $tongSach = (int)$stmtDem->fetchColumn();

    // Lấy danh sách sách
    $sqlSach = "
        SELECT s.maSach, s.tenSach, s.giaBan, s.soLuongTon, s.namSX, s.loaiBia, s.trangThai,
               nxb.tenNXB,
               (SELECT ha.urlAnh FROM HinhAnhSach ha WHERE ha.maSach = s.maSach LIMIT 1) AS anhBia,
               GROUP_CONCAT(DISTINCT tg.tenTG ORDER BY tg.tenTG SEPARATOR ', ') AS dsTacGia,
               GROUP_CONCAT(DISTINCT tl.tenTL ORDER BY tl.tenTL SEPARATOR ', ') AS dsTheLoai
        FROM Sach s
        LEFT JOIN NhaXuatBan nxb ON s.maNXB = nxb.maNXB
        LEFT JOIN Sach_TacGia stg ON s.maSach = stg.maSach
        LEFT JOIN TacGia tg ON stg.maTG = tg.maTG
        LEFT JOIN Sach_TheLoai st ON s.maSach = st.maSach
        LEFT JOIN TheLoai tl ON st.maTL = tl.maTL
        $dieuKien
        GROUP BY s.maSach
        ORDER BY s.maSach DESC
        LIMIT $moiTrang OFFSET $offset
    ";
    $stmtSach = $pdo->prepare($sqlSach);
    $stmtSach->execute($params);
    $dsSach = $stmtSach->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $dsSach = []; $tongSach = 0;
    $dsTheLoai = $dsNXB = $dsTacGia = [];
}

$tongTrang = max(1, (int)ceil($tongSach / $moiTrang));
$baseUrl   = 'index.php?trang=sachVaTonKho';

if (!function_exists('fmtTien')) {
    function fmtTien(float $so): string {
        return number_format($so, 0, ',', '.') . '₫';
    }
}

// Đang sửa sách?
$suaMaSach = $_GET['sua'] ?? null;
$sachSua   = null;
if ($suaMaSach) {
    try {
        $stmtSua = $pdo->prepare("
            SELECT s.*,
                   GROUP_CONCAT(DISTINCT stg.maTG) AS maTG_list,
                   GROUP_CONCAT(DISTINCT st.maTL) AS maTL_list
            FROM Sach s
            LEFT JOIN Sach_TacGia stg ON s.maSach = stg.maSach
            LEFT JOIN Sach_TheLoai st ON s.maSach = st.maSach
            WHERE s.maSach = ?
            GROUP BY s.maSach
        ");
        $stmtSua->execute([$suaMaSach]);
        $sachSua = $stmtSua->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $sachSua = null; }
}
?>

<!-- ── SECTION HEADER ── -->
<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Sách & Tồn kho</div>
        <div class="adm-section-subtitle">Tổng cộng <?= number_format($tongSach) ?> cuốn sách</div>
    </div>
    <a href="<?= $baseUrl ?>&them=1" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Thêm sách mới
    </a>
</div>

<!-- ── FILTER BAR ── -->
<form method="GET" action="index.php">
<input type="hidden" name="trang" value="sachVaTonKho">
<div class="adm-card" style="margin-bottom:16px">
    <div class="adm-filter-bar">
        <div class="adm-search-input">
            <i class="fas fa-search"></i>
            <input type="text" name="tim" placeholder="Tên sách, mã sách..." value="<?= htmlspecialchars($timKiem) ?>">
        </div>
        <select class="adm-select" name="loc_tl">
            <option value="">Tất cả thể loại</option>
            <?php foreach ($dsTheLoai as $tl): ?>
                <option value="<?= $tl['maTL'] ?>" <?= $locTL == $tl['maTL'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tl['tenTL']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select class="adm-select" name="loc_tt">
            <option value="">Tất cả trạng thái</option>
            <option value="DangKD"  <?= $locTrang === 'DangKD'  ? 'selected' : '' ?>>Đang kinh doanh</option>
            <option value="NgungKD" <?= $locTrang === 'NgungKD' ? 'selected' : '' ?>>Ngưng kinh doanh</option>
        </select>
        <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline"><i class="fas fa-times"></i> Xóa lọc</a>
    </div>
</div>
</form>

<!-- ── TABLE ── -->
<div class="adm-card" style="margin-bottom:0">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Ảnh bìa</th>
                    <th>Mã / Tên sách</th>
                    <th>Tác giả</th>
                    <th>Thể loại</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsSach)): ?>
                <tr><td colspan="8"><div class="adm-empty"><i class="fas fa-book-open"></i><p>Không tìm thấy sách nào.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($dsSach as $sach): ?>
                <tr>
                    <td>
                        <?php if ($sach['anhBia']): ?>
                            <img src="<?= anhSach($sach['anhBia'] ?? null) ?>" alt="Bìa"
                                 style="width:40px;height:55px;object-fit:cover;border-radius:5px;border:1px solid #e2e8f0">
                        <?php else: ?>
                            <div style="width:40px;height:55px;background:#f1f5f9;border-radius:5px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:18px">
                                <i class="fas fa-book"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:14px"><?= htmlspecialchars($sach['tenSach']) ?></div>
                        <div style="font-size:12px;color:#94a3b8"><?= htmlspecialchars($sach['maSach']) ?> · <?= htmlspecialchars($sach['tenNXB'] ?? '') ?></div>
                    </td>
                    <td style="font-size:13px;color:#475569"><?= htmlspecialchars($sach['dsTacGia'] ?? '—') ?></td>
                    <td>
                        <?php foreach (explode(', ', $sach['dsTheLoai'] ?? '') as $tl): ?>
                            <?php if (trim($tl)): ?>
                            <span class="adm-badge adm-badge-info" style="margin:2px 2px 2px 0;font-size:11px"><?= htmlspecialchars(trim($tl)) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td><strong><?= fmtTien((float)$sach['giaBan']) ?></strong></td>
                    <td>
                        <?php
                        $ton = (int)$sach['soLuongTon'];
                        if ($ton <= 5):      echo "<span class=\"adm-stock-low\">$ton</span>";
                        elseif ($ton <= 20): echo "<span class=\"adm-stock-mid\">$ton</span>";
                        else:                echo "<span class=\"adm-stock-ok\">$ton</span>";
                        ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($sach['trangThai'] === 'DangKD'): ?>
                            <span class="adm-badge adm-badge-success">Đang KD</span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-gray">Ngưng KD</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="adm-action-group" style="justify-content:center">
                            <!-- Sửa -->
                            <a href="<?= $baseUrl ?>&sua=<?= urlencode($sach['maSach']) ?>"
                               class="adm-btn adm-btn-outline adm-btn-icon" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Toggle trạng thái -->
                            <form method="POST" action="XuLy/xoaSach.php" style="display:inline">
                                <input type="hidden" name="maSach" value="<?= htmlspecialchars($sach['maSach']) ?>">
                                <input type="hidden" name="hanh_dong" value="toggle_tt">
                                <button type="submit"
                                    class="adm-btn adm-btn-icon <?= $sach['trangThai'] === 'DangKD' ? 'adm-btn-warning' : 'adm-btn-success' ?>"
                                    title="<?= $sach['trangThai'] === 'DangKD' ? 'Ngưng kinh doanh' : 'Kích hoạt lại' ?>">
                                    <i class="fas <?= $sach['trangThai'] === 'DangKD' ? 'fa-pause' : 'fa-play' ?>"></i>
                                </button>
                            </form>
                            <!-- Xóa hẳn -->
                            <form method="POST" action="XuLy/xoaSach.php" style="display:inline"
                                  onsubmit="return confirm('Xóa vĩnh viễn sách <?= htmlspecialchars(addslashes($sach['tenSach'])) ?>?')">
                                <input type="hidden" name="maSach" value="<?= htmlspecialchars($sach['maSach']) ?>">
                                <input type="hidden" name="hanh_dong" value="xoa">
                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-icon" title="Xóa vĩnh viễn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <?php if ($tongTrang > 1): ?>
    <div class="adm-pagination">
        <span>Hiển thị <?= $offset + 1 ?>–<?= min($offset + $moiTrang, $tongSach) ?> / <?= $tongSach ?> cuốn</span>
        <div class="adm-pagination-btns">
            <?php if ($trang > 1): ?>
                <a href="<?= $baseUrl ?>&trang_so=<?= $trang - 1 ?>&loc_tl=<?= urlencode($locTL) ?>&tim=<?= urlencode($timKiem) ?>">‹</a>
            <?php endif; ?>
            <?php for ($i = max(1,$trang-2); $i <= min($tongTrang,$trang+2); $i++): ?>
                <?php if ($i == $trang): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="<?= $baseUrl ?>&trang_so=<?= $i ?>&loc_tl=<?= urlencode($locTL) ?>&tim=<?= urlencode($timKiem) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($trang < $tongTrang): ?>
                <a href="<?= $baseUrl ?>&trang_so=<?= $trang + 1 ?>&loc_tl=<?= urlencode($locTL) ?>&tim=<?= urlencode($timKiem) ?>">›</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ═══ FORM THÊM / SỬA SÁCH (popup) ═══ -->
<?php if (isset($_GET['them']) || $sachSua): ?>
<?php $isEdit = (bool)$sachSua; ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:680px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:1">
        <h3 style="font-size:16px;font-weight:700">
            <?= $isEdit ? 'Sửa sách: ' . htmlspecialchars($sachSua['tenSach']) : 'Thêm sách mới' ?>
        </h3>
        <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/themSuaSach.php" style="padding:24px" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
            <input type="hidden" name="maSach_cu" value="<?= htmlspecialchars($sachSua['maSach']) ?>">
        <?php endif; ?>

        <div class="adm-form-grid">
            <!-- Mã sách tự sinh -->
            <div class="adm-form-group">
                <label>Mã sách</label>
                <?php if ($isEdit): ?>
                    <input class="adm-input" type="text" value="<?= htmlspecialchars($sachSua['maSach']) ?>" readonly style="background:#f8fafc;color:#64748b">
                    <input type="hidden" name="maSach_cu" value="<?= htmlspecialchars($sachSua['maSach']) ?>">
                <?php else: ?>
                    <div style="background:#eff6ff;border:1px dashed #93c5fd;border-radius:8px;padding:10px 12px;font-size:13px;color:#1d4ed8">
                        <i class="fas fa-magic"></i> Tự động sinh khi lưu (dạng S024, S025...)
                    </div>
                <?php endif; ?>
            </div>
            <div class="adm-form-group">
                <label>Tên sách <span class="req">*</span></label>
                <input class="adm-input" type="text" name="tenSach" required
                       value="<?= htmlspecialchars($sachSua['tenSach'] ?? '') ?>" placeholder="Nhập tên sách">
            </div>
            <div class="adm-form-group">
                <label>Nhà xuất bản <span class="req">*</span></label>
                <select class="adm-input" name="maNXB" required>
                    <option value="">-- Chọn NXB --</option>
                    <?php foreach ($dsNXB as $nxb): ?>
                        <option value="<?= $nxb['maNXB'] ?>"
                            <?= ($sachSua['maNXB'] ?? '') == $nxb['maNXB'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nxb['tenNXB']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="adm-form-group">
                <label>Năm xuất bản</label>
                <input class="adm-input" type="number" name="namSX" min="1900" max="2030"
                       value="<?= htmlspecialchars($sachSua['namSX'] ?? date('Y')) ?>">
            </div>
            <div class="adm-form-group">
                <label>Giá bán (₫) <span class="req">*</span></label>
                <input class="adm-input" type="number" name="giaBan" min="0" required
                       value="<?= htmlspecialchars($sachSua['giaBan'] ?? '') ?>" placeholder="86000">
            </div>
            <div class="adm-form-group">
                <label>Số lượng tồn kho</label>
                <?php if ($isEdit): ?>
                    <input class="adm-input" type="number" value="<?= htmlspecialchars($sachSua['soLuongTon'] ?? 0) ?>" readonly
                           style="background:#f8fafc;color:#64748b" title="Chỉ cập nhật qua phiếu nhập hàng">
                <?php else: ?>
                    <div style="background:#f0fdf4;border:1px dashed #86efac;border-radius:8px;padding:10px 12px;font-size:13px;color:#15803d">
                        <i class="fas fa-info-circle"></i> Tồn kho = 0 khi thêm mới. Tăng dần qua <strong>phiếu nhập</strong>.
                    </div>
                <?php endif; ?>
            </div>
            <div class="adm-form-group">
                <label>Loại bìa</label>
                <select class="adm-input" name="loaiBia">
                    <option value="Bìa Mềm" <?= ($sachSua['loaiBia'] ?? '') === 'Bìa Mềm' ? 'selected' : '' ?>>Bìa Mềm</option>
                    <option value="Bìa Cứng" <?= ($sachSua['loaiBia'] ?? '') === 'Bìa Cứng' ? 'selected' : '' ?>>Bìa Cứng</option>
                </select>
            </div>
            <div class="adm-form-group">
                <label>Trạng thái</label>
                <select class="adm-input" name="trangThai">
                    <option value="DangKD"  <?= ($sachSua['trangThai'] ?? 'DangKD') === 'DangKD'  ? 'selected' : '' ?>>Đang kinh doanh</option>
                    <option value="NgungKD" <?= ($sachSua['trangThai'] ?? '') === 'NgungKD' ? 'selected' : '' ?>>Ngưng kinh doanh</option>
                </select>
            </div>
            <div class="adm-form-group full">
                <label>Tác giả (giữ Ctrl để chọn nhiều)</label>
                <select class="adm-input" name="maTG[]" multiple style="height:100px">
                    <?php
                    $cacMaTGSua = $sachSua ? explode(',', $sachSua['maTG_list'] ?? '') : [];
                    foreach ($dsTacGia as $tg): ?>
                        <option value="<?= $tg['maTG'] ?>"
                            <?= in_array($tg['maTG'], $cacMaTGSua) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tg['tenTG']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="adm-form-group full">
                <label>Thể loại (giữ Ctrl để chọn nhiều)</label>
                <select class="adm-input" name="maTL[]" multiple style="height:100px">
                    <?php
                    $cacMaTLSua = $sachSua ? explode(',', $sachSua['maTL_list'] ?? '') : [];
                    foreach ($dsTheLoai as $tl): ?>
                        <option value="<?= $tl['maTL'] ?>"
                            <?= in_array($tl['maTL'], $cacMaTLSua) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tl['tenTL']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="adm-form-group full">
                <label>Ảnh bìa</label>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <input class="adm-input" type="text" name="urlAnh"
                               placeholder="https://... (URL ảnh, hoặc để trống nếu upload file bên dưới)"
                               style="flex:1">
                    </div>
                    <div style="font-size:12px;color:#94a3b8;text-align:center">— HOẶC —</div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <input class="adm-input" type="file" name="anhBia_file"
                               accept="image/jpeg,image/png,image/webp,image/gif"
                               style="padding:6px">
                        <span style="font-size:12px;color:#94a3b8;white-space:nowrap">JPG, PNG, WEBP</span>
                    </div>
                    <?php if ($isEdit && !empty($sachSua['anhBia'])): ?>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:4px">
                        <img src="<?= anhSach($sachSua['anhBia'] ?? null) ?>" alt="Ảnh hiện tại"
                             style="width:40px;height:55px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0">
                        <span style="font-size:12px;color:#64748b">Ảnh hiện tại (upload mới sẽ thay thế)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="adm-form-group full">
                <label>Mô tả</label>
                <textarea class="adm-textarea" name="moTa" rows="3"
                          placeholder="Mô tả nội dung sách..."><?= htmlspecialchars($sachSua['moTa'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="adm-form-actions">
            <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Thêm sách' ?>
            </button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
