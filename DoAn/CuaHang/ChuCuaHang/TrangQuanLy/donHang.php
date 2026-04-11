<?php
// ══════════════════════════════════════════════════════
//  donHang.php — Quản lý đơn hàng
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';

$locTrangThai = $_GET['loc'] ?? 'TatCa';
$trang        = max(1, (int)($_GET['trang_so'] ?? 1));
$moi_trang    = 15;
$offset       = ($trang - 1) * $moi_trang;

// Map filter → điều kiện SQL
$cacTrangThai = ['TatCa', 'ChoDuyet', 'DangGiao', 'HoanThanh', 'DaHuy'];
if (!in_array($locTrangThai, $cacTrangThai)) $locTrangThai = 'TatCa';

$dieuKienTT = $locTrangThai !== 'TatCa' ? "WHERE dh.trangThai = " . $pdo->quote($locTrangThai) : "";

try {
    // Đếm tổng
    $tongDon = (int)$pdo->query("SELECT COUNT(*) FROM DonHang dh $dieuKienTT")->fetchColumn();

    // Lấy danh sách đơn
    $stmtDon = $pdo->prepare("
        SELECT dh.maDH, dh.ngayDat, dh.tongTien, dh.trangThai,
               nd.tenND AS tenKhach, nd.sdt, nd.email,
               dc.diaChiChiTiet,
               pt.tenPT AS phuongThuc
        FROM DonHang dh
        JOIN NguoiDung nd ON dh.maND = nd.maND
        JOIN DiaChiGiaoHang dc ON dh.maDC = dc.maDC
        JOIN PhuongThucThanhToan pt ON dh.maPT = pt.maPT
        $dieuKienTT
        ORDER BY dh.ngayDat DESC
        LIMIT $moi_trang OFFSET $offset
    ");
    $stmtDon->execute();
    $dsDonHang = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

    // Đếm theo từng trạng thái cho tab badge
    $stmtDem = $pdo->query("SELECT trangThai, COUNT(*) as so FROM DonHang GROUP BY trangThai");
    $demTT = ['TatCa' => $tongDon];
    foreach ($stmtDem->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $demTT[$r['trangThai']] = (int)$r['so'];
    }

} catch (Throwable $e) {
    $dsDonHang = [];
    $tongDon = 0;
    $demTT = [];
}

$tongTrang = max(1, (int)ceil($tongDon / $moi_trang));

// Helpers (dùng lại nếu đã khai báo ở tongQuan hoặc khai báo lại)
if (!function_exists('fmtTien')) {
    function fmtTien(float $so): string {
        return number_format($so, 0, ',', '.') . '₫';
    }
}
if (!function_exists('badgeDonHang')) {
    function badgeDonHang(string $tt): string {
        return match($tt) {
            'ChoDuyet'  => '<span class="adm-badge adm-badge-warning"><i class="fas fa-clock"></i> Chờ duyệt</span>',
            'DangGiao'  => '<span class="adm-badge adm-badge-info"><i class="fas fa-truck"></i> Đang giao</span>',
            'HoanThanh' => '<span class="adm-badge adm-badge-success"><i class="fas fa-check"></i> Hoàn thành</span>',
            'DaHuy'     => '<span class="adm-badge adm-badge-danger"><i class="fas fa-times"></i> Đã hủy</span>',
            default => '<span class="adm-badge adm-badge-gray">' . htmlspecialchars($tt) . '</span>',
        };
    }
}

$baseUrl = 'index.php?trang=donHang';
?>

<div class="adm-card" style="margin-bottom:0">

    <!-- TABS filter trạng thái -->
    <div class="adm-tabs">
        <?php
        $tabInfo = [
            'TatCa'     => ['label' => 'Tất cả',      'icon' => 'fa-list'],
            'ChoDuyet'  => ['label' => 'Chờ duyệt',   'icon' => 'fa-clock'],
            'DangGiao'  => ['label' => 'Đang giao',   'icon' => 'fa-truck'],
            'HoanThanh' => ['label' => 'Hoàn thành',  'icon' => 'fa-check-circle'],
            'DaHuy'     => ['label' => 'Đã hủy',      'icon' => 'fa-times-circle'],
        ];
        foreach ($tabInfo as $key => $info): ?>
        <a href="<?= $baseUrl ?>&loc=<?= $key ?>"
           class="adm-tab <?= $locTrangThai === $key ? 'active' : '' ?>">
            <i class="fas <?= $info['icon'] ?>"></i>
            <?= $info['label'] ?>
            <?php if (isset($demTT[$key]) && $demTT[$key] > 0): ?>
                <span style="background:#e2e8f0;color:#475569;padding:1px 7px;border-radius:10px;font-size:11px;">
                    <?= $demTT[$key] ?>
                </span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- TABLE -->
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Địa chỉ</th>
                    <th>Thanh toán</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsDonHang)): ?>
                <tr>
                    <td colspan="8">
                        <div class="adm-empty">
                            <i class="fas fa-inbox"></i>
                            <p>Không có đơn hàng nào.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($dsDonHang as $don): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($don['maDH']) ?></strong></td>
                    <td>
                        <div style="font-weight:600"><?= htmlspecialchars($don['tenKhach']) ?></div>
                        <div style="font-size:12px;color:#94a3b8"><?= htmlspecialchars($don['email'] ?? '') ?></div>
                    </td>
                    <td style="max-width:160px;font-size:13px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                        title="<?= htmlspecialchars($don['diaChiChiTiet']) ?>">
                        <?= htmlspecialchars($don['diaChiChiTiet']) ?>
                    </td>
                    <td><small><?= htmlspecialchars($don['phuongThuc']) ?></small></td>
                    <td><strong style="color:#0f172a"><?= fmtTien((float)$don['tongTien']) ?></strong></td>
                    <td style="font-size:13px;color:#64748b"><?= date('d/m/Y H:i', strtotime($don['ngayDat'])) ?></td>
                    <td><?= badgeDonHang($don['trangThai']) ?></td>
                    <td>
                        <div class="adm-action-group" style="justify-content:center">
                            <!-- Xem chi tiết -->
                            <a href="<?= $baseUrl ?>&xem=<?= urlencode($don['maDH']) ?>&loc=<?= $locTrangThai ?>"
                               class="adm-btn adm-btn-outline adm-btn-icon" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Duyệt (chỉ hiện khi ChoDuyet) -->
                            <?php if ($don['trangThai'] === 'ChoDuyet'): ?>
                            <form method="POST" action="XuLy/capNhatTrangThaiDH.php" style="display:inline">
                                <input type="hidden" name="maDH" value="<?= htmlspecialchars($don['maDH']) ?>">
                                <input type="hidden" name="trangThaiMoi" value="DangGiao">
                                <input type="hidden" name="redirect_loc" value="<?= $locTrangThai ?>">
                                <button type="submit" class="adm-btn adm-btn-success adm-btn-icon" title="Duyệt → Đang giao">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <!-- Giao xong (chỉ hiện khi DangGiao) -->
                            <?php if ($don['trangThai'] === 'DangGiao'): ?>
                            <form method="POST" action="XuLy/capNhatTrangThaiDH.php" style="display:inline">
                                <input type="hidden" name="maDH" value="<?= htmlspecialchars($don['maDH']) ?>">
                                <input type="hidden" name="trangThaiMoi" value="HoanThanh">
                                <input type="hidden" name="redirect_loc" value="<?= $locTrangThai ?>">
                                <button type="submit" class="adm-btn adm-btn-primary adm-btn-icon" title="Xác nhận hoàn thành">
                                    <i class="fas fa-flag-checkered"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <!-- Hủy (chỉ hiện khi ChoDuyet) -->
                            <?php if ($don['trangThai'] === 'ChoDuyet'): ?>
                            <form method="POST" action="XuLy/capNhatTrangThaiDH.php" style="display:inline"
                                  onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn <?= htmlspecialchars($don['maDH']) ?>?')">
                                <input type="hidden" name="maDH" value="<?= htmlspecialchars($don['maDH']) ?>">
                                <input type="hidden" name="trangThaiMoi" value="DaHuy">
                                <input type="hidden" name="redirect_loc" value="<?= $locTrangThai ?>">
                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-icon" title="Hủy đơn">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            <?php endif; ?>
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
        <span>Hiển thị <?= $offset + 1 ?>–<?= min($offset + $moi_trang, $tongDon) ?> / <?= $tongDon ?> đơn</span>
        <div class="adm-pagination-btns">
            <?php if ($trang > 1): ?>
                <a href="<?= $baseUrl ?>&loc=<?= $locTrangThai ?>&trang_so=<?= $trang - 1 ?>">‹</a>
            <?php endif; ?>
            <?php for ($i = max(1, $trang-2); $i <= min($tongTrang, $trang+2); $i++): ?>
                <?php if ($i == $trang): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>&loc=<?= $locTrangThai ?>&trang_so=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($trang < $tongTrang): ?>
                <a href="<?= $baseUrl ?>&loc=<?= $locTrangThai ?>&trang_so=<?= $trang + 1 ?>">›</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.adm-card -->

