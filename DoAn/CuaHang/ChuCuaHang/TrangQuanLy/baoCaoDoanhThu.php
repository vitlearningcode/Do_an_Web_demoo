<?php
// ══════════════════════════════════════════════════════
//  baoCaoDoanhThu.php — Báo cáo doanh thu theo kỳ
// ══════════════════════════════════════════════════════
require_once __DIR__ . '/../_kiemTraQuyen.php';

// ── Đọc bộ lọc ────────────────────────────────────────
$kyLoai  = $_GET['ky']    ?? 'thang';   // tuan | thang | quy | nam
$kyGiaTri= (int)($_GET['gia_tri'] ?? 0);
$namChon = (int)($_GET['nam']     ?? date('Y'));
if ($namChon < 2020 || $namChon > 2099) $namChon = (int)date('Y');

// ── Tính khoảng ngày từ bộ lọc ───────────────────────
$tuNgay = $denNgay = '';
$tieuDe = '';
$soHieu = '';

switch ($kyLoai) {
    case 'tuan':
        if ($kyGiaTri < 1 || $kyGiaTri > 53) $kyGiaTri = (int)date('W');
        // Tính ngày đầu/cuối tuần theo ISO week
        $dt       = new DateTime();
        $dt->setISODate($namChon, $kyGiaTri);
        $tuNgay   = $dt->format('Y-m-d');
        $dt->modify('+6 days');
        $denNgay  = $dt->format('Y-m-d');
        $tieuDe   = "TUẦN $kyGiaTri NĂM $namChon";
        $soHieu   = "BC-DT-T{$kyGiaTri}-{$namChon}";
        break;

    case 'quy':
        if ($kyGiaTri < 1 || $kyGiaTri > 4) $kyGiaTri = (int)ceil(date('n') / 3);
        $thangDau = ($kyGiaTri - 1) * 3 + 1;
        $thangCuoi= $kyGiaTri * 3;
        $tuNgay   = "$namChon-" . str_pad($thangDau,  2, '0', STR_PAD_LEFT) . "-01";
        $denNgay  = date('Y-m-t', mktime(0,0,0,$thangCuoi,1,$namChon));
        $tieuDe   = "QUÝ $kyGiaTri NĂM $namChon";
        $soHieu   = "BC-DT-Q{$kyGiaTri}-{$namChon}";
        break;

    case 'nam':
        $kyGiaTri = $namChon;
        $tuNgay   = "$namChon-01-01";
        $denNgay  = "$namChon-12-31";
        $tieuDe   = "NĂM $namChon";
        $soHieu   = "BC-DT-N{$namChon}";
        break;

    default: // thang
        $kyLoai  = 'thang';
        if ($kyGiaTri < 1 || $kyGiaTri > 12) $kyGiaTri = (int)date('n');
        $tuNgay  = "$namChon-" . str_pad($kyGiaTri, 2, '0', STR_PAD_LEFT) . "-01";
        $denNgay = date('Y-m-t', mktime(0,0,0,$kyGiaTri,1,$namChon));
        $tieuDe  = "THÁNG $kyGiaTri NĂM $namChon";
        $soHieu  = "BC-DT-M" . str_pad($kyGiaTri,2,'0',STR_PAD_LEFT) . "-{$namChon}";
        break;
}

$ngayXuat   = date('d/m/Y H:i:s');
$nguoiLap   = $_SESSION['ten_nguoi_dung'] ?? ($_SESSION['tendangnhap'] ?? 'Admin');
$kyBaoCao   = date('d/m/Y', strtotime($tuNgay)) . ' — ' . date('d/m/Y', strtotime($denNgay));

// ── Query doanh thu theo từng đầu sách ────────────────
$dsDoanhThu   = [];
$tongDoanhThu = 0;
$tongDon      = 0;
$tongSLBan    = 0;

