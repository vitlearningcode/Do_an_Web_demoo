<?php
// ══════════════════════════════════════════════════════
//  tongQuan.php — Dashboard Tổng quan
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';

// ── Truy vấn dữ liệu ──
try {
    // 1. Tổng đơn hôm nay + doanh thu hôm nay (chỉ tính đơn HoanThanh)
    $stmtHom = $pdo->query("
        SELECT
            COUNT(*) AS tongDon,
            COALESCE(SUM(CASE WHEN trangThai = 'HoanThanh' THEN tongTien ELSE 0 END), 0) AS doanhThu
        FROM DonHang
        WHERE DATE(ngayDat) = CURDATE()
    ");
    $homNay = $stmtHom->fetch(PDO::FETCH_ASSOC);

    // 2. Đơn theo trạng thái (tổng tất cả)
    $stmtTrangThai = $pdo->query("
        SELECT trangThai, COUNT(*) AS soLuong
        FROM DonHang
        GROUP BY trangThai
    ");
    $trangThaiMap = [];
    foreach ($stmtTrangThai->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $trangThaiMap[$r['trangThai']] = $r['soLuong'];
    }

    // 3. Tổng doanh thu tháng này (chỉ HoanThanh)
    $stmtThang = $pdo->query("
        SELECT COALESCE(SUM(tongTien), 0) AS doanhThuThang
        FROM DonHang
        WHERE trangThai = 'HoanThanh'
          AND MONTH(ngayDat) = MONTH(CURDATE())
          AND YEAR(ngayDat)  = YEAR(CURDATE())
    ");
    $doanhThuThang = $stmtThang->fetchColumn();

    // 4. Sách sắp hết hàng (soLuongTon < 20)
    $stmtSapHet = $pdo->query("
        SELECT s.maSach, s.tenSach, s.soLuongTon,
               GROUP_CONCAT(tl.tenTL SEPARATOR ', ') AS theLoai
        FROM Sach s
        LEFT JOIN Sach_TheLoai st ON s.maSach = st.maSach
        LEFT JOIN TheLoai tl ON st.maTL = tl.maTL
        WHERE s.soLuongTon < 20 AND s.trangThai = 'DangKD'
        GROUP BY s.maSach
        ORDER BY s.soLuongTon ASC
        LIMIT 8
    ");
    $dsSapHetHang = $stmtSapHet->fetchAll(PDO::FETCH_ASSOC);

    // 5. Đơn hàng gần đây (10 đơn)
    $stmtDon = $pdo->query("
        SELECT dh.maDH, dh.ngayDat, dh.tongTien, dh.trangThai,
               nd.tenND AS tenKhach
        FROM DonHang dh
        JOIN NguoiDung nd ON dh.maND = nd.maND
        ORDER BY dh.ngayDat DESC
        LIMIT 10
    ");
    $dsDonGanDay = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

    // 6. Doanh thu 7 ngày gần đây (cho mini chart text)
    $stmtBieuDo = $pdo->query("
        SELECT DATE(ngayDat) AS ngay,
               COALESCE(SUM(CASE WHEN trangThai='HoanThanh' THEN tongTien ELSE 0 END), 0) AS doanh_thu,
               COUNT(*) AS so_don
        FROM DonHang
        WHERE ngayDat >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(ngayDat)
        ORDER BY ngay ASC
    ");
    $dsBieuDo = $stmtBieuDo->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $homNay        = ['tongDon' => 0, 'doanhThu' => 0];
    $trangThaiMap  = [];
    $doanhThuThang = 0;
    $dsSapHetHang  = [];
    $dsDonGanDay   = [];
    $dsBieuDo      = [];
}

// ── Helper: format tiền ──────────────────────────────
function fmtTien(float $so): string {
    if ($so >= 1_000_000) return number_format($so / 1_000_000, 1) . 'M ₫';
    return number_format($so, 0, ',', '.') . '₫';
}

// ── Helper: badge trạng thái ─────────────────────────
function badgeDonHang(string $tt): string {
    return match($tt) {
        'ChoDuyet'   => '<span class="adm-badge adm-badge-warning"><i class="fas fa-clock"></i> Chờ duyệt</span>',
        'DangGiao'   => '<span class="adm-badge adm-badge-info"><i class="fas fa-truck"></i> Đang giao</span>',
        'HoanThanh'  => '<span class="adm-badge adm-badge-success"><i class="fas fa-check"></i> Hoàn thành</span>',
        'DaHuy'      => '<span class="adm-badge adm-badge-danger"><i class="fas fa-times"></i> Đã hủy</span>',
        default      => '<span class="adm-badge adm-badge-gray">' . htmlspecialchars($tt) . '</span>',
    };
}
?>

<!-- ── STAT CARDS ── -->
<div class="adm-stats-grid">
    <div class="adm-stat-card">
        <div class="adm-stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
        <div class="adm-stat-body">
            <h3><?= number_format($homNay['tongDon']) ?></h3>
            <p>Đơn hàng hôm nay</p>
            <span class="adm-stat-change up"><i class="fas fa-circle-dot"></i> Tổng tất cả</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
        <div class="adm-stat-body">
            <h3><?= fmtTien((float)$homNay['doanhThu']) ?></h3>
            <p>Doanh thu hôm nay</p>
            <span class="adm-stat-change up"><i class="fas fa-arrow-up"></i> Đơn hoàn thành</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
        <div class="adm-stat-body">
            <h3><?= number_format($trangThaiMap['ChoDuyet'] ?? 0) ?></h3>
            <p>Chờ duyệt</p>
            <span class="adm-stat-change down"><i class="fas fa-exclamation-circle"></i> Cần xử lý</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon purple"><i class="fas fa-chart-line"></i></div>
        <div class="adm-stat-body">
            <h3><?= fmtTien((float)$doanhThuThang) ?></h3>
            <p>Doanh thu tháng này</p>
            <span class="adm-stat-change up"><i class="fas fa-calendar-check"></i> Tháng <?= date('n/Y') ?></span>
        </div>
    </div>
</div>

<!-- ── 2 CỘT: Đơn gần đây + Sắp hết hàng ── -->
<div class="adm-grid-2">

    <!-- Đơn hàng gần đây -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><i class="fas fa-receipt" style="color:#2563eb;margin-right:6px"></i>Đơn hàng gần đây</h3>
            <a href="index.php?trang=donHang" class="adm-btn adm-btn-outline adm-btn-sm">Xem tất cả</a>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($dsDonGanDay)): ?>
                    <tr><td colspan="4" class="adm-empty"><i class="fas fa-inbox"></i><p>Chưa có đơn hàng nào.</p></td></tr>
                <?php else: ?>
                    <?php foreach ($dsDonGanDay as $don): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($don['maDH']) ?></strong></td>
                        <td><?= htmlspecialchars($don['tenKhach']) ?></td>
                        <td><?= fmtTien((float)$don['tongTien']) ?></td>
                        <td><?= badgeDonHang($don['trangThai']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sắp hết hàng -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><i class="fas fa-exclamation-triangle" style="color:#d97706;margin-right:6px"></i>Sách sắp hết hàng</h3>
            <a href="index.php?trang=sachVaTonKho" class="adm-btn adm-btn-outline adm-btn-sm">Xem kho</a>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Thể loại</th>
                        <th>Tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($dsSapHetHang)): ?>
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:#94a3b8;">
                        <i class="fas fa-check-circle" style="color:#16a34a;font-size:20px;margin-bottom:8px;display:block;"></i>
                        Tất cả sách đều còn đủ hàng!
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($dsSapHetHang as $sach): ?>
                    <tr>
                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <span title="<?= htmlspecialchars($sach['tenSach']) ?>">
                                <?= htmlspecialchars($sach['tenSach']) ?>
                            </span>
                        </td>
                        <td><small style="color:#64748b"><?= htmlspecialchars($sach['theLoai'] ?? '—') ?></small></td>
                        <td>
                            <?php if ($sach['soLuongTon'] <= 5): ?>
                                <span class="adm-badge adm-badge-danger"><?= $sach['soLuongTon'] ?></span>
                            <?php else: ?>
                                <span class="adm-badge adm-badge-warning"><?= $sach['soLuongTon'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($dsSapHetHang)): ?>
        <div style="padding:12px 20px;border-top:1px solid #f1f5f9;">
            <a href="index.php?trang=nhapHang" class="adm-btn adm-btn-primary adm-btn-sm" style="width:100%;justify-content:center;">
                <i class="fas fa-plus"></i> Tạo phiếu nhập hàng
            </a>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /.adm-grid-2 -->

<!-- ── BẢNG THỐNG KÊ 7 NGÀY ── -->
<?php if (!empty($dsBieuDo)): ?>
<div class="adm-card">
    <div class="adm-card-header">
        <h3><i class="fas fa-chart-bar" style="color:#7c3aed;margin-right:6px"></i>Doanh thu 7 ngày gần đây</h3>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Số đơn</th>
                    <th>Doanh thu (HT)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($dsBieuDo) as $row): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($row['ngay'])) ?></td>
                <td><?= number_format($row['so_don']) ?></td>
                <td><strong><?= fmtTien((float)$row['doanh_thu']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
