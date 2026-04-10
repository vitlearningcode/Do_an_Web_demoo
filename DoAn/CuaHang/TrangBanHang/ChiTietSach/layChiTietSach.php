<?php
/**
 * layChiTietSach.php
 * Trang chi tiết sách — render HTML thuần PHP (KHÔNG phải JSON API).
 *
 * Tham số GET:
 *   maSach (string) — mã định danh sách cần truy vấn
 *
 * Nếu không tìm thấy sách → hiển thị thông báo lỗi và nút quay lại.
 */

session_start();
require_once '../../../KetNoi/config/db.php';

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

if ($maSach !== '') {
    // ── Truy vấn đầy đủ ──────────────────────────────────────────────────────
    try {
        $truyVan = $pdo->prepare("
            SELECT
                s.maSach,
                s.tenSach,
                s.giaBan,
                s.namSX,
                s.moTa,
                s.soTrang,
                s.nhaXuatBan,
                s.hinhThucBia,
                s.kichThuoc,

                -- Ảnh bìa đầu tiên
                (SELECT urlAnh FROM HinhAnhSach
                 WHERE maSach = s.maSach LIMIT 1)                                  AS hinhAnh,

                -- Tác giả (ghép nhiều tác giả bằng dấu phẩy)
                (SELECT GROUP_CONCAT(tg.tenTG ORDER BY tg.tenTG SEPARATOR ', ')
                 FROM Sach_TacGia stg
                 JOIN TacGia tg ON tg.maTG = stg.maTG
                 WHERE stg.maSach = s.maSach)                                      AS tacGia,

                -- Thể loại đầu tiên
                (SELECT tl.tenTL
                 FROM Sach_TheLoai stl
                 JOIN TheLoai tl ON tl.maTL = stl.maTL
                 WHERE stl.maSach = s.maSach LIMIT 1)                              AS theLoai,

                -- Điểm đánh giá trung bình
                (SELECT ROUND(AVG(diemDG), 1)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS diemTB,

                -- Số lượt đánh giá
                (SELECT COUNT(*)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS soReview,

                -- Tổng đã bán (đơn Hoàn Thành)
                IFNULL((
                    SELECT SUM(ct.soLuong)
                    FROM ChiTietDH ct
                    JOIN DonHang dh ON dh.maDH = ct.maDH
                    WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                ), 0)                                                               AS tongBan,

                -- Phần trăm giảm Flash Sale đang áp dụng
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
        // Fallback: thử lại với query tối giản nếu bảng thiếu cột mở rộng
        try {
            $truyVanDuPhong = $pdo->prepare("
                SELECT
                    s.maSach, s.tenSach, s.giaBan, s.namSX,
                    NULL AS moTa,       NULL AS soTrang,
                    NULL AS nhaXuatBan, NULL AS hinhThucBia, NULL AS kichThuoc,

                    (SELECT urlAnh FROM HinhAnhSach
                     WHERE maSach = s.maSach LIMIT 1)                              AS hinhAnh,

                    (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
                     FROM Sach_TacGia stg JOIN TacGia tg ON tg.maTG = stg.maTG
                     WHERE stg.maSach = s.maSach)                                  AS tacGia,

                    (SELECT tl.tenTL FROM Sach_TheLoai stl
                     JOIN TheLoai tl ON tl.maTL = stl.maTL
                     WHERE stl.maSach = s.maSach LIMIT 1)                          AS theLoai,

                    (SELECT ROUND(AVG(diemDG), 1)
                     FROM DanhGiaSach WHERE maSach = s.maSach)                     AS diemTB,

                    (SELECT COUNT(*)
                     FROM DanhGiaSach WHERE maSach = s.maSach)                     AS soReview,

                    0                                                               AS tongBan,

                    (SELECT ckm.phanTramGiam
                     FROM ChiTietKhuyenMai ckm JOIN KhuyenMai km ON km.maKM = ckm.maKM
                     WHERE ckm.maSach = s.maSach
                       AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                     LIMIT 1)                                                       AS phanTramGiam

                FROM Sach s WHERE s.maSach = ?
            ");
            $truyVanDuPhong->execute([$maSach]);
            $sach = $truyVanDuPhong->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $loi2) {
            $sach = null;
        }
    }

    // Tính giá sau khuyến mãi
    if ($sach && !empty($sach['phanTramGiam'])) {
        $sach['giaSau'] = (int) round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
    } else if ($sach) {
        $sach['giaSau'] = null;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sach ? hienThiAn($sach['tenSach']) . ' - Book Sales' : 'Không tìm thấy sách' ?></title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .ct-container { max-width: 960px; margin: 40px auto; padding: 0 16px 80px; }
        .ct-back { display: inline-flex; align-items: center; gap: 6px; color: #6b7280;
                    font-size: .875rem; text-decoration: none; margin-bottom: 24px; }
        .ct-back:hover { color: #ee4d2d; }
        .ct-card { background: #fff; border-radius: 16px; padding: 36px;
                    box-shadow: 0 2px 16px rgba(0,0,0,.08); display: flex; gap: 36px; }
        .ct-anh { flex: 0 0 260px; }
        .ct-anh img { width: 100%; border-radius: 10px; object-fit: cover;
                       box-shadow: 0 4px 16px rgba(0,0,0,.15); }
        .ct-thongtin { flex: 1; }
        .ct-theloai { font-size: .8rem; color: #6366f1; font-weight: 600;
                       text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
        .ct-ten { font-size: 1.5rem; font-weight: 700; color: #111; margin: 0 0 8px; line-height: 1.35; }
        .ct-tacgia { color: #6b7280; font-size: .9rem; margin-bottom: 16px; }
        .ct-gia { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .ct-gia-moi { font-size: 1.6rem; font-weight: 800; color: #ee4d2d; }
        .ct-gia-cu  { font-size: 1rem; color: #9ca3af; text-decoration: line-through; }
        .ct-badge-giam { background: #fef2f2; color: #dc2626; font-size: .8rem;
                          font-weight: 700; padding: 2px 8px; border-radius: 6px; }
        .ct-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px;
                    padding: 16px 0; border-top: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6;
                    margin-bottom: 20px; }
        .ct-meta-item { font-size: .85rem; color: #6b7280; }
        .ct-meta-item strong { color: #374151; display: block; }
        .ct-mota { font-size: .9rem; color: #374151; line-height: 1.7; margin-bottom: 24px; }
        .ct-loi { text-align: center; padding: 60px 20px; }
        .ct-loi i { font-size: 3rem; color: #d1d5db; display: block; margin-bottom: 16px; }
        .ct-loi h2 { color: #374151; margin-bottom: 8px; }
        .ct-loi p { color: #9ca3af; margin-bottom: 24px; }
        .ct-btn-quaylai { display: inline-flex; align-items: center; gap: 8px;
                           background: #ee4d2d; color: #fff; padding: 10px 20px;
                           border-radius: 8px; text-decoration: none; font-weight: 600; }
        @media (max-width: 640px) {
            .ct-card { flex-direction: column; }
            .ct-anh { flex: none; }
        }
    </style>
</head>
<body>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="ct-container">
    <a href="../../../index.php" class="ct-back">
        <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
    </a>

    <?php if (!$sach): ?>
    <!-- Không tìm thấy sách -->
    <div class="ct-card">
        <div class="ct-loi">
            <i class="fas fa-book-open"></i>
            <h2>Không tìm thấy sách</h2>
            <p>Mã sách <strong><?= hienThiAn($maSach ?: '(trống)') ?></strong> không tồn tại hoặc đã bị xóa.</p>
            <a href="../../../index.php" class="ct-btn-quaylai">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- Thông tin chi tiết sách -->
    <div class="ct-card">
        <!-- Ảnh bìa -->
        <div class="ct-anh">
            <img
                src="<?= hienThiAn($sach['hinhAnh'] ?: 'https://placehold.co/260x380/eff6ff/2563eb?text=📚') ?>"
                alt="<?= hienThiAn($sach['tenSach']) ?>"
            >
        </div>

        <!-- Thông tin -->
        <div class="ct-thongtin">
            <?php if (!empty($sach['theLoai'])): ?>
            <p class="ct-theloai"><?= hienThiAn($sach['theLoai']) ?></p>
            <?php endif; ?>

            <h1 class="ct-ten"><?= hienThiAn($sach['tenSach']) ?></h1>

            <?php if (!empty($sach['tacGia'])): ?>
            <p class="ct-tacgia">
                <i class="fas fa-user-edit"></i>
                <?= hienThiAn($sach['tacGia']) ?>
            </p>
            <?php endif; ?>

            <!-- Giá -->
            <div class="ct-gia">
                <?php if (!empty($sach['giaSau'])): ?>
                    <span class="ct-gia-moi"><?= dinhDangGia($sach['giaSau']) ?></span>
                    <span class="ct-gia-cu"><?= dinhDangGia($sach['giaBan']) ?></span>
                    <span class="ct-badge-giam">-<?= (int)$sach['phanTramGiam'] ?>%</span>
                <?php else: ?>
                    <span class="ct-gia-moi"><?= dinhDangGia($sach['giaBan']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Thông tin xuất bản -->
            <div class="ct-meta">
                <div class="ct-meta-item">
                    <strong><?= hienThiAn($sach['nhaXuatBan'], 'Đang cập nhật') ?></strong>
                    Nhà xuất bản
                </div>
                <div class="ct-meta-item">
                    <strong><?= hienThiAn($sach['namSX'], 'Đang cập nhật') ?></strong>
                    Năm xuất bản
                </div>
                <div class="ct-meta-item">
                    <strong><?= hienThiAn($sach['hinhThucBia'], 'Đang cập nhật') ?></strong>
                    Hình thức bìa
                </div>
                <div class="ct-meta-item">
                    <strong><?= $sach['soTrang'] ? (int)$sach['soTrang'] . ' trang' : 'Đang cập nhật' ?></strong>
                    Số trang
                </div>
                <?php if (!empty($sach['diemTB'])): ?>
                <div class="ct-meta-item">
                    <strong>⭐ <?= number_format((float)$sach['diemTB'], 1) ?> / 5</strong>
                    <?= (int)$sach['soReview'] ?> đánh giá
                </div>
                <?php endif; ?>
                <?php if ((int)$sach['tongBan'] > 0): ?>
                <div class="ct-meta-item">
                    <strong><?= number_format((int)$sach['tongBan'], 0, ',', '.') ?> cuốn</strong>
                    Đã bán
                </div>
                <?php endif; ?>
            </div>

            <!-- Mô tả -->
            <?php if (!empty($sach['moTa'])): ?>
            <div class="ct-mota">
                <p><?= nl2br(hienThiAn($sach['moTa'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Nút hành động -->
            <a href="../../../index.php" class="ct-btn-quaylai">
                <i class="fas fa-store"></i> Mua thêm sách khác
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/footer.php'; ?>

</body>
</html>
