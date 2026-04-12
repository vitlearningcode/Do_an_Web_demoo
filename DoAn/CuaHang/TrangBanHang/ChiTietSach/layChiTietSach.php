<?php
/**
 * layChiTietSach.php — v2 (Premium)
 * Trang chi tiết sách — render HTML thuần PHP (KHÔNG phải JSON API).
 *
 * Tham số GET:
 *   maSach (string) — mã định danh sách cần truy vấn
 *
 * Layout: ảnh bìa | thông tin đầy đủ + sách liên quan
 */

session_start();
require_once '../../../KetNoi/config/db.php';
require_once '../../TrangBanHang/Components/bookCard.php';

// ── Hàm tiện ích ──────────────────────────────────────────────────────────────

/** Lấy văn bản hiển thị an toàn (tránh XSS) */
function hienThiAn($chuoi, $macc = '') {
    return htmlspecialchars((string)($chuoi ?? $macc), ENT_QUOTES, 'UTF-8');
}

/** Định dạng giá tiền kiểu Việt Nam */
function dinhDangGia($soTien) {
    return number_format((float)$soTien, 0, ',', '.') . ' ₫';
}

// ── Kiểm tra tham số đầu vào ──────────────────────────────────────────────────
$maSach = trim($_GET['maSach'] ?? '');
$sach   = null;
$ds_lienQuan = [];

if ($maSach !== '') {
    try {
        $truyVan = $pdo->prepare("
            SELECT
                s.maSach,
                s.tenSach,
                s.giaBan,
                s.namSX,
                s.moTa,
                s.loaiBia     AS hinhThucBia,
                s.soLuongTon,
                (SELECT tenNXB FROM NhaXuatBan nxb WHERE nxb.maNXB = s.maNXB) AS nhaXuatBan,

                (SELECT urlAnh FROM HinhAnhSach
                 WHERE maSach = s.maSach LIMIT 1)                                  AS hinhAnh,

                (SELECT GROUP_CONCAT(tg.tenTG ORDER BY tg.tenTG SEPARATOR ', ')
                 FROM Sach_TacGia stg
                 JOIN TacGia tg ON tg.maTG = stg.maTG
                 WHERE stg.maSach = s.maSach)                                      AS tacGia,

                (SELECT tl.tenTL
                 FROM Sach_TheLoai stl
                 JOIN TheLoai tl ON tl.maTL = stl.maTL
                 WHERE stl.maSach = s.maSach LIMIT 1)                              AS theLoai,

                (SELECT stl.maTL
                 FROM Sach_TheLoai stl
                 WHERE stl.maSach = s.maSach LIMIT 1)                              AS maTL,

                (SELECT ROUND(AVG(diemDG), 1)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS diemTB,

                (SELECT COUNT(*)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS soReview,

                IFNULL((
                    SELECT SUM(ct.soLuong)
                    FROM ChiTietDH ct
                    JOIN DonHang dh ON dh.maDH = ct.maDH
                    WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                ), 0)                                                               AS tongBan,

                (SELECT ckm.phanTramGiam
                 FROM ChiTietKhuyenMai ckm
                 JOIN KhuyenMai km ON km.maKM = ckm.maKM
                 WHERE ckm.maSach = s.maSach
                   AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                 LIMIT 1)                                                           AS phanTramGiam

            FROM Sach s
            WHERE s.maSach = ?
        ");
        $truyVan->execute([$maSach]);
        $sach = $truyVan->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $loi) {
        // DEV: ghi lỗi để debug
        error_log('[layChiTietSach] PDOException: ' . $loi->getMessage());
        $sach = null;
    }

    // Tính giá sau khuyến mãi
    if ($sach) {
        if (!empty($sach['phanTramGiam'])) {
            $sach['giaSau'] = (int) round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
        } else {
            $sach['giaSau'] = null;
        }

        // ── Sách liên quan (cùng thể loại, loại trừ sách hiện tại) ────────
        if (!empty($sach['maTL'])) {
            try {
                $truyVanLQ = $pdo->prepare("
                    SELECT
                        s.maSach,
                        s.tenSach,
                        s.giaBan,
                        s.soLuongTon,
                        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1)     AS hinhAnh,
                        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
                         FROM Sach_TacGia stg JOIN TacGia tg ON tg.maTG = stg.maTG
                         WHERE stg.maSach = s.maSach)                                        AS tacGia,
                        (SELECT tl.tenTL FROM Sach_TheLoai stl
                         JOIN TheLoai tl ON tl.maTL = stl.maTL
                         WHERE stl.maSach = s.maSach LIMIT 1)                               AS theLoai,
                        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach
                         WHERE maSach = s.maSach)                                            AS diemTB,
                        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach)           AS soReview,
                        IFNULL((
                            SELECT SUM(ct.soLuong) FROM ChiTietDH ct
                            JOIN DonHang dh ON dh.maDH = ct.maDH
                            WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                        ), 0)                                                                AS tongBan,
                        (SELECT ckm.phanTramGiam FROM ChiTietKhuyenMai ckm
                         JOIN KhuyenMai km ON km.maKM = ckm.maKM
                         WHERE ckm.maSach = s.maSach
                           AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                         LIMIT 1)                                                            AS phanTramGiam
                    FROM Sach s
                    JOIN Sach_TheLoai stl ON stl.maSach = s.maSach
                    WHERE stl.maTL = ?
                      AND s.maSach <> ?
                    ORDER BY RAND()
                    LIMIT 8
                ");
                $truyVanLQ->execute([$sach['maTL'], $maSach]);
                $ds_lienQuan = $truyVanLQ->fetchAll(PDO::FETCH_ASSOC);

                // Tính giá sau giảm cho từng sách liên quan
                foreach ($ds_lienQuan as &$sl) {
                    if (!empty($sl['phanTramGiam'])) {
                        $sl['giaSau'] = (int) round($sl['giaBan'] * (1 - $sl['phanTramGiam'] / 100));
                    } else {
                        $sl['giaSau'] = null;
                    }
                }
                unset($sl);

            } catch (PDOException $e) {
                $ds_lienQuan = [];
            }
        }
    }
}

