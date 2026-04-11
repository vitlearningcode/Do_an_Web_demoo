<?php
/**
 * layDuLieuDonHang.php — Truy vấn DB lấy danh sách đơn hàng + chi tiết sản phẩm
 *
 * Input:  $maND (int), $tabHienTai (string), $trangThaiMap (array)
 * Output: $dsDonHang (array), $chiTietDH (array), $daDanhGia (array)
 * Yêu cầu: $pdo đã khai báo
 */

// ── Điều kiện lọc theo tab ──────────────────────────────────────────────────
$dieuKienWhere = 'WHERE dh.maND = :maND';
$thamSoTruyVan = [':maND' => $maND];

if ($tabHienTai !== 'tat-ca' && $trangThaiMap[$tabHienTai] !== null) {
    $dieuKienWhere .= ' AND dh.trangThai = :trangThai';
    $thamSoTruyVan[':trangThai'] = $trangThaiMap[$tabHienTai];
}

// ── Lấy danh sách đơn hàng ──────────────────────────────────────────────────
$sqlLayDonHang = "
    SELECT dh.maDH, dh.ngayDat, dh.tongTien, dh.trangThai,
           pt.tenPT, dcgh.diaChiChiTiet
    FROM DonHang dh
    JOIN PhuongThucThanhToan pt ON dh.maPT = pt.maPT
    JOIN DiaChiGiaoHang dcgh ON dh.maDC = dcgh.maDC
    $dieuKienWhere
    ORDER BY dh.ngayDat DESC
";

$stmtLayDonHang = $pdo->prepare($sqlLayDonHang);
$stmtLayDonHang->execute($thamSoTruyVan);
$dsDonHang = $stmtLayDonHang->fetchAll();

// ── Lấy chi tiết sản phẩm từng đơn ─────────────────────────────────────────
$chiTietDH = [];  // [maDH => [items]]
$daDanhGia  = []; // [maSach => true] — sách đã được user đánh giá

if (!empty($dsDonHang)) {
    $danhSachMaDH = array_column($dsDonHang, 'maDH');
    $danhSachMaDH_placeholder = implode(',', array_fill(0, count($danhSachMaDH), '?'));

    $sqlChiTiet = "
        SELECT ct.maDH, ct.maSach, ct.soLuong, ct.giaBan, ct.thanhTien,
               s.tenSach,
               ha.urlAnh
        FROM ChiTietDH ct
        JOIN Sach s ON ct.maSach = s.maSach
        LEFT JOIN (
            SELECT maSach, MIN(urlAnh) AS urlAnh
            FROM HinhAnhSach
            GROUP BY maSach
        ) ha ON ha.maSach = ct.maSach
        WHERE ct.maDH IN ($danhSachMaDH_placeholder)
        ORDER BY ct.maDH, ct.maSach
    ";
    $stmtChiTiet = $pdo->prepare($sqlChiTiet);
    $stmtChiTiet->execute($danhSachMaDH);
    $tatCaSanPham = $stmtChiTiet->fetchAll();

    foreach ($tatCaSanPham as $sanPham) {
        $chiTietDH[$sanPham['maDH']][] = $sanPham;
    }

    // Kiểm tra sách nào đã được user này đánh giá
    $sqlDanhGia = "SELECT maSach FROM DanhGiaSach WHERE maND = ?";
    $stmtDanhGia = $pdo->prepare($sqlDanhGia);
    $stmtDanhGia->execute([$maND]);
    foreach ($stmtDanhGia->fetchAll() as $danhGia) {
        $daDanhGia[$danhGia['maSach']] = true;
    }
}