try {
    $stmt = $pdo->prepare("
        SELECT
            s.maSach,
            s.tenSach,
            SUM(ct.soLuong)    AS tongSL,
            AVG(ct.giaBan)     AS giaTB,
            SUM(ct.thanhTien)  AS doanhThu,
            COUNT(DISTINCT dh.maDH) AS soDon
        FROM ChiTietDH ct
        JOIN DonHang dh ON ct.maDH = dh.maDH
        JOIN Sach s     ON ct.maSach = s.maSach
        WHERE dh.trangThai = 'HoanThanh'
          AND DATE(dh.ngayDat) BETWEEN ? AND ?
        GROUP BY s.maSach, s.tenSach
        ORDER BY doanhThu DESC
    ");
    $stmt->execute([$tuNgay, $denNgay]);
    $dsDoanhThu = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tổng cộng
    $stmtTong = $pdo->prepare("
        SELECT
            COUNT(DISTINCT dh.maDH)   AS tongDon,
            COALESCE(SUM(dh.tongTien),0) AS doanhThu
        FROM DonHang dh
        WHERE dh.trangThai = 'HoanThanh'
          AND DATE(dh.ngayDat) BETWEEN ? AND ?
    ");
    $stmtTong->execute([$tuNgay, $denNgay]);
    $row          = $stmtTong->fetch(PDO::FETCH_ASSOC);
    $tongDoanhThu = (float)$row['doanhThu'];
    $tongDon      = (int)$row['tongDon'];
    $tongSLBan    = array_sum(array_column($dsDoanhThu, 'tongSL'));

} catch (Throwable $e) {
    $dsDoanhThu = [];
}

// ── Helper ──────────────────────────────────────────
if (!function_exists('fmtTien')) {
    function fmtTien(float $so): string {
        return number_format($so, 0, ',', '.') . '₫';
    }
}

$baseUrl = 'index.php?trang=baoCaoDoanhThu';

// ── Danh sách năm để chọn ────────────────────────────
$namHienTai = (int)date('Y');
$dsNam = range($namHienTai, $namHienTai - 4);
?>

<!-- ══ CSS print ══ -->


<!-- ══ BỘ LỌC (no-print) ══ -->
<div class="adm-section-header no-print">
    <div>
        <div class="adm-section-title">Báo cáo Doanh thu</div>
        <div class="adm-section-subtitle">Thống kê doanh thu theo tuần, tháng, quý, năm</div>
    </div>
    <button onclick="window.print()" class="adm-btn adm-btn-primary">
        <i class="fas fa-print"></i> In báo cáo
    </button>
</div>

<div class="adm-card no-print" style="margin-bottom:20px">
    <form method="GET" action="index.php" style="padding:16px 20px;display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <input type="hidden" name="trang" value="baoCaoDoanhThu">

        <div>
            <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Loại kỳ</label>
            <select name="ky" class="adm-input" style="min-width:130px" onchange="capNhatGiaTri(this.value)">
                <option value="tuan"  <?= $kyLoai==='tuan'  ?'selected':'' ?>>Theo tuần</option>
                <option value="thang" <?= $kyLoai==='thang' ?'selected':'' ?>>Theo tháng</option>
                <option value="quy"   <?= $kyLoai==='quy'   ?'selected':'' ?>>Theo quý</option>
                <option value="nam"   <?= $kyLoai==='nam'   ?'selected':'' ?>>Theo năm</option>
            </select>
        </div>

        <div id="wrap-gia-tri">
            <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px" id="lbl-gia-tri">
                <?= match($kyLoai) {
                    'tuan'  => 'Tuần (1–53)',
                    'thang' => 'Tháng (1–12)',
                    'quy'   => 'Quý (1–4)',
                    default => ''
                } ?>
            </label>
            <input type="number" name="gia_tri" id="inp-gia-tri" class="adm-input"
                   value="<?= $kyGiaTri ?>" min="1" max="<?= match($kyLoai){
                       'tuan'=>'53','thang'=>'12','quy'=>'4', default=>'0'
                   } ?>"
                   style="width:90px;<?= $kyLoai==='nam' ? 'display:none' : '' ?>">
        </div>

        <div>
            <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Năm</label>
            <select name="nam" class="adm-input" style="min-width:90px">
                <?php foreach ($dsNam as $y): ?>
                <option value="<?= $y ?>" <?= $y===$namChon?'selected':'' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="adm-btn adm-btn-primary">
            <i class="fas fa-search"></i> Xem báo cáo
        </button>
    </form>
</div>

<!-- ══ NỘI DUNG BÁO CÁO (in được) ══ -->
<div class="print-page adm-card">

    <!-- Header -->
    <div style="text-align:center;margin-bottom:20px">
        <div style="font-size:12px;color:#64748b;font-weight:600;letter-spacing:1px">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div style="font-size:11px;color:#64748b;margin-bottom:16px">Độc lập – Tự do – Hạnh phúc</div>
        <div class="bc-header-title">BÁO CÁO DOANH THU <?= $tieuDe ?></div>
        <div class="bc-subtitle">Kỳ báo cáo: <?= $kyBaoCao ?></div>
    </div>

    <!-- Thông tin báo cáo -->
    <div class="bc-meta-grid">
        <div>
            <div class="label">Số hiệu báo cáo</div>
            <div class="val"><?= htmlspecialchars($soHieu) ?></div>
        </div>
        <div>
            <div class="label">Người lập</div>
            <div class="val"><?= htmlspecialchars($nguoiLap) ?></div>
        </div>
        <div>
            <div class="label">Ngày giờ xuất</div>
            <div class="val"><?= $ngayXuat ?></div>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="bc-stat-row">
        <div class="bc-stat">
            <div class="num"><?= number_format($tongDon) ?></div>
            <div class="lbl"><i class="fas fa-shopping-bag"></i> Đơn hoàn thành</div>
        </div>
        <div class="bc-stat amber">
            <div class="num"><?= number_format($tongSLBan) ?></div>
            <div class="lbl"><i class="fas fa-book"></i> Sách đã bán</div>
        </div>
        <div class="bc-stat green">
            <div class="num" style="font-size:20px"><?= fmtTien($tongDoanhThu) ?></div>
            <div class="lbl" ><i class="fas fa-money-bill-wave"></i> Tổng doanh thu</div>
        </div>
    </div>

    <!-- Bảng chi tiết -->
    <table class="bc-table">
        <thead>
            <tr>
                <th style="width:40px;text-align:center">STT</th>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th style="text-align:right">SL bán</th>
                <th style="text-align:right">Đơn giá TB</th>
                <th style="text-align:right">Số đơn</th>
                <th style="text-align:right">Doanh thu</th>
                <th style="text-align:right">Tỷ trọng</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($dsDoanhThu)): ?>
            <tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8">
                <i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block"></i>
                Không có dữ liệu trong kỳ này.
            </td></tr>
        <?php else: ?>
            <?php foreach ($dsDoanhThu as $i => $r):
                $tyTrong = $tongDoanhThu > 0 ? round((float)$r['doanhThu'] / $tongDoanhThu * 100, 1) : 0;
            ?>
            <tr>
                <td style="text-align:center;color:#94a3b8"><?= $i + 1 ?></td>
                <td style="font-weight:600;color:#2563eb"><?= htmlspecialchars($r['maSach']) ?></td>
                <td><?= htmlspecialchars($r['tenSach']) ?></td>
                <td style="text-align:right"><?= number_format($r['tongSL']) ?></td>
                <td style="text-align:right"><?= fmtTien((float)$r['giaTB']) ?></td>
                <td style="text-align:right"><?= number_format($r['soDon']) ?></td>
                <td style="text-align:right;font-weight:700;color:#1d4ed8"><?= fmtTien((float)$r['doanhThu']) ?></td>
                <td style="text-align:right">
                    <span style="background:<?= $tyTrong>=10?'#dbeafe':'#f1f5f9' ?>;color:<?= $tyTrong>=10?'#1d4ed8':'#475569' ?>;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700">
                        <?= $tyTrong ?>%
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
           <tr  style="background:#1e3a5f;color:#fff">
            <td colspan="3" style="text-align:right;padding:10px 20px 10px 12px;color:red;font-weight:700;letter-spacing:.3px">TỔNG CỘNG</td>
            
            <td style="text-align:right;padding-top:10px;padding-bottom:10px;color:red;font-weight:700"><?= number_format($tongSLBan) ?></td>
            <td style="text-align:right;padding-top:10px;padding-bottom:10px;color:red">—</td>
            <td style="text-align:right;padding-top:10px;padding-bottom:10px;color:red;font-weight:700"><?= number_format($tongDon) ?></td>
            <td style="text-align:right;padding-top:10px;padding-bottom:10px;color:red;font-weight:800;font-size:15px"><?= fmtTien($tongDoanhThu) ?></td>
            <td style="text-align:right;padding-top:10px;padding-bottom:10px;color:red;font-weight:700">100%</td>
        </tr>
            
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Ghi chú -->
    <p style="font-size:11px;color:#94a3b8;margin-top:12px;font-style:italic">
        * Báo cáo chỉ tính các đơn hàng có trạng thái <strong>Hoàn thành</strong> trong kỳ.
        * Doanh thu = Tổng thực thu sau chiết khấu theo đơn hàng.
    </p>

    <!-- Chữ ký -->
    <div class="bc-ky-sig">
        <div class="bc-sig-box">
            <div class="title">Người lập báo cáo</div>
            <div class="note">(Ký, ghi rõ họ tên)</div>
            <div class="name"><?= htmlspecialchars($nguoiLap) ?></div>
        </div>
        <div class="bc-sig-box">
            <div class="title">Kế toán trưởng</div>
            <div class="note">(Ký, ghi rõ họ tên)</div>
            <div class="name">&nbsp;</div>
        </div>
        <div class="bc-sig-box">
            <div class="title">Giám đốc</div>
            <div class="note">(Ký, đóng dấu)</div>
            <div class="name">&nbsp;</div>
        </div>
    </div>

</div>

<script>
function capNhatGiaTri(ky) {
    var inp = document.getElementById('inp-gia-tri');
    var lbl = document.getElementById('lbl-gia-tri');
    var wrap = document.getElementById('wrap-gia-tri');
    if (ky === 'nam') {
        inp.style.display = 'none';
        lbl.textContent = '';
    } else {
        inp.style.display = '';
        var cfg = {
            tuan:  {label: 'Tuần (1–53)',  max: 53, def: <?= (int)date('W') ?>},
            thang: {label: 'Tháng (1–12)', max: 12, def: <?= (int)date('n') ?>},
            quy:   {label: 'Quý (1–4)',    max: 4,  def: <?= (int)ceil(date('n')/3) ?>},
        };
        var c = cfg[ky];
        if (c) {
            lbl.textContent = c.label;
            inp.max = c.max;
            inp.value = c.def;
        }
    }
}
</script>