// ── Kiểm tra đăng nhập ────────────────────────────────────────────────────────
$isLoggedIn    = isset($_SESSION['maNguoiDung']);
$phai_xoa_cart = false;
$duong_dan_goc = '/DoAn-Web/DoAn/';

// ── Biến $tonKho luôn có giá trị mặc định ────────────────────────────────────
$tonKho = 0;

// Giá hiển thị
$giaHienTai = $sach ? ($sach['giaSau'] ?? $sach['giaBan']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sach ? hienThiAn($sach['tenSach']) . ' — Book Sales' : 'Không tìm thấy sách' ?></title>
    <meta name="description" content="<?= $sach ? hienThiAn(mb_substr($sach['moTa'] ?? '', 0, 160)) : 'Trang chi tiết sách' ?>">
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <?php if ($isLoggedIn): ?>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <?php else: ?>
    <script>var cartServerData = null;</script>
    <?php endif; ?>
    <style>
    /* ================================================================
       CSS — Trang Chi Tiết Sách (Premium inBook style)
       ================================================================ */
    *, *::before, *::after { box-sizing: border-box; }

    body { font-family: 'Inter', sans-serif; background: #f5f6fa; color: #1a202c; }

    /* ── Breadcrumb ── */
    .ct-breadcrumb {
        max-width: 1200px;
        margin: 0 auto;
        padding: 18px 24px 0;
        font-size: 13px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .ct-breadcrumb a { color: #6b7280; text-decoration: none; transition: color .2s; }
    .ct-breadcrumb a:hover { color: #2563eb; }
    .ct-breadcrumb .sep { color: #d1d5db; }
    .ct-breadcrumb .current { color: #1a202c; font-weight: 500; }

    /* ── Main container ── */
    .ct-wrap {
        max-width: 1200px;
        margin: 20px auto 60px;
        padding: 0 24px;
    }

    /* ── Layout toàn trang: 2 cột ── */
    .ct-layout-wrap {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }

    /* Cột trái — ảnh bìa — sticky độc lập (không có overflow parent) */
    .ct-col-left {
        flex: 0 0 300px;
        position: sticky;
        top: 180px;          /* header ~160px + 20px khoảng cách */
        align-self: flex-start;
    }

    /* Cột phải — thông tin — scroll bình thường */
    .ct-col-right {
        flex: 1;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 2px 24px rgba(0,0,0,.07);
        padding: 36px;
    }

    .ct-cover-wrap {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,.18);
        aspect-ratio: 3/4;
        background: #f1f5f9;
    }
    .ct-cover-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }
    .ct-cover-wrap:hover img { transform: scale(1.03); }

    .ct-badge-km {
        position: absolute;
        top: 14px;
        left: 14px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(220,38,38,.4);
    }

    /* ── Cột phải: Thông tin ── */
    .ct-right {}

    .ct-theloai-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: #6366f1;
        background: #eef2ff;
        border-radius: 20px;
        padding: 4px 12px;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 14px;
    }

    .ct-ten-sach {
        font-size: 1.7rem;
        font-weight: 800;
        color: #111827;
        line-height: 1.3;
        margin: 0 0 10px;
    }

    .ct-tac-gia {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 16px;
    }
    .ct-tac-gia a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }
    .ct-tac-gia a:hover { text-decoration: underline; }

    /* Rating bar */
    .ct-rating-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .ct-sao { display: flex; gap: 2px; }
    .ct-sao i { font-size: 15px; color: #facc15; }
    .ct-diem { font-size: 15px; font-weight: 700; color: #374151; }
    .ct-so-review { font-size: 13px; color: #6b7280; }
    .ct-da-ban { font-size: 13px; color: #6b7280; }
    .ct-sep { width: 4px; height: 4px; background: #d1d5db; border-radius: 50%; flex-shrink: 0; }

    /* Divider */
    .ct-divider { border: none; border-top: 1px solid #f3f4f6; margin: 20px 0; }

    /* Giá */
    .ct-gia-block {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .ct-gia-moi {
        font-size: 2rem;
        font-weight: 800;
        color: #ee4d2d;
        line-height: 1;
    }
    .ct-gia-cu {
        font-size: 1rem;
        color: #9ca3af;
        text-decoration: line-through;
        font-weight: 500;
    }
    .ct-pct-giam {
        display: inline-flex;
        align-items: center;
        background: #fef2f2;
        color: #dc2626;
        font-size: 13px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid #fecaca;
    }

    /* Số lượng + nút mua */
    .ct-mua-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .ct-so-luong {
        display: flex;
        align-items: center;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        height: 46px;
    }
    .ct-sl-btn {
        width: 40px;
        height: 100%;
        background: #f9fafb;
        border: none;
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: background .2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ct-sl-btn:hover { background: #e5e7eb; }
    .ct-sl-input {
        width: 52px;
        height: 100%;
        text-align: center;
        border: none;
        border-left: 2px solid #e5e7eb;
        border-right: 2px solid #e5e7eb;
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        outline: none;
    }

    .ct-btn-them-gio {
        flex: 1;
        min-width: 160px;
        height: 46px;
        background: #fff;
        border: 2px solid #2563eb;
        color: #2563eb;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all .25s;
    }
    .ct-btn-them-gio:hover {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 4px 16px rgba(37,99,235,.3);
    }

    .ct-btn-mua-ngay {
        flex: 1;
        min-width: 160px;
        height: 46px;
        background: linear-gradient(135deg, #ee4d2d, #ff6533);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: all .25s;
        box-shadow: 0 4px 16px rgba(238,77,45,.3);
    }
    .ct-btn-mua-ngay:hover {
        background: linear-gradient(135deg, #dc2626, #ee4d2d);
        box-shadow: 0 6px 20px rgba(238,77,45,.45);
        transform: translateY(-1px);
    }

    /* Thông tin sản phẩm dạng lưới */
    .ct-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 24px;
        background: #f9fafb;
        border-radius: 14px;
        padding: 20px;
    }
    .ct-meta-row {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .ct-meta-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
    }
    .ct-meta-value {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }

    /* ── Tồn kho tag ── */
    .ct-ton-kho-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        padding: 3px 10px;
        margin-top: 14px;
    }
    .ct-ton-kho-tag.con-hang {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .ct-ton-kho-tag.sap-het {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
    }
    .ct-ton-kho-tag.het-hang {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* ── Mô tả sách ── */
    .ct-mota-section {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 2px 16px rgba(0,0,0,.06);
        padding: 32px;
        margin-top: 24px;
    }
    .ct-section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #111827;
        margin: 0 0 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ct-section-title::before {
        content: '';
        display: block;
        width: 4px;
        height: 20px;
        background: linear-gradient(to bottom, #2563eb, #6366f1);
        border-radius: 4px;
        flex-shrink: 0;
    }
    .ct-mota-text {
        font-size: 14px;
        line-height: 1.85;
        color: #374151;
        white-space: pre-line;
        max-height: 220px;
        overflow: hidden;
        position: relative;
        transition: max-height .4s ease;
    }
    .ct-mota-text.expanded { max-height: 9999px; }
    .ct-mota-fade {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(to top, #fff 40%, transparent);
        pointer-events: none;
        transition: opacity .3s;
    }
    .ct-mota-text.expanded + .ct-mota-fade { opacity: 0; }
    .ct-btn-xem-them {
        background: none;
        border: none;
        color: #2563eb;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        padding: 10px 0 0;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color .2s;
    }
    .ct-btn-xem-them:hover { color: #1d4ed8; }

    /* ── Section sách liên quan ── */
    .ct-lien-quan-section {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 2px 16px rgba(0,0,0,.06);
        padding: 32px;
        margin-top: 24px;
    }

    /* ── Error state ── */
    .ct-error-card {
        background: #fff;
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 2px 24px rgba(0,0,0,.07);
    }
    .ct-error-icon { font-size: 3.5rem; color: #d1d5db; margin-bottom: 20px; }
    .ct-error-card h2 { font-size: 1.3rem; color: #374151; margin-bottom: 8px; }
    .ct-error-card p { color: #9ca3af; margin-bottom: 28px; }

    .ct-btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ee4d2d;
        color: #fff;
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        transition: all .25s;
    }
    .ct-btn-back:hover { background: #dc2626; transform: translateY(-1px); }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .ct-layout-wrap {
            flex-direction: column;
            gap: 20px;
        }
        .ct-col-left {
            flex: none;
            position: static; /* disable sticky on mobile */
            width: 100%;
        }
        .ct-cover-wrap { max-width: 200px; margin: 0 auto; }
        .ct-col-right { padding: 20px; }
        .ct-ten-sach { font-size: 1.3rem; }
        .ct-gia-moi { font-size: 1.5rem; }
        .ct-mua-wrap { gap: 10px; }
        .ct-btn-them-gio, .ct-btn-mua-ngay { min-width: 0; flex: 1; }
    }
    @media (max-width: 480px) {
        .ct-wrap { padding: 0 12px; }
        .ct-meta-grid { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<!-- ── Breadcrumb ── -->
<nav class="ct-breadcrumb" aria-label="Breadcrumb">
    <a href="<?= $duong_dan_goc ?>index.php"><i class="fas fa-home"></i> Trang chủ</a>
    <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px"></i></span>
    <?php if ($sach && !empty($sach['theLoai'])): ?>
    <a href="<?= $duong_dan_goc ?>index.php"><?= hienThiAn($sach['theLoai']) ?></a>
    <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px"></i></span>
    <?php endif; ?>
    <span class="current"><?= $sach ? hienThiAn(mb_substr($sach['tenSach'], 0, 50)) : 'Không tìm thấy' ?></span>
</nav>

<div class="ct-wrap">

<?php if (!$sach): ?>
    <!-- ── Lỗi: Không tìm thấy sách ── -->
    <div class="ct-error-card">
        <div class="ct-error-icon"><i class="fas fa-book-open"></i></div>
        <h2>Không tìm thấy sách</h2>
        <p>Mã sách <strong><?= hienThiAn($maSach ?: '(trống)') ?></strong> không tồn tại hoặc đã bị xóa.</p>
        <a href="<?= $duong_dan_goc ?>index.php" class="ct-btn-back">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
    </div>

<?php else:
    $giaBan     = (float)$sach['giaBan'];
    $giaSau     = $sach['giaSau'];
    $giaHienTai = $giaSau ?? $giaBan;
    $giam       = (int)($sach['phanTramGiam'] ?? 0);
    $diem       = (float)($sach['diemTB'] ?? 0);
    $soReview   = (int)($sach['soReview'] ?? 0);
    $tongBan    = (int)($sach['tongBan'] ?? 0);
    $tonKho     = (int)($sach['soLuongTon'] ?? 0);
    $hinhAnh    = !empty($sach['hinhAnh']) ? hienThiAn($sach['hinhAnh']) : 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
?>

    <!-- ── Layout 2 cột: ảnh | thông tin ── -->
    <div class="ct-layout-wrap">

        <!-- Cột trái: ảnh bìa (sticky riêng) -->
        <div class="ct-col-left">
            <div class="ct-cover-wrap">
                <img src="<?= $hinhAnh ?>" alt="<?= hienThiAn($sach['tenSach']) ?>" id="ct-anh-bia">
                <?php if ($giam > 0): ?>
                <div class="ct-badge-km">-<?= $giam ?>%</div>
                <?php endif; ?>
            </div>
        </div><!-- /ct-col-left -->

        <!-- Cột phải: thông tin -->
        <div class="ct-col-right">

            <!-- Thể loại chip -->
            <?php if (!empty($sach['theLoai'])): ?>
            <div class="ct-theloai-chip">
                <i class="fas fa-tag"></i>
                <?= hienThiAn($sach['theLoai']) ?>
            </div>
            <?php endif; ?>

            <!-- Tên sách -->
            <h1 class="ct-ten-sach"><?= hienThiAn($sach['tenSach']) ?></h1>

            <!-- Tác giả -->
            <?php if (!empty($sach['tacGia'])): ?>
            <p class="ct-tac-gia">
                <i class="fas fa-user-edit fa-sm" style="color:#6366f1;margin-right:5px"></i>
                Tác giả: <a href="#"><?= hienThiAn($sach['tacGia']) ?></a>
            </p>
            <?php endif; ?>

            <!-- Rating -->
            <div class="ct-rating-bar">
                <?php if ($diem > 0): ?>
                <div class="ct-sao">
                    <?php for ($i = 1; $i <= 5; $i++):
                        if ($i <= floor($diem)) echo '<i class="fas fa-star"></i>';
                        elseif ($i < $diem + 1) echo '<i class="fas fa-star-half-alt"></i>';
                        else echo '<i class="far fa-star" style="color:#d1d5db"></i>';
                    endfor; ?>
                </div>
                <span class="ct-diem"><?= number_format($diem, 1) ?></span>
                <span class="ct-sep"></span>
                <?php endif; ?>
                <?php if ($soReview > 0): ?>
                <span class="ct-so-review"><?= number_format($soReview, 0, ',', '.') ?> đánh giá</span>
                <span class="ct-sep"></span>
                <?php endif; ?>
                <?php if ($tongBan > 0): ?>
                <span class="ct-da-ban">Đã bán <?= $tongBan >= 1000 ? number_format($tongBan/1000, 1) . 'k+' : $tongBan . '+' ?></span>
                <?php endif; ?>
            </div>

            <hr class="ct-divider">

            <!-- Giá -->
            <div class="ct-gia-block">
                <span class="ct-gia-moi"><?= dinhDangGia($giaHienTai) ?></span>
                <?php if ($giaSau !== null && $giaBan > $giaHienTai): ?>
                <span class="ct-gia-cu"><?= dinhDangGia($giaBan) ?></span>
                <span class="ct-pct-giam">-<?= $giam ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Tồn kho -->
            <?php if ($tonKho <= 0): ?>
            <div class="ct-ton-kho-tag het-hang"><i class="fas fa-times-circle"></i> Hết hàng</div>
            <?php elseif ($tonKho <= 5): ?>
            <div class="ct-ton-kho-tag sap-het"><i class="fas fa-exclamation-triangle"></i> Sắp hết — còn <?= $tonKho ?> cuốn</div>
            <?php else: ?>
            <div class="ct-ton-kho-tag con-hang"><i class="fas fa-check-circle"></i> Còn hàng</div>
            <?php endif; ?>

            <hr class="ct-divider">

            <!-- Số lượng + Nút mua -->
            <?php if ($tonKho > 0): ?>
            <div class="ct-mua-wrap">
                <!-- Số lượng -->
                <div class="ct-so-luong">
                    <button class="ct-sl-btn" id="ct-giam-sl" type="button">−</button>
                    <input class="ct-sl-input" id="ct-so-luong" type="number" value="1" min="1" max="<?= $tonKho ?>">
                    <button class="ct-sl-btn" id="ct-tang-sl" type="button">+</button>
                </div>

                <!-- Thêm vào giỏ -->
                <button class="ct-btn-them-gio" id="ct-btn-them-gio"
                    data-id="<?= hienThiAn($sach['maSach']) ?>"
                    data-name="<?= hienThiAn($sach['tenSach']) ?>"
                    data-price="<?= $giaHienTai ?>"
                    data-image="<?= $hinhAnh ?>"
                    data-tac-gia="<?= hienThiAn($sach['tacGia'] ?? '') ?>">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>

                <!-- Mua ngay -->
                <button class="ct-btn-mua-ngay" id="ct-btn-mua-ngay"
                    data-id="<?= hienThiAn($sach['maSach']) ?>"
                    data-name="<?= hienThiAn($sach['tenSach']) ?>"
                    data-price="<?= $giaHienTai ?>"
                    data-image="<?= $hinhAnh ?>"
                    data-tac-gia="<?= hienThiAn($sach['tacGia'] ?? '') ?>">
                    <i class="fas fa-bolt"></i> Mua ngay
                </button>
            </div>
            <?php else: ?>
            <p style="color:#dc2626;font-weight:600;font-size:14px;margin-bottom:20px">
                <i class="fas fa-bell" style="margin-right:6px"></i>
                Sản phẩm tạm hết hàng, vui lòng quay lại sau.
            </p>
            <?php endif; ?>

            <!-- Meta thông tin -->
            <div class="ct-meta-grid">
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Nhà xuất bản</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['nhaXuatBan'] ?? 'Đang cập nhật') ?></span>
                </div>
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Năm xuất bản</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['namSX'] ?? 'Đang cập nhật') ?></span>
                </div>
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Hình thức bìa</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['hinhThucBia'] ?? 'Đang cập nhật') ?></span>
                </div>

                <div class="ct-meta-row">
                    <span class="ct-meta-label">Mã sách</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['maSach']) ?></span>
                </div>
            </div>

        </div><!-- /ct-col-right -->
    </div><!-- /ct-layout-wrap -->

    <!-- ── Mô tả sách ── -->
    <?php if (!empty($sach['moTa'])): ?>
    <div class="ct-mota-section">
        <h2 class="ct-section-title">Mô tả sản phẩm</h2>
        <div class="ct-mota-text" id="ct-mota-text">
            <?= nl2br(hienThiAn($sach['moTa'])) ?>
        </div>
        <div class="ct-mota-fade" id="ct-mota-fade"></div>
        <?php if (mb_strlen($sach['moTa']) > 300): ?>
        <button class="ct-btn-xem-them" id="ct-xem-them">
            <i class="fas fa-chevron-down" id="ct-icon-xem-them"></i>
            <span id="ct-text-xem-them">Xem thêm</span>
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Sách liên quan ── -->
    <?php if (!empty($ds_lienQuan)): ?>
    <div class="ct-lien-quan-section">
        <h2 class="ct-section-title">Có thể bạn quan tâm</h2>
        <div class="books-grid" style="--columns:4">
            <?php foreach ($ds_lienQuan as $sl):
                $nhanLQ = [];
                if (!empty($sl['phanTramGiam'])) {
                    $nhanLQ[] = ['class' => 'label-discount', 'label' => '-' . (int)$sl['phanTramGiam'] . '%'];
                }
                echo hienThiTheSach($sl, $nhanLQ);
            endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

</div><!-- /ct-wrap -->

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/footer.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/ChiTietSach/formXemNhanhSach.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>

<script src="<?= $duong_dan_goc ?>PhuongThuc/components/thongBao.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/cart.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/bookCard.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacThuc.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/chatbot.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/btnThemGioHang.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/xemNhanhSach.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>

<script>
(function () {
  'use strict';

  // ── Số lượng ─────────────────────────────────────────────────────────
  var slInput = document.getElementById('ct-so-luong');
  var tonKho  = <?= $tonKho ?>;

  var nutGiam = document.getElementById('ct-giam-sl');
  var nutTang = document.getElementById('ct-tang-sl');

  if (nutGiam && slInput) {
    nutGiam.addEventListener('click', function () {
      var v = parseInt(slInput.value) || 1;
      if (v > 1) { slInput.value = v - 1; }
    });
  }
  if (nutTang && slInput) {
    nutTang.addEventListener('click', function () {
      var v = parseInt(slInput.value) || 1;
      if (v < tonKho) { slInput.value = v + 1; }
    });
  }
  if (slInput) {
    slInput.addEventListener('change', function () {
      var v = parseInt(slInput.value) || 1;
      if (v < 1) v = 1;
      if (v > tonKho) v = tonKho;
      slInput.value = v;
    });
  }

  // ── Thêm vào giỏ từ trang chi tiết ──────────────────────────────────
  function layThongTinSach(btn) {
    return {
      maSach : btn.getAttribute('data-id')      || '',
      tenSach: btn.getAttribute('data-name')    || '',
      giaBan : parseFloat(btn.getAttribute('data-price')) || 0,
      hinhAnh: btn.getAttribute('data-image')   || '',
      tacGia : btn.getAttribute('data-tac-gia') || '',
    };
  }

  var nutThemGio = document.getElementById('ct-btn-them-gio');
  if (nutThemGio) {
    nutThemGio.addEventListener('click', function () {
      if (typeof dangDangNhap === 'undefined' || !dangDangNhap) {
        alert('Bạn cần đăng nhập để thêm sách vào giỏ hàng!');
        if (typeof openLogin === 'function') openLogin();
        return;
      }
      var sl = parseInt(slInput ? slInput.value : 1) || 1;
      if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(layThongTinSach(nutThemGio), sl);
      }
    });
  }

  var nutMuaNgay = document.getElementById('ct-btn-mua-ngay');
  if (nutMuaNgay) {
    nutMuaNgay.addEventListener('click', function () {
      if (typeof dangDangNhap === 'undefined' || !dangDangNhap) {
        alert('Bạn cần đăng nhập để mua hàng!');
        if (typeof openLogin === 'function') openLogin();
        return;
      }
      var sl = parseInt(slInput ? slInput.value : 1) || 1;
      if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(layThongTinSach(nutMuaNgay), sl);
        // Chuyển thẳng vào giỏ hàng (mở drawer)
        cartDrawer.open();
      }
    });
  }

  // ── Xem thêm / Thu gọn mô tả ─────────────────────────────────────────
  var elMota     = document.getElementById('ct-mota-text');
  var elFade     = document.getElementById('ct-mota-fade');
  var btnXemThem = document.getElementById('ct-xem-them');
  var iconArrow  = document.getElementById('ct-icon-xem-them');
  var textSpan   = document.getElementById('ct-text-xem-them');

  if (btnXemThem && elMota) {
    btnXemThem.addEventListener('click', function () {
      var expanded = elMota.classList.toggle('expanded');
      if (iconArrow) {
        iconArrow.className = expanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
      }
      if (textSpan) {
        textSpan.textContent = expanded ? 'Thu gọn' : 'Xem thêm';
      }
    });
  }

})();
</script>

</body>
</html>
