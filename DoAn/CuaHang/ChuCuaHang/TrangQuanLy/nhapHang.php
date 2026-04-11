<?php
// ══════════════════════════════════════════════════════
//  nhapHang.php — Quản lý phiếu nhập & công nợ NCC
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';
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
<?php
// Tự sinh maPN: PN + YYYYmm + 3 chữ số thứ tự tháng này
$thangNay  = date('Ym');
$stmtLast  = $pdo->prepare("
    SELECT MAX(CAST(SUBSTRING(maPN, 9) AS UNSIGNED))
    FROM PhieuNhap
    WHERE maPN LIKE ?
");
$stmtLast->execute(["PN{$thangNay}%"]);
$soLast    = (int)($stmtLast->fetchColumn() ?? 0);
$maPN_sinh = 'PN' . $thangNay . str_pad($soLast + 1, 3, '0', STR_PAD_LEFT);

// Lấy NCC kèm chiết khấu mặc định
$dsNCC_ck = $pdo->query("SELECT maNCC, tenNCC, chietKhauMacDinh FROM NhaCungCap ORDER BY tenNCC")->fetchAll(PDO::FETCH_ASSOC);
?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:800px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:92vh;overflow-y:auto">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:1">
        <div>
            <h3 style="font-size:16px;font-weight:700">Tạo phiếu nhập hàng mới</h3>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px">Mã phiếu tự sinh: <strong style="color:#2563eb"><?= htmlspecialchars($maPN_sinh) ?></strong></div>
        </div>
        <a href="<?= $baseUrl ?>&tab=phieu" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/taoPhieuNhap.php" style="padding:24px" id="form-phieu-nhap">
        <!-- Mã phiếu ẩn — đã tự sinh -->
        <input type="hidden" name="maPN" value="<?= htmlspecialchars($maPN_sinh) ?>">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">
            <div class="adm-form-group">
                <label style="font-size:13px;font-weight:600">Nhà cung cấp <span style="color:#ef4444">*</span></label>
                <select class="adm-input" name="maNCC" id="sel-ncc" required>
                    <option value="">-- Chọn NCC --</option>
                    <?php foreach ($dsNCC_ck as $ncc): ?>
                        <option value="<?= $ncc['maNCC'] ?>"
                                data-chiet-khau="<?= (float)$ncc['chietKhauMacDinh'] ?>">
                            <?= htmlspecialchars($ncc['tenNCC']) ?>
                            (CK <?= $ncc['chietKhauMacDinh'] ?>%)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="adm-form-group">
                <label style="font-size:13px;font-weight:600">Chiết khấu chung NCC (%)</label>
                <input class="adm-input" type="number" id="ck-chung" min="0" max="100" step="0.1"
                       value="0" readonly
                       style="background:#f8fafc;color:#1d4ed8;font-weight:700"
                       title="Lấy tự động từ NCC, áp vào tất cả dòng chưa sửa">
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <p style="font-weight:600;font-size:14px;margin:0">Chi tiết sách nhập:</p>
            <button type="button" id="btn-them-dong"
                    style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;padding:6px 14px;font-size:13px;cursor:pointer">
                <i class="fas fa-plus"></i> Thêm dòng sách
            </button>
        </div>

        <!-- Header cột -->
        <div style="background:#f8fafc;border-radius:10px 10px 0 0;padding:10px 14px">
            <div class="dong-nhap-grid" style="font-size:12px;font-weight:700;color:#64748b">
                <span>Sách</span>
                <span>Số lượng</span>
                <span>Giá nhập (₫)</span>
                <span>CK (%)</span>
                <span>Thành tiền</span>
                <span></span>
            </div>
        </div>

        <!-- Bảng dòng động -->
        <div id="bang-chi-tiet" style="background:#f8fafc;border-radius:0 0 10px 10px;padding:0 14px 14px">
            <!-- Dòng đầu được clone từ template -->
        </div>

        <!-- Template dòng sách (ẩn, JS cloneNode) -->
        <template id="tmpl-dong-sach">
            <div class="dong-nhap" style="margin-top:8px">
                <div class="dong-nhap-grid">
                    <select class="adm-input dong-select-sach" name="maSach[]" style="padding:7px" required>
                        <option value="">-- Chọn sách --</option>
                        <?php foreach ($dsSach as $s): ?>
                            <option value="<?= $s['maSach'] ?>"><?= htmlspecialchars($s['tenSach']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input class="adm-input dong-sl" type="number" name="soLuong[]" min="1" placeholder="SL" style="padding:7px" required>
                    <input class="adm-input dong-gia" type="number" name="giaNhap[]" min="0" placeholder="Giá nhập" style="padding:7px" required>
                    <input class="adm-input dong-ck" type="number" name="chietKhau[]" min="0" max="100" step="0.1" placeholder="%" style="padding:7px" value="0">
                    <input class="adm-input dong-thanh-tien" type="text" readonly style="padding:7px;background:#f1f5f9;color:#475569;font-weight:600" placeholder="0₫">
                    <button type="button" class="btn-xoa-dong"
                            style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:6px;padding:6px 10px;cursor:pointer;font-size:13px">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </template>

        <!-- Tổng tất cả -->
        <div style="display:flex;justify-content:flex-end;margin-top:14px;padding:12px 0;border-top:2px solid #e2e8f0">
            <div style="font-size:15px;font-weight:700;color:#1f2937">
                Tổng tiền phiếu: <span id="tong-phieu" style="color:#ea580c;font-size:18px">0₫</span>
            </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
            <a href="<?= $baseUrl ?>&tab=phieu" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> Lưu phiếu nhập</button>
        </div>
    </form>
</div>
</div>

<style>
.dong-nhap-grid {
    display: grid;
    grid-template-columns: 3fr 80px 130px 80px 130px 40px;
    gap: 8px;
    align-items: center;
}
</style>

<script>
(function () {
    var tmpl        = document.getElementById('tmpl-dong-sach');
    var bang        = document.getElementById('bang-chi-tiet');
    var selNCC      = document.getElementById('sel-ncc');
    var inputCKChung= document.getElementById('ck-chung');
    var tongPhieu   = document.getElementById('tong-phieu');
    var demDong     = 0;

    // Thêm 1 dòng rỗng lúc load
    themDong();

    document.getElementById('btn-them-dong').addEventListener('click', themDong);

    // Khi chọn NCC → cập nhật chiết khấu chung + áp vào tất cả dòng
    selNCC.addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var ck  = opt.dataset.chietKhau || '0';
        inputCKChung.value = ck;
        // Áp vào mọi dòng
        bang.querySelectorAll('.dong-ck').forEach(function (inp) {
            inp.value = ck;
        });
        tinhTong();
    });

    function themDong() {
        demDong++;
        var clone = tmpl.content.cloneNode(true);
        var dong  = clone.querySelector('.dong-nhap');

        // Áp chiết khấu chung NCC đang chọn
        dong.querySelector('.dong-ck').value = inputCKChung.value || '0';

        // Khi thay đổi số lượng / giá / CK → tính thành tiền
        dong.querySelector('.dong-sl').addEventListener('input',  capNhatDong.bind(null, dong));
        dong.querySelector('.dong-gia').addEventListener('input', capNhatDong.bind(null, dong));
        dong.querySelector('.dong-ck').addEventListener('input',  capNhatDong.bind(null, dong));

        // Nút xóa dòng
        dong.querySelector('.btn-xoa-dong').addEventListener('click', function () {
            dong.remove();
            tinhTong();
        });

        bang.appendChild(dong);
    }

    function capNhatDong(dong) {
        var sl  = parseFloat(dong.querySelector('.dong-sl').value)  || 0;
        var gia = parseFloat(dong.querySelector('.dong-gia').value)  || 0;
        var ck  = parseFloat(dong.querySelector('.dong-ck').value)   || 0;
        var tt  = sl * gia * (1 - ck / 100);
        dong.querySelector('.dong-thanh-tien').value = tt > 0 ? formatTien(tt) : '';
        tinhTong();
    }

    function tinhTong() {
        var tong = 0;
        bang.querySelectorAll('.dong-thanh-tien').forEach(function (inp) {
            tong += parseTien(inp.value);
        });
        tongPhieu.textContent = formatTien(tong);
    }

    function formatTien(so) {
        return so.toLocaleString('vi-VN') + '₫';
    }

    function parseTien(str) {
        return parseFloat(str.replace(/[^\d]/g, '')) || 0;
    }
})();
</script>
<?php endif; ?>