<!-- ── POPUP XEM CHI TIẾT ĐƠN HÀNG ── -->
<?php
$xemMaDH = $_GET['xem'] ?? null;
if ($xemMaDH): ?>
<?php
    try {
        $stmtCt = $pdo->prepare("
            SELECT ct.maSach, ct.soLuong, ct.giaBan, ct.thanhTien, ct.maKM,
                   s.tenSach
            FROM ChiTietDH ct
            JOIN Sach s ON ct.maSach = s.maSach
            WHERE ct.maDH = ?
        ");
        $stmtCt->execute([$xemMaDH]);
        $chiTietDon = $stmtCt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $chiTietDon = []; }
?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
            <h3 style="font-size:16px;font-weight:700">Chi tiết đơn hàng: <?= htmlspecialchars($xemMaDH) ?></h3>
            <a href="<?= $baseUrl ?>&loc=<?= $locTrangThai ?>" style="color:#94a3b8;font-size:20px;text-decoration:none">
                <i class="fas fa-times"></i>
            </a>
        </div>
        <div style="padding:20px 24px">
            <table class="adm-table">
                <thead><tr><th>Tên sách</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                <tbody>
                <?php foreach ($chiTietDon as $ct): ?>
                <tr>
                    <td><?= htmlspecialchars($ct['tenSach']) ?></td>
                    <td><?= $ct['soLuong'] ?></td>
                    <td><?= fmtTien((float)$ct['giaBan']) ?></td>
                    <td><strong><?= fmtTien((float)$ct['thanhTien']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:12px 24px;border-top:1px solid #f1f5f9;text-align:right">
            <a href="<?= $baseUrl ?>&loc=<?= $locTrangThai ?>" class="adm-btn adm-btn-outline">Đóng</a>
        </div>
    </div>
</div>
<?php endif; ?>
