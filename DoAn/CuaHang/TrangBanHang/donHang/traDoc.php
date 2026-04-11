<?php
/**
 * donHang/traDoc.php — Tra cứu đơn hàng không cần đăng nhập
 * Thuần PHP form POST — không AJAX, không JSON.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

$dsDonHang  = [];   // kết quả tra cứu
$thongBaoLoi = '';
$daTraCuu    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maDonHang  = trim($_POST['ma_don_hang']   ?? '');
    $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');

    if (empty($maDonHang) && empty($soDienThoai)) {
        $thongBaoLoi = 'Vui lòng nhập mã đơn hàng hoặc số điện thoại để tra cứu.';
    } else {
        $daTraCuu = true;
        // Tìm theo mã đơn hoặc SĐT của người đặt
        $sql = "
            SELECT dh.maDH, dh.ngayDat, dh.tongTien, dh.trangThai,
                   pt.tenPT, dcgh.diaChiChiTiet,
                   nd.tenND, nd.sdt
            FROM DonHang dh
            JOIN PhuongThucThanhToan pt  ON dh.maPT = pt.maPT
            JOIN DiaChiGiaoHang dcgh     ON dh.maDC = dcgh.maDC
            JOIN NguoiDung nd            ON dh.maND = nd.maND
            WHERE 1=0
        ";
        $thamSo = [];

        if (!empty($maDonHang)) {
            $sql .= " OR dh.maDH = :maDH";
            $thamSo[':maDH'] = $maDonHang;
        }
        if (!empty($soDienThoai)) {
            $sql .= " OR nd.sdt = :sdt";
            $thamSo[':sdt'] = $soDienThoai;
        }
        $sql .= " ORDER BY dh.ngayDat DESC LIMIT 20";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($thamSo);
        $dsDonHang = $stmt->fetchAll();

        if (empty($dsDonHang)) {
            $thongBaoLoi = 'Không tìm thấy đơn hàng nào. Vui lòng kiểm tra lại thông tin.';
        }
    }
}

// Hàm badge trạng thái
function nhanTrangThai(string $tt): array {
    return match($tt) {
        'ChoDuyet'  => ['cho-duyet',  'Chờ Duyệt',  '🕐'],
        'DangGiao'  => ['dang-giao',  'Đang Giao',  '🚚'],
        'HoanThanh' => ['hoan-thanh', 'Đã Giao',    '✅'],
        'DaHuy'     => ['da-huy',     'Đã Hủy',     '❌'],
        default     => ['cho-duyet',   $tt,           '📦'],
    };
}

// Lấy chi tiết sản phẩm của các đơn tìm được
$chiTietDH = [];
if (!empty($dsDonHang)) {
    $danhSachMa = array_column($dsDonHang, 'maDH');
    $chuoiDau   = implode(',', array_fill(0, count($danhSachMa), '?'));
    $sqlCT = "
        SELECT ct.maDH, ct.soLuong, ct.giaBan, ct.thanhTien,
               s.tenSach, ha.urlAnh
        FROM ChiTietDH ct
        JOIN Sach s ON ct.maSach = s.maSach
        LEFT JOIN (
            SELECT maSach, MIN(urlAnh) AS urlAnh FROM HinhAnhSach GROUP BY maSach
        ) ha ON ha.maSach = ct.maSach
        WHERE ct.maDH IN ($chuoiDau)
        ORDER BY ct.maDH
    ";
    $stmtCT = $pdo->prepare($sqlCT);
    $stmtCT->execute($danhSachMa);
    foreach ($stmtCT->fetchAll() as $dong) {
        $chiTietDH[$dong['maDH']][] = $dong;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Đơn Hàng - Book Sales</title>
    <meta name="description" content="Tra cứu tình trạng đơn hàng tại Book Sales bằng mã đơn hoặc số điện thoại.">
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="../../../GiaoDien/donHang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; }
        .td-trang { max-width: 720px; margin: 36px auto; padding: 0 16px 60px; }
        .td-quay-lai { display: inline-flex; align-items: center; gap: 6px; color: #6b7280; font-size: .875rem; text-decoration: none; margin-bottom: 20px; }
        .td-quay-lai:hover { color: var(--primary); }

        /* Form tra cứu */
        .td-form-khung {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 1px 8px rgba(0,0,0,.08);
            margin-bottom: 28px;
        }
        .td-form-khung h1 {
            font-size: 1.35rem; font-weight: 700; color: #111;
            margin: 0 0 6px; display: flex; align-items: center; gap: 10px;
        }
        .td-form-khung h1 i { color: var(--primary); }
        .td-form-khung .mo-ta { color: #6b7280; font-size: .875rem; margin: 0 0 28px; }
        .td-luoi { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 560px) { .td-luoi { grid-template-columns: 1fr; } }
        .td-nhom label { display: block; font-size: .83rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .td-nhom input {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: .9rem; outline: none; transition: border-color .2s;
            font-family: inherit;
        }
        .td-nhom input:focus { border-color: var(--primary); }
        .td-phan-cach {
            display: flex; align-items: center; justify-content: center;
            padding-top: 26px; font-size: .8rem; color: #9ca3af; font-weight: 600;
        }
        .td-nut-tim {
            margin-top: 20px;
            width: 100%; padding: 13px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 8px;
            font-size: .95rem; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: background .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .td-nut-tim:hover { background: var(--primary-dark); }

        /* Thông báo lỗi */
        .td-loi {
            background: #fee2e2; color: #dc2626;
            padding: 12px 18px; border-radius: 8px;
            font-weight: 500; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        /* Kết quả */
        .td-ket-qua-tieude {
            font-size: .9rem; color: #6b7280; font-weight: 600;
            margin-bottom: 14px;
        }
        .td-the-don {
            background: #fff; border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            margin-bottom: 16px; overflow: hidden;
        }
        .td-dau-don {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            gap: 10px; flex-wrap: wrap;
        }
        .td-ma-don { font-size: .875rem; color: #374151; }
        .td-ma-don strong { color: #111; font-size: .95rem; }
        .td-danh-sach-san-pham { padding: 14px 20px; display: flex; flex-direction: column; gap: 12px; }
        .td-dong-sp { display: flex; align-items: center; gap: 14px; }
        .td-dong-sp img { width: 52px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #f3f4f6; }
        .td-ten-sach { font-size: .875rem; font-weight: 600; color: #1f2937; }
        .td-chi-tiet-sp { font-size: .8rem; color: #9ca3af; margin-top: 2px; }
        .td-chan-don {
            padding: 12px 20px;
            border-top: 1px solid #f3f4f6;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;
            background: #fafafa;
        }
        .td-ngay { font-size: .8rem; color: #9ca3af; }
        .td-tong { font-size: .95rem; font-weight: 700; color: var(--primary); }
        /* Badge trạng thái */
        .td-badge { padding: 4px 12px; border-radius: 20px; font-size: .78rem; font-weight: 700; white-space: nowrap; }
        .td-badge.cho-duyet  { background: #fef3c7; color: #92400e; }
        .td-badge.dang-giao  { background: #dbeafe; color: #1e40af; }
        .td-badge.hoan-thanh { background: #dcfce7; color: #15803d; }
        .td-badge.da-huy     { background: #fee2e2; color: #dc2626; }

        /* Đăng nhập gợi ý */
        .td-goi-y-dn {
            background: linear-gradient(135deg,#eff6ff,#dbeafe);
            border: 1px solid #bfdbfe;
            border-radius: 12px; padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 20px;
        }
        .td-goi-y-dn i { color: #2563eb; font-size: 1.4rem; flex-shrink: 0; }
        .td-goi-y-dn p { margin: 0; font-size: .875rem; color: #1e40af; }
        .td-goi-y-dn a { font-weight: 700; color: #2563eb; text-decoration: underline; }
    </style>
</head>
<body>
<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="td-trang">
    <a href="../../../index.php" class="td-quay-lai"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>

    <!-- Form tra cứu -->
    <div class="td-form-khung">
        <h1><i class="fas fa-search"></i> Tra Cứu Đơn Hàng</h1>
        <p class="mo-ta">Nhập mã đơn hàng hoặc số điện thoại — không cần đăng nhập.</p>

        <?php if (!$isLoggedIn): ?>
        <div class="td-goi-y-dn">
            <i class="fas fa-info-circle"></i>
            <p>
                Bạn đã có tài khoản? <a href="javascript:void(0)" onclick="openLogin()">Đăng nhập</a>
                để xem đầy đủ lịch sử đơn hàng và đánh giá sản phẩm.
            </p>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="td-luoi">
                <div class="td-nhom">
                    <label for="td-ma-don"><i class="fas fa-hashtag"></i> Mã đơn hàng</label>
                    <input type="text" id="td-ma-don" name="ma_don_hang"
                           value="<?= htmlspecialchars($_POST['ma_don_hang'] ?? '') ?>"
                           placeholder="VD: DH1745000012" autocomplete="off">
                </div>
                <div class="td-phan-cach">hoặc</div>
                <div class="td-nhom">
                    <label for="td-sdt"><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="tel" id="td-sdt" name="so_dien_thoai"
                           value="<?= htmlspecialchars($_POST['so_dien_thoai'] ?? '') ?>"
                           placeholder="VD: 0901234567" autocomplete="off">
                </div>
            </div>
            <button type="submit" class="td-nut-tim">
                <i class="fas fa-search"></i> Tra cứu ngay
            </button>
        </form>
    </div>

    <!-- Thông báo lỗi -->
    <?php if (!empty($thongBaoLoi)): ?>
    <div class="td-loi">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($thongBaoLoi) ?>
    </div>
    <?php endif; ?>

    <!-- Kết quả -->
    <?php if ($daTraCuu && !empty($dsDonHang)): ?>
    <p class="td-ket-qua-tieude">
        <i class="fas fa-check-circle" style="color:#16a34a;"></i>
        Tìm thấy <strong><?= count($dsDonHang) ?></strong> đơn hàng:
    </p>
    <?php foreach ($dsDonHang as $don):
        [$lopBadge, $nhanBadge, $bieuTuong] = nhanTrangThai($don['trangThai']);
        $cacSanPham = $chiTietDH[$don['maDH']] ?? [];
    ?>
    <div class="td-the-don">
        <div class="td-dau-don">
            <div class="td-ma-don">
                <?= $bieuTuong ?> Mã đơn: <strong><?= htmlspecialchars($don['maDH']) ?></strong>
                &nbsp;·&nbsp; <?= htmlspecialchars($don['tenPT']) ?>
            </div>
            <span class="td-badge <?= $lopBadge ?>"><?= $nhanBadge ?></span>
        </div>

        <div class="td-danh-sach-san-pham">
        <?php foreach ($cacSanPham as $sp): ?>
            <div class="td-dong-sp">
                <img src="<?= htmlspecialchars($sp['urlAnh'] ?? 'https://placehold.co/52x70?text=📚') ?>"
                     alt="<?= htmlspecialchars($sp['tenSach']) ?>"
                     onerror="this.src='https://placehold.co/52x70?text=📚'">
                <div>
                    <div class="td-ten-sach"><?= htmlspecialchars($sp['tenSach']) ?></div>
                    <div class="td-chi-tiet-sp">
                        x<?= (int)$sp['soLuong'] ?> &nbsp;·&nbsp;
                        <?= number_format($sp['thanhTien'], 0, ',', '.') ?>đ
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <div class="td-chan-don">
            <div class="td-ngay">
                <i class="far fa-clock"></i>
                <?= date('d/m/Y H:i', strtotime($don['ngayDat'])) ?>
                &nbsp;·&nbsp;
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars(mb_strimwidth($don['diaChiChiTiet'], 0, 38, '…')) ?>
            </div>
            <div class="td-tong">Tổng: <?= number_format($don['tongTien'], 0, ',', '.') ?>đ</div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>
<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>
<script>
function toggleUserMenu(e) {
    e.stopPropagation();
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.toggle('open');
}
document.addEventListener('click', function() {
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.remove('open');
});
function moCapNhatThongTin(e) {
    if (e) e.preventDefault();
    window.location.href = '../../../CuaHang/TrangBanHang/taiKhoan/capNhat.php';
}
</script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>
</body>
</html>
